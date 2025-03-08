<?php
$servidor = "localhost";
$usuario = "root";
$senha = "cimatec";
$banco = "medcar";

$conn = new mysqli($servidor, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$conn->set_charset("utf8");

?>