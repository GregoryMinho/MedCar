<?php
require __DIR__ . '../../vendor/autoload.php';
require 'classe_usuario.php'; // Inclui a classe Usuario
//use usuario\Usuario;
//Usuario::logout(); // Chama o método logout da classe Usuario para encerrar a sessão
session_start();
session_destroy();
header("Location: ../area_cliente/menu_principal.php");
exit();
?>
