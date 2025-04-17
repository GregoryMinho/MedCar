<?php
require '../includes/classe_usuario.php'; // inclui o arquivo de validação de login
use usuario\Usuario; // usa o namespace usuario\Usuario

// Usuario::verificarPermissao('empresa'); // verifica se o usuário logado é uma empresa

require '../includes/conexao_BdAgendamento.php'; 

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Consulta para contar os agendamentos do dia de hoje com situacao = 'Agendado'
$query = "SELECT COUNT(*) AS total FROM agendamentos WHERE situacao = 'Agendado' AND data_consulta = CURDATE()";
$result = $conn->query($query);
$totalAgendadosHoje = 0;
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalAgendadosHoje = $row['total'];
}




$query_pendentes = "SELECT COUNT(*) AS total FROM agendamentos WHERE situacao = 'Pendente'";
$result_pendentes = $conn->query($query_pendentes);
$totalPendentes = 0;
if ($result_pendentes && $result_pendentes->num_rows > 0) {
    $row_pendentes = $result_pendentes->fetch_assoc();
    $totalPendentes = $row_pendentes['total'];
}

// Consulta para obter o próximo agendamento
$query_proximo = "SELECT 
                    c.nome AS paciente,
                    CONCAT(a.cidade_origem, ' - ', a.rua_origem, ', ', a.numero_origem) AS local_origem,
                    a.horario 
                  FROM agendamentos a
                  JOIN medcar_cadastro_login.clientes c ON a.cliente_id = c.id
                  WHERE a.data_consulta = CURDATE() 
                    AND a.situacao = 'Agendado'
                  ORDER BY a.horario ASC
                  LIMIT 1";

$result_proximo = $conn->query($query_proximo);
$proximo_agendamento = null;
if ($result_proximo && $result_proximo->num_rows > 0) {
    $proximo_agendamento = $result_proximo->fetch_assoc();
}

$conn->close();




// Conexão com o banco financeiro para buscar o faturamento
$db_fin = "MedCar_Financeiro";
$conn_fin = new mysqli($host, $user, $pass, $db_fin);
if ($conn_fin->connect_error) {
    die("Erro na conexão com financeiro: " . $conn_fin->connect_error);
}

// Consulta para buscar o faturamento na tabela metricas (assumindo que tipo = 'faturamento')
$query_fin = "SELECT valor FROM metricas WHERE tipo = 'faturamento' LIMIT 1";
$result_fin = $conn_fin->query($query_fin);
$faturamento = 0;
if ($result_fin && $result_fin->num_rows > 0) {
    $row_fin = $result_fin->fetch_assoc();
    $faturamento = $row_fin['valor'];
}
$conn_fin->close();




$db_avaliacoes = "medcar_avaliacoes";
$conn_avaliacoes = new mysqli($host, $user, $pass, $db_avaliacoes);
if ($conn_avaliacoes->connect_error) {
    die("Erro na conexão com avaliações: " . $conn_avaliacoes->connect_error);
}

// Consulta para calcular a média de avaliações
$query_avaliacoes = "SELECT AVG(nota) AS media FROM avaliacoes";
$result_avaliacoes = $conn_avaliacoes->query($query_avaliacoes);
$media_avaliacoes = 0.0;
if ($result_avaliacoes && $result_avaliacoes->num_rows > 0) {
    $row_avaliacoes = $result_avaliacoes->fetch_assoc();
    $media_avaliacoes = number_format($row_avaliacoes['media'], 1);
}


$query_total = "SELECT COUNT(*) AS total FROM avaliacoes";
$result_total = $conn_avaliacoes->query($query_total);
$total_avaliacoes = 0;
if ($result_total && $result_total->num_rows > 0) {
    $row_total = $result_total->fetch_assoc();
    $total_avaliacoes = $row_total['total'];
}
$conn_avaliacoes->close();




?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Área da Empresa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @media (max-width: 768px) {
            .logout-menu {
                display: none;
            }
        }

        .mobile-menu {
            transition: transform 0.3s ease-in-out;
            transform: translateX(100%);
        }

        .mobile-menu.open {
            transform: translateX(0);
        }

        .dashboard-card {
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .dashboard-card::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(56, 178, 172, 0.1));
            transform: rotate(45deg);
            transition: all 0.5s;
        }

        .dashboard-card:hover::before {
            animation: shine 1.5s;
        }

        @keyframes shine {
            0% {
                transform: rotate(45deg) translate(-50%, -50%);
            }

            100% {
                transform: rotate(45deg) translate(100%, 100%);
            }
        }

        .vehicle-status {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-available {
            background: #10b981;
        }

        .status-in-use {
            background: #f59e0b;
        }

        .status-maintenance {
            background: #ef4444;
        }

        .schedule-timeline {
            border-left: 3px solid #38b2ac;
            padding-left: 20px;
        }

        nav a {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            text-decoration: none;
        }

        nav a i {
            margin-right: 8px;
        }
    </style>
</head>

<body class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="#" class="flex items-center space-x-2 text-xl font-bold">
                    <i data-lucide="ambulance" class="h-6 w-6"></i>
                    <span>MedCar</span>
                </a>

                <div class="flex items-center space-x-4">
                    <div class="text-white mr-3">Bem-vindo, </div>
                    <img src="https://source.unsplash.com/random/40x40/?logo" class="rounded-full h-8 w-8" alt="Logo">
                    <a href="../includes/logout.php" class="logout-menu  btn btn-outline-light ms-3">Logout</a>
                    <button id="mobile-menu-button" class="md:hidden text-white ml-2">
                        <i data-lucide="menu" class="h-6 w-6"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="fixed inset-0 z-50 bg-blue-900 bg-opacity-95 flex flex-col text-white p-6 mobile-menu">
        <div class="flex justify-end">
            <button id="close-menu-button" class="text-white">
                <i class="bi bi-x-lg h-6 w-6"></i>
            </button>
        </div>

        <div class="flex flex-col items-start space-y-6 flex-grow text-xl ps-4">
            <a href="dashboard.php" class="w-full hover:text-teal-300 transition d-flex align-items-center gap-3">
                <i class="bi bi-grid fs-5"></i>
                Dashboard
            </a>
            <a href="agendamentos_pacientes.php" class="w-full hover:text-teal-300 transition d-flex align-items-center gap-3">
                <i class="bi bi-calendar-event fs-5"></i>
                Agendamentos
            </a>
            <a href="aprovar_agendamentos.php" class="w-full hover:text-teal-300 transition d-flex align-items-center gap-3">
                <i class="bi bi-calendar-check fs-5"></i>
                Aprovar Agendamentos
            </a>
            <a href="gestao_motoristas.php" class="w-full hover:text-teal-300 transition d-flex align-items-center gap-3">
                <i class="bi bi-people fs-5"></i>
                Motoristas
            </a>
            <a href="relatorios_financeiros.php" class="w-full hover:text-teal-300 transition d-flex align-items-center gap-3">
                <i class="bi bi-graph-up fs-5"></i>
                Financeiro
            </a>
            <a href="relatorios.php" class="w-full hover:text-teal-300 transition d-flex align-items-center gap-3">
                <i class="bi bi-file-text fs-5"></i>
                Relatórios
            </a>
            <div class="relative">
                <button id="dropdown-button-mobile" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition focus:outline-none">
                    <i data-lucide="settings" class="h-5 w-5"></i>
                    <span>Configurações</span>
                    <i data-lucide="chevron-down" class="h-5 w-5"></i>
                </button>
                <div id="dropdown-menu-mobile" class="absolute hidden bg-white text-blue-900 rounded-lg shadow-lg mt-2 w-48">
                    <a href="editar_empresa.php" class="block px-4 py-2 hover:bg-gray-100">Editar Cadastro</a>
                    <a href="seguranca.php" class="block px-4 py-2 hover:bg-gray-100">Segurança</a>
                    <a href="preferencias.php" class="block px-4 py-2 hover:bg-gray-100">Preferências</a>
                </div>
            </div>
            <a href="avaliacoes.php" class="w-full hover:text-teal-300 transition d-flex align-items-center gap-3">
                <i class="bi bi-star fs-5"></i>
                Avaliações
            </a>
            <a href="../includes/logout.php" class="btn btn-outline-light ms-3">Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex flex-col md:flex-row">
        <!-- Sidebar -->
        <div class="hidden md:block w-64 bg-blue-900 text-white min-h-screen pt-24">
            <nav class="flex flex-col space-y-2 px-4">
                <a href="dashboard.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-grid fs-6"></i>
                    <span>Dashboard</span>
                </a>
                <a href="agendamentos_pacientes.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-calendar-event fs-6"></i>
                    <span>Agendamentos</span>
                </a>
                <a href="aprovar_agendamentos.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-calendar-check fs-6"></i>
                    <span>Aprovar Agendamentos</span>
                </a>
                <a href="gestao_motoristas.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-people fs-6"></i>
                    <span>Motoristas</span>
                </a>
                <a href="relatorios_financeiros.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-graph-up fs-6"></i>
                    <span>Financeiro</span>
                </a>
                <a href="relatorios.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-file-text fs-6"></i>
                    <span>Relatórios</span>
                </a>
                <div class="relative">
                    <button id="dropdown-button" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition focus:outline-none">
                        <i data-lucide="settings" class="h-5 w-5"></i>
                        <span>Configurações</span>
                        <i data-lucide="chevron-down" class="h-5 w-5"></i>
                    </button>
                    <div id="dropdown-menu" class="absolute hidden bg-white text-blue-900 rounded-lg shadow-lg mt-2 w-48">
                        <a href="editar_empresa.php" class="block px-4 py-2 hover:bg-gray-100">Editar Cadastro</a>
                        <a href="seguranca.php" class="block px-4 py-2 hover:bg-gray-100">Segurança</a>
                        <a href="preferencias.php" class="block px-4 py-2 hover:bg-gray-100">Preferências</a>
                    </div>
                </div>
                <a href="avaliacoes.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-star fs-6"></i>
                    <span>Avaliações</span>
                </a>
            </nav>
        </div>


        <!-- Main Content Area -->
        <div class="flex-1">
            <!-- Header Section -->
            <section class="pt-24 pb-16 bg-gradient-to-r from-blue-900 to-blue-800 text-white">
                <div class="container mx-auto px-4">
                    <h1 class="text-3xl md:text-4xl font-bold mb-6">Relatório</h1>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <!-- Serviços Hoje -->
                        <div class="dashboard-card relative overflow-hidden bg-white text-blue-900 rounded-xl shadow-lg p-4 text-center">
                            <div class="mb-2">
                                <i data-lucide="calendar-check" class="h-8 w-8 mx-auto text-teal-500"></i>
                            </div>
                            <h5 class="text-sm font-semibold mb-1">Serviços Hoje</h5>
                            <p class="text-2xl font-bold"><?php echo $totalAgendadosHoje; ?></p>
                        </div>

                        <!-- Faturamento -->
                        <div class="dashboard-card relative overflow-hidden bg-white text-blue-900 rounded-xl shadow-lg p-4 text-center">
                            <div class="mb-2">
                                <i data-lucide="dollar-sign" class="h-8 w-8 mx-auto text-teal-500"></i>
                            </div>
                            <h5 class="text-sm font-semibold mb-1">Faturamento</h5>
                            <p class="text-xl font-bold">R$ <?php echo number_format($faturamento, 2, ',', '.'); ?></p>
                        </div>
                        <!-- Avaliação -->
                        <div class="dashboard-card relative overflow-hidden bg-white text-blue-900 rounded-xl shadow-lg p-4 text-center">
                            <div class="mb-2">
                                <i data-lucide="star" class="h-8 w-8 mx-auto text-yellow-500"></i>
                            </div>
                            <h5 class="text-sm font-semibold mb-1">Avaliação Média</h5>
                            <p class="text-xl font-bold">
                                <?php echo $media_avaliacoes; ?>
                                <i data-lucide="star" class="h-4 w-4 inline"></i>
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                (<?php echo $total_avaliacoes; ?> avaliações)
                            </p>
                        </div>

                        <!-- Pendências -->
                        <!-- Card de Pendências -->
                        <div class="dashboard-card relative overflow-hidden bg-amber-50 text-blue-900 rounded-xl shadow-lg p-4 text-center">
                            <div class="mb-2">
                                <i data-lucide="alert-triangle" class="h-8 w-8 mx-auto text-amber-500"></i>
                            </div>
                            <h5 class="text-sm font-semibold mb-1">Pendências</h5>
                            <p class="text-xl font-bold"><?php echo $totalPendentes; ?></p>
                        </div>

                    </div>
                </div>
            </section>
            <!-- Main Sections -->
            <div class="container mx-auto px-4 py-8">
                <!-- Agendamentos -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h4 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                        <i data-lucide="calendar-days" class="h-5 w-5 mr-2 text-teal-500"></i>
                        Agenda de Hoje
                    </h4>
                    <div class="schedule-timeline mt-3">
                        <?php if ($proximo_agendamento): ?>
                            <div class="mb-4">
                                <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                                    <div>
                                        <h5 class="font-semibold text-blue-900">Paciente: <?php echo htmlspecialchars($proximo_agendamento['paciente']); ?></h5>
                                        <p class="text-gray-600 text-sm">
                                            <?php echo htmlspecialchars($proximo_agendamento['local_origem']); ?> -
                                            <?php echo date('H:i', strtotime($proximo_agendamento['horario'])); ?>
                                        </p>
                                    </div>
                                    <div class="mt-2 md:mt-0">
                                        <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                            Agendado
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mb-4">
                                <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                                    <div>
                                        <h5 class="font-semibold text-blue-900">Nenhum agendamento para hoje</h5>
                                        <p class="text-gray-600 text-sm">Não há transportes agendados para o dia de hoje</p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Gestão de Frota e Motoristas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Gestão de Frota -->
                    <div class="bg-white rounded-xl shadow-lg p-6 h-full">
                        <h4 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                            <i data-lucide="ambulance" class="h-5 w-5 mr-2 text-teal-500"></i>
                            Gestão de Frota
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Veículo
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Última Manutenção
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            AMB-1234
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            <div class="vehicle-status status-available"></div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            15/08/2024
                                        </td>
                                    </tr>
                                    <!-- Mais veículos... -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Motoristas -->
                    <div class="bg-white rounded-xl shadow-lg p-6 h-full">
                        <h4 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                            <i data-lucide="users" class="h-5 w-5 mr-2 text-teal-500"></i>
                            Motoristas
                        </h4>
                        <div class="space-y-3">
                            <div class="border rounded-lg p-4 flex flex-col md:flex-row md:justify-between md:items-center">
                                <div>
                                    <h5 class="font-semibold text-blue-900">Carlos Silva</h5>
                                    <p class="text-gray-600 text-sm">Disponível</p>
                                </div>
                                <div class="mt-2 md:mt-0">
                                    <button class="bg-blue-100 hover:bg-blue-200 text-blue-800 text-sm font-medium py-1 px-3 rounded-lg transition-all hover:scale-105">
                                        Detalhes
                                    </button>
                                </div>
                            </div>
                            <!-- Mais motoristas... -->
                        </div>
                    </div>
                </div>

                <!-- Financeiro -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h4 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                        <i data-lucide="bar-chart-2" class="h-5 w-5 mr-2 text-teal-500"></i>
                        Desempenho Financeiro
                    </h4>
                    <div class="mt-3 h-64">
                        <!-- Gráfico (implementar com biblioteca) -->
                        <div class="bg-gray-50 text-center p-5 rounded-lg flex items-center justify-center h-full">
                            <p class="text-gray-500">Gráfico de Desempenho</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Mobile menu functionality
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeMenuButton = document.getElementById('close-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.add('open');
        });

        closeMenuButton.addEventListener('click', () => {
            mobileMenu.classList.remove('open');
        });
    </script>
    <script>
        const dropdownButtonMobile = document.getElementById('dropdown-button-mobile');
        const dropdownMenuMobile = document.getElementById('dropdown-menu-mobile');

        // Toggle dropdown visibility on button click
        dropdownButtonMobile.addEventListener('click', () => {
            dropdownMenuMobile.classList.toggle('hidden');
        });

        // Close dropdown if clicked outside
        document.addEventListener('click', (event) => {
            if (!dropdownButtonMobile.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenuMobile.classList.add('hidden');
            }
        });
    </script>
</body>
<script>
    const dropdownButton = document.getElementById('dropdown-button');
    const dropdownMenu = document.getElementById('dropdown-menu');

    // Toggle dropdown visibility on button click
    dropdownButton.addEventListener('click', () => {
        dropdownMenu.classList.toggle('hidden');
    });

    // Close dropdown if clicked outside
    document.addEventListener('click', (event) => {
        if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.add('hidden');
        }
    });
</script>
</body>

</html>