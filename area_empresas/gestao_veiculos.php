<?php
require '../includes/conexao_BdMotoristas.php'; // Make sure this returns a PDO connection as $pdo

// Function to list drivers
function listarMotoristas($pdo) {
    $motoristas = [];
    $sql = "SELECT id, nome FROM Motoristas";
    $stmt = $pdo->query($sql);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $motoristas[] = $row;
    }
    return $motoristas;
}

// Determine action
$acao = $_GET['acao'] ?? $_POST['acao'] ?? 'listar';

// Handle actions
if ($acao === 'salvar') {
    $id = $_POST['id'] ?? '';
    $dados = [
        'motorista_id' => $_POST['motorista_id'],
        'placa' => $_POST['placa'],
        'modelo' => $_POST['modelo'],
        'tipo' => $_POST['tipo'],
        'status' => $_POST['status'],
        'ultima' => $_POST['ultima_manutencao'],
        'proxima' => $_POST['proxima_manutencao']
    ];

    if ($id) {
        $dados['id'] = $id;
        $sql = "UPDATE Veiculos SET 
                    motorista_id=:motorista_id,
                    placa=:placa,
                    modelo=:modelo,
                    tipo=:tipo,
                    status=:status,
                    ultima_manutencao=:ultima,
                    proxima_manutencao=:proxima
                WHERE id=:id";
    } else {
        $sql = "INSERT INTO Veiculos (motorista_id, placa, modelo, tipo, status, ultima_manutencao, proxima_manutencao)
                VALUES (:motorista_id, :placa, :modelo, :tipo, :status, :ultima, :proxima)";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($dados);
    header('Location: ?acao=listar');
    exit;
}

if ($acao === 'excluir') {
    $id = $_GET['id'] ?? '';
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM Veiculos WHERE id = ?");
        $stmt->execute([$id]);
    }
    header('Location: ?acao=listar');
    exit;
}

// Get data for form
if ($acao === 'form') {
    $id = $_GET['id'] ?? '';
    $veiculo = [
        'id' => '',
        'motorista_id' => '',
        'placa' => '',
        'modelo' => '',
        'tipo' => '',
        'status' => '',
        'ultima_manutencao' => '',
        'proxima_manutencao' => ''
    ];
    
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM Veiculos WHERE id = ?");
        $stmt->execute([$id]);
        $veiculo = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    $motoristas = listarMotoristas($pdo);
}

// List vehicles
if ($acao === 'listar') {
    $stmt = $pdo->query("SELECT V.*, M.nome AS motorista_nome 
                        FROM Veiculos V 
                        LEFT JOIN Motoristas M ON V.motorista_id = M.id");
    $veiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Gestão de Frota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2a4f7e;
            --accent-color: #38b2ac;
            --navbar-height: 56px;
        }

        .vehicles-page {
            background: #f8f9fa;
            padding-top: var(--navbar-height);
            min-height: 100vh;
        }

        .vehicles-sidebar {
            background: var(--primary-color);
            color: white;
            min-height: calc(100vh - var(--navbar-height));
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: var(--navbar-height);
        }

        .vehicle-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9em;
        }

        .status-disponivel {
            background: #28a745;
            color: white;
        }

        .status-em_uso {
            background: #ffc107;
            color: black;
        }

        .status-manutencao {
            background: #dc3545;
            color: white;
        }

        .maintenance-history {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: var(--primary-color);">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-truck me-2"></i>
                MedCar Frota
            </a>
            <div class="d-flex align-items-center">
                <div class="text-white me-3">Gestão de Veículos</div>
                <img src="https://source.unsplash.com/random/40x40/?icon" 
                     class="rounded-circle" 
                     alt="Foto do perfil do usuário"
                     width="40"
                     height="40">
            </div>
        </div>
    </nav>

    <!-- Vehicle Management Page -->
    <div class="vehicles-page">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 vehicles-sidebar">
                <h5 class="mb-4"><i class="bi bi-menu-button me-2"></i>Menu</h5>
                <div class="d-flex flex-column gap-3">
                    <a href="dashboard.php" class="btn btn-outline-light text-start">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a href="gestao_veiculos.php" class="btn btn-light text-start">
                        <i class="bi bi-truck me-2"></i> Frota
                    </a>
                    <a href="gestao_motoristas.php" class="btn btn-outline-light text-start">
                        <i class="bi bi-people me-2"></i> Motoristas
                    </a>
                    <a href="agendamentos_pacientes.php" class="btn btn-outline-light text-start">
                        <i class="bi bi-calendar-event me-2"></i> Agendamentos
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 pt-4">
                <div class="container">
                    <?php if ($acao === 'form'): ?>
                        <!-- Edit/Create Form -->
                        <div class="vehicle-card p-4 mb-4">
                            <h2 class="mb-4"><i class="bi bi-truck me-2"></i><?= $veiculo['id'] ? 'Editar' : 'Novo' ?> Veículo</h2>
                            <form method="post" action="?acao=salvar">
                                <input type="hidden" name="id" value="<?= $veiculo['id'] ?>">
                                <input type="hidden" name="acao" value="salvar">
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Motorista:</label>
                                        <select name="motorista_id" class="form-select" required>
                                            <option value="">Selecione</option>
                                            <?php foreach ($motoristas as $m): ?>
                                                <option value="<?= $m['id'] ?>" <?= $m['id'] == $veiculo['motorista_id'] ? 'selected' : '' ?>>
                                                    <?= $m['nome'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">Placa:</label>
                                        <input type="text" name="placa" class="form-control" value="<?= $veiculo['placa'] ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">Modelo:</label>
                                        <input type="text" name="modelo" class="form-control" value="<?= $veiculo['modelo'] ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">Tipo:</label>
                                        <input type="text" name="tipo" class="form-control" value="<?= $veiculo['tipo'] ?>">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">Status:</label>
                                        <select name="status" class="form-select">
                                            <option value="disponivel" <?= $veiculo['status'] == 'disponivel' ? 'selected' : '' ?>>Disponível</option>
                                            <option value="em_uso" <?= $veiculo['status'] == 'em_uso' ? 'selected' : '' ?>>Em Uso</option>
                                            <option value="manutencao" <?= $veiculo['status'] == 'manutencao' ? 'selected' : '' ?>>Manutenção</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">Última Manutenção:</label>
                                        <input type="date" name="ultima_manutencao" class="form-control" value="<?= $veiculo['ultima_manutencao'] ?>">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">Próxima Manutenção:</label>
                                        <input type="date" name="proxima_manutencao" class="form-control" value="<?= $veiculo['proxima_manutencao'] ?>">
                                    </div>
                                    
                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="bi bi-save me-2"></i>Salvar
                                        </button>
                                        <a href="?acao=listar" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left me-2"></i>Voltar
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <!-- Vehicle Listing -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3><i class="bi bi-truck me-2"></i>Gestão de Veículos</h3>
                            <a href="?acao=form" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-2"></i>Adicionar Veículo
                            </a>
                        </div>

                        <div class="vehicle-card p-4">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Placa</th>
                                            <th>Modelo</th>
                                            <th>Tipo</th>
                                            <th>Motorista</th>
                                            <th>Status</th>
                                            <th>Últ. Manutenção</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($veiculos as $v): ?>
                                            <tr>
                                                <td><?= $v['placa'] ?></td>
                                                <td><?= $v['modelo'] ?></td>
                                                <td><?= $v['tipo'] ?></td>
                                                <td><?= $v['motorista_nome'] ?? 'Nenhum' ?></td>
                                                <td>
                                                    <span class="status-badge status-<?= $v['status'] ?>">
                                                        <?= ucfirst(str_replace('_', ' ', $v['status'])) ?>
                                                    </span>
                                                </td>
                                                <td><?= $v['ultima_manutencao'] ? date('d/m/Y', strtotime($v['ultima_manutencao'])) : 'N/A' ?></td>
                                                <td>
                                                    <a href="?acao=form&id=<?= $v['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="?acao=excluir&id=<?= $v['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja excluir este veículo?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>