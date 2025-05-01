<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "medcar_avaliacoes";

// Cria a conexão com o banco de dados
$conn = new mysqli($host, $user, $pass, $dbname);

// Verifica se ocorreu algum erro na conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>
