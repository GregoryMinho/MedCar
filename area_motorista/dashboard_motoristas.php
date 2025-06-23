<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Dashboard do Motorista</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1a365d',
                        secondary: '#2a4f7e',
                        accent: '#38b2ac',
                        lightblue: '#e6f4ff',
                        lightgreen: '#e6f7f1',
                        lightyellow: '#fffbe6'
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fa;
            overflow-x: hidden;
        }
        
        .vehicle-status {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .status-available { background-color: #10B981; }
        .status-in-use { background-color: #F59E0B; }
        .status-maintenance { background-color: #EF4444; }
        
        .sidebar-item {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .sidebar-item:hover, .sidebar-item.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid #38b2ac;
        }
        
        .dashboard-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .transport-card {
            transition: all 0.3s ease;
        }
        
        .transport-card:hover {
            transform: scale(1.02);
        }
        
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 20px;
            height: 20px;
            background-color: #ef4444;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease forwards;
        }
        
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        
        .bi-icon {
            font-size: 1.2rem;
            margin-right: 10px;
            width: 24px;
            text-align: center;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-primary to-secondary text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="#" class="flex items-center space-x-2 text-xl font-bold">
                    <i class="bi bi-ambulance"></i>
                    <span>MedCar</span>
                </a>
                
                <!-- Mobile menu button -->
                <button id="mobileMenuButton" class="md:hidden text-white focus:outline-none">
                    <i class="bi bi-list text-xl"></i>
                </button>
                
                <div class="hidden md:flex items-center space-x-6">
                    <button class="flex items-center space-x-2 font-medium hover:text-accent transition">
                        <i class="bi bi-person"></i>
                        <span>Perfil</span>
                    </button>
                    <div class="relative">
                        <button class="relative">
                            <i class="bi bi-bell"></i>
                            <span class="notification-badge">2</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Sidebar e Conteúdo -->
    <div class="flex pt-20">
        <!-- Sidebar Mobile -->
        <div id="mobileSidebar" class="fixed inset-0 z-40 bg-black bg-opacity-50 hidden">
            <div class="absolute left-0 top-0 bottom-0 w-64 bg-primary text-white pt-10 transform -translate-x-full transition-transform duration-300">
                <button id="closeSidebar" class="absolute top-4 right-4 text-white">
                    <i class="bi bi-x-lg"></i>
                </button>
                <nav class="flex flex-col space-y-2 px-4 mt-8">
                    <a href="dashboard_motoristas.php" class="sidebar-item active flex items-center gap-3 ps-4 py-3 rounded-lg font-semibold">
                        <i class="bi bi-speedometer2 bi-icon"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="meus_agendamentos.php"  class="sidebar-item flex items-center gap-3 ps-4 py-3 rounded-lg transition">
                        <i class="bi bi-calendar-event bi-icon"></i>
                        <span>Meus Agendamentos</span>
                    </a>
                     <a href="historico_pagamentos.php" class="sidebar-item flex items-center gap-3 ps-4 py-3 rounded-lg transition">
                        <i class="bi bi-truck bi-icon"></i>
                        <span>Veículo</span>
                    </a>
                    <a href="upload_documentos.php" class="sidebar-item flex items-center gap-3 ps-4 py-3 rounded-lg transition">
                        <i class="bi bi-gear bi-icon"></i>
                        <span>Configurações</span>
                    </a>
                    <a href="treinamento.php" class="sidebar-item flex items-center gap-3 ps-4 py-3 rounded-lg transition">
                        <i class="bi bi-mortarboard bi-icon"></i>
                        <span>Treinamento</span>
                    </a>
                    <a href="/MedCar/logout.php" class="sidebar-item flex items-center gap-3 ps-4 py-3 rounded-lg transition mt-8 text-red-400">
                        <i class="bi bi-box-arrow-right bi-icon"></i>
                        <span>Sair</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Sidebar Desktop -->
        <div class="hidden md:block w-64 bg-blue-900 text-white min-h-screen pt-10">
            <nav class="flex flex-col space-y-2 px-4">
                <a href="dashboard_motoristas.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg bg-blue-800 font-semibold">
                    <i class="bi bi-speedometer2 bi-icon"></i>
                    <span>Dashboard</span>
                </a>
                <a href="meus_agendamentos.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-calendar-event bi-icon"></i>
                    <span>Meus Agendamentos</span>
                </a>
                <a href="historico_pagamentos.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-currency-dollar bi-icon"></i>
                    <span>Histórico de Pagamentos</span>
                </a>
                <a href="upload_documentos.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-cloud-upload bi-icon"></i>
                    <span>Upload de Documentos</span>
                </a>
                <a href="treinamento.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-mortarboard bi-icon"></i>
                    <span>Treinamento</span>
                </a>
                <a href="perfil_motoristas.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-person bi-icon"></i>
                    <span>Meu Perfil</span>
                </a>
                <a href="/MedCar/logout.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition mt-8 text-red-400">
                    <i class="bi bi-box-arrow-right bi-icon"></i>
                    <span>Sair</span>
                </a>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 px-2 md:px-8 lg:px-10 py-6">
            <!-- Header e cards resumo -->
            <div class="animate-fade-in">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-primary">Olá, Carlos Silva!</h1>
                        <p class="text-gray-600">Bem-vindo ao seu dashboard de motorista</p>
                    </div>
                    <div class="md:hidden flex items-center space-x-4">
                        <button class="relative">
                            <i class="bi bi-bell text-primary"></i>
                            <span class="notification-badge">2</span>
                        </button>
                        <button class="flex items-center space-x-2 font-medium text-primary">
                            <i class="bi bi-person h-5 w-5"></i>
                        </button>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="dashboard-card bg-white rounded-xl shadow p-4 text-center animate-fade-in delay-100">
                        <div class="mb-2"><i class="bi bi-calendar-day text-2xl mx-auto text-accent"></i></div>
                        <div class="text-sm font-semibold text-gray-600 mb-1">Transportes Hoje</div>
                        <div class="text-2xl font-bold text-primary">4</div>
                    </div>
                    <div class="dashboard-card bg-white rounded-xl shadow p-4 text-center animate-fade-in delay-200">
                        <div class="mb-2"><i class="bi bi-clock text-2xl mx-auto text-yellow-500"></i></div>
                        <div class="text-sm font-semibold text-gray-600 mb-1">Próximo Transporte</div>
                        <div class="text-xl font-bold text-primary">09:30</div>
                    </div>
                    <div class="dashboard-card bg-white rounded-xl shadow p-4 text-center animate-fade-in delay-300">
                        <div class="mb-2"><i class="bi bi-check-circle text-2xl mx-auto text-green-500"></i></div>
                        <div class="text-sm font-semibold text-gray-600 mb-1">Concluídos</div>
                        <div class="text-2xl font-bold text-primary">1</div>
                    </div>
                    <div class="dashboard-card bg-yellow-50 rounded-xl shadow p-4 text-center animate-fade-in delay-400">
                        <div class="mb-2"><i class="bi bi-exclamation-triangle text-2xl mx-auto text-yellow-500"></i></div>
                        <div class="text-sm font-semibold text-gray-600 mb-1">Pendentes</div>
                        <div class="text-2xl font-bold text-primary">1</div>
                    </div>
                </div>
            </div>
            
            <!-- Próximo transporte -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6 animate-fade-in">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-xl font-bold text-primary flex items-center">
                        <i class="bi bi-geo-alt mr-2 text-accent"></i>
                        Próximo Transporte
                    </h4>
                    <span class="inline-block px-3 py-1 text-sm font-medium bg-green-100 text-green-800 rounded-full">Agendado</span>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="bi bi-person-wheelchair text-blue-600 mr-2"></i>
                            <span class="font-semibold text-blue-900">Paciente:</span>
                        </div>
                        <p class="text-lg font-medium">Maria Oliveira</p>
                    </div>
                    
                    <div class="bg-lightgreen p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="bi bi-geo-alt-fill text-green-600 mr-2"></i>
                            <span class="font-semibold text-green-900">Destino:</span>
                        </div>
                        <p class="text-gray-700">Hospital Central - Av. Paulista, 1200</p>
                    </div>
                    
                    <div class="bg-lightyellow p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="bi bi-clipboard-pulse text-yellow-600 mr-2"></i>
                            <span class="font-semibold text-yellow-900">Procedimento:</span>
                        </div>
                        <p class="text-gray-700">Hemodiálise</p>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 mt-4">
                    <button class="flex-1 bg-accent hover:bg-teal-600 text-white font-semibold py-3 px-4 rounded-lg shadow transition flex items-center justify-center">
                        <i class="bi bi-play-fill mr-2"></i> Iniciar Transporte
                    </button>
                    <button class="flex-1 bg-white border border-primary hover:bg-primary hover:text-white text-primary font-semibold py-3 px-4 rounded-lg transition flex items-center justify-center">
                        <i class="bi bi-person-lines-fill mr-2"></i> Detalhes Paciente
                    </button>
                    <button class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-3 px-4 rounded-lg transition flex items-center justify-center">
                        <i class="bi bi-signpost mr-2"></i> Ver Rota
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Agenda do dia -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-bold text-primary flex items-center">
                            <i class="bi bi-calendar-date mr-2 text-accent"></i>
                            Transportes de Hoje
                        </h4>
                        <span class="text-sm text-gray-500">08/05/2024</span>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="transport-card bg-blue-50 rounded-lg p-4 flex justify-between items-center">
                            <div>
                                <span class="font-semibold text-primary">09:30</span> - Maria Oliveira
                                <span class="ml-2 text-xs text-gray-500">(Hemodiálise)</span>
                                <span class="ml-2 text-xs bg-green-100 text-green-800 rounded px-2 py-0.5">Próximo</span>
                            </div>
                            <button class="text-primary hover:text-secondary">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                        <div class="transport-card bg-yellow-50 rounded-lg p-4 flex justify-between items-center">
                            <div>
                                <span class="font-semibold text-primary">12:00</span> - João Souza
                                <span class="ml-2 text-xs text-gray-500">(Consulta)</span>
                                <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 rounded px-2 py-0.5">Aguardando</span>
                            </div>
                            <button class="text-primary hover:text-secondary">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                        <div class="transport-card bg-green-50 rounded-lg p-4 flex justify-between items-center">
                            <div>
                                <span class="font-semibold text-primary">15:00</span> - Ana Costa
                                <span class="ml-2 text-xs text-gray-500">(Fisioterapia)</span>
                                <span class="ml-2 text-xs bg-green-100 text-green-800 rounded px-2 py-0.5">Concluído</span>
                            </div>
                            <button class="text-primary hover:text-secondary">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                        <div class="transport-card bg-yellow-50 rounded-lg p-4 flex justify-between items-center">
                            <div>
                                <span class="font-semibold text-primary">18:00</span> - Pedro Ramos
                                <span class="ml-2 text-xs text-gray-500">(Retorno)</span>
                                <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 rounded px-2 py-0.5">Aguardando</span>
                            </div>
                            <button class="text-primary hover:text-secondary">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Veículo Designado -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-bold text-primary flex items-center">
                            <i class="bi bi-truck mr-2 text-accent"></i>
                            Meu Veículo
                        </h4>
                        <div class="flex items-center">
                            <span class="vehicle-status status-available"></span>
                            <span class="text-sm font-medium text-green-700">Disponível</span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-3">
                                <i class="bi bi-car-front text-gray-600 mr-2"></i>
                                <span class="font-semibold text-gray-900">Veículo:</span>
                            </div>
                            <p class="text-lg font-medium text-primary">Toyota Corolla</p>
                            <p class="text-gray-600 text-sm">Placa: AMB-1234</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-3">
                                <i class="bi bi-fuel-pump text-gray-600 mr-2"></i>
                                <span class="font-semibold text-gray-900">Combustível:</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-green-600 h-2.5 rounded-full" style="width: 75%"></div>
                                </div>
                                <span class="ml-2 text-sm font-medium">75%</span>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="text-lg font-semibold text-primary mb-3">Próxima Manutenção</h5>
                    <div class="bg-red-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="font-medium text-red-800">Troca de Óleo</div>
                                <div class="text-sm text-red-600">Vencimento em 15 dias</div>
                            </div>
                            <button class="text-red-700 hover:text-red-900 flex items-center">
                                <i class="bi bi-calendar-plus mr-1"></i> Agendar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráfico de desempenho -->
            <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
                <h4 class="text-xl font-bold text-primary flex items-center mb-4">
                    <i class="bi bi-graph-up mr-2 text-accent"></i>
                    Desempenho Semanal
                </h4>
                <div class="w-full h-64">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="bg-primary text-white py-6 mt-10">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center">
                        <i class="bi bi-ambulance text-xl mr-2"></i>
                        <span class="text-xl font-bold">MedCar</span>
                    </div>
                    <p class="text-gray-300 mt-2">Transporte médico seguro e confiável</p>
                </div>
                
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-300 hover:text-white">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white">
                        <i class="bi bi-linkedin"></i>
                    </a>
                </div>
            </div>
            
            <div class="border-t border-blue-800 mt-6 pt-4 text-center text-gray-400 text-sm">
                &copy; 2024 MedCar. Todos os direitos reservados.
            </div>
        </div>
    </footer>
    
    <script>
        // Mobile sidebar toggle
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const closeSidebar = document.getElementById('closeSidebar');
        
        mobileMenuButton.addEventListener('click', () => {
            mobileSidebar.classList.remove('hidden');
            setTimeout(() => {
                document.querySelector('#mobileSidebar > div').classList.remove('-translate-x-full');
            }, 10);
        });
        
        closeSidebar.addEventListener('click', () => {
            document.querySelector('#mobileSidebar > div').classList.add('-translate-x-full');
            setTimeout(() => {
                mobileSidebar.classList.add('hidden');
            }, 300);
        });
        
        // Performance chart
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('performanceChart').getContext('2d');
            const performanceChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                    datasets: [{
                        label: 'Transportes Realizados',
                        data: [3, 5, 4, 6, 5, 2, 4],
                        backgroundColor: '#38b2ac',
                        borderColor: '#2a8f89',
                        borderWidth: 1,
                        borderRadius: 6,
                        barPercentage: 0.6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(26, 54, 93, 0.9)',
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 13
                            },
                            padding: 10,
                            cornerRadius: 6
                        }
                    }
                }
            });
        });
        
        // Simulate loading
        setTimeout(() => {
            document.querySelectorAll('.animate-fade-in').forEach(el => {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            });
        }, 100);
    </script>
</body>
</html>