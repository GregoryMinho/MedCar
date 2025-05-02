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
    <link rel="stylesheet" href="style/style_avaliacoes.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark medq-navbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="logo-medq.png" alt="MedCar" height="40" class="me-2">
                <span class="fw-bold">MedCar Transportes</span>
            </a>
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-light">
                    <i class="fas fa-sign-out-alt me-2"></i>Sair
                </button>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <main class="container py-4">
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
                                        <i class="fas fa-check me-2"></i>Marcar
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
            scrollButton.classList.toggle('show', window.scrollY > 300);
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
                card.style.transform = 'translateY(20px)';
                card.style.opacity = '0';
                card.style.transition = `all 0.5s ease ${index * 0.1}s`;

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.transform = 'translateY(0)';
                            entry.target.style.opacity = '1';
                        }
                    });
                });

                observer.observe(card);
            });
        }

        animateCards();
    </script>
</body>

</html>
<?php
$conn = null;
?>