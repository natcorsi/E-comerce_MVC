<?php

class CompraModelo extends Modelo {

	// Função para registrar uma compra
	public function registrarPedido($usuarioId, $itens, $total, $status) {
		try {
			// Converter $itens para JSON
			$itensJson = json_encode($itens);

			// Registrar tentativa de inserção
			registrar_log("Tentando registrar a compra: usuario_id=$usuarioId, itens=" . print_r($itens, true) . ", total=$total, status=$status");

			// Preparar a declaração para inserção
			$declaracao = $this->db->prepare("INSERT INTO pedidos (usuario_id, itens, total, status) VALUES (?, ?, ?, ?)");

			// Executar a declaração
			if ($declaracao->execute([$usuarioId, $itensJson, $total, $status])) {
				// Registrar sucesso na inserção
				registrar_log("Compra registrada com sucesso: usuario_id=$usuarioId, itens=$itensJson, total=$total, status=$status");
				return true;
			} else {
				// Registrar erro caso a inserção falhe
				$erroInfo = $declaracao->errorInfo();
				registrar_log("Erro ao registrar compra: usuario_id=$usuarioId, itens=$itensJson, total=$total, status=$status, erro: " . print_r($erroInfo, true));
				return false;
			}
		} catch (Exception $e) {
			// Registrar exceção
			registrar_log("Erro ao tentar registrar compra: " . $e->getMessage());
			return false;
		}
	}



	// Função para obter todas as compras de um usuário
	public function obterComprasPorUsuario($usuarioId) {
		// Preparar a consulta SQL para buscar as compras de um usuário
		$declaracao = $this->db->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY data_compra DESC");

		// Executar a consulta e passar o ID do usuário como parâmetro
		$declaracao->execute([$usuarioId]);

		// Retornar todos os resultados como um array associativo
		return $declaracao->fetchAll(PDO::FETCH_ASSOC);
	}
}

?>