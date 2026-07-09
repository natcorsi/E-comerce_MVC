<?php
require_once __DIR__ . '/../controladores/AdminControlador.php';

$controle = new AdminControlador();
$controle->sincronizar();
?>