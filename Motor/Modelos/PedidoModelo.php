<?php
require_once 'Modelo.php';  // Importando a classe Modelo

class PedidoModelo extends Modelo {

    // Função para buscar todos os pedidos
    public function buscarTodosPedidos() {
        $declaracao = $this->db->prepare("SELECT * FROM pedidos ORDER BY data DESC");
        $declaracao->execute();
        return $declaracao->fetchAll(PDO::FETCH_ASSOC);  // Retorna todos os pedidos
    }

    // Função para buscar pedido por ID
    public function buscarPedidoPorId($id) {
        $declaracao = $this->db->prepare("SELECT * FROM pedidos WHERE id = ?");
        $declaracao->execute([$id]);
        return $declaracao->fetch(PDO::FETCH_ASSOC);  // Retorna o pedido com esse ID
    }

    // Função para excluir um pedido
    public function excluirPedido($id) {
        $declaracao = $this->db->prepare("DELETE FROM pedidos WHERE id = ?");
        return $declaracao->execute([$id]);
    }
}