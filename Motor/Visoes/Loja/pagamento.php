<?php
// Verifica se a sessão já foi iniciada
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
	header('Location: <?= BASE_URL ?>usuario/entrar');
	exit();
}

// Instancia o modelo de usuário
require_once __DIR__ . '/../../Modelos/UsuarioModelo.php';
$usuarioModelo = new UsuarioModelo();

// Recupera os dados do usuário logado
$usuario_id = $_SESSION['usuario_id'];
$usuario = $usuarioModelo->recuperarUsuarioPorId($usuario_id);

if (!$usuario) {
	// Se o usuário não for encontrado, redireciona
	header('Location: ' . BASE_URL . 'usuario/entrar');
	exit();
}

// Calcula o total do carrinho 
$totalCarrinho = 0;
foreach ($dados['produtos'] as $produto) {
	$totalCarrinho += $produto['preco'] * $produto['quantidade'];
}

// Adicionar log para verificar os dados do carrinho
registrar_log("Dados do carrinho na página de pagamento: " . json_encode($dados['produtos']), __FILE__);

?>

<div class="FormPagamento">
	<h1>Efetuar Pagamento</h1>
	<p>Aqui você pode confirmar seus produtos e finalizar o pagamento.</p>

	<!-- Formulário de pagamento -->
	<form id="form-usuario" action="<?= BASE_URL ?>pagamento/criarPagamento" method="POST">
		<h2>Detalhes do Envio</h2>

		<!-- Nome -->
		<label for="nome">Nome:</label><br>
		<input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>"><br>

		<!-- Email -->
		<label for="email">Email:</label><br>
		<input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>"><br>

		<!-- Telefone -->
		<label for="telefone">Telefone:</label><br>
		<input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($usuario['telefone'], ENT_QUOTES, 'UTF-8') ?>" ><br>

		<!-- Endereço -->
		<label for="endereco">Endereço:</label><br>
		<input type="text" id="endereco" name="endereco" value="<?= htmlspecialchars($usuario['endereco'], ENT_QUOTES, 'UTF-8') ?>"><br>

		<!-- Endereço Complementar -->
		<label for="complemento">Complemento:</label><br>
		<input type="text" id="endereco_complementar" name="complemento" value="<?= htmlspecialchars($usuario['endereco'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Apto, Bloco, etc."><br>

		<!-- Cidade -->
		<label for="cidade">Cidade:</label><br>
		<input type="text" id="cidade"  name="cidade" placeholder="Sua Cidade"  value="<?= htmlspecialchars($usuario['cidade'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required><br>

		<!-- Cidade -->
		<label for="Estado">Estado:</label><br>
		<input type="text" id="estado" name="estado"  value="<?= htmlspecialchars($usuario['estado'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Ex: SP, RJ" required><br>

		<label for="pais">País:</label><br>
		<select id="pais" name="pais" value="<?= isset($usuario['pais']) && $usuario['pais'] === 'País' ? 'selected' : '' ?> " required>
			<option value="">Selecione seu país</option>
			<option value="Brasil" <?= isset($usuario['pais']) && $usuario['pais'] === 'Brasil' ? 'selected' : '' ?>>Brasil</option>
			<option value="Portugal" <?= isset($usuario['pais']) && $usuario['pais'] === 'Portugal' ? 'selected' : '' ?>>Portugal</option>
			<option value="Angola" <?= isset($usuario['pais']) && $usuario['pais'] === 'Angola' ? 'selected' : '' ?>>Angola</option>
			<option value="Moçambique" <?= isset($usuario['pais']) && $usuario['pais'] === 'Moçambique' ? 'selected' : '' ?>>Moçambique</option>
			<option value="Cabo Verde" <?= isset($usuario['pais']) && $usuario['pais'] === 'Cabo Verde' ? 'selected' : '' ?>>Cabo Verde</option>
			<option value="Guiné-Bissau" <?= isset($usuario['pais']) && $usuario['pais'] === 'Guiné-Bissau' ? 'selected' : '' ?>>Guiné-Bissau</option>
			<option value="Timor-Leste" <?= isset($usuario['pais']) && $usuario['pais'] === 'Timor-Leste' ? 'selected' : '' ?>>Timor-Leste</option>
			<option value="Argentina" <?= isset($usuario['pais']) && $usuario['pais'] === 'Argentina' ? 'selected' : '' ?>>Argentina</option>
			<option value="México" <?= isset($usuario['pais']) && $usuario['pais'] === 'México' ? 'selected' : '' ?>>México</option>
			<option value="Espanha" <?= isset($usuario['pais']) && $usuario['pais'] === 'Espanha' ? 'selected' : '' ?>>Espanha</option>
			<option value="Colômbia" <?= isset($usuario['pais']) && $usuario['pais'] === 'Colômbia' ? 'selected' : '' ?>>Colômbia</option>
			<option value="Chile" <?= isset($usuario['pais']) && $usuario['pais'] === 'Chile' ? 'selected' : '' ?>>Chile</option>
			<option value="Paraguai" <?= isset($usuario['pais']) && $usuario['pais'] === 'Paraguai' ? 'selected' : '' ?>>Paraguai</option>
			<option value="Uruguai" <?= isset($usuario['pais']) && $usuario['pais'] === 'Uruguai' ? 'selected' : '' ?>>Uruguai</option>
			<option value="Estados Unidos" <?= isset($usuario['pais']) && $usuario['pais'] === 'Estados Unidos' ? 'selected' : '' ?>>Estados Unidos</option>
			<option value="Canadá" <?= isset($usuario['pais']) && $usuario['pais'] === 'Canadá' ? 'selected' : '' ?>>Canadá</option>
			<option value="Reino Unido" <?= isset($usuario['pais']) && $usuario['pais'] === 'Reino Unido' ? 'selected' : '' ?>>Reino Unido</option>
			<option value="França" <?= isset($usuario['pais']) && $usuario['pais'] === 'França' ? 'selected' : '' ?>>França</option>
			<option value="Alemanha" <?= isset($usuario['pais']) && $usuario['pais'] === 'Alemanha' ? 'selected' : '' ?>>Alemanha</option>
			<option value="Itália" <?= isset($usuario['pais']) && $usuario['pais'] === 'Itália' ? 'selected' : '' ?>>Itália</option>
			<option value="Japão" <?= isset($usuario['pais']) && $usuario['pais'] === 'Japão' ? 'selected' : '' ?>>Japão</option>
			<option value="China" <?= isset($usuario['pais']) && $usuario['pais'] === 'China' ? 'selected' : '' ?>>China</option>
			<option value="Índia" <?= isset($usuario['pais']) && $usuario['pais'] === 'Índia' ? 'selected' : '' ?>>Índia</option>
		</select><br>

		<!-- Código Postal -->
		<label for="cep">Código Postal (CEP):</label><br>
		<input type="text" id="cep" name="cep"  value="<?= htmlspecialchars($usuario['cep'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="00000-000" required><br>


		<h2>Revisar Itens</h2>
		<?php if (!empty($dados['produtos'])): ?>
		<?php foreach ($dados['produtos'] as $produto): ?>
		<div>
			<span><?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?></span>
			<span>Quantidade: <?= $produto['quantidade'] ?></span>
			<span>Preço: R$<?= number_format($produto['preco'], 2, ',', '.') ?></span>
		</div>
		<input type="hidden" name="pedido[itens][<?php echo $produto['sku']; ?>][nome]" value="<?php echo htmlspecialchars($produto['nome']); ?>">
		<input type="hidden" name="pedido[itens][<?php echo $produto['sku']; ?>][quantidade]" value="<?php echo $produto['quantidade']; ?>">
		<input type="hidden" name="pedido[itens][<?php echo $produto['sku']; ?>][preco]" value="<?php echo $produto['preco']; ?>">
		<input type="hidden" name="pedido[itens][<?php echo $produto['sku']; ?>][sku]" value="<?php echo $produto['sku']; ?>">
		<?php endforeach; ?>
		<?php else: ?>
		<p>Seu carrinho está vazio.</p>
		<?php endif; ?>
		<div>
			<strong>Total do carrinho: </strong> R$<?= number_format($totalCarrinho, 2, ',', '.') ?><br><br>
			<button type="submit">Pagar com Mercado Pago</button>
		</div>
	</form>
</div>
