<?php


require_once './config.php'; // Configuração do banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Coletar os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];

    // Criptografar a senha
    $senha_cripto = password_hash($senha, PASSWORD_DEFAULT);

    // Verificar se o e-mail já existe
    $consultaSQL = "SELECT * FROM usuarios WHERE email = :email";
    $declaracao = $pdo->prepare($consultaSQL);
    $declaracao->bindParam(':email', $email);
    $declaracao->execute();

    if ($declaracao->rowCount() > 0) {
        echo "E-mail já cadastrado!";
    } else {
        // Inserir os dados no banco de dados
        $consultaSQL = "INSERT INTO usuarios (nome, email, senha, endereco, telefone) VALUES (:nome, :email, :senha, :endereco, :telefone)";
        $declaracao = $pdo->prepare($consultaSQL);
        $declaracao->bindParam(':nome', $nome);
        $declaracao->bindParam(':email', $email);
        $declaracao->bindParam(':senha', $senha_cripto);
        $declaracao->bindParam(':endereco', $endereco);
        $declaracao->bindParam(':telefone', $telefone);
        $declaracao->execute();

        echo "Cadastro realizado com sucesso!";
    }
}

?>

<form method="POST" action="/usuario/cadastrar">
    Nome: <input type="text" name="nome" required><br>
    E-mail: <input type="email" name="email" required><br>
    Senha: <input type="password" name="senha" required><br>
    Telefone:<input type="text" id="telefone" name="telefone" required><br>
    Endereço:<input type="text" id="endereco" name="endereco" required><br>
    <button type="submit">Cadastrar</button>
</form>