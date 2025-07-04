<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['tipo_login_google'] = 'empresa'; // Define o tipo de login como empresa
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar- Login Empresa</title>
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
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="/MedCar/paginas/pagina_inicial.php" class="flex items-center space-x-2 text-xl font-bold">
                    <i data-lucide="ambulance" class="h-6 w-6"></i>
                    <span>MedCar</span>
                </a>
                                
                <div class="hidden md:flex space-x-6">
                    <a href="/MedCar/area_cliente/menu_principal.php" class="font-medium hover:text-teal-300 transition">Home</a>
                    <a href="#" class="font-medium hover:text-teal-300 transition">Contato</a>
                </div>
                
                <button id="mobile-menu-button" class="md:hidden text-white">
                    <i data-lucide="menu" class="h-6 w-6"></i>
                </button>
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
            <a href="/MedCar/paginas/pagina_inicial.php" class="font-medium hover:text-teal-300 transition">Home</a>           
            <a href="#" class="font-medium hover:text-teal-300 transition">Contato</a>
        </div>
    </div>

    <!-- Login Section -->
    <section class="pt-32 pb-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-teal-500 text-white p-8 text-center">
                    <h2 class="text-3xl font-bold mb-2">Acesso da Empresa</h2>
                    <p class="text-xl">Entre com sua conta corporativa</p>
                </div>
                <div class="flex flex-col md:flex-row">
                    <!-- Login Form -->
                    <div class="w-full md:w-1/2 p-8">
                        <form action="actions/action_login_empresa.php" method="post" id="login-form" class="space-y-6">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">E-mail Corporativo</label>
                                <input type="email" id="email" name="email" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="empresa@exemplo.com" required>
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
                                <?php echo $_SESSION['login_erro'] ?? null ?>
                            </p>
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-m font-medium text-white bg-teal-500 hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                                Entrar
                            </button>
                        </form>
                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600">
                                Não tem conta?
                                <a href="/MedCar/paginas/cadastro_empresas.php" class="font-medium text-teal-500 hover:text-teal-600">Cadastre sua empresa</a>
                            </p>
                        </div>
                    </div>
                    <!-- Benefits -->
                    <div class="w-full md:w-1/2 bg-gray-50 p-8">
                        <h4 class="text-xl font-bold mb-4 text-gray-800">Benefícios de ser Cliente</h4>
                        <ul class="space-y-2 mb-8 border-l-4 border-teal-500 pl-4">
                            <li>...</li>
                            <li>Histórico de transportes</li>
                            <li>Dashboard detalhado</li>
                            <li>Benefícios para empresas bem avaliadas</li>
                            <li>...</li>
                        </ul>
                        <div class="mt-8">
                            <p class="text-sm text-gray-600 mb-4">Entrar com redes sociais</p>
                            <div class="grid grid-cols-2 gap-4">
                                 
                                <a href="#" class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <i data-lucide="google" class="h-5 w-5 mr-2"></i>
                                    Google
                                </a>
                                <a href="#" class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <i data-lucide="facebook" class="h-5 w-5 mr-2"></i>
                                    Facebook
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
    </script>
</body>

</html>