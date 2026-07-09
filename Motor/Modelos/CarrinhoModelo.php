<?php

class CarrinhoModelo extends Modelo {
	public function __construct() {
		parent::__construct();

		if (!isset($_SESSION['carrinho'])) {
			$_SESSION['carrinho'] = []; // Inicializa o carrinho como um array vazio
		}
		// Log de início do carrinho
		registrar_log('Carrinho iniciado.', __FILE__);
	}

	// Adicionar ao carrinho
	public function adicionarAoCarrinho($sku, $quantidade = 1) {
		if (isset($_SESSION['carrinho'][$sku])) {
			$_SESSION['carrinho'][$sku] += $quantidade;
		} else {
			$_SESSION['carrinho'][$sku] = $quantidade;
		}

		registrar_log("Carrinho atualizado: " . json_encode($_SESSION['carrinho']), __FILE__);
	}




	// Remover produto do carrinho
	public function removerDoCarrinho($produtoId) {
		if (isset($_SESSION['carrinho'][$produtoId])) {
			unset($_SESSION['carrinho'][$produtoId]);
		}
	}

	// Obter o carrinho completo
	public function obterCarrinho() {
		// Log ao obter carrinho
		registrar_log("Carrinho solicitado: " . json_encode($_SESSION['carrinho']), __FILE__);
		return $_SESSION['carrinho'];
	}

	// Limpar carrinho
	public function limparCarrinho() {
		// Log ao limpar carrinho
		registrar_log("Carrinho limpo.", __FILE__);
		$_SESSION['carrinho'] = [];
	}
	}
