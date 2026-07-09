<?php
require_once 'Modelo.php';  // Importando a classe Modelo

class UsuarioModelo extends Modelo {

	// Função para cadastrar um novo usuário
	public function cadastrar($nome, $email, $senha, $endereco, $telefone) {
		// Verifica se o e-mail já está cadastrado
		$declaracao = $this->db->prepare("SELECT * FROM usuarios WHERE email = ?");
		$declaracao->execute([$email]);
		$usuario = $declaracao->fetch(PDO::FETCH_ASSOC);

		if ($usuario) {
			registrar_log("Erro: E-mail já cadastrado - $email");
			return "E-mail já cadastrado!";
		}

		// Criptografa a senha antes de salvar
		$senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);

		// Inserir o novo usuário no banco de dados
		$declaracao = $this->db->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
		if ($declaracao->execute([$nome, $email, $senha_criptografada, $endereco, $telefone])) {
			registrar_log("Cadastro realizado com sucesso - $email");
			return "Cadastro realizado com sucesso!";
		} else {
			registrar_log("Erro ao cadastrar usuário - $email");
			return "Erro ao cadastrar usuário.";
		}
	}

	// Função para verificar login do usuário
	public function login($email, $senha) {
		// Busca o usuário pelo e-mail
		$declaracao = $this->db->prepare("SELECT * FROM usuarios WHERE email = ?");
		$declaracao->execute([$email]);
		$usuario = $declaracao->fetch(PDO::FETCH_ASSOC);

		// Se não encontrar o usuário, retorna erro
		if (!$usuario) {
			registrar_log("Falha no login - Usuário não encontrado - $email");
			return "E-mail ou senha incorretos!";
		}

		// Verifica se a senha corresponde à senha armazenada no banco
		if (password_verify($senha, $usuario['senha'])) {
			registrar_log("Login bem-sucedido - $email");
			return $usuario;  // Retorna os dados do usuário autenticado
		} else {
			registrar_log("Falha no login - Senha incorreta - $email");
			return "E-mail ou senha incorretos!";
		}
	}

	// Função para recuperar dados do usuário por ID
	public function recuperarUsuarioPorId($id) {
		$declaracao = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
		$declaracao->execute([$id]);
		$usuario = $declaracao->fetch(PDO::FETCH_ASSOC);

		if ($usuario) {
			registrar_log("Dados do usuário recuperados com sucesso - ID: $id");
			return $usuario;  // Retorna os dados do usuário
		} else {
			registrar_log("Erro ao recuperar dados do usuário - ID não encontrado: $id");
			return null;  // Caso o usuário não seja encontrado
		}
	}
	
	// Função para buscar usuário pelo email
	public function buscarPorEmail($email) {
		$sql = "SELECT * FROM usuarios WHERE email = :email";
		$consultaSQL = $this->db->prepare($sql);
		$consultaSQL->bindParam(':email', $email);
		$consultaSQL->execute();
		return $consultaSQL->fetch(PDO::FETCH_ASSOC);
	}


	// Função para atualizar os dados do usuário
	public function atualizarDados($id, $nome, $email, $telefone, $endereco, $complemento, $cidade, $estado, $pais, $cep) {
		// Verifica se o ID é válido
		if (!$id) {
			registrar_log("Erro: ID do usuário inválido - $id");
			return "ID do usuário é inválido.";
		}

		try {
			// Cria a declaração SQL
			$sql = "UPDATE usuarios 
SET nome = :nome, 
email = :email, 
telefone = :telefone, 
endereco = :endereco, 
complemento = :complemento, 
cidade = :cidade, 
estado = :estado, 
pais = :pais, 
cep = :cep 
WHERE id = :id";
 
			// Prepara a consulta
			$stmt = $this->db->prepare($sql);

			// Executa com os dados fornecidos
			$stmt->execute([
				':nome' => $nome,
				':email' => $email,
				':telefone' => $telefone,
				':endereco' => $endereco,
				':complemento' => $complemento,
				':cidade' => $cidade,
				':estado' => $estado,
				':pais' => $pais,
				':cep' => $cep,
				':id' => $id
			]);

			registrar_log("Dados do usuário atualizados com sucesso - ID: $id");
			return "Dados atualizados com sucesso.";
		} catch (PDOException $e) {
			registrar_log("Erro ao atualizar dados - ID: $id - " . $e->getMessage());
			return "Erro ao atualizar os dados: " . $e->getMessage();
		}
	}
	
	// Função para buscar pedidos pelo ID do usuário
	public function buscarPedidosPorUsuarioId($usuarioId) {
		$declaracao = $this->db->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY data DESC");
		$declaracao->execute([$usuarioId]);
		return $declaracao->fetchAll(PDO::FETCH_ASSOC);
	}
}
?>
