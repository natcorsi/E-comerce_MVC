<?php

require_once 'Controlador.php';

class PaginaControlador extends Controlador {
    public function inicio() {

        // Supondo que você tenha uma variável $produtos definida corretamente
        $produtos = []; // Exemplo: obtenha os produtos do modelo
        $this->vista('Paginas/Inicio', ['produtos' => $produtos]);       
    }   

    public function sobre() {
        echo "Esta é a página sobre.";
    }

    public function contato() {
        echo "Formulário de contato aqui.";
    }
}
?>