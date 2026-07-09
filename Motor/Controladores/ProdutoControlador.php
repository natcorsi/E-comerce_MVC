<?php

/* 
*   Controlador específico para gerenciar a lógica dos produtos.
*/


require_once 'Controlador.php';

class ProdutoControlador extends Controlador {
    private $produtoModelo;
    private $printfulModelo;

    public function __construct() {
        $this->produtoModelo = $this->modelo('ProdutoModelo');
        //$this->printfulModelo = $this->modelo('PrintfulModelo');
    }

    // Listar todos os produtos (apenas do banco de dados)
    public function listar() {
        $produtos = $this->produtoModelo->obterTodosProdutos();
        $this->vista('Loja/ListarProdutos', ['produtos' => $produtos]);
    }       

    // Mostrar detalhes de um produto específico
    public function mostrar($id) {
        // Primeiro tenta obter o produto do banco de dados
        $produto = $this->produtoModelo->obterProdutoPorId($id);

        // Caso não esteja no banco, usa a API Printful
        if (!$produto) {
            //$produto = $this->printfulModelo->obterProdutoPorId($id);
        }

        // Verifica se o produto foi encontrado
        if ($produto) {
            $this->vista('Loja/MostrarProduto', $produto);
        } else {
            echo 'Produto não encontrado';
        }
    }
}