<?php
session_start();

require '../includes/conexao_BdAgendamento.php';
require '../includes/classe_usuario.php';

use usuario\Usuario;
//Usuario::verificarPermissao('empresa');

$empresa_id = $_SESSION['usuario']['id'];

// Função para gerar o calendário
function gerarCalendario($mes, $ano, $agendamentos) {
    $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
    $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
    $primeiro_dia = date('w', strtotime("$ano-$mes-01"));
    $hoje = date('Y-m-d');
    
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
        $dataCalendario = "$ano-$mes-" . str_pad($dia, 2, '0', STR_PAD_LEFT);
        
        $eventos = array_filter($agendamentos, function($a) use ($dataCalendario) {
            $dataAgendamento = new DateTime($a['data_convertida'], new DateTimeZone('America/Sao_Paulo'));
            return $dataAgendamento->format('Y-m-d') === $dataCalendario;
        });
        
        $isToday = ($dataCalendario == $hoje) ? ' today' : '';
    
        $calendario .= '<div class="calendar-day' . (count($eventos) ? ' has-event' : '') . $isToday . '" 
                          onclick="showScheduleDetails(\'' . $dataCalendario . '\')">
                          <div class="day-number">' . $dia . '</div>
                          ' . (count($eventos) ? '<div class="event-indicator"><span>' . count($eventos) . '</span></div>' : '') . '
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


$mesAno = $_GET['mes'] ?? date('Y-m');

// Verifica se o valor está no formato "YYYY-MM"
if (preg_match('/^\d{4}-\d{2}$/', $mesAno)) {
    $dataFiltro = explode('-', $mesAno);
} else {
    $mesAno = date('Y-m');
    $dataFiltro = explode('-', $mesAno);
}

$filtros = [
    'status' => $_GET['status'] ?? 'all',
    'mes' => $mesAno,
    'tipo' => $_GET['tipo'] ?? 'all'
];

// Extrai ano e mês como inteiros
$ano = (int)$dataFiltro[0];
$mes = (int)$dataFiltro[1];

// Intervalo do mês
$inicio_mes = $filtros['mes'] . '-01';
$fim_mes = date('Y-m-t', strtotime($inicio_mes));

// QUERY ATUALIZADA COM FILTRO DE EMPRESA
$sql = "SELECT 
            a.*, 
            CONVERT_TZ(a.data_consulta, '+00:00', '+03:00') AS data_convertida, 
            c.nome 
        FROM medcar_agendamentos.agendamentos a
        JOIN medcar_cadastro_login.clientes c ON a.cliente_id = c.id
        WHERE a.empresa_id = :empresa_id
        AND DATE(CONVERT_TZ(a.data_consulta, '+00:00', '+03:00')) BETWEEN :inicio_mes AND :fim_mes";

// Parâmetros atualizados
$params = [
    ':inicio_mes' => $inicio_mes,
    ':fim_mes' => $fim_mes,
    ':empresa_id' => $empresa_id // USANDO ID DA SESSÃO
];

// Filtros adicionais
if ($filtros['status'] != 'all') {
    $sql .= " AND situacao = :status";
    $params[':status'] = $filtros['status'];
}

if ($filtros['tipo'] != 'all') {
    $sql .= " AND tipo_transporte = :tipo";
    $params[':tipo'] = $filtros['tipo'];
}

// Executa a query
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gera calendário
$dataFiltro = explode('-', $filtros['mes']);
$ano = $dataFiltro[0];
$mes = $dataFiltro[1];
$calendario = gerarCalendario($mes, $ano, $agendamentos);

// Conta total de agendamentos para o mês
$totalAgendamentos = count($agendamentos);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Agendamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #3a3b45;
            --accent-color: #36b9cc;
            --success-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-gray: #f8f9fc;
            --dark-gray: #5a5c69;
            --event-color: #4e73df;
            --today-color: #f8f0e3;
            --hover-color: #e9ecef;
        }
        
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: var(--light-gray);
            color: #333;
        }
        
        /* Navbar estilizada */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 0.8rem 1rem;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .navbar-brand i {
            font-size: 1.8rem;
            margin-right: 10px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            transition: all 0.3s;
        }
        
        .user-avatar:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }
        
        /* Sidebar estilizada */
        .sidebar {
            background: linear-gradient(180deg, var(--secondary-color) 0%, #2e2f3a 100%);
            color: white;
            min-height: 100vh;
            padding: 25px;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar h5 {
            font-weight: 600;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 15px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }
        
        .sidebar h5 i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .sidebar .form-label {
            font-weight: 500;
            color: #d1d3e2;
        }
        
        .sidebar .form-select, .sidebar .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .sidebar .form-select:focus, .sidebar .form-control:focus {
            background-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
            color: white;
        }
        
        .sidebar .form-select option {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .btn-schedule {
            background: linear-gradient(to right, var(--accent-color), var(--primary-color));
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px;
            border-radius: 8px;
            transition: all 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-schedule:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
            color: white;
        }
        
        .btn-light {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            font-weight: 600;
            padding: 10px;
            border-radius: 8px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-light:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        /* Estilos do calendário */
        .calendar-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .calendar-header-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            background: var(--primary-color);
            color: white;
            font-weight: 600;
            text-align: center;
        }
        
        .calendar-header {
            padding: 15px 10px;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .calendar-header:last-child {
            border-right: none;
        }
        
        .calendar-days-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background-color: #e0e0e0;
        }
        
        .calendar-day {
            background-color: white;
            min-height: 100px;
            padding: 10px;
            position: relative;
            cursor: pointer;
            transition: all 0.2s;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .calendar-day:hover {
            background-color: var(--hover-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }
        
        .calendar-day.has-event {
            background-color: #f0f5ff;
        }
        
        .calendar-day.has-event:hover {
            background-color: #e1ebff;
        }
        
        .calendar-day.today {
            background-color: var(--today-color);
            border-left: 4px solid var(--warning-color);
        }
        
        .calendar-day.today .day-number {
            color: var(--warning-color);
            font-weight: bold;
        }
        
        .calendar-day.empty {
            background-color: var(--light-gray);
            cursor: default;
        }
        
        .calendar-day.empty:hover {
            background-color: var(--light-gray);
            transform: none;
            box-shadow: none;
        }
        
        .day-number {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .event-indicator {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 26px;
            height: 26px;
            background: var(--event-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.8rem;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        /* Estilos para o conteúdo principal */
        .main-content {
            padding: 30px;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .dashboard-header h3 {
            font-weight: 700;
            color: var(--dark-gray);
            display: flex;
            align-items: center;
            margin: 0;
        }
        
        .dashboard-header h3 i {
            margin-right: 12px;
            color: var(--primary-color);
            font-size: 1.6rem;
        }
        
        .month-navigation {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .month-input-group {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .month-input-group .btn {
            border-radius: 0;
            background: white;
            color: var(--primary-color);
            font-weight: 600;
            padding: 8px 15px;
            border: none;
            transition: all 0.3s;
        }
        
        .month-input-group .btn:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .month-input-group input {
            border: none;
            padding: 8px 15px;
            text-align: center;
            font-weight: 500;
        }
        
        .agendamentos-count {
            background: var(--primary-color);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-left: 10px;
        }
        
        /* Modal estilizado */
        .modal-content {
            border-radius: 12px;
            overflow: hidden;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            border-bottom: none;
            padding: 20px;
        }
        
        .modal-title {
            font-weight: 600;
        }
        
        .btn-close {
            filter: invert(1);
        }
        
        .modal-body {
            padding: 25px;
        }
        
        .schedule-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            background: var(--light-gray);
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .schedule-item:hover {
            background: #e2e6f0;
            transform: translateX(5px);
        }
        
        .patient-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .patient-info {
            flex-grow: 1;
        }
        
        .patient-name {
            font-weight: 600;
            margin-bottom: 3px;
        }
        
        .schedule-time {
            font-size: 0.9rem;
            color: var(--dark-gray);
        }
        
        .schedule-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-pendente {
            background: rgba(246, 194, 62, 0.2);
            color: #b78a00;
        }
        
        .status-agendado {
            background: rgba(78, 115, 223, 0.2);
            color: var(--primary-color);
        }
        
        .status-concluido {
            background: rgba(28, 200, 138, 0.2);
            color: var(--success-color);
        }
        
        .status-cancelado {
            background: rgba(231, 74, 59, 0.2);
            color: var(--danger-color);
        }
        
        .no-schedules {
            text-align: center;
            padding: 30px;
            color: var(--dark-gray);
        }
        
        .no-schedules i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #d1d3e2;
        }
        
        /* Botão voltar */
        .btn-back {
            background: var(--light-gray);
            border: none;
            color: var(--dark-gray);
            font-weight: 600;
            padding: 8px 15px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background: #e2e6f0;
            color: var(--primary-color);
        }
        
        /* Responsividade */
        @media (max-width: 991px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -300px;
                width: 280px;
                z-index: 1050;
                transition: left 0.3s;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040;
                display: none;
            }
            
            .overlay.active {
                display: block;
            }
            
            .calendar-day {
                min-height: 70px;
            }
        }
        
        @media (max-width: 576px) {
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .month-navigation {
                width: 100%;
            }
            
            .month-input-group {
                width: 100%;
            }
            
            .month-input-group input {
                flex-grow: 1;
            }
            
            .calendar-header {
                padding: 10px 5px;
                font-size: 0.85rem;
            }
            
            .calendar-day {
                min-height: 60px;
                padding: 8px 5px;
            }
            
            .day-number {
                font-size: 0.9rem;
            }
            
            .event-indicator {
                top: 5px;
                right: 5px;
                width: 20px;
                height: 20px;
                font-size: 0.7rem;
            }
        }
        
        /* Loader para filtros */
        .loader {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
            display: none;
            margin-right: 10px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Botão de menu mobile */
        .menu-toggle {
            display: none;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        @media (max-width: 991px) {
            .menu-toggle {
                display: inline-flex;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/MedQ-2/area_empresas/menu_principal.php">
                <i class="fas fa-ambulance"></i>
                MedCar
            </a>
            <div class="d-flex align-items-center">
                <div class="text-white me-3"><?= $_SESSION['usuario']['nome']?></div>
                <div class="user-avatar"><?= substr($_SESSION['usuario']['nome'], 0, 2) ?></div>
            </div>
        </div>
    </nav>

    <div class="schedule-dashboard">
        <div class="container-fluid">
            <div class="row">
                <!-- Overlay para mobile -->
                <div class="overlay" id="overlay"></div>
                
                <!-- Botão de menu mobile -->
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars me-2"></i> Menu de Filtros
                </button>
                
                <!-- Sidebar -->
                <div class="col-lg-3 sidebar" id="sidebar">
                    <h5><i class="fas fa-filter me-2"></i>Filtros</h5>
                    <form method="GET" id="filterForm">
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

                        <button type="submit" class="btn btn-schedule w-100 mb-3" id="applyFiltersBtn">
                            <div class="loader" id="filterLoader"></div>
                            <i class="fas fa-sync me-2" id="filterIcon"></i>
                            <span id="filterText">Aplicar Filtros</span>
                        </button>
                    </form>

                    <a href="novo_agendamento.php" class="btn btn-light w-100">
                        <i class="fas fa-plus me-2"></i>Novo Agendamento
                    </a>
                </div>

                <!-- Conteúdo Principal -->
                <div class="col-lg-9 main-content">
                    <div class="dashboard-header">
                        <h3>
                            <i class="fas fa-calendar-alt"></i>Agendamentos
                            <span class="agendamentos-count"><?= $totalAgendamentos ?> agendamentos</span>
                        </h3>
                        <div class="month-navigation">
                            <div class="month-input-group">
                                <button class="btn" onclick="navigateMonth(-1)" aria-label="Mês anterior">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <input type="month" class="form-control" value="<?= $filtros['mes'] ?>" 
                                       onchange="window.location.href = '?mes=' + this.value">
                                <button class="btn" onclick="navigateMonth(1)" aria-label="Próximo mês">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body" id="modalAgendamentosList">
                    <!-- Lista de pacientes será carregada aqui -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Armazena a instância do modal globalmente
        const scheduleModal = new bootstrap.Modal(document.getElementById('scheduleModal'));
        let currentDate = null;
        
        function showScheduleDetails(data) {
            currentDate = data;
            
            fetch(`get_agendamentos.php?data=${data}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modalSelectedDate').textContent = formatarData(data);
                    document.getElementById('modalAgendamentosList').innerHTML = html;
                    scheduleModal.show();
                });
        }
        
        function showAppointmentDetails(id) {
            fetch(`get_detalhes_agendamento.php?id=${id}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modalAgendamentosList').innerHTML = `
                        <button onclick="backToList()" class="btn-back">
                            <i class="fas fa-arrow-left me-2"></i>Voltar
                        </button>
                        ${html}`;
                });
        }
        
        function backToList() {
            if(currentDate) {
                showScheduleDetails(currentDate);
            }
        }
        
        // Função auxiliar para formatar data
        function formatarData(dataString) {
            const [ano, mes, dia] = dataString.split('-');
            const data = new Date(ano, mes - 1, dia);
            const options = { day: '2-digit', month: 'long', year: 'numeric' };
            return data.toLocaleDateString('pt-BR', options);
        }
        
        // Navegação entre meses
        function navigateMonth(step) {
            const current = document.querySelector('input[type="month"]').value;
            const [year, month] = current.split('-');
            let date = new Date(year, month - 1 + step, 1);
            const newMonth = (date.getMonth() + 1).toString().padStart(2, '0');
            const newYear = date.getFullYear();
            window.location.href = `?mes=${newYear}-${newMonth}`;
        }
        
        // Menu mobile toggle
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });
        
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
        
        // Feedback de carregamento para filtros
        const filterForm = document.getElementById('filterForm');
        const filterLoader = document.getElementById('filterLoader');
        const filterIcon = document.getElementById('filterIcon');
        const filterText = document.getElementById('filterText');
        const applyFiltersBtn = document.getElementById('applyFiltersBtn');
        
        filterForm.addEventListener('submit', function() {
            applyFiltersBtn.disabled = true;
            filterLoader.style.display = 'block';
            filterIcon.style.display = 'none';
            filterText.textContent = 'Aplicando...';
        });
        
        // Destacar dia atual
        const today = new Date();
        const todayStr = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
        const todayElement = document.querySelector(`.calendar-day[onclick*="${todayStr}"]`);
        if (todayElement) {
            todayElement.classList.add('today');
        }
    </script>
</body>
</html>