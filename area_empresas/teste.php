<?php
require '../includes/conexao_BdAgendamento.php';

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Recupera o mês selecionado via GET
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

// Query para buscar apenas serviços concluídos
$sql = "SELECT * FROM pacientes_registros 
        WHERE DATE_FORMAT(data_consulta, '%Y-%m') = ?
        AND status = 'concluido'
        ORDER BY data_consulta DESC, horario DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro na preparação da query: " . $conn->error);
}

$stmt->bind_param("s", $selectedMonth);

if (!$stmt->execute()) {
    die("Erro na execução da query: " . $stmt->error);
}

$result = $stmt->get_result();

$patients = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}

$totalPatients = count($patients);
$daysInMonth = date('t', strtotime($selectedMonth));
$dailyAverage = $totalPatients > 0 ? number_format($totalPatients / $daysInMonth, 1) : 0;

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Pacientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { margin-top: 50px; }
        table { margin-top: 20px; }
        .stats-card { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Cabeçalho e Filtro -->
        <div class="stats-card">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4><?= date('F Y', strtotime($selectedMonth)) ?></h4>
                </div>
                <div class="col-md-6">
                    <form class="d-flex gap-2">
                        <input type="month" 
                            class="form-control" 
                            name="month" 
                            value="<?= $selectedMonth ?>"
                            onchange="this.form.submit()">
                    </form>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <h6>Total Concluído</h6>
                    <div class="display-6"><?= $totalPatients ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <h6>Média Diária</h6>
                    <div class="display-6"><?= $dailyAverage ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <h6>Dias no Mês</h6>
                    <div class="display-6"><?= $daysInMonth ?></div>
                </div>
            </div>
        </div>

        <!-- Tabela de Resultados -->
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Nome</th>
                    <th>Data Consulta</th>
                    <th>Horário</th>
                    <th>Destino</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                <tr>
                    <td><?= htmlspecialchars($patient['nome']) ?></td>
                    <td><?= date('d/m/Y', strtotime($patient['data_consulta'])) ?></td>
                    <td><?= date('H:i', strtotime($patient['horario'])) ?></td>
                    <td><?= htmlspecialchars($patient['destino']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($patients)): ?>
        <div class="alert alert-info">
            Nenhum registro encontrado para o período selecionado
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>