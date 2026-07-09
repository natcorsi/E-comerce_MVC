<?php if (!empty($dados)): ?>

<br><br><br><br><br>

<h1><?= htmlspecialchars($dados['nome'] ?? 'Nome não disponível', ENT_QUOTES, 'UTF-8') ?></h1>
<br><br>

<div class="mostrarProduto">

	<div class="produto-container galeria">
		<img id="imagemProduto" src="<?= htmlspecialchars($dados['imagem'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
			alt="<?= htmlspecialchars($dados['nome'] ?? 'Nome não disponível', ENT_QUOTES, 'UTF-8') ?>">
	</div>

	<div class="produto-container infos">
		<p><?= htmlspecialchars($dados['descricao'] ?? 'Descrição não disponível', ENT_QUOTES, 'UTF-8') ?></p>
		<p>Preço: <span id="precoProduto"><?= htmlspecialchars($dados['preco'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></span>
		</p>
		<p>ID do Produto: <?= htmlspecialchars($dados['id'] ?? 'ID não disponível', ENT_QUOTES, 'UTF-8') ?></p>

		<!-- Seletor de Variações - Cor -->
	
		<div id="opcoesCor">
		<b>Cor:</b>
			<?php
			$cores = array_unique(array_column($dados['variantes'], 'cor'));
			foreach ($cores as $cor): ?>
				<label class="variante">
					<input type="radio" name="cor" value="<?= htmlspecialchars($cor, ENT_QUOTES, 'UTF-8') ?>" onchange="atualizarProduto()" >
					<?= htmlspecialchars($cor, ENT_QUOTES, 'UTF-8') ?>
				</label>
			<?php endforeach; ?>
		</div>

		<!-- Seletor de Variações - Tamanho -->
		
		<div id="opcoesTamanho">
		<b>Tamanho:</b>
			<?php
			$tamanhos = array_unique(array_column($dados['variantes'], 'tamanho'));
			foreach ($tamanhos as $tamanho): ?>
				<label class="variante">
					<input type="radio" name="tamanho" value="<?= htmlspecialchars($tamanho, ENT_QUOTES, 'UTF-8') ?>" onchange="atualizarProduto()">
					<?= htmlspecialchars($tamanho, ENT_QUOTES, 'UTF-8') ?>
				</label>
			<?php endforeach; ?>
		</div>

		<!-- Botão "Adicionar ao Carrinho" -->
		<form id="adicionarCarrinho" method="post" onsubmit="return adicionarCarrinho(event)">
			<input type="hidden" name="produto_id" value="<?= htmlspecialchars($dados['id'], ENT_QUOTES, 'UTF-8') ?>">
			<input type="hidden" id="sku_variante" name="sku" value="">
			<button type="submit">Adicionar ao Carrinho</button>
		</form>

		<!-- Botão "Comprar Agora" -->
		<form id="comprarAgora" method="post" onsubmit="return comprarAgora(event)">
			<input type="hidden" name="produto_id" value="<?= htmlspecialchars($dados['id'], ENT_QUOTES, 'UTF-8') ?>">
			<input type="hidden" id="sku_variante_comprar" name="sku" value="">
			<button class="comprar" type="submit">Comprar Agora</button>
		</form>

		<br><br><br>
		<div class="formas-pagamentos">
			<fieldset class="pag">
			<legend>Compra segura garantida!</legend>
				<img width="250px" src="<?= BASE_URL ?>Ativos/img/pag.png">
			</fieldset>
		</div>


	</div>

	
</div>


<!-- Script para AJAX no botão "Adicionar ao Carrinho" e Atualização de Produto -->
<script type="text/javascript">

	const BASE_URL = "<?= BASE_URL ?>";

	function atualizarProduto() {
		var corSelecionada = document.querySelector('input[name="cor"]:checked');
		var tamanhoSelecionado = document.querySelector('input[name="tamanho"]:checked');
		var precoProduto = document.getElementById('precoProduto');
		var imagemProduto = document.getElementById('imagemProduto');

		var cor = corSelecionada ? corSelecionada.value : '';
		var tamanho = tamanhoSelecionado ? tamanhoSelecionado.value : '';

		var variantes = <?= json_encode($dados['variantes']) ?>;
		var precoAtualizado = 'N/A';
		var varianteSku = '';
		var imagemAtualizada = '<?= htmlspecialchars($dados['imagem'] ?? '', ENT_QUOTES, 'UTF-8') ?>';

		for (var i = 0; i < variantes.length; i++) {
			if (variantes[i]['cor'] === cor && variantes[i]['tamanho'] === tamanho) {
				precoAtualizado = variantes[i]['preco'];
				varianteSku = variantes[i]['sku'];
				imagemAtualizada = variantes[i]['imagem'];
				break;
			}
		}

		precoProduto.innerText = precoAtualizado;
		imagemProduto.src = imagemAtualizada;
		document.getElementById('sku_variante').value = varianteSku;
		document.getElementById('sku_variante_comprar').value = varianteSku;

		console.log("Variante SKU atualizado:", varianteSku);
		console.log("Preço atualizado:", precoAtualizado);
	}


	function adicionarCarrinho(event) {
		event.preventDefault(); // Impede o envio normal do formulário

		var produtoId = "<?= htmlspecialchars($dados['id'], ENT_QUOTES, 'UTF-8') ?>";
		var sku = document.getElementById('sku_variante').value;

		if (!sku) {
			alert("Por favor, selecione uma variação.");
			return;
		}

		var xhr = new XMLHttpRequest();
		xhr.open("POST", "<?= BASE_URL ?>index.php/carrinho/adicionar/" + produtoId, true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.onreadystatechange = function () {
			if (xhr.readyState == 4 && xhr.status == 200) {
				alert("Produto adicionado ao carrinho! Você pode continuar navegando.");
			}
		};
		console.log("Enviando ao backend: produto_id=" + produtoId + ", sku=" + sku); // Log para depuração
		xhr.send("produto_id=" + produtoId + "&sku=" + sku);
	}

	function comprarAgora(event) {
		event.preventDefault(); // Impede o envio normal do formulário

		var produtoId = "<?= htmlspecialchars($dados['id'], ENT_QUOTES, 'UTF-8') ?>";
		var sku = document.getElementById('sku_variante_comprar').value;

		if (!sku) {
			alert("Por favor, selecione uma variação.");
			return;
		}

		var xhr = new XMLHttpRequest();
		xhr.open("POST", "<?= BASE_URL ?>index.php/carrinho/adicionar/" + produtoId, true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.onreadystatechange = function () {
			if (xhr.readyState == 4 && xhr.status == 200) {
				window.location.href = BASE_URL + "pagamento";
			}
		};
		console.log("Enviando ao backend (comprar): produto_id=" + produtoId + ", sku=" + sku); // Log para depuração
		xhr.send("produto_id=" + produtoId + "&sku=" + sku);
	}

</script>
<?php else: ?>
<p>Produto não encontrado.</p>
<?php endif; ?>
