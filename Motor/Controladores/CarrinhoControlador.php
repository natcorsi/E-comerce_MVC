<?php

class CarrinhoControlador extends Controlador {
	private $carrinhoModelo;
	private $produtoModelo;

	public function __construct() {
		$this->carrinhoModelo = $this->modelo('CarrinhoModelo');
		$this->produtoModelo = $this->modelo('ProdutoModelo');
	}

	public function mostrar() {
		registrar_log("SESSION ID mostrar: " . session_id(), __FILE__);
		$carrinho = $this->carrinhoModelo->obterCarrinho();
		$produtosDetalhados = [];

		foreach ($carrinho as $sku => $quantidade) {
			$produto = $this->produtoModelo->obterProdutoPorSku($sku);
			if ($produto) {
				$produto['quantidade'] = $quantidade;
				$produtosDetalhados[] = $produto;
			}
		}

		echo "<pre>";
		print_r($carrinho);

		echo "\n\nProdutos encontrados:\n";
		print_r($produtosDetalhados);
		echo "</pre>";
		exit;

		//$this->vista('Loja/carrinho', ['produtos' => $produtosDetalhados]);
	}

	public function adicionar($id, $quantidade = 1) {
		$produto = $this->produtoModelo->obterProdutoPorId($id);


		registrar_log("Recebido no backend: produto_id={$id}, quantidade={$quantidade}, sku=" . $_POST['sku'], __FILE__);

		if ($produto) {
			$sku = $_POST['sku'] ?? null;

			registrar_log("SESSION ID: " . session_id(), __FILE__);
			registrar_log("POST: " . json_encode($_POST), __FILE__);

			if (!$sku) {
				echo 'SKU não informado!';
				exit;
			}

			// Validar o SKU nas variantes do produto
			$variantes = $produto['variantes'] ?? [];
			$varianteValida = null;

			foreach ($variantes as $variante) {
				if ($variante['sku'] === $sku) {
					$varianteValida = $variante;
					break;
				}
			}

			if ($varianteValida) {
				// Adicionar o SKU ao carrinho
				$this->carrinhoModelo->adicionarAoCarrinho($sku, $quantidade);
				registrar_log("SKU válido e adicionado ao carrinho: {$sku}", __FILE__);
				header('Location: ' . BASE_URL . 'carrinho/mostrar');
			} else {
				registrar_log("SKU inválido: {$sku}", __FILE__);
				echo 'Variante inválida!';
				exit;
			}
		} else {
			registrar_log("Produto não encontrado para ID: {$id}", __FILE__);
			echo 'Produto não encontrado.';
			exit;
		}
	}





	public function remover($id) {
		$this->carrinhoModelo->removerDoCarrinho($id);
		header('Location: ' . BASE_URL . 'carrinho/mostrar');
	}

	public function limpar() {
		$this->carrinhoModelo->limparCarrinho();
		header('Location: ' . BASE_URL . 'carrinho/mostrar');
	}

}

