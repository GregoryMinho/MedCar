<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Transport - Menu Principal</title>
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
                    <a href="../paginas/menu_principal.php" class="font-medium hover:text-teal-300 transition">Home</a>
                    <a href="../paginas/abas_menu_principal/aba_entrar.php" class="font-medium hover:text-teal-300 transition">Entrar</a>
                    <a href="../paginas/abas_menu_principal/aba_empresas.php" class="font-medium hover:text-teal-300 transition">Empresas</a>
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
            <a href="#" class="font-medium hover:text-teal-300 transition">Home</a>
            <a href="aba_entrar.php" class="font-medium hover:text-teal-300 transition">Entrar</a>
            <a href="#" class="font-medium hover:text-teal-300 transition">Empresas</a>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="pt-32 pb-16 bg-gradient-to-r from-blue-900 to-blue-800 text-white">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">
                Transporte médico seguro e confiável
            </h1>
            <p class="text-xl mb-8 max-w-2xl mx-auto">
                Conectamos pacientes a empresas de transporte especializado
            </p>
            <button class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-3 px-8 rounded-lg transform transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-opacity-50">
                Agendar Agora
            </button>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-xl shadow-lg p-6 transition-all hover:-translate-y-1 hover:shadow-xl">
                    <div class="text-teal-500 mb-4">
                        <i data-lucide="clock" class="h-10 w-10"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 mb-3">
                        Agendamento Rápido
                    </h2>
                    <p class="text-gray-600 mb-6">
                        Agende seu transporte em poucos minutos
                    </p>
                    <button class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-2 px-4 rounded-lg transition-all hover:scale-105">
                        Saiba Mais
                    </button>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-xl shadow-lg p-6 transition-all hover:-translate-y-1 hover:shadow-xl">
                    <div class="text-teal-500 mb-4">
                        <i data-lucide="shield" class="h-10 w-10"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 mb-3">
                        Segurança Garantida
                    </h2>
                    <p class="text-gray-600 mb-6">
                        Profissionais treinados e veículos adaptados
                    </p>
                    <button class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-2 px-4 rounded-lg transition-all hover:scale-105">
                        Saiba Mais
                    </button>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-xl shadow-lg p-6 transition-all hover:-translate-y-1 hover:shadow-xl">
                    <div class="text-teal-500 mb-4">
                        <i data-lucide="map-pin" class="h-10 w-10"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 mb-3">
                        Rastreamento em Tempo Real
                    </h2>
                    <p class="text-gray-600 mb-6">
                        Acompanhe seu transporte em tempo real
                    </p>
                    <button class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-2 px-4 rounded-lg transition-all hover:scale-105">
                        Saiba Mais
                    </button>
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