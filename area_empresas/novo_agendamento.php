<?php
$host = 'localhost';
$dbname = 'agendamentos_medcar';
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
        $stmt = $pdo->prepare("INSERT INTO agendamentos 
                            (paciente_id, transportadora_id, data_hora, destino, tipo, status)
                            VALUES (?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $_POST['paciente_id'],
            $_POST['transportadora_id'],
            $_POST['data_hora'],
            $_POST['destino'],
            $_POST['tipo'],
            $_POST['status']
        ]);

        header('Location: agendamentos.php');
        exit();
    } catch(PDOException $e) {
        die("Erro ao criar agendamento: " . $e->getMessage());
    }
}

// Buscar pacientes e transportadoras para o formulário
$pacientes = $pdo->query("SELECT id, nome FROM pacientes")->fetchAll();
$transportadoras = $pdo->query("SELECT id, nome FROM transportadoras")->fetchAll();
?>

<!-- Formulário HTML para novo agendamento -->