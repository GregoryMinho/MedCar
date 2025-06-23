<?php
session_start();

require '../includes/conexao_BdAgendamento.php';
require '../includes/classe_usuario.php';

use usuario\Usuario;
// Usuario::verificarPermissao('empresa');

$empresa_id = $_SESSION['usuario']['id'];

// ==== FILTROS ====
$filtros = [
    'status' => $_GET['status'] ?? 'all',
    'mes'    => $_GET['mes'] ?? date('Y-m'),
    'tipo'   => $_GET['tipo'] ?? 'all'
];

// Checa o formato do mês
if (!preg_match('/^\d{4}-\d{2}$/', $filtros['mes'])) {
    $filtros['mes'] = date('Y-m');
}
$ano = (int)substr($filtros['mes'], 0, 4);
$mes = (int)substr($filtros['mes'], 5, 2);

// Intervalo do mês selecionado
$inicio_mes = $filtros['mes'] . '-01';
$fim_mes = date('Y-m-t', strtotime($inicio_mes));

// ==== QUERY PRINCIPAL ====
$sql = "SELECT 
            a.*, 
            CONVERT_TZ(a.data_consulta, '+00:00', '+03:00') AS data_convertida, 
            c.nome 
        FROM medcar_agendamentos.agendamentos a
        JOIN medcar_cadastro_login.clientes c ON a.cliente_id = c.id
        WHERE a.empresa_id = :empresa_id
        AND DATE(CONVERT_TZ(a.data_consulta, '+00:00', '+03:00')) BETWEEN :inicio_mes AND :fim_mes";

$params = [
    ':inicio_mes' => $inicio_mes,
    ':fim_mes' => $fim_mes,
    ':empresa_id' => $empresa_id
];

if ($filtros['status'] != 'all') {
    $sql .= " AND a.situacao = :status";
    $params[':status'] = $filtros['status'];
}
if ($filtros['tipo'] != 'all') {
    $sql .= " AND a.tipo_transporte = :tipo";
    $params[':tipo'] = $filtros['tipo'];
}

$sql .= " ORDER BY a.data_consulta DESC, a.horario DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = count($agendamentos);
$totalConcluidos = 0;
foreach ($agendamentos as $a) if ($a['situacao'] === 'Concluído') $totalConcluidos++;
$completionRate = $total > 0 ? round(($totalConcluidos / $total) * 100) : 0;
$dailyAverage = $total / cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
$dailyAverage = number_format($dailyAverage, 1);

function nomeMes($mes) {
    $meses = [
        1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4 => "Abril",
        5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto",
        9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro"
    ];
    return $meses[(int)$mes] ?? $mes;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Relatórios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2a4f7e;
            --accent-color: #38b2ac;
        }
        body {
            background: #f8f9fa;
        }
        .reports-sidebar {
            background: var(--primary-color);
            color: white;
            min-height: 100vh;
            padding: 24px 16px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.07);
        }
        .filter-btn {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .filter-btn:hover {
            background: #2c7a7b;
        }
        .report-header {
            background: var(--secondary-color);
            color: white;
            border-radius: 12px;
            padding: 28px 30px 18px;
            margin-bottom: 30px;
        }
        .patient-card {
            background: white;
            border-radius: 13px;
            box-shadow: 0 4px 13px rgba(0,0,0,0.08);
            margin-bottom: 18px;
            padding: 18px 22px;
            transition: box-shadow 0.2s;
        }
        .patient-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.14);
        }
        .status-badge {
            padding: 7px 14px;
            border-radius: 18px;
            font-size: .94em;
            font-weight: 500;
        }
        .status-agendado { background: #ffc107; color: #222; }
        .status-concluido { background: #28a745; color: #fff; }
        .status-cancelado { background: #dc3545; color: #fff; }
        .status-pendente { background: #e67e22; color: #fff; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background: var(--primary-color);">
    <div class="container">
        <a class="navbar-brand" href="menu_principal.php">
            <i class="fas fa-ambulance me-2"></i>
            MedCar
        </a>
        <div class="d-flex align-items-center">
            <div class="text-white me-3"><?= $_SESSION['usuario']['nome'] ?? '' ?></div>
        </div>
    </div>
</nav>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Filtros -->
        <div class="col-md-3 reports-sidebar">
            <h5 class="mb-4"><i class="fas fa-filter me-2"></i>Filtros</h5>
            <form method="GET">
                <div class="mb-4">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="all" <?= $filtros['status']=='all'?'selected':'' ?>>Todos</option>
                        <option value="Pendente" <?= $filtros['status']=='Pendente'?'selected':'' ?>>Pendentes</option>
                        <option value="Agendado" <?= $filtros['status']=='Agendado'?'selected':'' ?>>Agendados</option>
                        <option value="Concluído" <?= $filtros['status']=='Concluído'?'selected':'' ?>>Concluídos</option>
                        <option value="Cancelado" <?= $filtros['status']=='Cancelado'?'selected':'' ?>>Cancelados</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label">Período</label>
                    <input type="month" class="form-control" name="mes" value="<?= htmlspecialchars($filtros['mes']) ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label">Tipo de Serviço</label>
                    <select class="form-select" name="tipo">
                        <option value="all" <?= $filtros['tipo']=='all'?'selected':'' ?>>Todos</option>
                        <option value="rotina" <?= $filtros['tipo']=='rotina'?'selected':'' ?>>Rotina</option>
                        <option value="exame" <?= $filtros['tipo']=='exame'?'selected':'' ?>>Exames</option>
                        <option value="emergencia" <?= $filtros['tipo']=='emergencia'?'selected':'' ?>>Emergência</option>
                    </select>
                </div>
                <button type="submit" class="btn filter-btn w-100 mt-2 mb-3">
                    <i class="fas fa-sync me-2"></i>Aplicar Filtros
                </button>
            </form>
        </div>
        <!-- Conteúdo Relatório -->
        <div class="col-md-9 pt-5 pb-5 px-4">
            <div class="report-header mb-4">
                <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between">
                    <div>
                        <h3 class="mb-1"><i class="fas fa-calendar-alt me-2"></i>
                            <?= nomeMes($mes) . " de $ano" ?>
                        </h3>
                        <div class="mb-0">Total de agendamentos: <strong><?= $total ?></strong></div>
                    </div>
                    <div>
                        <a class="btn btn-light" href="#">
                            <i class="fas fa-download me-2"></i>Exportar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="patient-card text-center mb-0">
                        <h5>Total de Transportes</h5>
                        <p class="display-6 mb-0"><?= $total ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="patient-card text-center mb-0">
                        <h5>Média Diária</h5>
                        <p class="display-6 mb-0"><?= $dailyAverage ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="patient-card text-center mb-0">
                        <h5>Taxa de Conclusão</h5>
                        <p class="display-6 mb-0"><?= $completionRate ?>%</p>
                    </div>
                </div>
            </div>

            <!-- Lista de Agendamentos -->
            <h5 class="mb-3"><i class="fas fa-list me-2"></i>Agendamentos deste Período</h5>
            <?php if ($agendamentos): ?>
                <div class="row">
                <?php foreach ($agendamentos as $a):
                    $dataFormatada = date("d/m/Y", strtotime($a['data_convertida']));
                    $horaFormatada = date("H:i", strtotime($a['horario']));
                    if ($a['situacao'] == 'Agendado') $statusClass = 'status-agendado';
                    elseif ($a['situacao'] == 'Concluído') $statusClass = 'status-concluido';
                    elseif ($a['situacao'] == 'Cancelado') $statusClass = 'status-cancelado';
                    elseif ($a['situacao'] == 'Pendente') $statusClass = 'status-pendente';
                    else $statusClass = '';
                ?>
                    <div class="col-md-6">
                        <div class="patient-card mb-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5><?= htmlspecialchars($a['nome']) ?></h5>
                                    <p class="mb-1"><i class="fas fa-calendar-day me-2"></i><?= "$dataFormatada - $horaFormatada" ?></p>
                                    <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($a['rua_origem'] ?? '') ?></p>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($a['tipo_transporte'] ?? '-') ?></span>
                                </div>
                                <span class="status-badge <?= $statusClass ?>"><?= $a['situacao'] ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info mt-4">Nenhum agendamento encontrado neste período com esses filtros.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
