<?php
$servidor = "localhost";
$usuario = "root";
$senha = ""; // cimatec
$banco = "medcar_avaliacoes";

try {
    $conn = new PDO("mysql:host=$servidor;dbname=$banco", $usuario, $senha);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erro de conexão banco avaliações: " . $e->getMessage());
}
?>
