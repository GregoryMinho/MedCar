<?php
$servidor = "localhost"; 
$usuario = "root";
$senha = "OgtoQmorr10#000***"; // cimatec
$banco = "medcar_agendamentos";

$conn = new mysqli($servidor, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$conn->set_charset("utf8");

?>