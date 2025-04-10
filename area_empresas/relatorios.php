<?php
require '../includes/conexao_BdAgendamento.php';
require '../includes/classe_usuario.php'; // inclui o arquivo de validação de login
use usuario\Usuario; // usa o namespace usuario\Usuario

// Usuario::verificarPermissao('empresa'); // verifica se o usuário logado é uma empresa

// Recupera o mês selecionado via GET
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : '2024-03';

// Query com filtro por mês usando consulta preparada
$sql = "SELECT * FROM agendamentos WHERE DATE_FORMAT(data_consulta, '%Y-%m') = :selectedMonth ORDER BY data_consulta DESC, horario DESC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':selectedMonth', $selectedMonth, PDO::PARAM_STR);
$stmt->execute();
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPatients = count($patients);

$completedCount = 0;
foreach ($patients as $patient) {
    if ($patient['status'] == 'Concluído') {
        $completedCount++;
    }
}
$completionRate = ($totalPatients > 0) ? round(($completedCount / $totalPatients) * 100) : 0;

$dailyAverage = ($totalPatients / 30);
$dailyAverage = number_format($dailyAverage, 1);

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Relatórios de Pacientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2a4f7e;
            --accent-color: #38b2ac;
        }

        .reports-page {
            background: #f8f9fa;
            min-height: 100vh;
        }

        .reports-sidebar {
            background: var(--primary-color);
            color: white;
            min-height: 100vh;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .patient-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            margin-bottom: 20px;
        }

        .patient-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .month-selector {
            background: var(--secondary-color);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9em;
        }

        .status-agendado {
            background: #ffc107;
            color: black;
        }

        .status-concluido {
            background: #28a745;
            color: white;
        }

        .status-cancelado {
            background: #dc3545;
            color: white;
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
            color: white;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: var(--primary-color);">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-chart-line me-2"></i>
                MedCar Relatórios
            </a>
            <div class="d-flex align-items-center">
                <div class="text-white me-3">Relatórios Mensais</div>
                <img src="https://source.unsplash.com/random/40x40/?icon" class="rounded-circle" alt="Perfil">
            </div>
        </div>
    </nav>

    <!-- Reports Page -->
    <div class="reports-page">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 reports-sidebar pt-5">
                <h5 class="mb-4"><i class="fas fa-filter me-2"></i>Filtros</h5>
                <form method="GET">
                    <div class="mb-4">
                        <label class="form-label">Selecione o Mês</label>
                        <select class="form-select" id="monthSelect" name="month">
                            <option value="2024-01" <?= $selectedMonth == '2024-01' ? 'selected' : '' ?>>Janeiro 2024</option>
                            <option value="2024-02" <?= $selectedMonth == '2024-02' ? 'selected' : '' ?>>Fevereiro 2024</option>
                            <option value="2024-03" <?= $selectedMonth == '2024-03' ? 'selected' : '' ?>>Março 2024</option>
                        </select>
                    </div>

                    <h6 class="mt-4 mb-3">Status do Transporte</h6>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Agendado" name="status[]" id="status1" checked>
                        <label class="form-check-label" for="status1">Agendados</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Concluído" name="status[]" id="status2" checked>
                        <label class="form-check-label" for="status2">Concluídos</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Cancelado" name="status[]" id="status3">
                        <label class="form-check-label" for="status3">Cancelados</label>
                    </div>

                    <button type="submit" class="btn filter-btn w-100 mt-4">
                        <i class="fas fa-sync me-2"></i>Aplicar Filtros
                    </button>
                </form>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 pt-5">
                <div class="container">
                    <!-- Header -->
                    <div class="month-selector">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3><i class="fas fa-calendar-alt me-2"></i>
                                    <?php
                                    $dataObj = DateTime::createFromFormat('Y-m', $selectedMonth);
                                    echo $dataObj ? $dataObj->format('F Y') : $selectedMonth;
                                    ?>
                                </h3>
                                <p class="mb-0">Total de pacientes: <?= $totalPatients ?></p>
                            </div>
                            <div>
                                <button class="btn btn-light">
                                    <i class="fas fa-download me-2"></i>Exportar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Patient List -->
                    <div class="row" id="patientList">
                        <?php foreach ($patients as $patient) :
                            $dataFormatada = date("d/m/Y", strtotime($patient['data_consulta']));
                            $horarioFormatado = date("H:i", strtotime($patient['horario']));
                            if ($patient['status'] == 'Agendado') {
                                $statusClass = 'status-agendado';
                            } elseif ($patient['status'] == 'Concluído') {
                                $statusClass = 'status-concluido';
                            } elseif ($patient['status'] == 'Cancelado') {
                                $statusClass = 'status-cancelado';
                            } else {
                                $statusClass = '';
                            }
                        ?>
                            <div class="col-md-6">
                                <!-- Patient Card -->
                                <div class="patient-card p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5><?= htmlspecialchars($patient['nome']) ?></h5>
                                            <p class="mb-1"><i class="fas fa-calendar-day me-2"></i><?= "$dataFormatada - $horarioFormatado" ?></p>
                                            <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($patient['destino']) ?></p>
                                        </div>
                                        <span class="status-badge <?= $statusClass ?>"><?= $patient['status'] ?></span>
                                    </div>
                                    <hr>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailsModal-<?= $patient['id'] ?>">
                                        <i class="fas fa-file-alt me-2"></i>Detalhes
                                    </button>
                                </div>

                                <!-- Modal - Agora posicionado fora do card mas na mesma coluna -->
                                <div class="modal fade" id="detailsModal-<?= $patient['id'] ?>" tabindex="-1" aria-labelledby="detailsModalLabel-<?= $patient['id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title" id="detailsModalLabel-<?= $patient['id'] ?>">
                                                    <i class="fas fa-info-circle me-2"></i>Opções de Detalhes
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                            </div>
                                            <div class="modal-body p-0">
                                                <div class="list-group list-group-flush">
                                                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                                        <i class="fas fa-calendar-check me-3 fa-fw text-primary"></i>
                                                        Detalhes da Consulta
                                                    </a>
                                                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                                        <i class="fas fa-user-injured me-3 fa-fw text-success"></i>
                                                        Tabela de Pacientes
                                                    </a>
                                                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                                        <i class="fas fa-truck-moving me-3 fa-fw text-warning"></i>
                                                        Tabela de Empresas de Transporte
                                                    </a>
                                                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                                        <i class="fas fa-calendar-alt me-3 fa-fw text-info"></i>
                                                        Tabela de Reservas/Agendamentos
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Estatísticas -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="patient-card p-3 text-center">
                                <h5>Total de Transportes</h5>
                                <p class="display-6 mb-0"><?= $totalPatients ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="patient-card p-3 text-center">
                                <h5>Média Diária</h5>
                                <p class="display-6 mb-0"><?= $dailyAverage ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="patient-card p-3 text-center">
                                <h5>Taxa de Conclusão</h5>
                                <p class="display-6 mb-0"><?= $completionRate ?>%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>