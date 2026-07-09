<?php

class Componente {
    public static function renderizar(string $nome, array $dados = []) {
        $caminho = __DIR__ . "/$nome.php";

        if (file_exists($caminho)) {
        // Extrai as variáveis do array $dados
        extract($dados);

        // Inclui o componente
        include $caminho;
        } else {
        echo "Componente '$nome' não encontrado!";
        }
    }
}
