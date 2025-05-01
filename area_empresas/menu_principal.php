<?php

if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

// --- VERIFICA SE O USUÁRIO ESTÁ LOGADO E SE O ID EXISTE ---
if (!isset($_SESSION['usuario']['id'])) {
    header('Location: ../paginas/login_empresas.php');
    exit();
}

// --- DEFINE A VARIÁVEL GLOBALMENTE ---
$empresa_id = $_SESSION['usuario']['id']; // Agora está acessível em todo o script

// --- CONEXÃO AGENDAMENTOS ---
require '../includes/conexao_BdAgendamento.php';

// =============================================
// CONSULTA 1: PRÓXIMO AGENDAMENTO (HOJE)
// =============================================
$proximo_agendamento = null;
try {
    $stmt = $conn->prepare("
        SELECT 
            c.nome AS paciente,
            CONCAT(a.cidade_origem, ' - ', a.rua_origem, ', ', a.numero_origem) AS local_origem,
            a.horario
        FROM agendamentos a
        INNER JOIN medcar_cadastro_login.clientes c ON a.cliente_id = c.id
        WHERE a.data_consulta = CURDATE()
            AND a.situacao = 'Agendado'
            AND a.empresa_id = :empresa_id
        ORDER BY a.horario ASC
        LIMIT 1
    ");
    $stmt->bindValue(':empresa_id', $empresa_id, PDO::PARAM_INT); // Usa a variável definida
    $stmt->execute();
    $proximo_agendamento = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao buscar próximo agendamento: " . $e->getMessage());
}

// =============================================
// CONSULTA 2: AGENDAMENTOS PENDENTES
// =============================================
$totalPendentes = 0;
try {
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM agendamentos 
        WHERE situacao = 'Pendente' 
        AND empresa_id = :empresa_id
    ");
    $stmt->bindValue(':empresa_id', $_SESSION['usuario']['id'], PDO::PARAM_INT);
    $stmt->execute();
    $totalPendentes = $stmt->fetchColumn();
} catch (PDOException $e) {
    error_log("Erro ao contar agendamentos pendentes: " . $e->getMessage());
}

$totalAgendadosHoje = 0; // Inicializa a variável para evitar "Undefined variable"
try {
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM agendamentos 
        WHERE data_consulta = CURDATE() 
            AND situacao = 'Agendado' 
            AND empresa_id = :empresa_id
    ");
    $stmt->bindValue(':empresa_id', $empresa_id, PDO::PARAM_INT);
    $stmt->execute();
    $totalAgendadosHoje = $stmt->fetchColumn() ?? 0; // Se não houver resultados, usa 0
} catch (PDOException $e) {
    error_log("Erro ao contar agendamentos de hoje: " . $e->getMessage());
    $totalAgendadosHoje = 0; // Garante que a variável tenha um valor
}












$conn = null; // Fecha conexão agendamentos


// =============================================
// CONSULTA 4: AVALIAÇÕES (MÉDIA E TOTAL)
// =============================================
require '../includes/conexao_BdAvaliacoes.php';

$media_avaliacoes = 0.0;
$total_avaliacoes = 0;
try {
    // Média das avaliações
    $stmt = $conn->prepare("SELECT AVG(nota) AS media FROM avaliacoes WHERE empresa_id = ?");
    
    // O tipo de dado 'i' significa inteiro, já que o id da empresa provavelmente é inteiro
    $stmt->bind_param("i", $_SESSION['usuario']['id']);
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $media = $row['media'];
    
    $media_avaliacoes = number_format($media ?? 0.0, 1);
    
} catch (mysqli_sql_exception $e) {
    echo "Erro: " . $e->getMessage();
}
$conn = null; // Fecha conexão avaliações
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
    <link rel="stylesheet" href="style/style_menu_principal.css">
    <script src="https://unpkg.com/lucide@latest"></script>
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
                <a href="aprovar_agendamentos.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition group">
    <i class="bi bi-check-circle fs-6 text-white group-hover:text-green-400"></i>
    <span class="text-white group-hover:text-green-400">Aprovar Agendamentos</span>
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
                <a href="avaliacoes.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-star fs-6"></i>
                    <span>Avaliações</span>
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
              
            </nav>
        </div>


        <!-- Main Content Area -->
        <div class="flex-1">
            <!-- Header Section -->
            <section class="pt-24 pb-16 bg-gradient-to-r from-blue-900 to-blue-800 text-white">
                <div class="container mx-auto px-4">
                    <h1 class="text-3xl md:text-4xl font-bold mb-6">Relatório</h1>

                    <!-- DIV SERVIÇÕS HOJE-->
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
                            <h5 class="text-sm font-semibold mb-1">Faturamento </h5>
                            <p class="text-xl font-bold">R$ </p>
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
                    <?php if ($proximo_agendamento): ?>
                        <div class=" mt-3 h-56 overflow-y-auto">
                            <?php foreach ($proximo_agendamento as $chave): ?>
                                <a href="agendamentos_pacientes.php">
                                    <div class="schedule-timeline mb-4 bg-teal-50 hover:bg-gray-100 transition duration-200 ease-in-out rounded-lg p-4">
                                        <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                                            <div>
                                                <h5 class="font-semibold text-blue-900">Paciente: <?php echo htmlspecialchars($chave['paciente']); ?></h5>
                                                <p class="text-gray-600 text-sm">
                                                    <?php echo htmlspecialchars($chave['local_origem']); ?> -
                                                    <?php echo date('H:i', strtotime($chave['horario'])); ?>
                                                </p>
                                            </div>
                                            <div class="mt-2 md:mt-0">
                                                <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                    Agendado
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class=" mt-3">

                            <div class="mb-4">
                                <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                                    <div>
                                        <h5 class="font-semibold text-blue-900">Nenhum agendamento para hoje</h5>
                                        <p class="text-gray-600 text-sm">Não há transportes agendados para o dia de hoje</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
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