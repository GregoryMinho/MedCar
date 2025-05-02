<?php
require '../includes/classe_usuario.php'; // inclui o arquivo de validação de login
use usuario\Usuario; // usa o namespace usuario\Usuario

Usuario::verificarPermissao('empresa'); // verifica se o usuário logado é uma empresa

require '../includes/conexao_BdFinanceiro.php'; 


// Funções para buscar dados
function buscarMetricas($conn) {
    $metricas = [];
    $sql = "SELECT tipo, valor FROM metricas";
    $stmt = $conn->query($sql);
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Padroniza as chaves removendo acentos e espaços
        $key = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $row['tipo']));
        $metricas[$key] = $row['valor'];
    }
    return $metricas;
}

function buscarTransacoes($conn) {
    $transacoes = [];
    $sql = "SELECT DATE_FORMAT(data, '%d/%m') as data, descricao, valor, status FROM transacoes ORDER BY data DESC";
    $stmt = $conn->query($sql);
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $transacoes[] = $row;
    }
    return $transacoes;
}

function buscarFaturamentoMensal($conn) {
    $faturamento = [];
    $sql = "SELECT mes, ano, faturamento FROM faturamento_mensal ORDER BY ano, mes";
    $stmt = $conn->query($sql);
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $faturamento[] = $row;
    }
    return $faturamento;
}

// Buscar dados
$metricas = buscarMetricas($conn);
$transacoes = buscarTransacoes($conn);
$faturamentoMensal = buscarFaturamentoMensal($conn);

// Fechar conexão
$conn = null;

// Preparar dados para o gráfico
$meses = ['', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dec'];
$labels = [];
$data = [];

foreach ($faturamentoMensal as $item) {
    $labels[] = $meses[$item['mes']] . ' ' . $item['ano'];
    $data[] = $item['faturamento'];
}

// Calcular variações
$mesAtual = end($faturamentoMensal);
$mesAnterior = prev($faturamentoMensal);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Dashboard Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style/style_relatorios_financeiros.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="menu_principal.php">
                <i class="fas fa-chart-line me-2"></i>
                MedCar Financeiro
            </a>
            <div class="d-flex align-items-center">
                <div class="text-white me-3">Relatório Mensal</div>
                <img src="https://source.unsplash.com/random/40x40/?finance" class="rounded-circle" alt="Perfil">
            </div>
        </div>
    </nav>

    <div class="finance-dashboard">
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-3 sidebar pt-5">
                    <div class="mb-4">
                        <h5><i class="fas fa-filter me-2"></i>Filtros</h5>
                        <input type="month" class="form-control" value="2024-03">
                    </div>
                    
                    <div class="mb-4">
                        <h6>Indicadores Chave</h6>
                        <div class="metric-badge mb-2">
                            <i class="fas fa-wallet me-2"></i>
                            faturamento: R$ <?= number_format($metricas['faturamento'], 0, ',', '.') ?>
                        </div>
                        <div class="metric-badge mb-2">
    <i class="fas fa-coins me-2"></i>
    Lucro: R$ <?= isset($metricas['lucroliquido']) ? number_format($metricas['lucroliquido'], 0, ',', '.') : '0' ?>
</div>
<div class="metric-badge">
    <i class="fas fa-percentage me-2"></i>
    Margem: <?= isset($metricas['faturamento'], $metricas['lucroliquido']) ? 
        number_format(($metricas['lucroliquido'] / $metricas['faturamento'] * 100), 1) : 
        '0.0' ?>%
</div>
                    </div>

                    <button class="btn btn-light w-100">
                        <i class="fas fa-download me-2"></i>Exportar Relatório
                    </button>
                </div>

                <!-- Main Content -->
                <div class="col-md-9 pt-5">
                    <div class="container">
                        <!-- Financial Overview -->
                        <div class="row mb-4">
                            <div class="col-lg-4">
                                <div class="financial-card text-center">
                                    <h5>Faturamento Total</h5>
                                    <div class="display-4 profit">
    R$ <?= isset($metricas['lucroliquido']) ? number_format($metricas['lucroliquido'] / 1000, 0, ',', '.') : '0' ?>K
</div>
                                    <small>
                                        <?php if ($mesAnterior): ?>
                                            <?= number_format(($mesAtual['faturamento'] - $mesAnterior['faturamento']) / $mesAnterior['faturamento'] * 100, 0) ?>%
                                            vs último mês
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                            </div>

                            <div class="revenue-chart mb-4">
                            <h5><i class="fas fa-chart-bar me-2"></i>Histórico de Faturamento</h5>
                            <canvas id="revenueChart"></canvas>
                        </div>

                        <!-- Transações Recentes -->
                        <div class="transaction-table">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Data</th>
                                        <th>Descrição</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transacoes as $transacao): ?>
                                    <tr>
                                        <td><?= $transacao['data'] ?></td>
                                        <td><?= $transacao['descricao'] ?></td>
                                        <td class="<?= $transacao['valor'] > 0 ? 'profit' : 'loss' ?>">
                                            R$ <?= number_format($transacao['valor'], 2, ',', '.') ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $transacao['status'] === 'Pago' ? 'success' : 'warning' ?>">
                                                <?= $transacao['status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>


                        <!-- Métricas Rápidas -->
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="financial-card text-center">
                                    <h6>Ticket Médio</h6>
                                    <div class="h4 profit">
                                        R$ <?= number_format($metricas['ticketmedio'], 2, ',', '.') ?>
                                    </div>
                                </div>
                            </div>
                            <!-- Repita para outras métricas -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Configuração do gráfico com dados dinâmicos
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Faturamento Mensal (R$)',
                    data: <?= json_encode($data) ?>,
                    borderColor: '#1a365d',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    </script>
</body>
</html>