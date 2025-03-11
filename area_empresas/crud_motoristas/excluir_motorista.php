<?php
$host = 'localhost';
$dbname = 'Motoristas_MedCar';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Não foi possível conectar ao banco de dados: " . $e->getMessage());
}

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];

try {
    // A foreign key cascade já remove o veículo associado
    $stmt = $pdo->prepare("DELETE FROM Motoristas WHERE id = ?");
    $stmt->execute([$id]);
    
    header('Location: index.php');
} catch(PDOException $e) {
    die("Erro ao excluir motorista: " . $e->getMessage());
}


?>