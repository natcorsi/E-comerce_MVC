<?php

// Aqui estão todas as funções para acessar e controlar os recursos de usuarios e contas

include 'Motor/Modelos/UsuarioModelo.php';

class UsuarioControlador extends Controlador {
   public function cadastrar() {
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
         // Lógica para cadastrar o usuário no banco de dados
         $nome = $_POST['nome'];
         $email = $_POST['email'];
         $senha = $_POST['senha'];
         $endereco = $_POST['endereco'];
         $telefone = $_POST['telefone'];

         // Chamar o modelo para cadastrar o usuário
         $usuarioModelo = new UsuarioModelo();
         $resultado = $usuarioModelo->cadastrar($nome, $email, $senha, $endereco, $telefone);

         // Exibir uma mensagem ou redirecionar após o cadastro
         registrar_log("Cadastro solicitado - $email");
         echo $resultado;
      } else {
         // Exibir o formulário de cadastro
         include 'Motor/Visoes/Usuarios/cadastro.php';
      }
   }
   
   public function entrar() {
      include 'Motor/Visoes/Usuarios/entrar.php';
   }

   public function conta() {
      $email = $_POST['email'];
      $senha = $_POST['senha'];

      $modelo = new UsuarioModelo();
      $usuario = $modelo->buscarPorEmail($email);

      if ($usuario && password_verify($senha, $usuario['senha'])) {
         session_start();
         $_SESSION['usuario_id'] = $usuario['id'];
         $_SESSION['usuario_nome'] = $usuario['nome'];
         $_SESSION['usuario_admin'] = $usuario['administrador']; // Adiciona a informação de administrador
         registrar_log("Usuário logado - $email");
         header('Location: ' . BASE_URL . 'usuario/perfil');
      } else {
         registrar_log("Falha no login - $email");
         echo 'Email ou senha inválidos.';
      }
   }

   public function perfil() {
      if (!isset($_SESSION['usuario_id'])) {
         header('Location: ' . BASE_URL . 'usuario/entrar');
         exit();
      }
      include 'Motor/Visoes/Usuarios/PainelUsuario.php';
   }

   public function sair() {
      session_start();
      session_unset();
      session_destroy();
      registrar_log("Usuário deslogado");
      header('Location: ' . BASE_URL . 'usuario/entrar');
   }
	
	// Função para editar os dados do usuário
	public function editar() {
		// Verifica se o usuário está logado
		session_start();
		if (!isset($_SESSION['usuario_id'])) {
			header('Location: ' . BASE_URL . 'usuario/entrar');
			exit();
		}

		// Verifica se o formulário foi enviado
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			// Captura os dados do formulário
			$nome = $_POST['nome'];
			$email = $_POST['email'];
			$telefone = $_POST['telefone'];
			$endereco = $_POST['endereco'];
			$complemento = $_POST['complemento'];
			$cidade = $_POST['cidade'];
			$estado = $_POST['estado'];
			$pais = $_POST['pais'];
			$cep = $_POST['cep'];

			// Recupera o ID do usuário da sessão
			$usuarioId = $_SESSION['usuario_id'];

			// Instancia o modelo de usuário
			$usuarioModelo = new UsuarioModelo();

			// Chama a função para atualizar os dados
			$resultado = $usuarioModelo->atualizarDados($usuarioId, $nome, $email, $telefone, $endereco, $complemento, $cidade, $estado, $pais, $cep);

			// Exibe o resultado (sucesso ou erro)
			registrar_log("Edição de dados solicitada - ID: $usuarioId");
			echo $resultado;
		} else {
			// Caso o método seja GET, exibe o formulário de edição com os dados do usuário
			$usuarioId = $_SESSION['usuario_id'];
			$usuarioModelo = new UsuarioModelo();
			$usuario = $usuarioModelo->recuperarUsuarioPorId($usuarioId);
			
			// Exibe o formulário de edição
		}
	}
}

?>