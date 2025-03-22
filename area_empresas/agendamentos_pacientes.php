<?php
$host = 'localhost';
$dbname = 'medcar_agendamentos';
$user = 'root';
$pass = '';

date_default_timezone_set('America/Sao_Paulo'); // Ou seu fuso horário

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Não foi possível conectar ao banco de dados: " . $e->getMessage());
}

// Função para gerar o calendário
function gerarCalendario($mes, $ano, $agendamentos) {
    $mes = str_pad($mes, 2, '0', STR_PAD_LEFT); // Garante 2 dígitos
    $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
    $primeiro_dia = date('w', strtotime("$ano-$mes-01"));
    
    $calendario = '<div class="calendar-container">';
    
    // Cabeçalho
    $calendario .= '<div class="calendar-header-grid">';
    foreach (['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'] as $dia) {
        $calendario .= '<div class="calendar-header">'.$dia.'</div>';
    }
    $calendario .= '</div>';
    
    // Dias
    $calendario .= '<div class="calendar-days-grid">';
    
    // Dias vazios
    for ($i = 0; $i < $primeiro_dia; $i++) {
        $calendario .= '<div class="calendar-day empty"></div>';
    }
    
    // Preenche dias
    for ($dia = 1; $dia <= $dias_mes; $dia++) {
        $data = "$ano-$mes-".str_pad($dia, 2, '0', STR_PAD_LEFT);
        $eventos = array_filter($agendamentos, fn($a) => $a['data_formatada'] == $data);
        
        $calendario .= '<div class="calendar-day'.(count($eventos) ? ' has-event' : '').'" 
                          onclick="showScheduleDetails(\''.$data.'\')">
                          <div class="day-number">'.$dia.'</div>
                          '.(count($eventos) ? '<div class="event-dot"></div>' : '').'
                        </div>';
    }
    
    // Completa grid
    $total_cells = $primeiro_dia + $dias_mes;
    $remaining = (7 - ($total_cells % 7)) % 7;
    for ($i = 0; $i < $remaining; $i++) {
        $calendario .= '<div class="calendar-day empty"></div>';
    }
    
    $calendario .= '</div></div>';
    return $calendario;
}
$filtros = [
    'status' => $_GET['status'] ?? 'all',
    'mes' => $_GET['mes'] ?? date('Y-m'),
    'tipo' => $_GET['tipo'] ?? 'all'
];

// Buscar agendamentos
$sql = "SELECT 
            a.*, 
            DATE(CONVERT_TZ(a.data_consulta, '+00:00', '-03:00')) AS data_formatada,
            c.nome 
        FROM medcar_agendamentos.agendamentos a
        JOIN medcar_cadastro_login.clientes c ON a.cliente_id = c.id
        WHERE DATE_FORMAT(CONVERT_TZ(a.data_consulta, '+00:00', '-03:00'), '%Y-%m') = :mes";

$params = [':mes' => $filtros['mes']];

if ($filtros['status'] != 'all') {
    $sql .= " AND situacao = :status";
    $params[':status'] = $filtros['status'];
}

if ($filtros['tipo'] != 'all') {
    $sql .= " AND tipo_transporte = :tipo";
    $params[':tipo'] = $filtros['tipo'];
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params); // Linha 84
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Processar datas para o calendário
$dataFiltro = explode('-', $filtros['mes']);
$ano = $dataFiltro[0];
$mes = $dataFiltro[1];
$calendario = gerarCalendario($mes, $ano, $agendamentos);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedQ - Agendamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    :root {
        --primary-color: #1a365d;
        --secondary-color: #2a4f7e;
        --accent-color: #38b2ac;
        --confirmed-color: #28a745;
        --pending-color: #ffc107;
        --cancelled-color: #dc3545;
    }

    /* Layout do Calendário */
    .calendar-container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
    }

    .calendar-header-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
        margin-bottom: 5px;
    }

    .calendar-days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
    }

    .calendar-header {
        text-align: center;
        font-weight: bold;
        padding: 10px;
        background: var(--primary-color);
        color: white;
        border-radius: 5px;
    }

    .calendar-day {
        background: white;
        border-radius: 10px;
        padding: 15px;
        min-height: 120px;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .calendar-day.empty {
        background: #f8f9fa;
        cursor: default;
    }

    .calendar-day.has-event {
        border: 2px solid var(--accent-color);
    }

    .day-number {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .event-dot {
        width: 8px;
        height: 8px;
        background: var(--accent-color);
        border-radius: 50%;
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2;
    }

    /* Estilos Gerais */
    body {
        background: #f8f9fa;
        font-family: Arial, sans-serif;
    }

    .schedule-dashboard {
        background: #f8f9fa;
        min-height: 100vh;
    }

    .schedule-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
        margin-bottom: 20px;
        padding: 20px;
        position: relative;
    }

    .schedule-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .status-badge {
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.9em;
        position: absolute;
        top: 15px;
        right: 15px;
    }

    .status-confirmed {
        background: var(--confirmed-color);
        color: white;
    }

    .status-pending {
        background: var(--pending-color);
        color: black;
    }

    .status-cancelled {
        background: var(--cancelled-color);
        color: white;
    }

    .schedule-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--accent-color);
        color: white;
    }

    .timeline {
        border-left: 3px solid var(--primary-color);
        padding-left: 1rem;
        margin: 1rem 0;
    }

    .btn-schedule {
        background: var(--accent-color);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .btn-schedule:hover {
        background: #2c7a7b;
        color: white;
    }

    .schedule-details {
        display: none;
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-top: 20px;
    }

    .schedule-card-details {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .schedule-card-details label.form-label {
        font-weight: bold;
        color: var(--primary-color);
    }

    #appointmentDetails p {
        margin-bottom: 0.5rem;
        padding: 8px;
        background: #f8f9fa;
        border-radius: 5px;
    }

    /* Navbar */
    .navbar {
        background: var(--primary-color);
    }

    .navbar-brand {
        font-weight: bold;
    }

    .navbar-brand i {
        margin-right: 10px;
    }

    /* Sidebar */
    .sidebar {
        background: var(--secondary-color);
        color: white;
        min-height: 100vh;
    }

    .sidebar h5 {
        color: white;
    }

    .sidebar .form-label {
        color: white;
    }

    .sidebar .form-select,
    .sidebar .form-control {
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
        border: none;
    }

    .sidebar .form-select:focus,
    .sidebar .form-control:focus {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .sidebar .btn-light {
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
        border: none;
    }

    .sidebar .btn-light:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }

    /* Modal */
    .modal-content {
        border-radius: 15px;
    }

    .modal-header {
        background: var(--primary-color);
        color: white;
        border-radius: 15px 15px 0 0;
    }

    .modal-title {
        font-weight: bold;
    }

    .modal-body {
        padding: 20px;
    }
</style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: var(--primary-color);">
        <div class="container">
            <a class="navbar-brand" href="/MedQ-2/paginas/pagina_inicial.php">
                <i class="fas fa-ambulance me-2"></i>
                MedQ Transportes
            </a>
            <div class="d-flex align-items-center">
                <div class="text-white me-3">Transportadora Saúde Total</div>
                <img src="https://source.unsplash.com/random/40x40/?icon" class="rounded-circle" alt="Perfil">
            </div>
        </div>
    </nav>

    <div class="schedule-dashboard">
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-3 p-4" style="background: var(--secondary-color); color: white; min-height: 100vh;">
                    <h5 class="mb-4"><i class="fas fa-filter me-2"></i>Filtros</h5>
                    <form method="GET">
                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="all" <?= $filtros['status'] == 'all' ? 'selected' : '' ?>>Todos</option>
                                <option value="Pendente" <?= $filtros['status'] == 'Pendente' ? 'selected' : '' ?>>Pendentes</option>
                                <option value="Agendado" <?= $filtros['status'] == 'Agendado' ? 'selected' : '' ?>>Agendados</option>
                                <option value="Concluído" <?= $filtros['status'] == 'Concluído' ? 'selected' : '' ?>>Concluídos</option>
                                <option value="Cancelado" <?= $filtros['status'] == 'Cancelado' ? 'selected' : '' ?>>Cancelados</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Período</label>
                            <input type="month" class="form-control" name="mes" value="<?= $filtros['mes'] ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Tipo de Serviço</label>
                            <select class="form-select" name="tipo">
                                <option value="all" <?= $filtros['tipo'] == 'all' ? 'selected' : '' ?>>Todos</option>
                                <option value="rotina" <?= $filtros['tipo'] == 'rotina' ? 'selected' : '' ?>>Rotina</option>
                                <option value="exame" <?= $filtros['tipo'] == 'exame' ? 'selected' : '' ?>>Exames</option>
                                <option value="emergencia" <?= $filtros['tipo'] == 'emergencia' ? 'selected' : '' ?>>Emergência</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-schedule w-100 mb-3">
                            <i class="fas fa-sync me-2"></i>Aplicar Filtros
                        </button>
                    </form>

                    <a href="novo_agendamento.php" class="btn btn-light w-100">
                        <i class="fas fa-plus me-2"></i>Novo Agendamento
                    </a>
                </div>

                <!-- Conteúdo Principal -->
                <div class="col-md-9 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3><i class="fas fa-calendar-alt me-2"></i>Agendamentos</h3>
                        <div class="d-flex gap-2">
                            <input type="month" class="form-control" value="<?= $filtros['mes'] ?>" 
                                   onchange="window.location.href = '?mes=' + this.value">
                        </div>
                    </div>

                    <!-- Calendário -->
                    <?= $calendario ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Agendamentos -->
    <div class="modal fade" id="scheduleModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agendamentos - <span id="modalSelectedDate"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalAgendamentosList">
                    <!-- Lista de pacientes será carregada aqui -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentDate = null; // Variável para armazenar a data selecionada

        // Função para mostrar o modal com a lista de pacientes
        function showScheduleDetails(data) {
            currentDate = data;
            fetch(`get_agendamentos.php?data=${data}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modalSelectedDate').textContent = formatarData(data);
                    document.getElementById('modalAgendamentosList').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('scheduleModal')).show();
                });
        }

        // Função para mostrar detalhes do paciente
        function showAppointmentDetails(id) {
            fetch(`get_detalhes_agendamento.php?id=${id}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modalAgendamentosList').innerHTML = `
                        <button onclick="backToList()" class="btn btn-secondary mb-3">
                            <i class="fas fa-arrow-left me-2"></i>Voltar para Lista
                        </button>
                        ${html}
                    `;
                });
        }

        // Função para voltar à lista de pacientes
        function backToList() {
            if(currentDate) {
                showScheduleDetails(currentDate);
            }
        }

        // Função auxiliar para formatar data
        function formatarData(dataString) {
    // Adiciona 'T00:00:00' para forçar UTC
    const data = new Date(dataString + 'T00:00:00Z');
    const options = { day: '2-digit', month: '2-digit', year: 'numeric', timeZone: 'UTC' };
    return data.toLocaleDateString('pt-BR', options);
}
    </script>
</body>
</html>