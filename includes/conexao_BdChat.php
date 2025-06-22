<?php
$host = 'localhost';
$db   = 'medcar_chat';
$user = 'root'; // ou o nome do seu usuário MySQL
$pass = '';     // senha do seu MySQL
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    exit('Erro na conexão com o banco de dados do chat: ' . $e->getMessage());
}
