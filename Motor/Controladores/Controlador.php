<?php

/* 
*   Classe base para todos os controladores.
*/

require_once './config.php';

// Métodos para carregar modelos (modelo()) e visões (vista())
class Controlador {
    public function modelo($modelo) {
        require_once 'Motor/Modelos/' . $modelo . '.php';
        return new $modelo();
    }

    public function vista($vista, $dados = []) {
        require_once 'Motor/Visoes/' . $vista . '.php';
    }


}
?>