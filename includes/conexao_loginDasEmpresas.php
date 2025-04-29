<?php
$servidor = "localhost:3306";
$usuario = "root";
$senha = ""; // cimatec
$banco = "medq_transporte";

try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$banco", $usuario, $senha);
    $pdo ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>
