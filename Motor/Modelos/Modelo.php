<?php
require_once './config.php';

/*
    Aqui estão os tratamentos dos dados
*/

class Modelo {
    protected $db;

    public function __construct() {
        try {
            $this->db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NOME, DB_USUARIO, DB_SENHA);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, value: PDO::ERRMODE_EXCEPTION);
        }

        catch (PDOException $e) {
            die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
        }
    }

}
?>
