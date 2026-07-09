<?php
require_once __DIR__ . '/../Interfaces/ImportadorProdutos.php';

class ImportadorPrintful implements ImportadorProdutos {
	private $token;

	public function __construct($token) {
		$this->token = $token;
	}

	private function requisicao($endpoint) {
		$url = 'https://api.printful.com/' . $endpoint;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Authorization: Bearer ' . $this->token,
			'Content-Type: application/json'
		]);

		$resposta = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		registrar_log('Requisição à API: ' . $url, __FILE__);
		registrar_log('Resposta da API: ' . $resposta, __FILE__);

		if ($httpcode >= 200 && $httpcode < 300) {
			return json_decode($resposta, true);
		} else {
			throw new Exception('Erro na requisição à API: ' . $resposta);
		}
	}

	public function importarProdutos() {
		// Implementação de acordo com os requisitos da interface
		$resposta = $this->requisicao('store/products');
		registrar_log('Produtos importados: ' . json_encode($resposta['result']), __FILE__);
		return $resposta['result'];
	}

	public function importarVariantes($produtoId) {
		$resposta = $this->requisicao('store/products/' . $produtoId);
		registrar_log('Detalhes do produto para ' . $produtoId . ': ' . json_encode($resposta['result']), __FILE__);

		if (isset($resposta['result']['sync_variants'])) {
			$variantes = [];
			$precoMinimo = PHP_INT_MAX;
			$precoMaximo = PHP_INT_MIN;

			foreach ($resposta['result']['sync_variants'] as $variant) {
				$detalhes_variacao = $this->requisicao('store/variants/' . $variant['id']);

				if (isset($detalhes_variacao['result'])) {
					// Adiciona a imagem da variante
					$imagem = isset($detalhes_variacao['result']['product']['image'])
						? $detalhes_variacao['result']['product']['image']
						: null;

					$variantes[] = [
						'id' => $detalhes_variacao['result']['id'],
						'sku' => $detalhes_variacao['result']['sku'] ?? 'N/A',
						'name' => $detalhes_variacao['result']['name'],
						'price' => floatval($detalhes_variacao['result']['retail_price']),
						'currency' => $detalhes_variacao['result']['currency'] ?? 'USD',
						'size' => $detalhes_variacao['result']['size'] ?? null,
						'color' => $detalhes_variacao['result']['color'] ?? null,
						'image' => $imagem,
					];

					$preco = floatval($detalhes_variacao['result']['retail_price']);
					if ($preco < $precoMinimo) {
						$precoMinimo = $preco;
					}
					if ($preco > $precoMaximo) {
						$precoMaximo = $preco;
					}
				} else {
					registrar_log('Detalhes não encontrados para a variante ' . $variant['id'], __FILE__);
				}
			}

			return [
				'variantes' => $variantes,
				'preco_minimo' => $precoMinimo,
				'preco_maximo' => $precoMaximo
			];
		} else {
			throw new Exception('Variantes não encontradas para o produto: ' . $produtoId);
		}
	}
}
?>
