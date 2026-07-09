<div>
	<h1>Carrinho de Compras</h1>
	<?php if (!empty($dados['produtos'])): ?>
	<table>
		<thead>
			<tr>
				<th>Produto</th>
				<th>Qtd</th>
				<th>Preço</th>

			</tr>
		</thead>
		<tbody>
			<?php
			$totalCarrinho = 0; // Inicializa o total do carrinho
			foreach ($dados['produtos'] as $produto): 
			$preco = is_numeric($produto['preco']) ? (float)$produto['preco'] : 0;
			$quantidade = is_numeric($produto['quantidade']) ? (int)$produto['quantidade'] : 0;
			$totalCarrinho += $preco * $quantidade; // Calcula o total do carrinho
			?>
			<tr>
				<td><?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?></td>
				<td><?= $produto['quantidade'] ?></td>
				<td>R$<?= number_format($preco, 2, ',', '.') ?></td>
				<td>
					<a href="/carrinho/remover/<?= $produto['sku'] ?>">Remover</a>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<br>
	<div>
		<strong>Total do carrinho: </strong> R$<?= number_format($totalCarrinho, 2, ',', '.') ?>
	</div>
	<br>
	<a href="carrinho/limpar">Limpar | </a>
	<a href="index.php/pagamento">Finalizar Compra</a>
	<?php else: ?>
	<p>Seu carrinho está vazio.</p>
	<?php endif; ?>
</div>

