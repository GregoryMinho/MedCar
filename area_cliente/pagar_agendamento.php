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
</head>

<body class="min-h-screen bg-gray-50">
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="menu_principal.php" class="flex items-center space-x-2 text-white hover:text-teal-300 transition cursor-pointer">
                        <i data-lucide="arrow-left" class="h-6 w-6"></i>
                        <span class="hidden sm:inline">Voltar</span>
                    </a>
                </div>
                <a href="menu_principal.php" class="flex items-center space-x-2 text-xl font-bold">
                    <i data-lucide="ambulance" class="h-6 w-6"></i>
                    <span>MedCar</span>
                </a>
                <div class="hidden md:flex space-x-6">
                    <a href="historico.php" class="font-medium hover:text-teal-300 transition">Histórico</a>
                    <a href="../includes/logout.php" class="font-medium hover:text-teal-300 transition">Sair</a>
                </div>
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-white hover:text-teal-300">
                        <i data-lucide="menu" class="h-6 w-6"></i>
                    </button>
                </div>
            </div>
        </div>
        <div id="mobile-menu" class="hidden bg-blue-800 md:hidden">
            <div class="container mx-auto px-4 py-2">
                <a href="historico.php" class="block py-2 text-white hover:text-teal-300">Histórico</a>
                <a href="../includes/logout.php" class="block py-2 text-white hover:text-teal-300">Sair</a>
            </div>
        </div>
    </nav>

    <section class="pt-24 pb-10">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-xl shadow-lg p-4 md:p-8">
                <h1 class="text-2xl font-bold text-blue-900 mb-6">Pagar Agendamentos</h1>

                <?php if (isset($_GET['status']) && $_GET['status'] === 'success') : ?>
                    <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg mb-6 flex items-start">
                        <i data-lucide="check-circle" class="h-5 w-5 mr-2 mt-0.5"></i>
                        <div>
                            <h3 class="font-bold">Pagamento realizado com sucesso!</h3>
                            <p>Seu agendamento foi confirmado. Você pode verificar os detalhes no histórico.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['status']) && $_GET['status'] === 'error') : ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg mb-6 flex items-start">
                        <i data-lucide="alert-circle" class="h-5 w-5 mr-2 mt-0.5"></i>
                        <div>
                            <h3 class="font-bold">Erro no pagamento</h3>
                            <p>Houve um problema ao processar seu pagamento. Por favor, tente novamente.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($agendamento_selecionado) : ?>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-white border rounded-lg shadow-sm p-4 md:p-6">
                            <h2 class="text-xl font-bold text-blue-900 mb-4">Detalhes do Agendamento</h2>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Data</p>
                                    <p class="text-lg"><?= date("d/m/Y", strtotime($agendamento_selecionado['data_consulta'])) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Horário</p>
                                    <p class="text-lg"><?= date("H:i", strtotime($agendamento_selecionado['horario'])) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Destino</p>
                                    <p class="text-lg"><?= htmlspecialchars($agendamento_selecionado['rua_destino']) ?>, <?= htmlspecialchars($agendamento_selecionado['cidade_destino']) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Empresa</p>
                                    <p class="text-lg"><?= htmlspecialchars($agendamento_selecionado['empresa_nome']) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Valor</p>
                                    <p class="text-2xl font-bold text-blue-900">R$ <?= number_format($agendamento_selecionado['valor'], 2, ',', '.') ?></p>
                                </div>
                            </div>
                            <div class="mt-6">
                                <a href="pagar_agendamento.php" class="inline-block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg transition">
                                    Voltar
                                </a>
                            </div>
                        </div>

                        <div class="bg-white border rounded-lg shadow-sm p-4 md:p-6">
                            <h2 class="text-xl font-bold text-blue-900 mb-4">Pagamento</h2>
                            <div class="mb-6">
                                <div class="flex border-b">
                                    <button id="tab-checkout" class="py-2 px-4 font-medium border-b-2 border-blue-500 text-blue-600 flex items-center">
                                        <i data-lucide="credit-card" class="h-8 w-8 mr-2"></i> Checkout
                                    </button>
                                    <button id="tab-pix" class="py-2 px-4 font-medium text-gray-500 hover:text-blue-600 flex items-center">
                                        <i data-lucide="qr-code" class="h-8 w-8 mr-2"></i> Pix
                                    </button>
                                </div>
                            </div>

                            <div id="payment-loading" class="flex flex-col items-center justify-center p-8">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-900 mb-4"></div>
                                <p class="text-gray-600">Carregando opções de pagamento...</p>
                            </div>

                            <div id="checkout-container" class="hidden">
                                <div id="checkout-bricks-container"></div>
                            </div>

                            <div id="pix-container" class="hidden">
                                <div id="pix-bricks-container"></div>
                            </div>

                            <div id="payment-error" class="hidden bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg mt-4">
                                <p class="font-bold">Erro ao processar o pagamento</p>
                                <p id="payment-error-message">Por favor, tente novamente mais tarde.</p>
                                <button onclick="window.location.reload()" class="mt-2 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition">
                                    Tentar novamente
                                </button>
                            </div>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 p-4 rounded-lg mb-6 flex items-start">
                        <i data-lucide="alert-triangle" class="h-5 w-5 mr-2 mt-0.5"></i>
                        <p>Nenhum agendamento pendente de pagamento encontrado.</p>
                        <a href="menu_principal.php" class="mt-4 inline-block bg-blue-900 hover:bg-blue-800 text-white font-medium py-2 px-4 rounded-lg transition">
                            Voltar ao Menu Principal
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

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

        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

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
        });

        tabPix.addEventListener('click', () => {
            showPixContainer();
            hideCheckoutContainer();
            tabPix.classList.add('border-b-2', 'border-blue-500', 'text-blue-600');
            tabCheckout.classList.remove('border-b-2', 'border-blue-500', 'text-blue-600');
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
    </script>
</body>

</html>