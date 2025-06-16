<?php
require '../includes/classe_usuario.php'; // inclui o arquivo de validação de login
require '../includes/conexao_BdAgendamento.php'; // inclui o arquivo de conexão com o banco de dados

use usuario\Usuario;

Usuario::verificarPermissao('cliente'); // verifica se o usuário logado é um cliente

// Busca o próximo agendamento agendado mais próximo para o usuário logado 
    
$query = "SELECT data_consulta, horario, rua_destino, cidade_destino, situacao 
          FROM agendamentos 
          WHERE cliente_id = :cliente_id AND data_consulta >= CURDATE() AND situacao = 'Agendado'
          ORDER BY data_consulta ASC, horario ASC 
          LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(':cliente_id', $_SESSION['usuario']['id'], PDO::PARAM_INT);
$stmt->execute();
$proximoTransporte = $stmt->fetch(PDO::FETCH_ASSOC);


// Consulta para buscar o número total de agendamentos concluídos do usuário
$queryConcluidos = "SELECT COUNT(*) AS total_concluidos 
                    FROM agendamentos 
                    WHERE cliente_id = :cliente_id AND situacao = 'Concluído'";
$stmtConcluidos = $conn->prepare($queryConcluidos);
$stmtConcluidos->bindParam(':cliente_id', $_SESSION['usuario']['id'], PDO::PARAM_INT);
$stmtConcluidos->execute();
$totalConcluidos = $stmtConcluidos->fetch(PDO::FETCH_ASSOC)['total_concluidos'];


// Consulta para buscar o número de agendamentos confirmados no mês atual
$queryConfirmadosMes = "SELECT COUNT(*) AS total_confirmados_mes 
                        FROM agendamentos 
                        WHERE cliente_id = :cliente_id AND situacao = 'Agendado' 
                        AND DATE_FORMAT(data_consulta, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')";
$stmtConfirmadosMes = $conn->prepare($queryConfirmadosMes);
$stmtConfirmadosMes->bindParam(':cliente_id', $_SESSION['usuario']['id'], PDO::PARAM_INT);
$stmtConfirmadosMes->execute();
$totalConfirmadosMes = $stmtConfirmadosMes->fetch(PDO::FETCH_ASSOC)['total_confirmados_mes'];


// Consulta para buscar os dois últimos registros de agendamento do usuário logado
$queryMensagens = "SELECT data_consulta, horario, observacoes, rua_destino, cidade_destino, situacao
                   FROM agendamentos 
                   WHERE cliente_id = :cliente_id 
                   ORDER BY id DESC 
                   LIMIT 2";
$stmtMensagens = $conn->prepare($queryMensagens);
$stmtMensagens->bindParam(':cliente_id', $_SESSION['usuario']['id'], PDO::PARAM_INT);
$stmtMensagens->execute();
$ultimasMensagens = $stmtMensagens->fetchAll(PDO::FETCH_ASSOC);

require '../includes/conexao_BdCadastroLogin.php'; // Inclui a conexão com o banco de dados

$stmtmedico = $conn->prepare("SELECT alergias, doencas_cronicas, remedio_recorrente FROM detalhe_medico WHERE id_cliente = :id");
$stmtmedico->bindParam(':id', $_SESSION['usuario']['id'], PDO::PARAM_INT);
$stmtmedico->execute();
$detalhesMedicos = $stmtmedico->fetch(PDO::FETCH_ASSOC);


$conn = null;


// Verifica se há mensagens de sucesso ou erro na sessão
$mensagemSucesso = $_SESSION['sucesso'] ?? null;
$mensagemErro = $_SESSION['erro'] ?? null;

// Remove as mensagens da sessão
unset($_SESSION['sucesso'], $_SESSION['erro']);
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
    <link rel="stylesheet" href="style/style_menu_principal.css">
</head>

<body class="min-h-screen bg-gray-50">
    <!-- modal mensagens -->
    <?php if ($mensagemSucesso || $mensagemErro): ?>
        <div id="modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-96">
                <h2 class="text-lg font-bold mb-4 <?= $mensagemSucesso ? 'text-green-600' : 'text-red-600' ?>">
                    <?= $mensagemSucesso ? 'Sucesso' : 'Erro' ?>
                </h2>
                <p class="text-gray-700 mb-4">
                    <?= htmlspecialchars($mensagemSucesso ?? $mensagemErro) ?>
                </p>
                <button id="close-modal" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg">
                    Fechar
                </button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="" class="flex items-center space-x-2 text-xl font-bold">
                    <i data-lucide="ambulance" class="h-6 w-6"></i>
                    <span>MedCar</span>
                </a>

                <div class="flex items-center space-x-6">
                    <div class="relative group">
                        <button class="flex items-center space-x-1 font-medium hover:text-teal-300 transition">
                            <i data-lucide="user" class="h-5 w-5"></i>
                            <span>Perfil</span>
                            <i data-lucide="chevron-down" class="h-4 w-4"></i>
                        </button>
                        <div class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 invisible group-hover:visible transition-all duration-300 opacity-0 group-hover:opacity-100 transform group-hover:translate-y-0 translate-y-2">
                            <div class="py-1">
                                <a href="perfil_cliente.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="user" class="h-4 w-4 inline mr-2"></i>Minha Conta
                                </a>
                                <a href="../paginas/pesquisar_empresa.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="calendar" class="h-4 w-4 inline mr-2"></i>Agendar
                                </a>
                                <a href="historico.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="clock" class="h-4 w-4 inline mr-2"></i>Meus Agendamentos
                                </a>
                                <div class="border-t border-gray-300"></div>
                                <a href="../includes/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i data-lucide="log-out" class="h-4 w-4 inline mr-2"></i>Sair
                                </a>
                            </div>
                        </div>
                    </div>
                    <button id="mobile-menu-button" class="md:hidden text-white ml-2">
                        <i data-lucide="menu" class="h-6 w-6"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="fixed inset-0 z-50 bg-blue-900 bg-opacity-95 flex flex-col text-white p-6 hidden overflow-y-auto">
        <div class="flex justify-end">
            <button id="close-menu-button" class="text-white">
                <i data-lucide="x" class="h-6 w-6"></i>
            </button>
        </div>
        <!-- Adicionando itens do menu lateral -->
        <a href="#" class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-blue-800 text-white hover:bg-blue-700 transition">
            <i data-lucide="home" class="h-5 w-5"></i>
            <span>Dashboard</span>
        </a>
        <a href="../paginas/pesquisar_empresa.php" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
            <i data-lucide="calendar" class="h-5 w-5"></i>
            <span>Agendar</span>
        </a>
        <a href="pagar_agendamento.php" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
            <i data-lucide="banknote-arrow-up" class="h-5 w-5"></i>
            <span>Pagar Agendamento</span>
        </a>
        <a href="historico.php" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
            <i data-lucide="clock" class="h-5 w-5"></i>
            <span>Histórico</span>
        </a>
        <a href="#" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
            <i data-lucide="heart" class="h-5 w-5"></i>
            <span>Favoritos</span>
        </a>
        <div class="relative">
            <button id="mobile-dropdown-button" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition focus:outline-none">
                <i data-lucide="settings" class="h-5 w-5"></i>
                <span>Configurações</span>
                <i data-lucide="chevron-down" class="h-5 w-5"></i>
            </button>
            <div id="mobile-dropdown-menu" class="hidden bg-white text-blue-900 rounded-lg shadow-lg mt-2 w-48">
                <a href="perfil_cliente.php" class="block px-4 py-2 hover:bg-gray-100">Editar Cadastro</a>
                <a href="#" class="block px-4 py-2 hover:bg-gray-100">Alterar Senha</a>
            </div>
        </div>
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
                <a href="../paginas/pesquisar_empresa.php" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
                    <i data-lucide="calendar" class="h-5 w-5"></i>
                    <span>Agendar</span>
                </a>
                <a href="pagar_agendamento.php" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
            <i data-lucide="banknote-arrow-up" class="h-5 w-5"></i>
            <span>Pagar Agendamento</span>
        </a>
                <!-- comentei caso esse dash não de para montrar amanha -->

                <!-- <a href="PowerBiClientes.php" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
                <i data-lucide="user" class="h-5 w-5"></i>
                    <span>Dashboard Clientes</span>
                </a> -->
                <a href="historico.php" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
                    <i data-lucide="clock" class="h-5 w-5"></i>
                    <span>Histórico</span>
                </a>
                <a href="#" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
                    <i data-lucide="heart" class="h-5 w-5"></i>
                    <span>Favoritos</span>
                </a>
                <div class="relative">
                    <button id="dropdown-button" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition focus:outline-none">
                        <i data-lucide="settings" class="h-5 w-5"></i>
                        <span>Configurações</span>
                        <i data-lucide="chevron-down" class="h-5 w-5"></i>
                    </button>
                    <div id="dropdown-menu" class="absolute hidden bg-white text-blue-900 rounded-lg shadow-lg mt-2 w-48">
                        <a href="editar_cliente.php" class="block px-4 py-2 hover:bg-gray-100">Editar Cadastro</a>
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
                            <a id="box_agendar" href="../paginas/pesquisar_empresa.php">
                                <i data-lucide="ambulance" class="h-10 w-10 mx-auto text-teal-500 mb-3"></i>
                                <h5 class="text-lg font-semibold text-blue-900 mb-2">Agendar Transporte</h5>
                                <p class="text-sm text-gray-600 mb-4">Agende seu transporte médico com antecedência</p>
                                <div class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-2 px-4 rounded-lg transition-all hover:scale-105">
                                    Agendar Agora
                                </div>
                            </a>
                        </div>

                        <div class="dashboard-card relative overflow-hidden bg-white rounded-xl shadow-lg p-6 text-center">
                            <a id="box_historico" href="historico.php">
                                <i data-lucide="clock" class="h-10 w-10 mx-auto text-teal-500 mb-3"></i>
                                <h5 class="text-lg font-semibold text-blue-900 mb-2">Histórico Completo</h5>
                                <p class="text-sm text-gray-600 mb-4">Veja todos seus transportes realizados</p>
                                <div class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-2 px-4 rounded-lg transition-all hover:scale-105">
                                    Acessar Histórico
                                </div>
                            </a>
                        </div>

                        <div class="dashboard-card relative overflow-hidden bg-white rounded-xl shadow-lg p-6 text-center">
                            <a id="box_favoritos" href="">
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
                                <li class="py-3 text-gray-700">Alergias: <span class="font-medium"><?= $detalhesMedicos['alergias'] ?? 'não cadastrado'?></span></li>
                                <li class="py-3 text-gray-700">Doença cronica: <span class="font-medium"><?= $detalhesMedicos['doencas_cronicas'] ?? 'não cadastrado' ?></span></li>
                                <li class="py-3 text-gray-700">Medicação Regular: <span class="font-medium"><?= $detalhesMedicos['remedio_recorrente'] ?? 'não cadastrado'?></span></li>
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
        const desktopMenu = document.getElementById('desktop-menu');
        const mobileDropdownButton = document.getElementById('mobile-dropdown-button');
        const mobileDropdownMenu = document.getElementById('mobile-dropdown-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.remove('hidden');
            desktopMenu.classList.add('invisible');
        });

        closeMenuButton.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
            desktopMenu.classList.remove('invisible');
        });

        mobileDropdownButton.addEventListener('click', () => {
            mobileDropdownMenu.classList.toggle('hidden');
        });
    </script>

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
    <script>
        // Inicializa os ícones do Lucide
        lucide.createIcons();

        
        // Fecha o modal de avisos ao clicar no botão "Fechar"
        const closeModalButton = document.getElementById('close-modal');
        const modal = document.getElementById('modal');

        if (closeModalButton) {
            closeModalButton.addEventListener('click', () => {
                modal.style.display = 'none';
            });
        }
    </script>
</body>

</html>