<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../vendor/autoload.php'; // Inclua o Composer autoloader



use Google\Client as GoogleClient;

// Carrega o arquivo client_secret.json
$clientSecretPath = '../client_secret.json';
$clientSecretData = json_decode(file_get_contents($clientSecretPath), true);

// Configurações do Google OAuth 2.0 usando os dados do client_secret.json
$client = new GoogleClient([
    'client_id' => $clientSecretData['web']['client_id'],
    'client_secret' => $clientSecretData['web']['client_secret'],
    'redirect_uri' => $clientSecretData['web']['redirect_uris'][0],
    'access_type' => 'offline',
    'scope' => ['email', 'profile'],
    'prompt' => 'consent'
]);

$authUrl = $client->createAuthUrl(['openid', 'email', 'profile']); // Escopos que você precisa

// Verifica se há mensagens de sucesso ou na sessão, para exibir no modal confirme no email
$mensagemSucesso = $_SESSION['sucesso'] ?? null;
$mensagemErro = $_SESSION['erro'] ?? null;

// Remove as mensagens da sessão
unset($_SESSION['sucesso']);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Login Cliente</title>
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
    </style>
</head>

<body class="min-h-screen bg-gradient-to-r from-blue-900 to-blue-800">
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="" class="flex items-center space-x-2 text-xl font-bold">
                    <i data-lucide="ambulance" class="h-6 w-6"></i>
                    <span>MedCar</span>
                </a>


                <div class="hidden md:flex space-x-6">
                    <a href="/MedCar/paginas/pagina_inicial.php" class="font-medium hover:text-teal-300 transition">Home</a>
                    <a href="#" class="font-medium hover:text-teal-300 transition">Contato</a>
                </div>

                <button id="mobile-menu-button" class="md:hidden text-white">
                    <i data-lucide="menu" class="h-6 w-6"></i>
                </button>
            </div>
        </div>
    </nav>

    <div id="mobile-menu" class="fixed inset-0 z-50 bg-blue-900 bg-opacity-95 flex flex-col text-white p-6 mobile-menu">
        <div class="flex justify-end">
            <button id="close-menu-button" class="text-white">
                <i data-lucide="x" class="h-6 w-6"></i>
            </button>
        </div>

        <div class="flex flex-col items-center justify-center space-y-8 flex-grow text-xl">
            <a href="/MedCar/paginas/pagina_inicial.php" class="font-medium hover:text-teal-300 transition">Home</a>
            <a href="#" class="font-medium hover:text-teal-300 transition">Contato</a>
        </div>
    </div>

    <section class="pt-32 pb-16">
        <!-- modal mensagens -->
        <?php if ($mensagemSucesso || $mensagemErro): ?>
            <div id="modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white rounded-lg shadow-lg p-6 w-96">
                    <h2 class="text-lg font-bold mb-4 'text-green-600' ">
                        <?= $mensagemSucesso ? 'Sucesso!' : 'Erro!' ?>
                    </h2>
                    <p class="text-gray-700 mb-4">
                        <?php echo $mensagemSucesso ?? $mensagemErro; ?>
                    </p>
                        <button id="close-modal" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg">
                            Fechar
                        </button>
                </div>
            </div>
        <?php endif; ?>

        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-teal-500 text-white p-8 text-center">
                    <h2 class="text-3xl font-bold mb-2">Olá! Você já é nosso cliente?</h2>
                    <p class="text-xl">Acesse sua conta para agendar seus transportes</p>
                </div>
                <div class="flex flex-col md:flex-row">
                    <div class="w-full md:w-1/2 p-8">
                        <form action="actions/action_login_cliente.php" method="post" id="login-form" class="space-y-6">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                                <input type="email" id="email" maxlength="50" name="email" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="seuemail@exemplo.com" required>
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                                <input type="password" id="password" name="password" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="••••••••" minlength="8" required>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input type="checkbox" id="remember-me" class="h-4 w-4 text-teal-500 focus:ring-teal-500 border-gray-300 rounded">
                                    <label for="remember-me" class="ml-2 block text-sm text-gray-700">Lembrar-me</label>
                                </div>
                                <a href="#" class="text-sm text-teal-500 hover:text-teal-600">Esqueceu a senha?</a>
                            </div>
                            <p class="text-m text-red-600">
                                <?php echo $_SESSION['login_erro'] ?? null; ?>
                            </p>
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-m font-medium text-white bg-teal-500 hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                                Entrar
                            </button>
                        </form>
                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600">
                                Não tem conta?
                                <a href="/MedCar/paginas/cadastro_cliente.php" class="font-medium text-teal-500 hover:text-teal-600">Cadastre-se aqui</a>
                            </p>
                        </div>
                    </div>
                    <div class="w-full md:w-1/2 bg-gray-50 p-8">
                        <h4 class="text-xl font-bold mb-4 text-gray-800">Benefícios de ser Cliente</h4>
                        <ul class="space-y-2 mb-8 border-l-4 border-teal-500 pl-4">
                            <li>Agendamento rápido e seguro</li>
                            <li>Histórico de transportes</li>
                            <li>Cupons de desconto para clientes frequentes</li>
                            <li>Suporte 24 horas</li>
                        </ul>

                        <div class="mt-8">
                            <p class="text-sm text-gray-600 mb-4">Entrar com o Google</p>
                            <div class="grid grid-cols-1">
                                <a href="<?php echo $authUrl; ?>" class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    <img src="https://img.icons8.com/color/16/000000/google-logo.png" alt="Google Logo" class="mr-2">
                                    Entrar com o Google
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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