<?php
session_start();
session_unset();
session_destroy();
header("Location: ../paginas/pagina_inicial.php");
exit();
?>
