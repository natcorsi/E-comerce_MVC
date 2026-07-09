<?php

class PrintfulModelo extends Modelo {
   private $token;

   public function __construct() {
      parent::__construct();
      $this->token = 'yMsLvsYNEQuegKPSPkSU1FiwuafuKJp2BZm9Qid3'; // Substitua pela sua chave de API da Printful
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
      error_log("Resposta da API para o endpoint {$endpoint}: " . $resposta);

      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      if ($httpcode >= 200 && $httpcode < 300) {
         return json_decode($resposta, true);
      } else {
         throw new Exception('Erro na requisição à API: ' . $resposta);
      }
   }

   public function obterProdutos() {
      try {
         // Requisita a lista de produtos
         $resposta = $this->requisicao('store/products');
         registrar_log("Produtos obtidos: " . print_r($resposta, true));  // Log da resposta dos produtos

         $produtos = $resposta['result'];

         foreach ($produtos as &$produto) {
            // Verifica se o produto tem variantes antes de fazer a requisição
            $detalhesProduto = $this->requisicao('store/products/' . $produto['id']);

            // Verifica se 'variants' é um array e se contém variantes
            if (is_array($produto['variants']) && count($produto['variants']) > 0) {
               $produto['variantes'] = $this->obterVariantes($produto['id']);
            } else {
               // Se não houver variantes, apenas registre o produto sem variantes
               $produto['variantes'] = [];
            }

            // Log para depuração
            registrar_log("Produto ID " . $produto['id'] . " - Variantes: " . print_r($produto['variantes'], true));

            // A descrição e o preço do produto principal
            $produto['descricao'] = $detalhesProduto['result']['description'] ?? '';
            $produto['preco'] = $produto['variantes'][0]['price'] ?? 0;


            // A imagem do produto
            $produto['imagem'] = $detalhesProduto['result']['thumbnail_url'] ?? '';

            // Sincronizando variantes
            $produto['variantes'] = $this->obterVariantes($produto['id']);
         }

         return $produtos;
      } catch (Exception $e) {
         registrar_log("Erro ao obter produtos: " . $e->getMessage());
         throw new Exception('Erro ao obter produtos: ' . $e->getMessage());
      }
   }

   // Insere ou atualiza o produto principal na tabela produtos
   public function salvarProduto($produto) {
      registrar_log("Salvando produto: " . print_r($produto, true));

      $sql = "INSERT INTO produtos (nome, descricao, preco, preco_max, imagem, categoria_id, data_criacao, data_atualizacao) 
            VALUES (:nome, :descricao, :preco, :preco_max, :imagem, :categoria_id, NOW(), NOW()) 
            ON DUPLICATE KEY UPDATE nome = VALUES(nome), descricao = VALUES(descricao), preco = VALUES(preco), preco_max = VALUES(preco_max),
            imagem = VALUES(imagem), data_atualizacao = NOW()";
      $stmt = $this->db->prepare($sql);

      $stmt->bindParam(':nome', $produto['name']);
      $stmt->bindParam(':descricao', $produto['descricao']);
      $stmt->bindParam(':preco', $produto['preco']);
		$stmt->bindParam(':preco_max', $produto['preco_max']);
      $stmt->bindParam(':imagem', $produto['imagem']);
      $stmt->bindParam(':categoria_id', $produto['categoria_id'], PDO::PARAM_INT);
      $stmt->execute();

      $produtoId = $this->db->lastInsertId();
      registrar_log("Produto salvo com ID: " . $produtoId);
      return $produtoId;
   }

   public function obterVariantes($produtoId) {
      try {
         // Requisita as variantes do produto, agora para cada variante individual
         $resposta = $this->requisicao('store/products/' . $produtoId);

         // Log para depuração
         registrar_log("Produto ID {$produtoId} - Detalhes: " . print_r($resposta, true));

         if (isset($resposta['result']['variants']) && is_array($resposta['result']['variants'])) {
            $variantes = $resposta['result']['variants'];
         } else {
            // Caso não haja variantes ou o formato seja diferente
            registrar_log("Nenhuma variante encontrada para o produto {$produtoId}");
            return [];
         }

         $variantesDetalhadas = [];

         foreach ($variantes as $variacao) {
            // Requisita os detalhes da variante individual
            $endpoint = "products/variant/{$variacao['id']}";
            $detalhesVariacao = $this->requisicao($endpoint);

            // Adiciona os detalhes da variante ao array de variantes detalhadas
            $variantesDetalhadas[] = [
               'name' => $detalhesVariacao['result']['name'] ?? $variacao['name'],
               'price' => $detalhesVariacao['result']['retail_price'] ?? $variacao['retail_price'],  // Preço da variante
               'description' => $detalhesVariacao['result']['description'] ?? '',  // Descrição da variante
               'stock_quantity' => 0, // Isso pode ser ajustado conforme necessidade
               'image_url' => $detalhesVariacao['result']['files'][0]['thumbnail_url'] ?? '', // Imagem da variante
            ];
         }

         return $variantesDetalhadas;
      } catch (Exception $e) {
         registrar_log("Erro ao obter variantes para o produto {$produtoId}: " . $e->getMessage());
         throw new Exception('Erro ao obter variantes: ' . $e->getMessage());
      }
   }	public function salvarVariantes($variacao, $produtoId) {
		registrar_log("Salvando variante: " . print_r($variacao, true) . " para o produto ID: {$produtoId}");

		$sql = "INSERT INTO variantes (produto_id, nome, preco, descricao, estoque, imagem) 
           VALUES (:produto_id, :nome, :preco, :descricao, :estoque, :imagem)
           ON DUPLICATE KEY UPDATE nome = VALUES(nome), preco = VALUES(preco), descricao = VALUES(descricao),
           estoque = VALUES(estoque), imagem = VALUES(imagem)";
		$stmt = $this->db->prepare($sql);

		$preco = $variacao['price'] ?? 0;
		$estoque = $variacao['stock_quantity'] ?? 0;
		$imagem = $variacao['image_url'] ?? '';

		$stmt->bindParam(':produto_id', $produtoId, PDO::PARAM_INT);
		$stmt->bindParam(':nome', $variacao['name']);
		$stmt->bindParam(':preco', $preco);
		$stmt->bindParam(':descricao', $variacao['description']);
		$stmt->bindParam(':estoque', $estoque, PDO::PARAM_INT);
		$stmt->bindParam(':imagem', $imagem);
		$stmt->execute();

		registrar_log("Variantes salvas com sucesso.");
	}

	public function sincronizarProdutos() {
		registrar_log("Iniciando a sincronização de produtos.");
		$produtos = $this->obterProdutos();

		foreach ($produtos as $produto) {
			// Salvar o produto principal
			$produtoId = $this->salvarProduto($produto);
			registrar_log("Produto salvo com ID: " . $produtoId);

			// Agora, salvar as variantes desse produto
			if (!empty($produto['variantes'])) {
				foreach ($produto['variantes'] as $variacao) {
					// Salvar a variante no banco de dados
					$this->salvarVariantes($variacao, $produtoId);
				}
			}
		}

		registrar_log("Sincronização de produtos concluída.");
		return true;
	}
}

?>