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


$dia = $_GET['dia'] ?? null;
$mes = $_GET['mes'] ?? null;

if(!$dia || !$mes) die("Dia ou mês não especificado");

$data = "$mes-$dia";

$sql = "SELECT a.*, p.nome AS paciente, t.nome AS transportadora 
        FROM agendamentos a
        JOIN pacientes p ON a.paciente_id = p.id
        JOIN transportadoras t ON a.transportadora_id = t.id
        WHERE DATE(data_hora) = :data
        ORDER BY data_hora";

$stmt = $pdo->prepare($sql);
$stmt->execute([':data' => $data]);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($agendamentos as $agendamento) {
    echo '<div class="mb-3 schedule-item" onclick="showAppointmentDetails('.$agendamento['id'].')">';
    echo '<div class="d-flex align-items-center gap-3">';
    echo '<div class="schedule-icon"><i class="fas fa-user-injured"></i></div>';
    echo '<div>';
    echo '<h6 class="mb-0">'.htmlspecialchars($agendamento['paciente']).'</h6>';
    echo '<small class="text-muted">';
    echo date('H:i', strtotime($agendamento['data_hora'])).' - ';
    echo htmlspecialchars($agendamento['destino']).'</small>';
    echo '<span class="status-badge status-'.$agendamento['status'].'">';
    echo ucfirst($agendamento['status']).'</span>';
    echo '</div></div></div>';
}
?>