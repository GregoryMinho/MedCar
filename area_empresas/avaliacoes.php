<?php
// avaliacoes.php
require '../includes/conexao_BdAgendamento.php'; 

// Cria a conexão com o banco de dados
$conn = new mysqli($host, $user, $pass, $dbname);

// Verifica se ocorreu algum erro na conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Query para recuperar avaliações com dados dos pacientes e motoristas
$sql = "SELECT a.avaliacao_id, a.nota, a.comentario, a.lida, a.data_avaliacao,
               p.nome AS paciente_nome,
               m.nome AS motorista_nome, m.foto_perfil
        FROM avaliacoes a
        JOIN pacientes p ON a.paciente_id = p.paciente_id
        JOIN motoristas m ON a.motorista_id = m.motorista_id
        ORDER BY a.data_avaliacao DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliações de Motoristas - MedQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --medq-primary: #2C3E50;
            --medq-secondary: #18BC9C;
            --medq-light: #ECF0F1;
        }
        body {
            background-color: var(--medq-light);
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        .medq-navbar {
            background-color: var(--medq-primary) !important;
            padding: 15px 0;
        }
        .evaluation-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .rating-stars {
            color: var(--medq-secondary);
            font-size: 1.2rem;
        }
        .driver-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }
        .badge-status {
            padding: 8px 15px;
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg medq-navbar">
        <div class="container">
            <a class="navbar-brand text-white" href="#">
                <img src="logo-medq.png" alt="MedQ" height="30" class="d-inline-block align-top">
                MedQ Transportes
            </a>
            <div class="d-flex">
                <button class="btn btn-outline-light">Sair</button>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <div class="container py-5">
        <h2 class="mb-4">Avaliações dos Pacientes</h2>

        <!-- Estatísticas Simplificadas (exemplo estático) -->
        <div class="row mb-5">
            <div class="col-md-4">
                <div class="card bg-white">
                    <div class="card-body">
                        <h5 class="card-title">Média Geral</h5>
                        <div class="d-flex align-items-center">
                            <div class="display-4 me-3">4.8</div>
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
                <div class="card bg-white">
                    <div class="card-body">
                        <h5 class="card-title">Total de Avaliações</h5>
                        <div class="display-4">
                            <?php echo $result->num_rows; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Avaliações -->
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="col-md-6">
                        <div class="card evaluation-card">
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="position-relative">
                                        <!-- Exibe a foto do motorista, caso não exista, usa uma imagem padrão -->
                                        <img src="<?php echo (!empty($row['foto_perfil']) ? $row['foto_perfil'] : 'default_motorista.jpg'); ?>" class="driver-avatar me-3" alt="Motorista">
                                        <!-- Exemplo de status: se 'lida' for true, exibe verde; caso contrário, amarelo -->
                                        <span class="position-absolute bottom-0 start-75 translate-middle p-1 <?php echo ($row['lida'] ? 'bg-success' : 'bg-warning'); ?> border border-2 border-white rounded-circle"></span>
                                    </div>
                                    <div>
                                        <h5 class="mb-1 fw-bold"><?php echo $row['motorista_nome']; ?></h5>
                                        <div class="rating-stars mb-2">
                                            <?php 
                                            // Exibe as estrelas de acordo com a nota
                                            for($i = 1; $i <= 5; $i++){
                                                if($i <= $row['nota']){
                                                    echo '<i class="fas fa-star"></i>';
                                                } else {
                                                    echo '<i class="far fa-star"></i>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <span class="badge <?php echo ($row['lida'] ? 'bg-success' : 'bg-warning'); ?> badge-status">
                                            <?php echo ($row['lida'] ? 'Viagem concluída' : 'Pendente'); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <strong class="d-block mb-1">
                                        <i class="fas fa-user-injured me-2"></i><?php echo $row['paciente_nome']; ?>
                                    </strong>
                                    <span class="text-muted">
                                        <i class="fas fa-calendar-day me-2"></i>
                                        <?php echo date("d/m/Y", strtotime($row['data_avaliacao'])); ?>
                                    </span>
                                </div>
                                
                                <p class="comment-text">
                                    <?php echo $row['comentario']; ?>
                                </p>
                                
                                <div class="mt-4 d-flex gap-2">
                                    <button class="btn btn-medq btn-sm">
                                        <i class="fas fa-reply me-2"></i>Responder
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-check-circle me-2"></i>Marcar como lida
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nenhuma avaliação encontrada.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="medq-navbar text-white mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">© 2023 MedQ Transportes. Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top -->
    <div class="scroll-to-top">
        <i class="fas fa-arrow-up"></i>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Scroll to Top
        const scrollToTop = document.querySelector('.scroll-to-top');
        window.addEventListener('scroll', () => {
            scrollToTop.classList.toggle('show', window.scrollY > 300);
        });
        
        scrollToTop.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Card Animation on Scroll
        const cards = document.querySelectorAll('.evaluation-card');
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if(entry.isIntersecting) {
                    entry.target.style.opacity = 1;
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });

        cards.forEach(card => {
            card.style.opacity = 0;
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s cubic-bezier(0.4,0,0.2,1)';
            observer.observe(card);
        });
    </script>
</body>
</html>
<?php
// Fecha a conexão com o banco
$conn->close();
?>
