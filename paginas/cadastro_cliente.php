<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Cliente</title>
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
    <header>
        <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-md">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between h-16">
                    <a href="#" class="flex items-center space-x-2 text-xl font-bold">
                        <i data-lucide="ambulance" class="h-6 w-6"></i>
                        <span>MedCar</span>
                    </a>
                    <div class="hidden md:flex space-x-6">
                        <a href="/MedQ-2/paginas/pagina_inicial.php" class="font-medium hover:text-teal-300 transition">Home</a>
                        <a href="#" class="font-medium hover:text-teal-300 transition">Contato</a>
                    </div>
                    <button id="mobile-menu-button" aria-expanded="false" aria-controls="mobile-menu" class="md:hidden text-white">
                        <i data-lucide="menu" class="h-6 w-6"></i>
                    </button>
                </div>
            </div>
        </nav>
    </header>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="fixed inset-0 z-50 bg-blue-900 bg-opacity-95 flex flex-col text-white p-6 mobile-menu">
        <div class="flex justify-end">
            <button id="close-menu-button" class="text-white">
                <i data-lucide="x" class="h-6 w-6"></i>
            </button>
        </div>

        <div class="flex flex-col items-center justify-center space-y-8 flex-grow text-xl">
            <a href="#" class="font-medium hover:text-teal-300 transition">Home</a>
            <a href="#" class="font-medium hover:text-teal-300 transition">Empresas</a>
            <a href="#" class="font-medium hover:text-teal-300 transition">Contato</a>

        </div>
    </div>

    <!-- Cadastro Section -->
    <section class="pt-32 pb-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-teal-500 text-white p-8 text-center">
                    <h2 class="text-3xl font-bold mb-2">Cadastre-se</h2>
                    <p class="text-xl">Crie sua conta para agendar seus transportes</p>
                </div>
                <div class="flex flex-col md:flex-row">
                    <!-- Cadastro Form -->
                    <div class="w-full md:w-1/2 p-8">
                        <form id="cadastro-form" class="space-y-6" action="cadastro_cliente.php" method="POST">
                            <div>
                                <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                                <input type="text" id="nome" name="nome" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Seu nome completo" required>
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                                <input type="email" id="email" name="email" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="seunome@exemplo.com" required>
                            </div>
                            <div>
                                <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
                                <input type="text" id="cpf" name="cpf" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="000.000.000-00" required>
                            </div>
                            <div>
                                <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                                <input type="text" id="telefone" name="telefone" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="(00) 00000-0000" required>
                            </div>
                            <div>
                                <label for="senha" class="block text-sm font-medium text-gray-700">Senha</label>
                                <input type="password" id="senha" name="senha" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="••••••••" required>
                            </div>
                            <div>
                                <label for="confirmar_senha" class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                                <input type="password" id="confirmar_senha" name="confirmar_senha" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="••••••••" required>
                            </div>
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-500 hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                                Cadastrar
                            </button>
                        </form>
                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600">
                                Já tem conta?
                                <a href="/MedQ-2/paginas/login_clientes.php" class="font-medium text-teal-500 hover:text-teal-600">Faça login aqui</a>
                            </p>
                        </div>
                    </div>
                    <!-- Benefits -->
                    <div class="w-full md:w-1/2 bg-gray-50 p-8">
                        <h4 class="text-xl font-bold mb-4 text-gray-800">Benefícios de ser Cliente</h4>
                        <ul class="space-y-2 mb-8 border-l-4 border-teal-500 pl-4">
                            <li>Agendamento rápido e seguro</li>
                            <li>Histórico de transportes</li>
                            <li>Acompanhamento em tempo real</li>
                            <li>Cupons de desconto para clientes frequentes</li>
                            <li>Suporte 24 horas</li>
                        </ul>
                        <div class="mt-8">
                            <p class="text-sm text-gray-600 mb-4">Entrar com redes sociais</p>
                            <div class="grid grid-cols-2 gap-4">
                                <a href="https://accounts.google.com/" class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <i data-lucide="google" class="h-5 w-5 mr-2"></i>
                                    Google
                                </a>
                                <a href="https://m.facebook.com/" class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
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

        // Form submission
        document.getElementById('cadastro-form').addEventListener('submit', function(e) {
            e.preventDefault();
            // Aqui pode adicionar validações ou enviar o formulário
                const senha = document.getElementById('senha').value;
                const confirmarSenha = document.getElementById('confirmar_senha').value;

        if (senha !== confirmarSenha) {
            alert('As senhas não coincidem!');
        return;
            }
            this.submit();
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>

    <script src="../jquery.mask.min.js"></script>

    <script>
        $('#cpf').mask('000.000.000-00');
        $('#telefone').mask('(00) 00000-0000');
    </script>

</body>

</html>