<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedQ - Dashboard Empresarial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2a4f7e;
            --accent-color: #38b2ac;
        }

        .dashboard-container {
            background: #f8f9fa;
            min-height: 100vh;
        }

        .sidebar {
            background: var(--primary-color);
            color: white;
            min-height: 100vh;
            padding: 20px;
            width: 280px;
            position: fixed;
        }

        .main-content {
            margin-left: 280px;
            padding: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px;
            border-radius: 10px;
            margin: 8px 0;
        }

        .nav-link:hover {
            background: var(--secondary-color);
            color: white;
        }

        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-top: 30px;
        }

        .recent-activity {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="text-center mb-5">
                <img src="https://source.unsplash.com/random/100x100/?logo" class="rounded-circle mb-3" alt="Logo">
                <h4>Transportadora MedQ</h4>
            </div>

            <nav class="nav flex-column">
                <a class="nav-link active" href="#">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a class="nav-link" href="#">
                    <i class="fas fa-cog me-2"></i> Configurações
                </a>
                <a class="nav-link" href="#">
                    <i class="fas fa-user me-2"></i> Perfil
                </a>
                <a class="nav-link" href="#">
                    <i class="fas fa-bell me-2"></i> Notificações
                </a>
                <a class="nav-link" href="#">
                    <i class="fas fa-question-circle me-2"></i> Suporte
                </a>
                <a class="nav-link" href="#">
                    <i class="fas fa-sign-out-alt me-2"></i> Sair
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2><i class="fas fa-tachometer-alt me-2"></i> Dashboard</h2>
                <div class="d-flex align-items-center">
                    <span class="me-3">Bem-vindo, Empresa MedQ</span>
                    <img src="https://source.unsplash.com/random/40x40/?user" class="rounded-circle" alt="Perfil">
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle p-3 me-3">
                                <i class="fas fa-user-injured fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">1,240</h5>
                                <small>Pacientes Transportados</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="bg-success text-white rounded-circle p-3 me-3">
                                <i class="fas fa-truck fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">18</h5>
                                <small>Motoristas Ativos</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning text-dark rounded-circle p-3 me-3">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">45</h5>
                                <small>Agendamentos Pendentes</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger text-white rounded-circle p-3 me-3">
                                <i class="fas fa-percent fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">94%</h5>
                                <small>Taxa de Conclusão</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico -->
            <div class="chart-container">
                <h4 class="mb-4"><i class="fas fa-chart-area me-2"></i> Transportes Mensais</h4>
                <canvas id="transportChart"></canvas>
            </div>

            <!-- Atividades Recentes -->
            <div class="recent-activity">
                <h4 class="mb-4"><i class="fas fa-list-ul me-2"></i> Atividades Recentes</h4>
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-ambulance me-3 text-primary"></i>
                            Novo agendamento para João Silva
                        </div>
                        <span class="badge bg-primary rounded-pill">15/03 - 14:00</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-check-circle me-3 text-success"></i>
                            Transporte concluído para Maria Oliveira
                        </div>
                        <span class="badge bg-success rounded-pill">14/03 - 10:30</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-exclamation-triangle me-3 text-warning"></i>
                            Agendamento cancelado para Pedro Souza
                        </div>
                        <span class="badge bg-warning rounded-pill">13/03 - 09:15</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gráfico de Transportes
        const ctx = document.getElementById('transportChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                datasets: [{
                    label: 'Transportes Realizados',
                    data: [120, 135, 150, 145, 160, 175],
                    borderColor: '#1a365d',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(26,54,93,0.1)'
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>