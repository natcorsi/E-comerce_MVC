<h1>Produtos</h1>

<?php if (!empty($dados['produtos'])): 

var_dump($dados['produtos']);

include_once 'Motor/Componentes/Componentes.php';

/*
*   Arquivo de visão responsável por exibir a lista de produtos.
*/

?>
<ul>
	<?php foreach ($dados['produtos'] as $produto): ?>
	<div class="produto">
		<li>
			<a href="/produto/mostrar/<?= htmlspecialchars($produto['id'], ENT_QUOTES, 'UTF-8') ?>">
				<img src="<?= htmlspecialchars($produto['imagem'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>">
				<h2><?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?></h2>
				<p class='preco'><?= htmlspecialchars($produto['preco'], ENT_QUOTES, 'UTF-8') ?></p>
			</a>
		</li>
	</div>
	<?php endforeach; ?>
</ul>



<?php else: ?>
<p>Nenhum produto encontrado.</p>
<?php endif; ?>

<?php Componente::renderizar('Produtos'); ?>
