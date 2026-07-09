<?php

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Coletar os dados do formulário
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Buscar o usuário no banco de dados
    $consultaSQL = "SELECT * FROM usuarios WHERE email = :email";
    $consultaSQL = $pdo->prepare($consultaSQL);
    $declaracao->bindParam(':email', $email);
    $declaracao->execute();
    $usuario = $declaracao->fetch();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Login bem-sucedido
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        header("Location: area_usuario.php");
    } else {
        echo "E-mail ou senha incorretos!";
    }
}
?>
 

 <br><br><br><br><br><br>
<form class="conta-form" method="POST" action="conta">
    <h2>Entrar na Conta</h2>
    E-mail: <input type="email" name="email" required><br>
    Senha: <input type="password" name="senha" required>
    <br><br>
    <button type="submit">Entrar</button><br><br>
</form>
<br><br><br><br>