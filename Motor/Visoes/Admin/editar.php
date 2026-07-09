<?php
require_once './Motor/Modelos/ProdutoModelo.php';
$produtoModelo = new ProdutoModelo();

// Capturar o ID do produto da URL
$url = $_SERVER['REQUEST_URI'];
$urlParts = explode('/', $url);
$produtoId = end($urlParts);

// Verificar se o ID do produto é válido
if (strpos($produtoId, '?') !== false) {
	list($produtoId) = explode('?', $produtoId);
}

if (!is_numeric($produtoId)) {
	die('ID do produto inválido.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Dados do produto principal
	$produto = [
		'id' => $produtoId,
		'nome' => $_POST['nome'],
		'descricao' => $_POST['descricao'],
		'preco' => $_POST['preco'],
	];

	// Atualizar o produto principal
	$produtoModelo->atualizarProduto($produto);

	// Atualizar as variações do produto
	if (!empty($_POST['variantes'])) {
		foreach ($_POST['variantes'] as $varianteId => $varianteDados) {
			$variante = [
				'id' => $varianteId,
				'produto_id' => $produtoId,
				'nome' => $varianteDados['nome'],
				'descricao' => $varianteDados['descricao'],
				'preco' => $varianteDados['preco'],
				'sku' => $varianteDados['sku'],
				'imagem' => $varianteDados['imagem'] ?? '', // Defina um valor padrão para a imagem
			];

			if (isset($_FILES['variantes']['name'][$varianteId]['nova_imagem']) && $_FILES['variantes']['name'][$varianteId]['nova_imagem'] !== '') {
				$file_tmp = $_FILES['variantes']['tmp_name'][$varianteId]['nova_imagem'];
				$file_name = $_FILES['variantes']['name'][$varianteId]['nova_imagem'];
				$variante['imagem'] = uploadImagem($file_tmp, $file_name);
			}

			$produtoModelo->atualizarVariante($variante);
		}
	}

	// Gerenciar Imagens da Galeria
	if (!empty($_POST['galeria'])) {
		foreach ($_POST['galeria'] as $imagemId => $imagemDados) {
			if (isset($imagemDados['remover'])) {
				$produtoModelo->removerImagemGaleria($imagemId);
			}
		}
	}

	// Adicionar novas imagens à galeria
	if (!empty($_FILES['nova_imagem']['name'][0])) {
		foreach ($_FILES['nova_imagem']['tmp_name'] as $key => $tmp_name) {
			$file_name = $_FILES['nova_imagem']['name'][$key];
			$file_tmp = $_FILES['nova_imagem']['tmp_name'][$key];

			$imagemUrl = uploadImagem($file_tmp, $file_name);
			$produtoModelo->adicionarImagemGaleria($produtoId, $imagemUrl);
		}
	}

	// Redirecionar para a página de edição com mensagem de sucesso
	header("Location: " . BASE_URL . "admin/editar/$produtoId?sucesso=1");
	exit;
}

// Obter os dados do produto e suas variações para exibir no formulário
$produto = $produtoModelo->obterProdutoPorId($produtoId);
$variantes = $produtoModelo->obterVariantesPorProdutoId($produtoId);
$galeria = $produtoModelo->obterGaleriaPorProdutoId($produtoId);

// Verificar se o produto foi encontrado
if (!$produto) {
	die('Produto não encontrado.');
}

function uploadImagem($file_tmp, $file_name) {
	$upload_dir = 'uploads/';
	$upload_file = $upload_dir . basename($file_name);

	if (move_uploaded_file($file_tmp, $upload_file)) {
		return $upload_file;
	} else {
		die('Erro ao fazer upload da imagem.');
	} 
}

?>


<h1>Editar Produto</h1>
<?php if (isset($_GET['sucesso'])): ?>
<p>Produto atualizado com sucesso!</p>
<?php endif; ?>
<?php if ($produto): ?>
<form method="POST" action="" enctype="multipart/form-data">
	<label for="nome">Nome:</label>
	<input type="text" id="nome" name="nome" value="<?$produto['nome']?>"><br>
	<label for="descricao">Descrição:</label>
	<textarea id="descricao" name="descricao"><?$produto['descricao']?></textarea><br>

	<label for="preco">Preço:</label>
	<input type="text" id="preco" name="preco" value="<?$produto['preco']?>"><br>

	<h2>Imagem Principal</h2>
	<?php if (!empty($produto['imagem'])): ?>
	<img src="<?= htmlspecialchars($produto['imagem'], ENT_QUOTES, 'UTF-8') ?>" alt="Imagem Principal" class="miniatura"><br>
	<?php endif; ?>
	<label for="imagem_principal">Alterar Imagem Principal:</label>
	<input type="file" id="imagem_principal" name="imagem_principal"><br>

	<h2>Imagens da Galeria</h2>
	<?php if (!empty($galeria)): ?>
	<?php foreach ($galeria as $imagem): ?>
	<div>
		<img src="<?= htmlspecialchars($imagem['imagem_url'], ENT_QUOTES, 'UTF-8') ?>" alt="Imagem da Galeria" class="miniatura"><br>
		<label for="remover_imagem_<?= $imagem['id'] ?>">Remover</label>
		<input type="checkbox" id="remover_imagem_<?= $imagem['id'] ?>" name="galeria[<?= $imagem['id'] ?>][remover]">
	</div>
	<?php endforeach; ?>

	<?php else: ?>
	<p>Não há imagens na galeria.</p>
	<?php endif; ?>

	<label for="nova_imagem">Adicionar Imagens:</label>
	<input type="file" id="nova_imagem" name="nova_imagem[]" multiple><br>

	<h2>Variações</h2>
	<?php if (!empty($variantes)): ?>
	<table border="1">
		<thead>
			<tr>
				<th>Imagem</th>
				<th>Nome</th>
				<th>Preço</th>
				<th>SKU</th>
				<th>Nova Imagem</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($variantes as $variante): ?>
			<tr>
				<td>
					<?php if (!empty($variante['imagem'])): ?>
					<img src="<?= htmlspecialchars($variante['imagem'], ENT_QUOTES, 'UTF-8') ?>" alt="Imagem da Variante" class="miniatura">
					<?php endif; ?>
				</td>
				<td>
					<input type="text" name="variantes[<?$variante['id']?>][nome]" value="<?= htmlspecialchars($variante['nome'], ENT_QUOTES, 'UTF-8') ?>">
				</td>
				<td>
					<input type="text" name="variantes[<?$variante['id']?>][preco]" value="<?= htmlspecialchars($variante['preco'], ENT_QUOTES, 'UTF-8') ?>">
				</td>
				<td>
					<input type="text" name="variantes[<?$variante['id']?>][sku]" value="<?= htmlspecialchars($variante['sku'], ENT_QUOTES, 'UTF-8') ?>">
				</td>
				<td>
					<input type="file" name="variantes[<? $variante['id']?>][nova_imagem]">
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php else: ?>
	<p>Não há variações para este produto.</p>
	<?php endif; ?>
	<button type="submit">Salvar</button>
</form>
<?php else: ?>
<p>Produto não encontrado.</p>
<?php endif; ?>

