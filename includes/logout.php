<?php
require __DIR__ . '../../vendor/autoload.php';

use usuario\Usuario;

Usuario::logout(); // Chama o método logout da classe Usuario para encerrar a sessão
exit();
?>
