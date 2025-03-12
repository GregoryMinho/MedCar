<?php
$host = 'localhost';
$dbname = 'medcar_agendamentos';
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

if (!$dia || !$mes) die("Dia ou mês não especificado");

// Padroniza o dia com 2 dígitos (ex: 5 → 05)
$dia = str_pad($dia, 2, '0', STR_PAD_LEFT);
$data = "$mes-$dia";

// Verifica se a data é válida
if (!DateTime::createFromFormat('Y-m-d', $data)) {
    die("Data inválida: $data");
}

$sql = "SELECT a.*, u.nome AS cliente 
        FROM agendamentos a
        JOIN usuarios u ON a.cliente_id = u.id
        WHERE data_consulta = :data
        ORDER BY horario";

$stmt = $pdo->prepare($sql);
$stmt->execute([':data' => $data]);

// Verifica erros no SQL
if ($stmt->errorCode() != '00000') {
    print_r($stmt->errorInfo());
    die();
}

$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($agendamentos as $agendamento) {
    $destino = implode(', ', array_filter([
        $agendamento['rua_destino'],
        $agendamento['numero_destino'],
        $agendamento['complemento_destino'],
        $agendamento['cidade_destino']
    ]));

    echo '<div class="mb-3 schedule-item" onclick="showAppointmentDetails('.$agendamento['id'].')">';
    echo '<div class="d-flex align-items-center gap-3">';
    echo '<div class="schedule-icon"><i class="fas fa-user-injured"></i></div>';
    echo '<div>';
    echo '<h6 class="mb-0">'.htmlspecialchars($agendamento['cliente']).'</h6>';
    echo '<small class="text-muted">';
    echo htmlspecialchars($agendamento['horario']).' - ';
    echo htmlspecialchars($destino).'</small>';
    echo '<span class="status-badge status-'.strtolower($agendamento['situacao']).'">';
    echo htmlspecialchars($agendamento['situacao']).'</span>';
    echo '</div></div></div>';
}
?>