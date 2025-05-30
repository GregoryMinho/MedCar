<?php
require '../includes/classe_usuario.php'; 
use usuario\Usuario;
// Usuario::verificarPermissao('empresa'); // Verifica se o usuário logado é do tipo 'empresa'

require '../includes/conexao_BdAgendamento.php';

// Consultas para os dados do dashboard
$queries = [
    'total_transportes' => "SELECT COUNT(*) AS total FROM agendamentos WHERE situacao = 'Concluido'",
    'taxa_conclusao' => "SELECT 
                        (SUM(CASE WHEN situacao = 'Concluido' THEN 1 ELSE 0 END) / COUNT(*)) * 100 AS taxa
                        FROM agendamentos",
    'pendencias' => "SELECT COUNT(*) AS total FROM agendamentos WHERE situacao = 'Pendente'", // Corrigido para valor válido
    'veiculos_ativos' => "SELECT COUNT(*) AS total FROM Motoristas_MedCar.Veiculos WHERE status IN ('disponivel', 'em_uso')", // Banco especificado
    'status_motoristas' => "SELECT status, COUNT(*) AS total FROM Motoristas_MedCar.Motoristas GROUP BY status", // Banco especificado
    'atividades_recentes' => "SELECT * FROM agendamentos ORDER BY agendado_em DESC LIMIT 5",

        'desempenho_mensal' => "SELECT 
                                MONTHNAME(data_resumo) AS mes,
                                SUM(quantidade_servicos) AS total
                                FROM dashboard_medcar.resumo_diario  
                                GROUP BY MONTH(data_resumo)
                                ORDER BY MONTH(data_resumo) DESC LIMIT 6"
    ];


// Executar consultas e armazenar resultados
$results = [];
foreach ($queries as $key => $sql) {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    if ($key === 'status_motoristas') {
        $results[$key] = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[$key][$row['status']] = $row['total'];
        }
    } elseif ($key === 'desempenho_mensal' || $key === 'atividades_recentes') {
        $results[$key] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $results[$key] = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Dashboard Empresarial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style/style_dashboard.css">
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="text-center mb-5">
                <img src="https://source.unsplash.com/random/100x100/?logo" class="rounded-circle mb-3" alt="Logo">
                <h4>Transportadora MedCar</h4>
            </div>

            <nav class="nav flex-column">
                <a class="nav-link active" href="#">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a class="nav-link" href="#">
                    <i class="fas fa-users me-2"></i> Motoristas
                </a>
                <a class="nav-link" href="#">
                    <i class="fas fa-calendar-check me-2"></i> Agendamentos
                </a>
                <a class="nav-link" href="#">
                    <i class="fas fa-file-invoice-dollar me-2"></i> Financeiro
                </a>
                <a class="nav-link" href="#">
                    <i class="fas fa-cog me-2"></i> Configurações
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2><i class="fas fa-tachometer-alt me-2"></i> Dashboard Empresarial</h2>
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <small class="text-muted">Última atualização:</small><br>
                        <span class="text-primary"><?= date('d/m/Y H:i') ?></span>
                    </div>
                    <img src="https://source.unsplash.com/random/40x40/?user" class="rounded-circle" alt="Perfil">
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle p-3 me-3">
                                <i class="fas fa-user-injured fa-2x"></i>
                            </div>
                            <div>
                                <h3 class="mb-0"><?= $results['total_transportes']['total'] ?? 0 ?></h3>
                                <small class="text-muted">Transportes Realizados</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="bg-success text-white rounded-circle p-3 me-3">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                            <div>
                                <h3 class="mb-0"><?= number_format($results['taxa_conclusao']['taxa'] ?? 0, 0) ?>%</h3>
                                <small class="text-muted">Taxa de Conclusão</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning text-white rounded-circle p-3 me-3">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                            <div>
                                <h3 class="mb-0"><?= $results['pendencias']['total'] ?? 0 ?></h3>
                                <small class="text-muted">Pendentes</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="bg-info text-white rounded-circle p-3 me-3">
                                <i class="fas fa-truck fa-2x"></i>
                            </div>
                            <div>
                                <h3 class="mb-0"><?= $results['veiculos_ativos']['total'] ?? 0 ?></h3>
                                <small class="text-muted">Veículos Ativos</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos e Seções -->
            <div class="row mt-4 g-4">
                <div class="col-lg-8">
                    <div class="chart-container">
                        <h4 class="mb-4"><i class="fas fa-chart-line me-2"></i> Desempenho Mensal</h4>
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="driver-status">
                        <h4 class="mb-4"><i class="fas fa-user-clock me-2"></i> Status dos Motoristas</h4>
                        <div class="list-group">
                            <div class="list-group-item d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-circle text-success"></i>
                                </div>
                                <div>
                                    <strong>Disponíveis</strong>
                                    <div class="text-muted"><?= $results['status_motoristas']['Disponível'] ?? 0 ?> motoristas</div>
                                </div>
                            </div>
                            <div class="list-group-item d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-circle text-warning"></i>
                                </div>
                                <div>
                                    <strong>Em transporte</strong>
                                    <div class="text-muted"><?= $results['status_motoristas']['Em serviço'] ?? 0 ?> motoristas</div>
                                </div>
                            </div>
                            <div class="list-group-item d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-circle text-danger"></i>
                                </div>
                                <div>
                                    <strong>Indisponíveis</strong>
                                    <div class="text-muted"><?= $results['status_motoristas']['Indisponível'] ?? 0 ?> motoristas</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Atividades Recentes -->
            <div class="recent-activity mt-4">
                <h4 class="mb-4"><i class="fas fa-history me-2"></i> Atividades Recentes</h4>
                <div class="list-group">
                    <?php foreach ($results['atividades_recentes'] as $atividade): ?>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-ambulance me-2 text-primary"></i>
                                    <?= htmlspecialchars($atividade['nome_paciente']) ?> - <?= htmlspecialchars($atividade['hospital']) ?>
                                </div>
                                <small class="text-muted">
                                    <?= date('d/m H:i', strtotime($atividade['data_hora_agendamento'])) ?>
                                </small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gráfico de Desempenho com dados do banco
        const ctx = document.getElementById('performanceChart').getContext('2d');

        // Preparar dados do gráfico
        const meses = <?= json_encode(array_column($results['desempenho_mensal'] ?? [], 'mes')) ?>;
        const transportes = <?= json_encode(array_column($results['desempenho_mensal'] ?? [], 'total')) ?>;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Transportes Concluídos',
                    data: transportes,
                    backgroundColor: 'rgba(26,54,93,0.8)',
                    borderColor: '#1a365d',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ` ${context.dataset.label}: ${context.raw} transportes`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Quantidade de Transportes'
                        }
                    }
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>