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
                <a href="#" class="flex items-center space-x-2 text-xl font-bold">
                    <i data-lucide="ambulance" class="h-6 w-6"></i>
                    <span>MedCar</span>
                </a>

                <div class="flex items-center space-x-6">
                    <a href="menu_principal.php" class="font-medium hover:text-teal-300 transition">Home</a>
                    <a href="/MedQ-2/paginas/abas_menu_principal/aba_empresas.php" class="font-medium hover:text-teal-300 transition">Empresas</a>  <!-- conectado as empresas , checa os outros butooes estao funcionando. -->
                    <a href="#" class="font-medium hover:text-teal-300 transition">Contato</a>
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
                    <span>Agendamentos</span>
                </a>
                <a href="#" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
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
                            <p class="font-bold mb-1">15/08 - 14:00</p>
                            <p class="text-xs text-gray-600">Hospital Santa Maria</p>
                            <div class="mt-2">
                                <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Confirmado</span>
                            </div>
                        </div>

                        <!-- Transportes Realizados -->
                        <div class="dashboard-card relative overflow-hidden bg-white text-blue-900 rounded-xl shadow-lg p-4 text-center">
                            <div class="mb-2">
                                <i data-lucide="bar-chart-2" class="h-8 w-8 mx-auto text-teal-500"></i>
                            </div>
                            <h5 class="text-sm font-semibold mb-1">Transportes Realizados</h5>
                            <p class="text-2xl font-bold">12</p>
                            <p class="text-xs text-gray-600">+2 este mês</p>
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
                            <i data-lucide="ambulance" class="h-10 w-10 mx-auto text-teal-500 mb-3"></i>
                            <h5 class="text-lg font-semibold text-blue-900 mb-2">Agendar Transporte</h5>
                            <p class="text-sm text-gray-600 mb-4">Agende seu transporte médico com antecedência</p>
                            <button class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-2 px-4 rounded-lg transition-all hover:scale-105">
                                Agendar Agora
                            </button>
                        </div>

                        <div class="dashboard-card relative overflow-hidden bg-white rounded-xl shadow-lg p-6 text-center">
                            <i data-lucide="clock" class="h-10 w-10 mx-auto text-teal-500 mb-3"></i>
                            <h5 class="text-lg font-semibold text-blue-900 mb-2">Histórico Completo</h5>
                            <p class="text-sm text-gray-600 mb-4">Veja todos seus transportes realizados</p>
                            <button class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-2 px-4 rounded-lg transition-all hover:scale-105">
                                Acessar Histórico
                            </button>
                        </div>

                        <div class="dashboard-card relative overflow-hidden bg-white rounded-xl shadow-lg p-6 text-center">
                            <i data-lucide="star" class="h-10 w-10 mx-auto text-teal-500 mb-3"></i>
                            <h5 class="text-lg font-semibold text-blue-900 mb-2">Empresas Favoritas</h5>
                            <p class="text-sm text-gray-600 mb-4">Gerencie suas empresas preferidas</p>
                            <button class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-2 px-4 rounded-lg transition-all hover:scale-105">
                                Ver Favoritos
                            </button>
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
                            Próximos Transportes
                        </h4>
                        <div class="border rounded-lg overflow-hidden">
                            <div class="p-4 border-b flex flex-col md:flex-row md:items-center md:justify-between">
                                <div>
                                    <h5 class="font-semibold text-blue-900">Consulta Cardiológica</h5>
                                    <p class="text-gray-600 text-sm">Hospital Santa Maria - 15/08 - 14:00</p>
                                </div>
                                <div class="flex space-x-2 mt-3 md:mt-0">
                                    <button class="bg-teal-500 hover:bg-teal-600 text-white text-sm font-medium py-1 px-3 rounded-lg transition-all hover:scale-105">
                                        Detalhes
                                    </button>
                                    <button class="border border-red-500 text-red-500 hover:bg-red-50 text-sm font-medium py-1 px-3 rounded-lg transition-all hover:scale-105">
                                        Cancelar
                                    </button>
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
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded">
                                    Confirmação de transporte para 15/08
                                </div>
                                <div class="bg-green-50 border-l-4 border-green-500 p-3 rounded">
                                    Avaliação registrada com sucesso
                                </div>
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