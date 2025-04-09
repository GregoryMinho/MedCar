<?php
require '../includes/classe_usuario.php'; // inclui o arquivo de validação de login
require '../includes/conexao_BdAgendamento.php'; // inclui o arquivo de conexão com o banco de dados

// verificarPermissao('cliente'); // verifica se o usuário logado é um cliente

$_SESSION['usuario'] = [
    'id' => 1,
    'tipo' => 'cliente',
    'nome' => 'João Silva',
];

// Busca o próximo agendamento agendado mais próximo para o usuário logado 
$usuarioId = $_SESSION['usuario']['id'];
$query = "SELECT data_consulta, horario, rua_destino, cidade_destino, situacao 
          FROM agendamentos 
          WHERE cliente_id = :cliente_id AND data_consulta >= CURDATE() AND situacao = 'Agendado'
          ORDER BY data_consulta ASC, horario ASC 
          LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(':cliente_id', $usuarioId, PDO::PARAM_INT);
$stmt->execute();
$proximoTransporte = $stmt->fetch(PDO::FETCH_ASSOC);

// Consulta para buscar o número total de agendamentos concluídos do usuário
$queryConcluidos = "SELECT COUNT(*) AS total_concluidos 
                    FROM agendamentos 
                    WHERE cliente_id = :cliente_id AND situacao = 'Concluído'";
$stmtConcluidos = $conn->prepare($queryConcluidos);
$stmtConcluidos->bindParam(':cliente_id', $usuarioId, PDO::PARAM_INT);
$stmtConcluidos->execute();
$totalConcluidos = $stmtConcluidos->fetch(PDO::FETCH_ASSOC)['total_concluidos'];

// Consulta para buscar o número de agendamentos confirmados no mês atual
$queryConfirmadosMes = "SELECT COUNT(*) AS total_confirmados_mes 
                        FROM agendamentos 
                        WHERE cliente_id = :cliente_id AND situacao = 'Agendado' 
                        AND DATE_FORMAT(data_consulta, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')";
$stmtConfirmadosMes = $conn->prepare($queryConfirmadosMes);
$stmtConfirmadosMes->bindParam(':cliente_id', $usuarioId, PDO::PARAM_INT);
$stmtConfirmadosMes->execute();
$totalConfirmadosMes = $stmtConfirmadosMes->fetch(PDO::FETCH_ASSOC)['total_confirmados_mes'];

// Consulta para buscar os dois últimos registros de agendamento do usuário logado
$queryMensagens = "SELECT data_consulta, horario, observacoes, rua_destino, cidade_destino, situacao
                   FROM agendamentos 
                   WHERE cliente_id = :cliente_id 
                   ORDER BY id DESC 
                   LIMIT 2";
$stmtMensagens = $conn->prepare($queryMensagens);
$stmtMensagens->bindParam(':cliente_id', $usuarioId, PDO::PARAM_INT);
$stmtMensagens->execute();
$ultimasMensagens = $stmtMensagens->fetchAll(PDO::FETCH_ASSOC);

$conn = null;

echo $_POST;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Área do Paciente</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
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

        .emergency-card {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }

            100% {
                transform: scale(1);
            }
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
    </style>
</head>

<body class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="/MedQ-2/paginas/pagina_inicial.php" class="flex items-center space-x-2 text-xl font-bold">
                    <i data-lucide="ambulance" class="h-6 w-6"></i>
                    <span>MedCar</span>
                </a>

                <div class="flex items-center space-x-6">
                    <a href="menu_principal.php" class="font-medium hover:text-teal-300 transition">Home</a>
                    <a href="/MedQ-2/paginas/abas_menu_principal/aba_empresas.php" class="font-medium hover:text-teal-300 transition">Empresas</a> <!-- conectado as empresas , checa os outros butooes estao funcionando. -->
                    <a href="#" class="font-medium hover:text-teal-300 transition">Contato</a>
                    <a href="../includes/logout.php" class="font-medium hover:text-teal-300 transition">Logout</a>
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
                <i data-lucide="x" class="h-6 w-6"></i>
            </button>
        </div>

        <div class="flex flex-col items-center justify-center space-y-8 flex-grow text-xl">
            <a href="menu_principal.php" class="font-medium hover:text-teal-300 transition">Home</a>
            <a href="../paginas/abas_menu_principal/aba_empresas.php" class="font-medium hover:text-teal-300 transition">Empresas</a>
            <a href="#" class="font-medium hover:text-teal-300 transition">Contato</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex flex-col md:flex-row">
        <!-- Sidebar -->
        <div class="hidden md:block w-64 bg-blue-900 text-white min-h-screen pt-24 px-4">
            <nav class="flex flex-col space-y-2">
                <a href="#" class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-blue-800 text-white hover:bg-blue-700 transition">
                    <i data-lucide="home" class="h-5 w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
                    <i data-lucide="calendar" class="h-5 w-5"></i>
                    <span>Agendar</span>
                </a>
                <a href="historico.php" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
                    <i data-lucide="clock" class="h-5 w-5"></i>
                    <span>Histórico</span>
                </a>
                <a href="#" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
                    <i data-lucide="heart" class="h-5 w-5"></i>
                    <span>Favoritos</span>
                </a>
                <a href="#" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
                    <i data-lucide="settings" class="h-5 w-5"></i>
                    <span>Configurações</span>
                </a>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1">
            <!-- Header Section -->
            <section class="pt-24 pb-16 bg-gradient-to-r from-blue-900 to-blue-800 text-white">
                <div class="container mx-auto px-4">
                    <h1 class="text-3xl md:text-4xl font-bold mb-6">Área do Paciente</h1>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <!-- Próximo Transporte -->
                        <div class="dashboard-card relative overflow-hidden bg-white text-blue-900 rounded-xl shadow-lg p-4 text-center">
                            <div class="mb-2">
                                <i data-lucide="calendar-check" class="h-8 w-8 mx-auto text-teal-500"></i>
                            </div>
                            <h5 class="text-sm font-semibold mb-1">Próximo Transporte</h5>
                            <?php if ($proximoTransporte): ?>
                                <p class="font-bold mb-1">
                                    <?= date('d/m', strtotime($proximoTransporte['data_consulta'])) ?> - <?= date('H:i', strtotime($proximoTransporte['horario'])) ?>
                                </p>
                                <p class="text-xs text-gray-600"><?= $proximoTransporte['rua_destino'] ?>, <?= $proximoTransporte['cidade_destino'] ?></p>
                                <div class="mt-2">
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        <?= $proximoTransporte['situacao'] ?>
                                    </span>
                                </div>
                            <?php else: ?>
                                <p class="font-bold mb-1">Nenhum transporte agendado</p>
                            <?php endif; ?>
                        </div>

                        <!-- Transportes Realizados -->
                        <div class="dashboard-card relative overflow-hidden bg-white text-blue-900 rounded-xl shadow-lg p-4 text-center">
                            <div class="mb-2">
                                <i data-lucide="bar-chart-2" class="h-8 w-8 mx-auto text-teal-500"></i>
                            </div>
                            <?php if ($totalConcluidos > 0): ?>
                                <h5 class="text-sm font-semibold mb-1">Transportes Realizados</h5>
                                <p class="text-2xl font-bold"><?= $totalConcluidos ?></p>
                                <p class="text-xs text-gray-600">+<?= $totalConfirmadosMes ?> este mês</p>
                            <?php else: ?>
                                <h5 class="text-sm font-semibold mb-1">Nenhum transporte realizado</h5>
                                <p class="text-2xl font-bold">0</p>
                                <p class="text-xs text-gray-600">+0 este mês</p>
                            <?php endif; ?>
                        </div>

                        <!-- Avaliação Média -->
                        <div class="dashboard-card relative overflow-hidden bg-white text-blue-900 rounded-xl shadow-lg p-4 text-center">
                            <div class="mb-2">
                                <i data-lucide="star" class="h-8 w-8 mx-auto text-yellow-500"></i>
                            </div>
                            <h5 class="text-sm font-semibold mb-1">Avaliação Média</h5>
                            <p class="font-bold">4.8</p>
                            <div class="flex justify-center text-yellow-500 text-sm">
                                <i data-lucide="star" class="h-4 w-4 fill-current"></i>
                                <i data-lucide="star" class="h-4 w-4 fill-current"></i>
                                <i data-lucide="star" class="h-4 w-4 fill-current"></i>
                                <i data-lucide="star" class="h-4 w-4 fill-current"></i>
                                <i data-lucide="star-half" class="h-4 w-4 fill-current"></i>
                            </div>
                            <p class="text-xs text-gray-600">(18 avaliações)</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quick Actions -->
            <section class="py-8">
                <div class="container mx-auto px-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="dashboard-card relative overflow-hidden bg-white rounded-xl shadow-lg p-6 text-center">
                            <a href="../paginas/abas_menu_principal/aba_empresas.php">
                                <i data-lucide="ambulance" class="h-10 w-10 mx-auto text-teal-500 mb-3"></i>
                                <h5 class="text-lg font-semibold text-blue-900 mb-2">Agendar Transporte</h5>
                                <p class="text-sm text-gray-600 mb-4">Agende seu transporte médico com antecedência</p>
                                <div class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-2 px-4 rounded-lg transition-all hover:scale-105">
                                    Agendar Agora
                                </div>
                            </a>
                        </div>

                        <div class="dashboard-card relative overflow-hidden bg-white rounded-xl shadow-lg p-6 text-center">
                            <a href="historico.php">
                                <i data-lucide="clock" class="h-10 w-10 mx-auto text-teal-500 mb-3"></i>
                                <h5 class="text-lg font-semibold text-blue-900 mb-2">Histórico Completo</h5>
                                <p class="text-sm text-gray-600 mb-4">Veja todos seus transportes realizados</p>
                                <div class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-2 px-4 rounded-lg transition-all hover:scale-105">
                                    Acessar Histórico
                                </div>
                            </a>
                        </div>

                        <div class="dashboard-card relative overflow-hidden bg-white rounded-xl shadow-lg p-6 text-center">
                            <a href="">
                                <i data-lucide="star" class="h-10 w-10 mx-auto text-teal-500 mb-3"></i>
                                <h5 class="text-lg font-semibold text-blue-900 mb-2">Empresas Favoritas</h5>
                                <p class="text-sm text-gray-600 mb-4">Gerencie suas empresas preferidas</p>
                                <div class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-2 px-4 rounded-lg transition-all hover:scale-105">
                                    Ver Favoritos
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Upcoming Transports -->
            <section class="py-6">
                <div class="container mx-auto px-4">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h4 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                            <i data-lucide="calendar" class="h-5 w-5 mr-2 text-teal-500"></i>
                            Agendamento Mais Recente
                        </h4>
                        <div class="border rounded-lg overflow-hidden">
                            <div class="p-4 border-b flex flex-col md:flex-row md:items-center md:justify-between">
                                <?php
                                // Determina a classe de estilo com base na situação do último agendamento
                                $statusClass = '';
                                $ultimoAgendamento = $ultimasMensagens[0] ?? null; // Pega o último agendamento, se existir
                                if (!is_null($ultimoAgendamento)) {

                                    if ($ultimoAgendamento['situacao'] == 'Agendado') {
                                        $statusClass = 'bg-yellow-500 text-black';
                                    } elseif ($ultimoAgendamento['situacao'] == 'Concluido') {
                                        $statusClass = 'bg-green-500 text-white';
                                    } elseif ($ultimoAgendamento['situacao'] == 'Cancelado') {
                                        $statusClass = 'bg-red-500 text-white';
                                    } else {
                                        $statusClass = 'bg-gray-500 text-white';
                                    }
                                }
                                ?>

                                <!-- Exibição do Último Agendamento -->
                                <div class="p-4 border-b flex flex-col text-lg font-large md:flex-row md:items-center md:justify-between">
                                    <?php if (!empty($ultimoAgendamento)): ?>
                                        <div>
                                            <h5 class="font-semibold text-blue-900">Último Agendamento</h5>
                                            <p class="text-gray-600 text-sm">
                                                <?= date('d/m/Y', strtotime($ultimoAgendamento['data_consulta'])) ?> - <?= date('H:i', strtotime($ultimoAgendamento['horario'])) ?>
                                            </p>
                                            <p class="text-gray-600 text-sm">
                                                <?= htmlspecialchars($ultimoAgendamento['rua_destino']) ?>, <?= htmlspecialchars($ultimoAgendamento['cidade_destino']) ?>
                                            </p>
                                        </div>
                                        <div class="mt-4 md:mt-0 md:ml-6">
                                            <span class="inline-block px-2 py-1 rounded-full <?= $statusClass ?>">
                                                <?= htmlspecialchars($ultimoAgendamento['situacao']) ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <div>
                                            <p class="font-bold text-blue-900">Nenhum agendamento recente encontrado.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Health Information -->
            <section class="py-6">
                <div class="container mx-auto px-4 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h4 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                                <i data-lucide="file-text" class="h-5 w-5 mr-2 text-teal-500"></i>
                                Informações Médicas
                            </h4>
                            <ul class="divide-y">
                                <li class="py-3 text-gray-700">Tipo Sanguíneo: <span class="font-medium">O+</span></li>
                                <li class="py-3 text-gray-700">Alergias: <span class="font-medium">Nenhuma</span></li>
                                <li class="py-3 text-gray-700">Medicação Regular: <span class="font-medium">Não</span></li>
                            </ul>
                        </div>

                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h4 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                                <i data-lucide="message-square" class="h-5 w-5 mr-2 text-teal-500"></i>
                                Últimas Mensagens
                            </h4>
                            <div class="space-y-3">
                                <?php if (!empty($ultimasMensagens)): ?>
                                    <?php foreach ($ultimasMensagens as $mensagem): ?>
                                        <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded">
                                            <p class="font-semibold text-blue-900">
                                                <?= empty($mensagem['observacoes']) ? 'Sem mensagens nesse agendamento' : htmlspecialchars($mensagem['observacoes']) ?>
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                Destino: <?= htmlspecialchars($mensagem['rua_destino']) ?>, <?= htmlspecialchars($mensagem['cidade_destino']) ?>
                                            </p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="bg-gray-50 border-l-4 border-gray-500 p-3 rounded">
                                        <p class="font-semibold text-gray-700">Nenhuma mensagem encontrada.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
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
</body>

</html>