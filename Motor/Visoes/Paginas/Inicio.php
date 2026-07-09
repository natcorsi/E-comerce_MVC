
<script>
	let currentSlide = 0;

	function moveToSlide(index) {
		currentSlide = index;
		const slideWidth = document.querySelector('.slide').offsetWidth;
		document.querySelector('.slides').style.transform = `translateX(-${slideWidth * index}px)`;

		document.querySelectorAll('.dot').forEach((dot, i) => {
			dot.classList.toggle('active', i === index);
		});
	}

	function autoSlide() {
		currentSlide = (currentSlide + 1) % 3;
		moveToSlide(currentSlide);
	}

	setInterval(autoSlide, 4000); // Muda o slide a cada 4 segundos
</script>



<!-------------------------- Slides --------------------------> 
<div class="slider">
        <div class="slides">
            <div class="slide slide-1">
                <h2>Peças Artesanais Únicas</h2>
                <p>Descubra roupas em crochê, tricô e tie-dye feitas à mão.</p>
                <a href="#" class="cta-btn">Ver Produtos</a>
            </div>
            <div class="slide slide-2">
                <h2>Toque de Magia e Exclusividade</h2>
                <p>Cada peça é única, feita com carinho e energia especial.</p>
                <a href="#" class="cta-btn">Quero o Meu</a>
            </div>
            <div class="slide slide-3">
                <h2>Parcelamos em 10x Sem Juros</h2>
                <p>Facilitamos sua compra para você levar sua peça favorita.</p>
                <a href="#" class="cta-btn">Comprar Agora</a>
            </div>
        </div>
        
        <div class="dots">
            <div class="dot active" onclick="moveToSlide(0)"></div>
            <div class="dot" onclick="moveToSlide(1)"></div>
            <div class="dot" onclick="moveToSlide(2)"></div>
        </div>
    </div>


<!-------------------------- Informações -------------------------->
<br><br><br><br>
<div class="container icons-home">

	<div class="loja-icon frete">
		<svg class="icone" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 640 512">
			<path fill="white" d="M368 0C394.5 0 416 21.49 416 48V96H466.7C483.7 96 499.1 102.7 512 114.7L589.3 192C601.3 204 608 220.3 608 237.3V352C625.7 352 640 366.3 640 384C640 401.7 625.7 416 608 416H576C576 469 533 512 480 512C426.1 512 384 469 384 416H256C256 469 213 512 160 512C106.1 512 64 469 64 416H48C21.49 416 0 394.5 0 368V48C0 21.49 21.49 0 48 0H368zM416 160V256H544V237.3L466.7 160H416zM160 368C133.5 368 112 389.5 112 416C112 442.5 133.5 464 160 464C186.5 464 208 442.5 208 416C208 389.5 186.5 368 160 368zM480 464C506.5 464 528 442.5 528 416C528 389.5 506.5 368 480 368C453.5 368 432 389.5 432 416C432 442.5 453.5 464 480 464z"></path>
		</svg>
		<div class="icons-titulo caixa">
			<h5 class="icon-titulo">Frete grátis</h5>
			<p class="icon-desc">Para todo brasil</p>
		</div>
	</div>

	<div class="loja-icon parcele">
		<svg class="icone" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 576 512">
			<path fill="white" d="M512 32C547.3 32 576 60.65 576 96V128H0V96C0 60.65 28.65 32 64 32H512zM576 416C576 451.3 547.3 480 512 480H64C28.65 480 0 451.3 0 416V224H576V416zM112 352C103.2 352 96 359.2 96 368C96 376.8 103.2 384 112 384H176C184.8 384 192 376.8 192 368C192 359.2 184.8 352 176 352H112zM240 384H368C376.8 384 384 376.8 384 368C384 359.2 376.8 352 368 352H240C231.2 352 224 359.2 224 368C224 376.8 231.2 384 240 384z"></path>
		</svg>
		<div class="icons-titulo caixa">
			<h5 class="icon-titulo">Parcele</h5>
			<p class="icon-desc"> Em até 12x </p>
		</div>
	</div>
	
	<div class="loja-icon seguro">
	<svg class="icone" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 512 512">
    	<path fill="white" d="M256-.0078C260.7-.0081 265.2 1.008 269.4 2.913L457.7 82.79C479.7 92.12 496.2 113.8 496 139.1C495.5 239.2 454.7 420.7 282.4 503.2C265.7 511.1 246.3 511.1 229.6 503.2C57.25 420.7 16.49 239.2 15.1 139.1C15.87 113.8 32.32 92.12 54.3 82.79L242.7 2.913C246.8 1.008 251.4-.0081 256-.0078V-.0078zM256 444.8C393.1 378 431.1 230.1 432 141.4L256 66.77L256 444.8z"></path>
	</svg>
		<div class="icons-titulo caixa">
			<h5 class="icon-titulo">Site Seguro</h5>
			<p class="icon-desc"> Seus dados protegidos</p>
		</div>
	</div>


	<div class="loja-icon monitorado">
	<svg class="icone" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 512 512">
    	<path fill="white" d="M236 176c0 15.46-12.54 28-28 28S180 191.5 180 176S192.5 148 208 148S236 160.5 236 176zM500.3 500.3c-15.62 15.62-40.95 15.62-56.57 0l-119.7-119.7c-40.41 27.22-90.9 40.65-144.7 33.46c-91.55-12.23-166-87.28-177.6-178.9c-17.24-136.2 97.29-250.7 233.4-233.4c91.64 11.6 166.7 86.07 178.9 177.6c7.19 53.8-6.236 104.3-33.46 144.7l119.7 119.7C515.9 459.3 515.9 484.7 500.3 500.3zM294.1 182.2C294.1 134.5 255.6 96 207.1 96C160.4 96 121.9 134.5 121.9 182.2c0 38.35 56.29 108.5 77.87 134C201.8 318.5 204.7 320 207.1 320c3.207 0 6.26-1.459 8.303-3.791C237.8 290.7 294.1 220.5 294.1 182.2z"></path>
	</svg>
		<div class="icons-titulo caixa">
			<h5 class="icon-titulo">Monitorado</h5>
			<p class="icon-desc">Sua compra protegida</p>
		</div>
	</div>
</div>


<!-------------------------- Produtos -------------------------->

<br><br>
<div class="ondas"></div>
<div class="container produtos">





<h1>Confira nossos produtos:</h1>

	<?php
	require_once 'Motor/Componentes/Componentes.php';
	Componente::renderizar('Produtos');
	?>

</div>


<!-------------------------- Formas de Pagamento -------------------------->
<div class="container pagamentos">
	<img width="30%" src="Ativos/img/pagm.png">
</div>