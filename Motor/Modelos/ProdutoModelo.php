<?php

class ProdutoModelo extends Modelo {
	public function obterTodosProdutos() {
		$sql = "SELECT * FROM produtos";
		$consultaSQL = $this->db->query($sql);
		return $consultaSQL->fetchAll(PDO::FETCH_ASSOC);
	}

	public function obterProdutoPorId($id) {
		// Buscar o produto principal
		$consultaSQL = $this->db->prepare("SELECT * FROM produtos WHERE id = :id");
		$consultaSQL->bindParam(':id', $id, PDO::PARAM_INT);
		$consultaSQL->execute();
		$produto = $consultaSQL->fetch(PDO::FETCH_ASSOC);

		if ($produto) {
			// Buscar variantes relacionadas
			$produto['variantes'] = $this->obterVariantesPorProdutoId($id);

			// Log para depuração
			registrar_log("Produto encontrado: " . json_encode($produto), __FILE__);
		} else {
			registrar_log("Produto não encontrado para ID: " . $id, __FILE__);
		}

		return $produto;
	}

	public function obterProdutoPorSku($sku) {
		$consultaSQL = $this->db->prepare("SELECT * FROM variantes WHERE sku = :sku");
		$consultaSQL->bindParam(':sku', $sku, PDO::PARAM_STR);
		$consultaSQL->execute();
		$variante = $consultaSQL->fetch(PDO::FETCH_ASSOC);

		if ($variante) {
			$produtoId = $variante['produto_id'];
			// Buscar o produto principal
			$produtoConsultaSQL = $this->db->prepare("SELECT * FROM produtos WHERE id = :id");
			$produtoConsultaSQL->bindParam(':id', $produtoId, PDO::PARAM_INT);
			$produtoConsultaSQL->execute();
			$produto = $produtoConsultaSQL->fetch(PDO::FETCH_ASSOC);

			if ($produto) {
				$produto['variantes'] = $this->obterVariantesPorProdutoId($produtoId);
				$produto['sku'] = $sku;
				$produto['preco'] = $variante['preco'];
				$produto['nome'] = $variante['nome'];
				$produto['descricao'] = $variante['descricao'];
				$produto['imagem'] = $variante['imagem'];
				$produto['quantidade'] = 1; // Quantidade padrão
				// Log para depuração
				registrar_log("Produto encontrado para SKU {$sku}: " . json_encode($produto), __FILE__);
				return $produto;
			}
		} else {
			registrar_log("Produto não encontrado para SKU: " . $sku, __FILE__);
		}

		return null;
	}

	public function salvarProduto($produto) {
		try {
			// Verificar se a descrição está presente, caso contrário, definir um valor padrão
			$descricao = !empty($produto['description']) ? $produto['description'] : 'Descrição não disponível';

			// Preparar SQL para inserção ou atualização do produto
			$sql = "INSERT INTO produtos (nome, descricao, preco, preco_max, imagem, categoria_id, data_criacao, data_atualizacao)
VALUES (:nome, :descricao, :preco, :preco_max, :imagem, :categoria_id, NOW(), NOW())
ON DUPLICATE KEY UPDATE nome = VALUES(nome), descricao = VALUES(descricao), preco = VALUES(preco), preco_max = VALUES(preco_max), imagem = VALUES(imagem), data_atualizacao = NOW()";
			$consultaSQL = $this->db->prepare($sql);

			// Vincular os parâmetros
			$consultaSQL->bindParam(':nome', $produto['name']);
			$consultaSQL->bindParam(':descricao', $descricao);
			$consultaSQL->bindParam(':preco', $produto['price']);
			$consultaSQL->bindParam(':preco_max', $produto['preco_max']);
			$consultaSQL->bindParam(':imagem', $produto['thumbnail_url']);
			$consultaSQL->bindParam(':categoria_id', $produto['categoria_id'], PDO::PARAM_INT);
			$consultaSQL->execute();

			// Recuperar o ID do produto salvo
			$produtoId = $this->db->lastInsertId();

			// Verificar e salvar as variantes associadas
			if (!empty($produto['variants'])) {
				foreach ($produto['variants'] as $variacao) {
					$this->salvarVariantes($variacao, $produtoId);
				}
			}

			registrar_log('Produto salvo no banco de dados: ' . json_encode($produto), __FILE__);
			return $produtoId;
		} catch (Exception $e) {
			registrar_log('Erro ao salvar produto: ' . $e->getMessage(), __FILE__);
			throw $e;
		}
	}


	public function salvarVariantes($variacao, $produtoId) {
		registrar_log('info da Variação: ' . $produtoId . ' Variacao: ' . json_encode($variacao), __FILE__);
		try {
			// Verificar e sanitizar os dados da variante
			$descricao = !empty($variacao['description']) ? $variacao['description'] : 'Descrição não disponível';
			$preco = isset($variacao['price']) && is_numeric($variacao['price']) ? $variacao['price'] : 0.00;
			$estoque = isset($variacao['in_stock']) ? ($variacao['in_stock'] ? 1 : 0) : 0;
			$imagem = !empty($variacao['image']) ? $variacao['image'] : '';
			$sku = isset($variacao['sku']) ? $variacao['sku'] : 'SKU nao informado';

			// Registrar log para verificação do SKU
			registrar_log('Dados da variante antes de salvar: ' . json_encode($variacao), __FILE__);
			registrar_log('SKU da variante: ' . $sku, __FILE__);  // Adicionado para garantir que o SKU esteja correto

			$sql = "INSERT INTO variantes (sku, produto_id, nome, preco, descricao, estoque, imagem, tamanho, cor)
VALUES (:sku, :produto_id, :nome, :preco, :descricao, :estoque, :imagem, :tamanho, :cor)
ON DUPLICATE KEY UPDATE sku = VALUES(sku), nome = VALUES(nome), preco = VALUES(preco), descricao = VALUES(descricao), estoque = VALUES(estoque), imagem = VALUES(imagem), tamanho = VALUES(tamanho), cor = VALUES(cor)";
			$consultaSQL = $this->db->prepare($sql);

			// Vincular os parâmetros
			$consultaSQL->bindParam(':produto_id', $produtoId, PDO::PARAM_INT);
			$consultaSQL->bindParam(':sku', $sku);  // Garantir que o SKU está sendo passado corretamente
			$consultaSQL->bindParam(':nome', $variacao['name']);
			$consultaSQL->bindParam(':preco', $preco);
			$consultaSQL->bindParam(':descricao', $descricao);
			$consultaSQL->bindParam(':estoque', $estoque, PDO::PARAM_INT);
			$consultaSQL->bindParam(':imagem', $imagem);
			$consultaSQL->bindParam(':tamanho', $variacao['size']);
			$consultaSQL->bindParam(':cor', $variacao['color']);
			$consultaSQL->execute();


			registrar_log('Variante salva no banco de dados: ' . json_encode($variacao), __FILE__);
		} catch (Exception $e) {
			registrar_log('Erro ao salvar variante: ' . $e->getMessage(), __FILE__);
			throw $e;
		}
	}


	public function obterVariantesPorProdutoId($produtoId) {
		$consultaSQL = $this->db->prepare("SELECT * FROM variantes WHERE produto_id = :produto_id");
		$consultaSQL->bindParam(':produto_id', $produtoId, PDO::PARAM_INT);
		$consultaSQL->execute();
		$variantes = $consultaSQL->fetchAll(PDO::FETCH_ASSOC);

		// Log para depuração
		registrar_log("Variantes encontradas para produto ID {$produtoId}: " . json_encode($variantes), __FILE__);

		return $variantes;
	}


	public function excluirProduto($produtoId) {
		try {
			$sql = "DELETE FROM produtos WHERE id = :id";
			$consultaSQL = $this->db->prepare($sql);
			$consultaSQL->bindParam(':id', $produtoId, PDO::PARAM_INT);
			$consultaSQL->execute();

			registrar_log('Produto excluído do banco de dados', __FILE__);
		} catch (Exception $e) {
			registrar_log('Erro ao excluir produto: ' . $e->getMessage(), __FILE__);
			throw $e;
		}
	}

	public function excluirVariantes($produtoId) {
		try {
			$sql = "DELETE FROM variantes WHERE produto_id = :produto_id";
			$consultaSQL = $this->db->prepare($sql);
			$consultaSQL->bindParam(':produto_id', $produtoId, PDO::PARAM_INT);
			$consultaSQL->execute();

			registrar_log('Variantes excluídas do banco de dados', __FILE__);
		} catch (Exception $e) {
			registrar_log('Erro ao excluir variantes: ' . $e->getMessage(), __FILE__);
			throw $e;
		}
	}

	public function atualizarProduto($produto) {
		try {
			// Atualizar o produto no banco de dados local
			$sql = "UPDATE produtos SET nome = :nome, descricao = :descricao, preco = :preco WHERE id = :id";
			$consultaSQL = $this->db->prepare($sql);
			$consultaSQL->bindParam(':nome', $produto['nome']);
			$consultaSQL->bindParam(':descricao', $produto['descricao']);
			$consultaSQL->bindParam(':preco', $produto['preco']);
			$consultaSQL->bindParam(':id', $produto['id'], PDO::PARAM_INT);
			$consultaSQL->execute();

			registrar_log('Produto atualizado no banco de dados: ' . json_encode($produto), __FILE__);
			return true;
		} catch (Exception $e) {
			registrar_log('Erro ao atualizar produto: ' . $e->getMessage(), __FILE__);
			return false;
		}
	}

	private function atualizarProdutoPrintful($produto) {
		$url = 'https://api.printful.com/store/products/' . $produto['id'];
		$data = [
			'sync_product' => [
				'name' => $produto['nome'],
				'description' => $produto['descricao'],
			],
			'sync_variants' => [
				// Adicione variantes aqui, se necessário
			]
		];

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Authorization: Bearer yMsLvsYNEQuegKPSPkSU1FiwuafuKJp2BZm9Qid3'
		]);

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return ['code' => $httpCode, 'response' => json_decode($response, true)];
	}

	public function obterGaleriaPorProdutoId($produtoId) {
		$consultaSQL = $this->db->prepare("SELECT * FROM galeria WHERE produto_id = :produto_id");
		$consultaSQL->bindParam(':produto_id', $produtoId, PDO::PARAM_INT);
		$consultaSQL->execute();
		return $consultaSQL->fetchAll(PDO::FETCH_ASSOC);
	}

	public function adicionarImagemGaleria($produtoId, $imagemUrl) {
		$sql = "INSERT INTO galeria (produto_id, imagem_url) VALUES (:produto_id, :imagem_url)";
		$consultaSQL = $this->db->prepare($sql);
		$consultaSQL->bindParam(':produto_id', $produtoId, PDO::PARAM_INT);
		$consultaSQL->bindParam(':imagem_url', $imagemUrl);
		$consultaSQL->execute();
	}

	public function removerImagemGaleria($imagemId) {
		$sql = "DELETE FROM galeria WHERE id = :id";
		$consultaSQL = $this->db->prepare($sql);
		$consultaSQL->bindParam(':id', $imagemId, PDO::PARAM_INT);
		$consultaSQL->execute();
	}

	public function atualizarVariante($variante) {
		try {
			$sql = "UPDATE variantes SET 
nome = :nome, 
descricao = :descricao, 
preco = :preco, 
sku = :sku, 
imagem = :imagem 
WHERE id = :id AND produto_id = :produto_id";
			$consultaSQL = $this->db->prepare($sql);
			$consultaSQL->bindParam(':nome', $variante['nome']);
			$consultaSQL->bindParam(':descricao', $variante['descricao']);
			$consultaSQL->bindParam(':preco', $variante['preco']);
			$consultaSQL->bindParam(':sku', $variante['sku']);
			$consultaSQL->bindParam(':imagem', $variante['imagem']);
			$consultaSQL->bindParam(':id', $variante['id'], PDO::PARAM_INT);
			$consultaSQL->bindParam(':produto_id', $variante['produto_id'], PDO::PARAM_INT);
			$consultaSQL->execute();

			registrar_log('Variante atualizada no banco de dados: ' . json_encode($variante), __FILE__);
			return true;
		} catch (Exception $e) {
			registrar_log('Erro ao atualizar variante: ' . $e->getMessage(), __FILE__);
			return false;
		}
	}
}
?>