<?php 
$host = 'localhost';
$dbname = 'Motoristas_MedCar';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Não foi possível conectar ao banco de dados: " . $e->getMessage());
}
$sql = "SELECT m.*, v.placa, v.modelo 
        FROM Motoristas m
        LEFT JOIN Veiculos v ON m.id = v.motorista_id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$motoristas = $stmt->fetchAll(PDO::FETCH_ASSOC); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedQ - Gestão de Motoristas</title>
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

        .driver-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
            margin-bottom: 20px;
            padding: 20px;
        }

        .driver-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9em;
        }

        .status-active { background: #28a745; color: white; }
        .status-inactive { background: #dc3545; color: white; }
        .status-on-duty { background: #ffc107; color: black; }

        .btn-driver {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-driver:hover {
            background: #2c7a7b;
            color: white;
        }

        .navbar {
            background: var(--primary-color) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .sidebar {
            background: var(--secondary-color);
            color: white;
            min-height: 100vh;
            padding: 20px;
        }

        .driver-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-users-cog me-2"></i>
                MedQ - Gestão de Motoristas
            </a>
            <div class="d-flex align-items-center">
                <div class="text-white me-3">Bem-vindo, Admin</div>
                <img src="https://source.unsplash.com/random/40x40/?icon" class="rounded-circle" alt="Perfil">
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 sidebar pt-5">
                <h5 class="mb-4"><i class="fas fa-filter me-2"></i>Filtros</h5>
                <div class="mb-4">
                    <label class="form-label">Status</label>
                    <select class="form-select">
                        <option value="all">Todos</option>
                        <option value="active">Ativos</option>
                        <option value="inactive">Inativos</option>
                        <option value="on-duty">Em Serviço</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Localização</label>
                    <select class="form-select">
                        <option value="all">Todas</option>
                        <option value="sp">São Paulo</option>
                        <option value="rj">Rio de Janeiro</option>
                        <option value="mg">Minas Gerais</option>
                    </select>
                </div>

                <button class="btn btn-light w-100 mb-3">
                    <i class="fas fa-sync me-2"></i>Aplicar Filtros
                </button>

                <button class="btn btn-driver w-100">
                    <i class="fas fa-plus me-2"></i>Adicionar Motorista
                </button>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 pt-5">
                <div class="container">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3><i class="fas fa-users me-2"></i>Motoristas Cadastrados</h3>
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control" placeholder="Pesquisar motorista...">
                            <button class="btn btn-driver">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Driver List -->
                    <div class="row">
                        <?php foreach ($motoristas as $motorista): ?>
                        <div class="col-md-6 mb-4">
                            <div class="driver-card">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="<?= $motorista['foto_url'] ?>" class="driver-avatar me-3" alt="Motorista">
                                    <div>
                                        <h5><?= htmlspecialchars($motorista['nome']) ?></h5>
                                        <p class="mb-0">CNH: <?= htmlspecialchars($motorista['cnh']) ?></p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <p class="mb-1">
                                            <i class="fas fa-map-marker-alt me-2"></i>
                                            <?= htmlspecialchars($motorista['cidade']) ?>, <?= $motorista['estado'] ?>
                                        </p>
                                        <p class="mb-0">
                                            <i class="fas fa-car me-2"></i>
                                            <?= htmlspecialchars($motorista['placa']) ?>
                                        </p>
                                    </div>
                                    <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $motorista['status'])) ?>">
                                        <?= $motorista['status'] ?>
                                    </span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="crud_motoristas/editar_motorista.php?id=<?= $motorista['id'] ?>" class="btn btn-driver btn-sm w-100">
                                        <i class="fas fa-edit me-2"></i>Editar
                                    </a>
                                    <button class="btn btn-danger btn-sm w-100" 
                                            onclick="confirmarExclusao(<?= $motorista['id'] ?>)">
                                        <i class="fas fa-trash me-2"></i>Remover
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function confirmarExclusao(id) {
        if (confirm('Tem certeza que deseja excluir este motorista?')) {
            window.location.href = 'crud_motoristas/excluir_motorista.php?id=' + id;
        }
    }
    </script>
</body>
</html>