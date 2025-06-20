<?php
require '../includes/conexao_BdAgendamento.php';
require '../includes/classe_usuario.php';
require_once '../vendor/autoload.php';

// Carregar variáveis de ambiente
try {
    $dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
    $dotenv->load();
} catch (Exception $e) {
    // Continuar mesmo se o .env não for encontrado
}

use usuario\Usuario;

Usuario::verificarPermissao('cliente');

$usuario_id = $_SESSION['usuario']['id'];

// Consulta agendamentos com situação "agendado"
$query = "SELECT a.id, a.data_consulta, a.horario, a.valor, a.rua_destino, a.cidade_destino, e.nome as empresa_nome 
        FROM agendamentos a 
        INNER JOIN medcar_cadastro_login.empresas e ON a.empresa_id = e.id 
        WHERE a.cliente_id = :id AND a.situacao = 'agendado'";
$stmt = $conn->prepare($query);
$stmt->bindParam(":id", $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verifica se um agendamento específico foi selecionado
$agendamento_selecionado = null;
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $agendamento_id = $_GET['id'];
    $query = "SELECT a.id, a.data_consulta, a.horario, a.valor, a.rua_destino, a.cidade_destino, e.nome as empresa_nome 
            FROM agendamentos a 
            INNER JOIN medcar_cadastro_login.empresas e ON a.empresa_id = e.id 
            WHERE a.id = :id AND a.cliente_id = :cliente_id AND a.situacao = 'agendado'";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":id", $agendamento_id, PDO::PARAM_INT);
    $stmt->bindParam(":cliente_id", $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    $agendamento_selecionado = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Busca informações do cliente para o pagamento
$query_cliente = "SELECT nome, email FROM medcar_cadastro_login.clientes WHERE id = :id";
$stmt_cliente = $conn->prepare($query_cliente);
$stmt_cliente->bindParam(":id", $usuario_id, PDO::PARAM_INT);
$stmt_cliente->execute();
$cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);

// Configurações do Mercado Pago
$mercadopago_public_key = getenv('MERCADO_PAGO_PUBLIC_KEY');

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
    <title>MedCar - Pagar Agendamentos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <link rel="stylesheet" href="style/style_menu_principal.css">
</head>

<body class="min-h-screen bg-gray-50">
    <!-- Modal mensagens -->
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
                <a href="menu_principal.php" class="flex items-center space-x-2 text-xl font-bold">
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
        <a href="menu_principal.php" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
            <i data-lucide="home" class="h-5 w-5"></i>
            <span>Dashboard</span>
        </a>
        <a href="../paginas/pesquisar_empresa.php" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
            <i data-lucide="calendar" class="h-5 w-5"></i>
            <span>Agendar</span>
        </a>
        <a href="pagar_agendamento.php" class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-blue-800 text-white hover:bg-blue-700 transition">
            <i data-lucide="credit-card" class="h-5 w-5"></i>
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

    <!-- Main Content -->
    <div class="flex flex-col md:flex-row">
        <!-- Sidebar -->
        <div class="hidden md:block w-64 bg-blue-900 text-white min-h-screen pt-24 px-4">
            <nav class="flex flex-col space-y-2">
                <a href="menu_principal.php" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
                    <i data-lucide="home" class="h-5 w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="../paginas/pesquisar_empresa.php" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
                    <i data-lucide="calendar" class="h-5 w-5"></i>
                    <span>Agendar</span>
                </a>
                <a href="pagar_agendamento.php" class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-blue-800 text-white hover:bg-blue-700 transition">
                    <i data-lucide="credit-card" class="h-5 w-5"></i>
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
            <section class="pt-24 pb-8 bg-gradient-to-r from-blue-900 to-blue-800 text-white">
                <div class="container mx-auto px-4">
                    <div class="flex items-center mb-6">
                        <a href="menu_principal.php" class="flex items-center space-x-2 text-white hover:text-teal-300 transition mr-4">
                            <i data-lucide="arrow-left" class="h-6 w-6"></i>
                            <span class="hidden sm:inline">Voltar</span>
                        </a>
                        <h1 class="text-3xl md:text-4xl font-bold">Pagar Agendamentos</h1>
                    </div>

                    <!-- Status Messages -->
                    <?php if (isset($_GET['status']) && $_GET['status'] === 'success') : ?>
                        <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg mb-6 flex items-start">
                            <i data-lucide="check-circle" class="h-5 w-5 mr-2 mt-0.5 text-green-600"></i>
                            <div>
                                <h3 class="font-bold">Pagamento realizado com sucesso!</h3>
                                <p>Seu agendamento foi confirmado. Você pode verificar os detalhes no histórico.</p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['status']) && $_GET['status'] === 'error') : ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg mb-6 flex items-start">
                            <i data-lucide="alert-circle" class="h-5 w-5 mr-2 mt-0.5 text-red-600"></i>
                            <div>
                                <h3 class="font-bold">Erro no pagamento</h3>
                                <p>Houve um problema ao processar seu pagamento. Por favor, tente novamente.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Payment Content -->
            <section class="py-8">
                <div class="container mx-auto px-4">
                    <?php if ($agendamento_selecionado) : ?>
                        <!-- Payment Details -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Agendamento Details Card -->
                            <div class="dashboard-card relative overflow-hidden bg-white rounded-xl shadow-lg p-6">
                                <h2 class="text-xl font-bold text-blue-900 mb-6 flex items-center">
                                    <i data-lucide="calendar-check" class="h-6 w-6 mr-2 text-teal-500"></i>
                                    Detalhes do Agendamento
                                </h2>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                        <span class="text-gray-600 font-medium">Data:</span>
                                        <span class="text-blue-900 font-semibold"><?= date("d/m/Y", strtotime($agendamento_selecionado['data_consulta'])) ?></span>
                                    </div>
                                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                        <span class="text-gray-600 font-medium">Horário:</span>
                                        <span class="text-blue-900 font-semibold"><?= date("H:i", strtotime($agendamento_selecionado['horario'])) ?></span>
                                    </div>
                                    <div class="flex justify-between items-start py-3 border-b border-gray-100">
                                        <span class="text-gray-600 font-medium">Destino:</span>
                                        <span class="text-blue-900 font-semibold text-right"><?= htmlspecialchars($agendamento_selecionado['rua_destino']) ?>, <?= htmlspecialchars($agendamento_selecionado['cidade_destino']) ?></span>
                                    </div>
                                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                        <span class="text-gray-600 font-medium">Empresa:</span>
                                        <span class="text-blue-900 font-semibold"><?= htmlspecialchars($agendamento_selecionado['empresa_nome']) ?></span>
                                    </div>
                                    <div class="flex justify-between items-center py-4 bg-blue-50 rounded-lg px-4">
                                        <span class="text-blue-900 font-bold text-lg">Valor Total:</span>
                                        <span class="text-blue-900 font-bold text-2xl">R$ <?= number_format($agendamento_selecionado['valor'], 2, ',', '.') ?></span>
                                    </div>
                                </div>
                                <div class="mt-6">
                                    <a href="pagar_agendamento.php" class="inline-block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-4 rounded-lg transition">
                                        <i data-lucide="arrow-left" class="h-4 w-4 inline mr-2"></i>
                                        Voltar à Lista
                                    </a>
                                </div>
                            </div>

                            <!-- Payment Methods Card -->
                            <div class="dashboard-card relative overflow-hidden bg-white rounded-xl shadow-lg p-6">
                                <h2 class="text-xl font-bold text-blue-900 mb-6 flex items-center">
                                    <i data-lucide="credit-card" class="h-6 w-6 mr-2 text-teal-500"></i>
                                    Métodos de Pagamento
                                </h2>
                                
                                <!-- Payment Tabs -->
                                <div class="mb-6">
                                    <div class="flex border-b border-gray-200">
                                        <button id="tab-checkout" class="py-3 px-4 font-medium border-b-2 border-blue-500 text-blue-600 flex items-center">
                                            <i data-lucide="credit-card" class="h-5 w-5 mr-2"></i> 
                                            Cartão
                                        </button>
                                        <button id="tab-pix" class="py-3 px-4 font-medium text-gray-500 hover:text-blue-600 flex items-center">
                                            <i data-lucide="qr-code" class="h-5 w-5 mr-2"></i> 
                                            Pix
                                        </button>
                                    </div>
                                </div>

                                <!-- Loading State -->
                                <div id="payment-loading" class="flex flex-col items-center justify-center p-8">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-900 mb-4"></div>
                                    <p class="text-gray-600">Carregando opções de pagamento...</p>
                                </div>

                                <!-- Payment Containers -->
                                <div id="checkout-container" class="hidden">
                                    <div id="checkout-bricks-container"></div>
                                </div>

                                <div id="pix-container" class="hidden">
                                    <div id="pix-bricks-container"></div>
                                </div>

                                <!-- Error State -->
                                <div id="payment-error" class="hidden bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg mt-4">
                                    <div class="flex items-start">
                                        <i data-lucide="alert-circle" class="h-5 w-5 mr-2 mt-0.5"></i>
                                        <div>
                                            <p class="font-bold">Erro ao processar o pagamento</p>
                                            <p id="payment-error-message">Por favor, tente novamente mais tarde.</p>
                                            <button onclick="window.location.reload()" class="mt-2 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition">
                                                Tentar novamente
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php elseif (count($agendamentos) > 0) : ?>
                        <!-- Lista de agendamentos disponíveis -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <div class="mb-6">
                                <h2 class="text-2xl font-bold text-blue-900 mb-2 flex items-center">
                                    <i data-lucide="list" class="h-6 w-6 mr-2 text-teal-500"></i>
                                    Agendamentos Disponíveis
                                </h2>
                                <p class="text-gray-600">Selecione um agendamento abaixo para realizar o pagamento:</p>
                            </div>

                            <!-- Desktop Table -->
                            <div class="hidden lg:block overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
                                    <thead class="bg-blue-900 text-white">
                                        <tr>
                                            <th class="py-4 px-6 text-left font-semibold">Data</th>
                                            <th class="py-4 px-6 text-left font-semibold">Horário</th>
                                            <th class="py-4 px-6 text-left font-semibold">Destino</th>
                                            <th class="py-4 px-6 text-left font-semibold">Empresa</th>
                                            <th class="py-4 px-6 text-left font-semibold">Valor</th>
                                            <th class="py-4 px-6 text-center font-semibold">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($agendamentos as $agendamento) : ?>
                                            <tr class="border-b hover:bg-gray-50 transition">
                                                <td class="py-4 px-6"><?= date("d/m/Y", strtotime($agendamento['data_consulta'])) ?></td>
                                                <td class="py-4 px-6"><?= date("H:i", strtotime($agendamento['horario'])) ?></td>
                                                <td class="py-4 px-6"><?= htmlspecialchars($agendamento['cidade_destino']) ?></td>
                                                <td class="py-4 px-6"><?= htmlspecialchars($agendamento['empresa_nome']) ?></td>
                                                <td class="py-4 px-6 font-bold text-blue-900">R$ <?= number_format($agendamento['valor'], 2, ',', '.') ?></td>
                                                <td class="py-4 px-6 text-center">
                                                    <a href="pagar_agendamento.php?id=<?= $agendamento['id'] ?>" class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-2 px-6 rounded-lg inline-block transition hover:scale-105">
                                                        <i data-lucide="credit-card" class="h-4 w-4 inline mr-1"></i>
                                                        Pagar
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Cards -->
                            <div class="lg:hidden space-y-4">
                                <?php foreach ($agendamentos as $agendamento) : ?>
                                    <div class="dashboard-card relative overflow-hidden bg-white border rounded-xl shadow-sm p-4">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <p class="text-sm font-medium text-gray-500">Data e Hora</p>
                                                <p class="text-lg font-semibold text-blue-900"><?= date("d/m/Y", strtotime($agendamento['data_consulta'])) ?> às <?= date("H:i", strtotime($agendamento['horario'])) ?></p>
                                            </div>
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Agendado</span>
                                        </div>
                                        <div class="mb-3">
                                            <p class="text-sm font-medium text-gray-500">Destino</p>
                                            <p class="text-base text-blue-900"><?= htmlspecialchars($agendamento['cidade_destino']) ?></p>
                                        </div>
                                        <div class="mb-3">
                                            <p class="text-sm font-medium text-gray-500">Empresa</p>
                                            <p class="text-base text-blue-900"><?= htmlspecialchars($agendamento['empresa_nome']) ?></p>
                                        </div>
                                        <div class="mb-4">
                                            <p class="text-sm font-medium text-gray-500">Valor</p>
                                            <p class="text-xl font-bold text-blue-900">R$ <?= number_format($agendamento['valor'], 2, ',', '.') ?></p>
                                        </div>
                                        <a href="pagar_agendamento.php?id=<?= $agendamento['id'] ?>" class="w-full bg-teal-500 hover:bg-teal-600 text-white font-medium py-3 px-4 rounded-lg inline-block text-center transition hover:scale-105">
                                            <i data-lucide="credit-card" class="h-4 w-4 inline mr-2"></i>
                                            Pagar Agendamento
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    <?php else : ?>
                        <!-- Empty State -->
                        <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                            <div class="mb-6">
                                <i data-lucide="calendar-x" class="h-16 w-16 mx-auto text-gray-400 mb-4"></i>
                                <h2 class="text-2xl font-bold text-blue-900 mb-2">Nenhum agendamento pendente</h2>
                                <p class="text-gray-600 text-lg">Não há agendamentos disponíveis para pagamento no momento.</p>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <a href="../paginas/pesquisar_empresa.php" class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-3 px-6 rounded-lg transition hover:scale-105">
                                    <i data-lucide="calendar-plus" class="h-4 w-4 inline mr-2"></i>
                                    Fazer Novo Agendamento
                                </a>
                                <a href="menu_principal.php" class="bg-blue-900 hover:bg-blue-800 text-white font-medium py-3 px-6 rounded-lg transition hover:scale-105">
                                    <i data-lucide="home" class="h-4 w-4 inline mr-2"></i>
                                    Voltar ao Dashboard
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-blue-900 text-white py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <a href="#" class="flex items-center space-x-2 text-xl font-bold">
                        <i data-lucide="ambulance" class="h-6 w-6"></i>
                        <span>MedCar</span>
                    </a>
                </div>
                <div class="text-center md:text-right">
                    <p class="text-blue-200">&copy; <?= date('Y') ?> MedCar. Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Mobile menu functionality
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeMenuButton = document.getElementById('close-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileDropdownButton = document.getElementById('mobile-dropdown-button');
        const mobileDropdownMenu = document.getElementById('mobile-dropdown-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.remove('hidden');
        });

        closeMenuButton.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
        });

        mobileDropdownButton.addEventListener('click', () => {
            mobileDropdownMenu.classList.toggle('hidden');
        });

        // Desktop dropdown functionality
        const dropdownButton = document.getElementById('dropdown-button');
        const dropdownMenu = document.getElementById('dropdown-menu');

        if (dropdownButton && dropdownMenu) {
            dropdownButton.addEventListener('click', () => {
                dropdownMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', (event) => {
                if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.add('hidden');
                }
            });
        }

        // Modal functionality
        const closeModalButton = document.getElementById('close-modal');
        const modal = document.getElementById('modal');

        if (closeModalButton) {
            closeModalButton.addEventListener('click', () => {
                modal.style.display = 'none';
            });
        }

        <?php if ($agendamento_selecionado) : ?>
        // Payment functionality
        const publicKey = '<?= $mercadopago_public_key ?>';
        const mp = new MercadoPago(publicKey);
        const agendamentoId = <?= $agendamento_selecionado ? $agendamento_selecionado['id'] : 'null' ?>;
        const valorAgendamento = <?= $agendamento_selecionado ? $agendamento_selecionado['valor'] : '0' ?>;
        const clienteNome = '<?= $cliente['nome'] ?? '' ?>';
        const clienteEmail = '<?= $cliente['email'] ?? '' ?>';

        const tabCheckout = document.getElementById('tab-checkout');
        const tabPix = document.getElementById('tab-pix');
        const checkoutContainer = document.getElementById('checkout-container');
        const pixContainer = document.getElementById('pix-container');
        const paymentLoading = document.getElementById('payment-loading');
        const paymentError = document.getElementById('payment-error');
        const paymentErrorMessage = document.getElementById('payment-error-message');

        tabCheckout.addEventListener('click', () => {
            showCheckoutContainer();
            hidePixContainer();
            tabCheckout.classList.add('border-b-2', 'border-blue-500', 'text-blue-600');
            tabPix.classList.remove('border-b-2', 'border-blue-500', 'text-blue-600');
            tabPix.classList.add('text-gray-500');
        });

        tabPix.addEventListener('click', () => {
            showPixContainer();
            hideCheckoutContainer();
            tabPix.classList.add('border-b-2', 'border-blue-500', 'text-blue-600');
            tabCheckout.classList.remove('border-b-2', 'border-blue-500', 'text-blue-600');
            tabCheckout.classList.add('text-gray-500');
        });

        function showCheckoutContainer() {
            checkoutContainer.classList.remove('hidden');
        }

        function hideCheckoutContainer() {
            checkoutContainer.classList.add('hidden');
        }

        function showPixContainer() {
            pixContainer.classList.remove('hidden');
        }

        function hidePixContainer() {
            pixContainer.classList.add('hidden');
        }

        function showPaymentLoading() {
            paymentLoading.classList.remove('hidden');
        }

        function hidePaymentLoading() {
            paymentLoading.classList.add('hidden');
        }

        function showPaymentError(message) {
            paymentError.classList.remove('hidden');
            paymentErrorMessage.textContent = message;
        }

        async function initializeCheckoutBricks() {
            try {
                const bricksBuilder = mp.bricks();

                // Initialize CardPayment Brick
                await bricksBuilder.create('cardPayment', 'checkout-bricks-container', {
                    initialization: {
                        amount: valorAgendamento,
                    },
                    callbacks: {
                        onReady: () => {
                            hidePaymentLoading();
                        },
                        onSubmit: async (formData) => {
                            try {
                                const response = await fetch('/area_cliente/actions/criar_preferencia.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        ...formData,
                                        agendamentoId,
                                        clienteNome,
                                        clienteEmail,
                                    }),
                                });

                                const result = await response.json();
                                if (result.status === 'approved') {
                                    window.location.href = `pagamento_sucesso.php?agendamento_id=${agendamentoId}`;
                                } else {
                                    showPaymentError(result.error_message || 'Pagamento não aprovado.');
                                }
                            } catch (error) {
                                showPaymentError('Erro ao processar o pagamento. Tente novamente.');
                            }
                        },
                        onError: (error) => {
                            showPaymentError('Erro ao inicializar o pagamento. Tente novamente.');
                        },
                    },
                });

                // Initialize Pix Brick
                await bricksBuilder.create('pix', 'pix-bricks-container', {
                    initialization: {
                        amount: valorAgendamento,
                    },
                    callbacks: {
                        onReady: () => {
                            hidePaymentLoading();
                        },
                        onSubmit: async (formData) => {
                            try {
                                const response = await fetch('/area_cliente/actions/criar_preferencia.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        ...formData,
                                        agendamentoId,
                                        clienteNome,
                                        clienteEmail,
                                    }),
                                });

                                const result = await response.json();
                                if (result.status === 'approved') {
                                    window.location.href = `pagamento_sucesso.php?agendamento_id=${agendamentoId}`;
                                } else {
                                    showPaymentError(result.error_message || 'Pagamento não aprovado.');
                                }
                            } catch (error) {
                                showPaymentError('Erro ao processar o pagamento. Tente novamente.');
                            }
                        },
                        onError: (error) => {
                            showPaymentError('Erro ao inicializar o pagamento. Tente novamente.');
                        },
                    },
                });
            } catch (error) {
                showPaymentError('Erro ao carregar os métodos de pagamento.');
            }
        }

        showPaymentLoading();
        initializeCheckoutBricks();
        <?php endif; ?>
    </script>
</body>

</html>