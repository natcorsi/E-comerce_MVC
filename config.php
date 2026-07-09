<?php

/**
 * @file
 * Arquivo de configuração principal da aplicação. Carrega variáveis de ambiente,
 * define constantes de configuração do banco de dados e API, e fornece uma 
 * função para registrar logs.
 * 
 */

require_once __DIR__ . '/vendor/autoload.php';


/**
 * Definir diretório base.
*/

define('BASE_URL', '/E-comerce_MVC/');



/**
 * Carrega as variáveis de ambiente.
 */

$ambiente = Dotenv\Dotenv::createImmutable(__DIR__);
$ambiente->load();




/**
 * Definições de constantes de configuração do banco de dados e API.
 *
 * Estas definições podem ser utilizadas ao longo da aplicação para acessar
 * o banco de dados e a API da Printful.
 *
 */







define('DB_HOST', 'localhost');
define('DB_USUARIO', 'natcorsi');
define('DB_SENHA', 'marcia1974');
define('DB_NOME', 'Loja_MVC');

define('PRINTFUL_API_KEY', $_ENV['PRINTFUL_API_KEY']);
define('MERCADOPAGO_ACCESS_TOKEN', $_ENV['MERCADOPAGO_ACCESS_TOKEN']);



/*
 * Registra uma mensagem de log em um arquivo de log.
 *
 * Esta função formata a mensagem de log com a data, hora, nome do arquivo
 * e a mensagem fornecida, e então escreve essa mensagem em um arquivo
 * de log localizado na raiz do projeto.
 *
 * @param string $mensagem A mensagem a ser registrada no log.
 * @param string $arquivo O nome do arquivo de onde a mensagem está sendo registrada. O padrão é o próprio arquivo de configuração.
 * @return void
 */

function registrar_log($mensagem, $arquivo = __FILE__) {
   // Caminho do arquivo de log na raiz do projeto
   $caminho_log = __DIR__ . '/log.txt';   // Extrair apenas o nome do arquivo
   $nome_arquivo = basename($arquivo);

   // Formatar a mensagem com data e hora
   $data_hora = date('d-m-Y H:i:s'); 
   $mensagem_formatada = "[$data_hora][$nome_arquivo] - $mensagem \n";

   // Escrever a mensagem no arquivo de log
   error_log($mensagem_formatada, 3, $caminho_log);
}




error_reporting(E_ALL & ~E_DEPRECATED);