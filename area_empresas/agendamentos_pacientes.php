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

// Função para gerar o calendário
function gerarCalendario($mes, $ano, $agendamentos) {
    $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
    $primeiro_dia = date('N', strtotime("$ano-$mes-01"));
    
    $calendario = '<div class="calendar-grid">';
    
    // Cabeçalho dos dias da semana
    $diasSemana = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];
    foreach ($diasSemana as $diaSemana) {
        $calendario .= '<div class="calendar-header">'.$diaSemana.'</div>';
    }
    
    // Dias vazios no início
    for($i = 1; $i < $primeiro_dia; $i++) {
        $calendario .= '<div class="calendar-day empty"></div>';
    }
    
    // Dias do mês
    for($dia = 1; $dia <= $dias_mes; $dia++) {
        $data = "$ano-$mes-".str_pad($dia, 2, '0', STR_PAD_LEFT);
        $eventos = array_filter($agendamentos, function($a) use ($data) {
            return date('Y-m-d', strtotime($a['data_hora'])) == $data;
        });
        
        $calendario .= '<div class="calendar-day'.(count($eventos) ? ' has-event' : '').'" 
                          onclick="showScheduleDetails('.$dia.')">
                          <div class="day-number">'.$dia.'</div>
                          '.(count($eventos) ? '<div class="event-dot"></div>' : '').'
                        </div>';
    }
    
    // Completa a última semana
    $total_cells = count($diasSemana) + $primeiro_dia - 1 + $dias_mes;
    $remaining_days = 7 - ($total_cells % 7);
    if($remaining_days < 7) {
        for($i = 0; $i < $remaining_days; $i++) {
            $calendario .= '<div class="calendar-day empty"></div>';
        }
    }
    
    $calendario .= '</div>';
    return $calendario;
}

$filtros = [
    'status' => $_GET['status'] ?? 'all',
    'mes' => $_GET['mes'] ?? date('Y-m'),
    'tipo' => $_GET['tipo'] ?? 'all'
];

// Buscar agendamentos
$sql = "SELECT a.*, p.nome AS paciente, t.nome AS transportadora 
        FROM agendamentos a
        JOIN pacientes p ON a.paciente_id = p.id
        JOIN transportadoras t ON a.transportadora_id = t.id
        WHERE DATE_FORMAT(data_hora, '%Y-%m') = :mes";

$params = [':mes' => $filtros['mes']];

if($filtros['status'] != 'all') {
    $sql .= " AND status = :status";
    $params[':status'] = $filtros['status'];
}

if($filtros['tipo'] != 'all') {
    $sql .= " AND tipo = :tipo";
    $params[':tipo'] = $filtros['tipo'];
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Processar datas para o calendário
$mes = date('m', strtotime($filtros['mes']));
$ano = date('Y', strtotime($filtros['mes']));
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

        body {
            background: #f8f9fa;
        }

        .schedule-dashboard {
            background: #f8f9fa;
            min-height: 100vh;
        }

        .schedule-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
            margin-bottom: 20px;
            padding: 20px;
            position: relative;
        }

        .schedule-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            position: absolute;
            top: 15px;
            right: 15px;
        }

        .status-confirmed { background: var(--confirmed-color); color: white; }
        .status-pending { background: var(--pending-color); color: black; }
        .status-cancelled { background: var(--cancelled-color); color: white; }

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

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            margin-bottom: 20px;
        }

        /* Estilo para os dias do calendário */
        .calendar-day {
            background: white;
            border-radius: 10px;
            padding: 15px;
            min-height: 100px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        /* Caixa quadrada para os dias 1 e 2 */
        .calendar-day.first-two-days {
            width: 80px;  /* Ajuste o tamanho conforme necessário */
            height: 80px; /* Mantém as caixas quadradas */
        }

        /* Caixa larga para os outros dias */
        .calendar-day:not(.first-two-days) {
            width: 100%;
        }

        .calendar-day:hover {
            background: #f8f9fa;
            transform: scale(1.02);
        }

        .calendar-day.active {
            border: 2px solid var(--accent-color);
        }

        .event-dot {
            width: 8px;
            height: 8px;
            background: var(--accent-color);
            border-radius: 50%;
            position: absolute;
            bottom: 5px;
            left: 50%;
            transform: translateX(-50%);
        }

        .schedule-details {
            display: none;
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }
        .calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 5px;
    margin-bottom: 20px;
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
    min-height: 100px;
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

.day-number {
    font-weight: bold;
    margin-bottom: 5px;
}

.event-dot {
    width: 8px;
    height: 8px;
    background: var(--accent-color);
    border-radius: 50%;
    align-self: center;
}
/* Adicione estes estilos */
.schedule-card-details {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: var(--primary-color);">
        <div class="container">
            <a class="navbar-brand" href="#">
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
                                <option value="pending" <?= $filtros['status'] == 'pending' ? 'selected' : '' ?>>Pendentes</option>
                                <option value="confirmed" <?= $filtros['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmados</option>
                                <option value="cancelled" <?= $filtros['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelados</option>
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

                    <!-- Detalhes do Agendamento -->
                    <div class="schedule-details" id="scheduleDetails">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>Agendamentos - <span id="selectedDate"></span></h5>
        <button class="btn btn-close" onclick="hideScheduleDetails()"></button>
    </div>
    
    <div id="agendamentosList"></div>
    <div id="appointmentDetails" style="display: none;"></div>
</div>
                        
                        <div class="timeline" id="agendamentosList">
                            <?php foreach ($agendamentos as $agendamento): ?>
                            <div class="mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="schedule-icon">
                                        <i class="fas fa-user-injured"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0"><?= htmlspecialchars($agendamento['paciente']) ?></h6>
                                        <small class="text-muted">
                                            <?= date('H:i', strtotime($agendamento['data_hora'])) ?> - 
                                            <?= htmlspecialchars($agendamento['destino']) ?>
                                        </small>
                                        <span class="status-badge status-<?= $agendamento['status'] ?>">
                                            <?= ucfirst($agendamento['status']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script>
    // Funções para mostrar detalhes
   // Adicione estas funções
function showAppointmentDetails(agendamentoId) {
    fetch(`get_detalhes_agendamento.php?id=${agendamentoId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('appointmentDetails').innerHTML = data;
            document.getElementById('appointmentDetails').style.display = 'block';
            document.getElementById('agendamentosList').style.display = 'none';
        });
}

function backToDaySchedule() {
    document.getElementById('appointmentDetails').style.display = 'none';
    document.getElementById('agendamentosList').style.display = 'block';
}

// Modifique a função existente
function showScheduleDetails(dia) {
    const mes = "<?= $filtros['mes'] ?>";
    fetch(`get_agendamentos.php?dia=${dia}&mes=${mes}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('scheduleDetails').style.display = 'block';
            document.getElementById('selectedDate').textContent = `${dia}/${mes.split('-')[1]}/${mes.split('-')[0]}`;
            document.getElementById('agendamentosList').innerHTML = data;
            document.getElementById('appointmentDetails').style.display = 'none';
        });
}
    </script>
</body>
</html>
