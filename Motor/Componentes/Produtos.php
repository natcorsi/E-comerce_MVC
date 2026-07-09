
<?php
require_once 'Motor/Modelos/ProdutoModelo.php';

if (!isset($dados['produtos'])) {
    $produtoModelo = new ProdutoModelo();
    $dados['produtos'] = $produtoModelo->obterTodosProdutos();
}
?>

<div class="produtos">
    <ul>
        <?php foreach ($dados['produtos'] as $produto): ?>
            <li>
                <a href="<?= BASE_URL ?>produto/mostrar/<?= htmlspecialchars($produto['id'], ENT_QUOTES, 'UTF-8') ?>">
                <img src="<?= $produto['imagem'] ?>" alt="<?= $produto['nome'] ?>">
                <h3><?= $produto['nome'] ?></h3>
                <p class="montante">
                    <b>R$</b><?= $produto['preco'] ?>
                    <?php if (isset($produto['preco_max'])) { echo "- ", $produto['preco_max'];}?>
                </p>
            </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
