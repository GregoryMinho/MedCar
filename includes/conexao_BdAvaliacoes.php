<?php
$servidor = "localhost:3306";
$usuario = "root";
$senha = ""; // cimatec
$banco = "medcar_avaliacoes";

try {
    $conn = new PDO("mysql:host=$servidor;dbname=$banco", $usuario, $senha);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>
