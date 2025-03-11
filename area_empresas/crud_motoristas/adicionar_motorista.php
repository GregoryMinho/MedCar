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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Inserir motorista
        $stmt = $pdo->prepare("INSERT INTO Motoristas 
                            (nome, cnh, status, cidade, estado, foto_url)
                            VALUES (?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $_POST['nome'],
            $_POST['cnh'],
            $_POST['status'],
            $_POST['cidade'],
            $_POST['estado'],
            $_POST['foto_url']
        ]);

        $motorista_id = $pdo->lastInsertId();

        // Inserir veículo
        $stmt = $pdo->prepare("INSERT INTO Veiculos
                            (motorista_id, placa, modelo)
                            VALUES (?, ?, ?)");
        
        $stmt->execute([
            $motorista_id,
            $_POST['placa'],
            $_POST['modelo']
        ]);

        header('Location: index.php');
        exit();
    } catch(PDOException $e) {
        die("Erro ao cadastrar motorista: " . $e->getMessage());
    }
}
?>

<!-- Formulário HTML para adicionar novo motorista -->