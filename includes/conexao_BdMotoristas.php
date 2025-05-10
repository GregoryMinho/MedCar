<?php
$host = 'localhost:3306';
$dbname = 'Motoristas_MedCar';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conexão bem-sucedida!"; // Apenas para teste
} catch (PDOException $e) {
    die("Erro na conexão banco motorista: " . $e->getMessage());
}
