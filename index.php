<?php

/**
 * @file
 * Este é o ponto de entrada principal da aplicação. Ele configura a exibição de erros,
 * inclui os arquivos de configuração e controladores necessários, define funções de 
 * utilidade para análise de URL e carrega classes automaticamente.
 * 
 * @package LojaMVC
 * @version 1.1.0
 * @since 2024
 */

// Configura a exibição de erros do PHP
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir controladores aqui
require_once 'config.php';  
require_once 'Motor/Controladores/ProdutoControlador.php';
require_once 'Motor/Controladores/HomeControlador.php';
require_once 'Motor/Controladores/UsuarioControlador.php';
require_once 'Motor/Controladores/CarrinhoControlador.php';
require_once 'Motor/Controladores/PagamentoControlador.php';
require_once 'Motor/Controladores/AdminControlador.php';

// Iniciar Sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/**
 * Função para obter o argumento inicial da URL
 *
 * Esta função percorre os segmentos da URI e localiza a posição
 * do script 'index.php'. Retorna o índice do argumento inicial 
 * ou 0 se estiver na raiz.
 *
 * @param array $uri Array de segmentos da URI
 * @return int Índice do argumento inicial ou 0 se na raiz
 */
function obterArgumentoInicial($uri) {
    foreach ($uri as $chave => $valor) {
        if ($valor == 'index.php') {
            if ($chave == count($uri) - 1) return -1;
            return $chave + 1;
        }
    }
    return 0; // Agora que estamos na raiz, retornamos 0
}




/**
 * Função principal da aplicação. Analisa a URL e chama o controlador e método apropriados.
 *
 * Esta função decompõe a URL em parâmetros, identifica o controlador e método
 * a serem chamados com base nesses parâmetros, e então executa a função apropriada.
 *
 * @return void
 */
function main() {
    $uri = parse_url($_SERVER['REQUEST_URI']);
    $path = $uri['path']; // Remover '/LojaMVC' se necessário

    if (!isset($path)) {
        echo "Erro 404: Caminho não definido!";
        return;
    }

    $parametros = explode('/', trim($path, '/'));


    if (!empty($parametros) && $parametros[0] === 'E-comerce_MVC') {
        array_shift($parametros);
    }


    if (empty($parametros[0])) {
        $parametros = ['pagina', 'inicio'];
    }

    $iniciar = obterArgumentoInicial($parametros);

    if ($iniciar != -1 && isset($parametros[$iniciar]) && !empty($parametros[$iniciar])) {
        $controlador_nome = ucfirst($parametros[$iniciar]) . 'Controlador';
        $funcao_nome = isset($parametros[$iniciar + 1]) ? $parametros[$iniciar + 1] : 'index';
        $iniciar += 2;
        $args = array();
        for (; $iniciar < count($parametros); $iniciar++) {
            array_push($args, $parametros[$iniciar]);
        }
        if (class_exists($controlador_nome) && method_exists($controlador_nome, $funcao_nome)) {
            call_user_func_array(array(new $controlador_nome, $funcao_nome), $args);
        } else {
            echo "Erro 404: Controlador ou método não encontrado!";
        }
    } else {
        echo "Erro 404: Parâmetros inválidos!";
    }
}




/**
 * Função para carregar automaticamente as classes
 *
 * Esta função registra uma função de autoload que percorre os 
 * diretórios especificados para encontrar e incluir o arquivo
 * da classe solicitada.
 *
 * @param string $nome_classe Nome da classe a ser carregada
 * @return void
 */
spl_autoload_register(function ($nome_classe) {
    // Caminhos das pastas onde as classes estão localizadas
    $paths = [
        __DIR__ . 'Motor/API/interfaces',
        __DIR__ . 'Motor/API/importadores',
        __DIR__ . 'Motor/API/gerenciadores',
        __DIR__ . 'Motor/Controladores',
        __DIR__ . 'Motor/Modelos'
    ];

    foreach ($paths as $path) {
        $file = $path . '/' . $nome_classe . '.php';
        if (file_exists($file)) {
           require_once $file;
           return;
        }
    }
});



// Corpo cabeçalho e rodape
include 'Motor/Visoes/Partes/topo.php';
main();
include 'Motor/Visoes/Partes/base.php';