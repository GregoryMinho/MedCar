<?php
// conexao_BdFinanceiro.php (PDO)
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "medcar_financeiro";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Falha na conexão: " . $e->getMessage());
}
?>