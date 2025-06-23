<?php
// avaliacoes.php
require '../includes/conexao_BdAvaliacoes.php';

// Query para recuperar avaliações com dados dos pacientes e motoristas
$sql = "SELECT a.avaliacao_id, a.nota, a.comentario, a.lida, a.data_avaliacao,
               p.nome AS paciente_nome,
               m.nome AS motorista_nome, m.foto_perfil
        FROM avaliacoes a
        JOIN pacientes p ON a.paciente_id = p.paciente_id
        JOIN motoristas m ON a.motorista_id = m.motorista_id
        ORDER BY a.data_avaliacao DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$conn = null; // Fecha a conexão com o banco de dados
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliações de Motoristas - MedCar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --medq-primary: #1a365d;
            --medq-accent: #38b2ac;
            --medq-light: #f8f9fa;
            --medq-gray: #6c757d;
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar-menu {
            background: var(--medq-primary);
            color: white;
            min-height: 100vh;
            padding-top: 48px;
        }
        
        .sidebar-menu nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.85);
            border-radius: 8px;
            margin-bottom: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .sidebar-menu nav a:hover, 
        .sidebar-menu nav a.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar-menu nav a i {
            width: 20px;
            text-align: center;
        }
        
        .sidebar-menu .sidebar-title {
            font-size: 1.15rem;
            font-weight: bold;
            margin-bottom: 24px;
            padding-left: 20px;
            color: white;
        }
        
        .btn-voltar {
            background: #e5e7eb !important;
            color: #374151 !important;
            font-weight: 500;
            border-radius: 6px;
            margin: 28px 0 20px 0;
            transition: all 0.2s;
        }
        
        .btn-voltar:hover {
            background: #d1d5db !important;
        }
        
        .btn-voltar i {
            margin-right: 6px;
        }
        
        .text-medq-primary {
            color: var(--medq-primary);
        }
        
        .text-medq-accent {
            color: var(--medq-accent);
        }
        
        .btn-medq-accent {
            background-color: var(--medq-accent);
            border-color: var(--medq-accent);
            color: white;
        }
        
        .btn-medq-accent:hover {
            background-color: #2d9a94;
            border-color: #2d9a94;
        }
        
        .btn-outline-medq-accent {
            border-color: var(--medq-accent);
            color: var(--medq-accent);
        }
        
        .btn-outline-medq-accent:hover {
            background-color: var(--medq-accent);
            color: white;
        }
        
        .stat-card {
            border-radius: 12px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .evaluation-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: all 0.3s;
            height: 100%;
            opacity: 0;
            transform: translateY(20px);
        }
        
        .evaluation-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }
        
        .driver-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        
        .rating-stars {
            color: #f8d150;
        }
        
        .badge-status {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        
        .comment-bubble {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 16px;
            position: relative;
        }
        
        .comment-bubble::before {
            content: '';
            position: absolute;
            top: -10px;
            left: 20px;
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 10px solid #f1f5f9;
        }
        
        .scroll-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--medq-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .scroll-to-top.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .scroll-to-top:hover {
            background: #142849;
        }
        
        footer {
            background: var(--medq-primary);
            color: white;
        }
        
        @media (max-width: 991.98px) {
            .sidebar-menu {
                min-height: unset;
                padding-top: 0;
            }
            
            .sidebar-menu .sidebar-title {
                margin-bottom: 12px;
                padding: 10px 20px;
            }
            
            nav {
                display: flex;
                flex-wrap: wrap;
                padding: 10px;
            }
            
            nav a {
                flex: 1 0 auto;
                margin: 4px;
                padding: 8px 12px;
                font-size: 0.85rem;
            }
            
            nav a i {
                display: none;
            }
        }
        
        @media (max-width: 767.98px) {
            .stat-card .display-4 {
                font-size: 2.5rem;
            }
            
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row min-vh-100">
            <!-- MENU LATERAL INÍCIO -->
            <aside class="col-lg-2 col-md-3 sidebar-menu d-flex flex-column">
                <div class="sidebar-title mt-4 mb-3">Menu</div>
                <nav class="flex-grow-1">
                    <a href="dashboard.php"><i class="fa fa-chart-line"></i>Estatísticas</a>
                    <a href="agendamentos_pacientes.php"><i class="fa fa-calendar-alt"></i>Agendamentos</a>
                    <a href="aprovar_agendamentos.php"><i class="fa fa-clipboard-check"></i>Aprovar Agendamentos</a>
                    <a href="gestao_motoristas.php"><i class="fa fa-users"></i>Motoristas</a>
                    <a href="gestao_veiculos.php"><i class="fa fa-truck"></i>Frota</a>
                    <a href="relatorios_financeiros.php"><i class="fa fa-coins"></i>Financeiro</a>
                    <a href="relatorios.php"><i class="fa fa-file-alt"></i>Relatórios</a>
                    <a href="avaliacoes.php" class="active"><i class="fa fa-star"></i>Avaliações</a>
                    <a href="batepapo_clientes.php"><i class="fa fa-comments"></i>Bate-Papo com Clientes</a>
                    <a href="perfil_empresa.php"><i class="fa fa-user"></i>Minha Conta</a>
                </nav>
            </aside>
            <!-- MENU LATERAL FIM -->

            <!-- Conteúdo Principal -->
            <main class="col-lg-10 col-md-9 p-4">
                <!-- BOTÃO DE VOLTAR -->
                <a href="menu_principal.php" class="btn btn-light btn-voltar mb-3">
                    <i class="fa fa-arrow-left"></i>Voltar
                </a>
                <!-- TÍTULO E FILTRO -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-medq-primary">Avaliações dos Pacientes</h2>
                    <div class="d-flex gap-2">
                        <button class="btn btn-medq-accent">
                            <i class="fas fa-filter me-2"></i>Filtrar
                        </button>
                    </div>
                </div>
                <!-- Estatísticas -->
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h5 class="card-title text-muted mb-2">Média Geral</h5>
                                        <div class="display-4 fw-bold text-medq-primary">4.8</div>
                                    </div>
                                    <div class="rating-stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm stat-card">
                            <div class="card-body">
                                <h5 class="card-title text-muted mb-2">Total de Avaliações</h5>
                                <div class="display-4 fw-bold text-medq-primary">
                                    <?= htmlspecialchars(count($result)) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Lista de Avaliações -->
                <div class="row g-4">
                    <?php if (count($result) > 0): ?>
                        <?php foreach ($result as $row): ?>
                            <div class="col-lg-6">
                                <div class="evaluation-card">
                                    <div class="card-body">
                                        <!-- Cabeçalho -->
                                        <div class="d-flex align-items-start gap-3 mb-4">
                                            <div class="position-relative">
                                                <img src="<?= htmlspecialchars($row['foto_perfil'] ?? 'default_motorista.jpg') ?>"
                                                    class="driver-avatar"
                                                    alt="<?= htmlspecialchars($row['motorista_nome']) ?>">
                                                <span class="position-absolute bottom-0 end-0 translate-middle p-1 bg-<?= $row['lida'] ? 'success' : 'warning' ?> 
                                                    border border-2 border-white rounded-circle"></span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1 fw-bold"><?= htmlspecialchars($row['motorista_nome']) ?></h5>
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <div class="rating-stars">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <i class="<?= $i <= $row['nota'] ? 'fas' : 'far' ?> fa-star"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <span class="badge bg-<?= $row['lida'] ? 'success' : 'warning' ?> badge-status">
                                                        <?= $row['lida'] ? 'Concluída' : 'Pendente' ?>
                                                    </span>
                                                </div>
                                                <div class="text-muted small">
                                                    <i class="fas fa-calendar-day me-2"></i>
                                                    <?= date("d/m/Y H:i", strtotime($row['data_avaliacao'])) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Detalhes da Avaliação -->
                                        <div class="comment-bubble mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-user-injured me-2 text-medq-accent"></i>
                                                <strong><?= htmlspecialchars($row['paciente_nome']) ?></strong>
                                            </div>
                                            <p class="mb-0 text-dark"><?= nl2br(htmlspecialchars($row['comentario'])) ?></p>
                                        </div>
                                        <!-- Ações -->
                                        <div class="d-flex gap-2 mt-3">
                                            <button class="btn btn-outline-medq-accent btn-sm flex-grow-1">
                                                <i class="fas fa-reply me-2"></i>Responder
                                            </button>
                                            <button class="btn btn-medq-accent btn-sm">
                                                <i class="fas fa-<?= $row['lida'] ? 'check' : 'envelope' ?> me-2"></i>
                                                <?= $row['lida'] ? 'Concluído' : 'Marcar' ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center py-4">
                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                <h5>Nenhuma avaliação encontrada</h5>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-medq-primary text-white mt-5">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 small">
                        © 2023 MedCar Transportes. Todos os direitos reservados.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top -->
    <div class="scroll-to-top">
        <i class="fas fa-chevron-up"></i>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Scroll to Top
        window.addEventListener('scroll', () => {
            const scrollButton = document.querySelector('.scroll-to-top');
            if (window.scrollY > 300) {
                scrollButton.classList.add('show');
            } else {
                scrollButton.classList.remove('show');
            }
        });

        document.querySelector('.scroll-to-top').addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Animações das Cards
        const animateCards = () => {
            const cards = document.querySelectorAll('.evaluation-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.transform = 'translateY(0)';
                    card.style.opacity = '1';
                }, index * 100);
            });
        }
        
        // Inicia animações quando o documento carregar
        document.addEventListener('DOMContentLoaded', animateCards);
        
        // Botão "Marcar" interativo
        document.querySelectorAll('.evaluation-card .btn-medq-accent').forEach(button => {
            button.addEventListener('click', function() {
                const card = this.closest('.evaluation-card');
                const badge = card.querySelector('.badge-status');
                const icon = this.querySelector('i');
                
                if (badge.classList.contains('bg-warning')) {
                    // Marcar como lida
                    badge.classList.remove('bg-warning');
                    badge.classList.add('bg-success');
                    badge.textContent = 'Concluída';
                    this.innerHTML = '<i class="fas fa-check me-2"></i>Concluído';
                    card.querySelector('.position-absolute').classList.remove('bg-warning');
                    card.querySelector('.position-absolute').classList.add('bg-success');
                } else {
                    // Marcar como não lida
                    badge.classList.remove('bg-success');
                    badge.classList.add('bg-warning');
                    badge.textContent = 'Pendente';
                    this.innerHTML = '<i class="fas fa-envelope me-2"></i>Marcar';
                    card.querySelector('.position-absolute').classList.remove('bg-success');
                    card.querySelector('.position-absolute').classList.add('bg-warning');
                }
                
                // Efeito visual de confirmação
                this.classList.add('btn-success');
                setTimeout(() => {
                    this.classList.remove('btn-success');
                    this.classList.add('btn-medq-accent');
                }, 1000);
            });
        });
    </script>
</body>
</html>