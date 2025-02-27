<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Transport - Escolha como entrar</title>
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
        .choice-card {
            transition: all 0.3s ease;
        }
        .choice-card:hover {
            transform: translateY(-10px);
        }
    </style>
</head>
<body class="min-h-screen bg-white">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="#" class="flex items-center space-x-2 text-xl font-bold">
                    <i data-lucide="ambulance" class="h-6 w-6"></i>
                    <span>MedCar</span>
                </a>
                
                <div class="hidden md:flex space-x-6">
                    <a href="../menu_principal.php" class="font-medium hover:text-teal-300 transition">Home</a>
                    <a href="aba_empresas.php" class="font-medium hover:text-teal-300 transition">Empresas</a>
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
            <a href="../menu_principal.php" class="font-medium hover:text-teal-300 transition">Home</a>
            <a href="aba_empresas.php" class="font-medium hover:text-teal-300 transition">Empresas</a>
            <a href="#" class="font-medium hover:text-teal-300 transition">Contato</a>
        </div>
    </div>

    <!-- Header Section -->
    <section class="pt-32 pb-16 bg-gradient-to-r from-blue-900 to-blue-800 text-white">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-3">
                Escolha como deseja entrar
            </h1>
            <p class="text-xl mb-4">
                Selecione seu perfil para continuar
            </p>
        </div>
    </section>

    <!-- Choice Cards -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Card Empresa -->
                <div class="choice-card bg-white rounded-xl shadow-lg p-8 min-h-[400px] relative flex flex-col items-center text-center">
                    <div class="text-teal-500 mb-6">
                        <i data-lucide="building" class="h-16 w-16"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">
                        Represento uma Empresa
                    </h3>
                    <p class="text-gray-600 mb-12">
                        Cadastre sua empresa de transporte médico na nossa plataforma e aumente sua visibilidade para pacientes que necessitam de seus serviços.
                    </p>
                    <a href="../login_empresas.php" class="absolute bottom-8 bg-teal-500 hover:bg-teal-600 text-white font-medium py-3 px-8 rounded-full transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-opacity-50">
                        Acessar Área da Empresa
                    </a>
                </div>

                <!-- Card Cliente -->
                <div class="choice-card bg-white rounded-xl shadow-lg p-8 min-h-[400px] relative flex flex-col items-center text-center">
                    <div class="text-teal-500 mb-6">
                        <i data-lucide="user" class="h-16 w-16"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">
                        Sou um Cliente
                    </h3>
                    <p class="text-gray-600 mb-12">
                        Encontre a melhor empresa de transporte médico para suas necessidades. Agende seu transporte com segurança e praticidade.
                    </p>
                    <a href="../login_clientes.php" class="absolute bottom-8 bg-teal-500 hover:bg-teal-600 text-white font-medium py-3 px-8 rounded-full transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-opacity-50">
                        Buscar Transporte
                    </a>
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

