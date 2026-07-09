<?php
// session_start();

if (!isset($_SESSION['usuario_id'])) {
	header('Location: /usuario/entrar');
	exit();
}

require_once 'Motor/Modelos/UsuarioModelo.php';
require_once 'Motor/Modelos/ProdutoModelo.php';

$usuarioId = $_SESSION['usuario_id'];
$nomeUsuario = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Usuário';
$produtoModelo = new ProdutoModelo();
$usuarioModelo = new UsuarioModelo();


// Recuperar pedidos do usuário
$usuario = $usuarioModelo->recuperarUsuarioPorId($usuarioId);
$pedidos = $usuarioModelo->buscarPedidosPorUsuarioId($usuarioId);
?>

<h1 class="usuario-nome">Bem-vindo<br><div><?php echo htmlspecialchars($nomeUsuario); ?>!</div></h1>
<p>Esta é a área do usuário.</p>
<a class="link-sair" href="/usuario/sair">Sair</a>


<h2>Seus dados:</h2>
<!--Nome -->

<form id="form-usuario">

<div class="nome">
<label for="nome">Nome:</label>
<span id="nome-text"><?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?></span>
<input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>" style="display:none;"><br>
</div>

<!-- Email -->
<div class="email">
<label for="email">Email:</label>
<span id="email-text"><?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?></span>
<input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>" style="display:none;"><br>
</div>

<!-- Endereço -->
<div class="endereco">
<label for="endereco">Endereço:</label>
<span id="endereco-text"><?= htmlspecialchars($usuario['endereco'], ENT_QUOTES, 'UTF-8') ?></span>
<input type="text" id="endereco" name="endereco" value="<?= htmlspecialchars($usuario['endereco'], ENT_QUOTES, 'UTF-8') ?>" style="display:none;"><br>
</div>


<!-- Telefone -->
<div class="telefone">
<label for="telefone">Telefone:</label>
<span id="telefone-text"><?= htmlspecialchars($usuario['telefone'], ENT_QUOTES, 'UTF-8') ?></span>
<input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($usuario['telefone'], ENT_QUOTES, 'UTF-8') ?>" style="display:none;"><br>
</div>

<br>
<!-- Botões de ação -->
<button type="button" id="btn-editar" onclick="editarDados()">Alterar Dados</button>
<button type="button" id="btn-cancelar" style="display:none;" onclick="cancelarEdicao()">Cancelar Edição</button>
<button type="button" id="btn-salvar" style="display:none;" onclick="salvarDados()">Salvar Dados</button><br>
</form>

<h2>Seus pedidos:</h2>
<?php if (!empty($pedidos)): ?>
<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">
	<thead>
		<tr>
			<th>ID do Pedido</th>
			<th>Itens</th>
			<th>Total</th>
			<th>Status</th>
			<th>Data</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($pedidos as $pedido): ?>
		<tr>
			<td><?php echo htmlspecialchars($pedido['id']); ?></td>
			<td>
				<?php 
				$itens = json_decode($pedido['itens'], true);  // Decodifica o JSON para um array
				
				if (is_array($itens)): 
				foreach ($itens as $item): 
				// Busca o nome do produto
				
				$nomeProduto = $item['produto'];
				$produto = $item['sku']; 				?>
				- <?php echo htmlspecialchars($nomeProduto); ?> 
				(Qtd: <?php echo htmlspecialchars($item['quantidade']); ?>, 
				Preço: R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?>)<br>
				<?php 
				endforeach; 
				endif; 
				?>
			</td>
			<td>R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></td>
			<td><?php echo htmlspecialchars($pedido['status']); ?></td>
			<td><?php echo htmlspecialchars($pedido['data']); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<p>Você ainda não realizou nenhum pedido.</p>
<?php endif; ?>



