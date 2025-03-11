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

// Buscar dados do motorista
$stmt = $pdo->prepare("SELECT m.*, v.placa, v.modelo 
                      FROM Motoristas m
                      LEFT JOIN Veiculos v ON m.id = v.motorista_id
                      WHERE m.id = ?");
$stmt->execute([$id]);
$motorista = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$motorista) {
    die("Motorista não encontrado");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Atualizar motorista
        $stmt = $pdo->prepare("UPDATE Motoristas SET
                            nome = ?,
                            cnh = ?,
                            status = ?,
                            cidade = ?,
                            estado = ?,
                            foto_url = ?
                            WHERE id = ?");
        
        $stmt->execute([
            $_POST['nome'],
            $_POST['cnh'],
            $_POST['status'],
            $_POST['cidade'],
            $_POST['estado'],
            $_POST['foto_url'],
            $id
        ]);

        // Atualizar veículo
        $stmt = $pdo->prepare("UPDATE Veiculos SET
                            placa = ?,
                            modelo = ?
                            WHERE motorista_id = ?");
        
        $stmt->execute([
            $_POST['placa'],
            $_POST['modelo'],
            $id
        ]);

        header('Location: index.php');
        exit();
    } catch(PDOException $e) {
        die("Erro ao atualizar motorista: " . $e->getMessage());
    }
}
?>

<!-- Formulário HTML para edição -->