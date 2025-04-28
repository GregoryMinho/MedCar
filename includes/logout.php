<?php
require 'classe_usuario.php'; // Inclui a classe Usuario
use usuario\Usuario;

Usuario::logOut(); // Chama o método logout da classe Usuario para encerrar a sessão e redirecionar
exit();
?>
