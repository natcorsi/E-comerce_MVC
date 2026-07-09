


    console.log("Script carregado!");


    ///////////////// Formulario de Edição de Informações do Usuario //////////////////////

    // Função para editar os dados
    function editarDados() {
    // Alterar a visibilidade dos campos de edição
    var nomeText = document.getElementById('nome-text');
    var nomeInput = document.getElementById('nome');
    var emailText = document.getElementById('email-text');
    var emailInput = document.getElementById('email');
    var enderecoText = document.getElementById('endereco-text');
    var enderecoInput = document.getElementById('endereco');
    var telefoneText = document.getElementById('telefone-text');
    var telefoneInput = document.getElementById('telefone');
    var btnSalvar = document.getElementById('btn-salvar');
    var btnCancelar = document.getElementById('btn-cancelar');
    var btnEditar = document.getElementById('btn-editar');

    // Exibir os campos de edição e esconder os textos
    nomeText.style.display = 'none';
    nomeInput.style.display = 'inline';
    emailText.style.display = 'none';
    emailInput.style.display = 'inline';
    enderecoText.style.display = 'none';
    enderecoInput.style.display = 'inline';
    telefoneText.style.display = 'none';
    telefoneInput.style.display = 'inline';

    // Exibir os botões de salvar e cancelar
    btnSalvar.style.display = 'inline';
    btnCancelar.style.display = 'inline';
    btnEditar.style.display = 'none';  // Esconde o botão "Alterar Dados"
    }

    // Função para cancelar a edição
    function cancelarEdicao() {
    // Voltar os campos para o estado de visualização
    var nomeText = document.getElementById('nome-text');
    var nomeInput = document.getElementById('nome');
    var emailText = document.getElementById('email-text');
    var emailInput = document.getElementById('email');
    var enderecoText = document.getElementById('endereco-text');
    var enderecoInput = document.getElementById('endereco');
    var telefoneText = document.getElementById('telefone-text');
    var telefoneInput = document.getElementById('telefone');
    var btnSalvar = document.getElementById('btn-salvar');
    var btnCancelar = document.getElementById('btn-cancelar');
    var btnEditar = document.getElementById('btn-editar');

    // Esconder os campos de entrada e exibir os textos
    nomeText.style.display = 'inline';
    nomeInput.style.display = 'none';
    emailText.style.display = 'inline';
    emailInput.style.display = 'none';
    enderecoText.style.display = 'inline';
    enderecoInput.style.display = 'none';
    telefoneText.style.display = 'inline';
    telefoneInput.style.display = 'none';

    // Esconder os botões de salvar e cancelar
    btnSalvar.style.display = 'none';
    btnCancelar.style.display = 'none';
    btnEditar.style.display = 'inline'; // Mostrar o botão "Alterar Dados"
    }

    // Função para salvar os dados
    function salvarDados() {
    // Obter os dados do formulário
    var form = document.getElementById('form-usuario');
    var formData = new FormData(form);

    // Fazer a requisição AJAX
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/usuario/editar", true);
    xhr.onreadystatechange = function() {
    if (xhr.readyState == 4 && xhr.status == 200) {
    // Processar a resposta
    var resposta = xhr.responseText;

    // Aqui você pode adicionar lógica para tratar a resposta (sucesso ou erro)
    alert('Dados atualizados com sucesso!');

    // Atualizar os textos com os novos valores
    var nomeText = document.getElementById('nome-text');
    var nomeInput = document.getElementById('nome');
    var emailText = document.getElementById('email-text');
    var emailInput = document.getElementById('email');
    var enderecoText = document.getElementById('endereco-text');
    var enderecoInput = document.getElementById('endereco');
    var telefoneText = document.getElementById('telefone-text');
    var telefoneInput = document.getElementById('telefone');

    nomeText.innerText = nomeInput.value;
    emailText.innerText = emailInput.value;
    enderecoText.innerText = enderecoInput.value;
    telefoneText.innerText = telefoneInput.value;

    // Desabilitar os campos novamente
    nomeText.style.display = 'inline';
    nomeInput.style.display = 'none';
    emailText.style.display = 'inline';
    emailInput.style.display = 'none';
    enderecoText.style.display = 'inline';
    enderecoInput.style.display = 'none';
    telefoneText.style.display = 'inline';
    telefoneInput.style.display = 'none';

    var btnSalvar = document.getElementById('btn-salvar');
    var btnCancelar = document.getElementById('btn-cancelar');
    var btnEditar = document.getElementById('btn-editar');
    btnSalvar.style.display = 'none';
    btnCancelar.style.display = 'none';
    btnEditar.style.display = 'inline'; // Mostrar o botão "Alterar Dados"
    }
    };
    xhr.send(formData);
    }





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