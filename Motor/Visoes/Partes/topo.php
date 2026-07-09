<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Toca dos Magos</title>
		<link rel="stylesheet" type="text/css"  href="<?= BASE_URL ?>Ativos/estilos.css" media="screen">
		<script type="text/javascript" src="Ativos/scripts.js"></script>



		<!-- Evitar Cokies --->
		<?php

		// Limpa todos os cookies existentes
		
	//	foreach ($_COOKIE as $cookie_name => $cookie_value) {
	//		setcookie($cookie_name, '', time() - 3600, '/');
	//	}
		
		?>
 
		<!-- Adicionar o meta tag no header para garantir que o navegador não armazene cookies 
		<meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, proxy-revalidate">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Expires" content="0">
		-->
		
	</head>
	<body>

		<header>
			<!-- Logo -->
			<div class="logo">
			<a href="<?= BASE_URL ?>">
				<img src="<?= BASE_URL ?>Ativos/img/logo.png">
			</a>
			</div>
			
			<!-- Botões de navegação -->
			<div class="botoes-topo">
			<!-- Carrinho -->
			<div class="carrinho">
				<a href="#" id="carrinho-icon">
				<i class="icon carrinho"></i>

				<span class="carrinho-itens-cont">0</span>

				<span class="carrinho-total">
					<b>R$</b> 00
				</span>
				</a>
				<!-- Carrinho flutuante -->
				<div id="carrinho-modal" class="carrinho-modal">
				<?php
					require_once 'Motor/Componentes/Componentes.php';
					Componente::renderizar('Carrinho');
				?>
				</div>
			</div>

			<!-- Perfil  -->
			<div class="perfil">
				<a href="<?= BASE_URL ?>usuario/perfil">
				<i class="icon usuario"></i>
				</a>
			</div>
			

			<script type="text/javascript">
				
				document.getElementById('carrinho-icon').addEventListener('click', (event) => {
				    event.preventDefault();
				    console.log("Carrinho carregado!");
				    const carrinhoModal = document.getElementById('carrinho-modal');
				    carrinhoModal.classList.toggle('active');
				});

				document.addEventListener("DOMContentLoaded", function () {
				    atualizarCarrinhoTopbar();
				});

				function atualizarCarrinhoTopbar() {
				    const modal = document.querySelector("#carrinho-modal");

				    if (!modal) return;

				    // Total de itens
				    let totalItens = 0;

				    modal.querySelectorAll("tbody tr").forEach(linha => {
				        const qtd = parseInt(linha.cells[1].textContent.trim()) || 0;
				        totalItens += qtd;
				    });

				    document.querySelector(".carrinho-itens-cont").textContent = totalItens;

				    // Total em dinheiro
				    const totalTexto = modal.querySelector("strong")?.parentNode.textContent || "";

				    const resultado = totalTexto.match(/R\$\s*([\d.,]+)/);

				    if (resultado) {
				        document.querySelector(".carrinho-total").innerHTML =
				            "<b>R$</b> " + resultado[1];
				    } else {
				        document.querySelector(".carrinho-total").innerHTML =
				            "<b>R$</b> 00";
				    }
				}
			</script>

		</header>	
		<content>