<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Transport - Empresas Parceiras</title>
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
        .company-rating {
            color: #ffd700;
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
                    <a href="/MedQ-2/area_cliente/menu_principal.php" class="font-medium hover:text-teal-300 transition">Home</a>
                    <a href="aba_entrar.php" class="font-medium hover:text-teal-300 transition">Entrar</a>
                    <a href="../abas_menu_principal/aba_empresas.php" class="font-medium hover:text-teal-300 transition">Empresas</a>
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

    <!-- Header Section -->
    <section class="pt-32 pb-16 bg-gradient-to-r from-blue-900 to-blue-800 text-white">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">
                Empresas Parceiras
            </h1>
            <p class="text-xl mb-8 max-w-2xl mx-auto">
                Encontre a melhor empresa para seu transporte médico
            </p>
        </div>
    </section>

    <!-- Search Section -->
    <section class="py-8 bg-blue-900 mb-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="md:w-8/12">
                    <input type="text" placeholder="Buscar por nome, cidade ou especialidade..." class="w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
                <div class="md:w-3/12">
                    <select class="w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 bg-white">
                        <option>Todas as Categorias</option>
                        <option>Transporte Urgente</option>
                        <option>Transporte Aéreo</option>
                        <option>Ambulância Leito</option>
                    </select>
                </div>
                <div class="md:w-1/12">
                    <button class="w-full bg-teal-500 hover:bg-teal-600 text-white font-semibold py-3 px-4 rounded-lg transform transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-opacity-50">
                        Filtrar
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Company List -->
    <section class="py-8">
        <div class="container mx-auto px-4">
            <!-- Company 1 -->
            <div class="bg-white rounded-xl shadow-lg mb-8 overflow-hidden hover:-translate-y-1 hover:shadow-xl transition-all">
                <div class="flex flex-col md:flex-row">
                    <div class="md:w-1/3">
                        <img src="../../imagens/img_plano_de_saude1.jpg" class="w-full h-64 object-cover" alt="Ambulância">
                    </div>
                    <div class="md:w-2/3 p-6">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-2xl font-bold text-gray-800">Resgate Médico Express</h3>
                            <span class="bg-teal-500 text-white px-3 py-1 rounded-lg text-sm font-medium">24 Horas</span>
                        </div>
                        <div class="flex items-center mb-3 company-rating">
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star-half" class="h-5 w-5 fill-current"></i>
                            <span class="text-gray-500 ml-2">(145 avaliações)</span>
                        </div>
                        <p class="text-gray-600 mb-4">Empresa especializada em transporte de urgência com equipe médica acompanhante. Atua em todo território nacional.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="flex items-center text-gray-700">
                                    <i data-lucide="map-pin" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    São Paulo - SP
                                </p>
                                <p class="flex items-center text-gray-700">
                                    <i data-lucide="phone" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    (11) 99999-9999
                                </p>
                            </div>
                            <div>
                                <p class="flex items-center text-gray-700">
                                    <i data-lucide="activity" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    Especialidades:
                                </p>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm">UTI Móvel</span>
                                    <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm">Neonatal</span>
                                    <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm">Cardíaco</span>
                                </div>
                            </div>
                        </div>
                        <button class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded-lg transform transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-opacity-50">
                            Solicitar Orçamento
                        </button>
                    </div>
                </div>
            </div>

            <!-- Company 2 -->
            <div class="bg-white rounded-xl shadow-lg mb-8 overflow-hidden hover:-translate-y-1 hover:shadow-xl transition-all">
                <div class="flex flex-col md:flex-row">
                    <div class="md:w-1/3">
                        <img src="../../imagens/img_plano_de_saude2.jpg" class="w-full h-64 object-cover" alt="Transporte Médico">
                    </div>
                    <div class="md:w-2/3 p-6">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-2xl font-bold text-gray-800">Saúde em Movimento</h3>
                            <span class="bg-teal-500 text-white px-3 py-1 rounded-lg text-sm font-medium">Plantão Diário</span>
                        </div>
                        <div class="flex items-center mb-3 company-rating">
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5"></i>
                            <span class="text-gray-500 ml-2">(89 avaliações)</span>
                        </div>
                        <p class="text-gray-600 mb-4">Transporte médico especializado em pacientes com mobilidade reduzida e tratamentos contínuos.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="flex items-center text-gray-700">
                                    <i data-lucide="map-pin" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    Rio de Janeiro - RJ
                                </p>
                                <p class="flex items-center text-gray-700">
                                    <i data-lucide="phone" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    (21) 99999-9999
                                </p>
                            </div>
                            <div>
                                <p class="flex items-center text-gray-700">
                                    <i data-lucide="activity" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    Especialidades:
                                </p>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm">Cadeirantes</span>
                                    <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm">Idosos</span>
                                    <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm">Fisioterapia</span>
                                </div>
                            </div>
                        </div>
                        <button class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded-lg transform transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-opacity-50">
                            Solicitar Orçamento
                        </button>
                    </div>
                </div>
            </div>

            <!-- Company 3 -->
            <div class="bg-white rounded-xl shadow-lg mb-8 overflow-hidden hover:-translate-y-1 hover:shadow-xl transition-all">
                <div class="flex flex-col md:flex-row">
                    <div class="md:w-1/3">
                        <img src="../../imagens/img_plano_de_saude3.jpg" class="w-full h-64 object-cover" alt="Ambulância">
                    </div>
                    <div class="md:w-2/3 p-6">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-2xl font-bold text-gray-800">Vida Rápida Transportes</h3>
                            <span class="bg-teal-500 text-white px-3 py-1 rounded-lg text-sm font-medium">Atendimento 24h</span>
                        </div>
                        <div class="flex items-center mb-3 company-rating">
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star-half" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5"></i>
                            <span class="text-gray-500 ml-2">(102 avaliações)</span>
                        </div>
                        <p class="text-gray-600 mb-4">Serviço rápido e eficiente com equipe preparada para emergências médicas.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="flex items-center text-gray-700">
                                    <i data-lucide="map-pin" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    Belo Horizonte - MG
                                </p>
                                <p class="flex items-center text-gray-700">
                                    <i data-lucide="phone" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    (31) 99876-5432
                                </p>
                            </div>
                            <div>
                                <p class="flex items-center text-gray-700">
                                    <i data-lucide="activity" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    Especialidades:
                                </p>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm">Emergências</span>
                                    <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm">Suporte Vital</span>
                                </div>
                            </div>
                        </div>
                        <button class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded-lg transform transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-opacity-50">
                            Solicitar Orçamento
                        </button>
                    </div>
                </div>
            </div>

            <!-- Company 4 -->
            <div class="bg-white rounded-xl shadow-lg mb-8 overflow-hidden hover:-translate-y-1 hover:shadow-xl transition-all">
                <div class="flex flex-col md:flex-row">
                    <div class="md:w-1/3">
                        <img src="../../imagens/img_plano_de_saude4.jpg" class="w-full h-64 object-cover" alt="Transporte Médico">
                    </div>
                    <div class="md:w-2/3 p-6">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-2xl font-bold text-gray-800">Transporte Vital</h3>
                            <span class="bg-teal-500 text-white px-3 py-1 rounded-lg text-sm font-medium">Emergência</span>
                        </div>
                        <div class="flex items-center mb-3 company-rating">
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5"></i>
                            <span class="text-gray-500 ml-2">(87 avaliações)</span>
                        </div>
                        <p class="text-gray-600 mb-4">Foco no transporte de pacientes em situação de risco com equipamentos modernos.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="flex items-center text-gray-700">
                                    <i data-lucide="map-pin" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    Curitiba - PR
                                </p>
                                <p class="flex items-center text-gray-700">
                                    <i data-lucide="phone" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    (41) 98765-4321
                                </p>
                            </div>
                            <div>
                                <p class="flex items-center text-gray-700">
                                    <i data-lucide="activity" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    Especialidades:
                                </p>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm">Emergência</span>
                                    <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm">Suporte Intensivo</span>
                                </div>
                            </div>
                        </div>
                        <button class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded-lg transform transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-opacity-50">
                            Solicitar Orçamento
                        </button>
                    </div>
                </div>
            </div>

            <!-- Company 5 -->
            <div class="bg-white rounded-xl shadow-lg mb-8 overflow-hidden hover:-translate-y-1 hover:shadow-xl transition-all">
                <div class="flex flex-col md:flex-row">
                    <div class="md:w-1/3">
                        <img src="../../imagens/img_plano_de_saude1.jpg" class="w-full h-64 object-cover" alt="Ambulância">
                    </div>
                    <div class="md:w-2/3 p-6">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-2xl font-bold text-gray-800">Cuidado Móvel</h3>
                            <span class="bg-teal-500 text-white px-3 py-1 rounded-lg text-sm font-medium">Serviço 24h</span>
                        </div>
                        <div class="flex items-center mb-3 company-rating">
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star-half" class="h-5 w-5 fill-current"></i>
                            <span class="text-gray-500 ml-2">(156 avaliações)</span>
                        </div>
                        <p class="text-gray-600 mb-4">Transporte seguro com suporte médico e equipamentos avançados para cuidados intensivos.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="flex items-center text-gray-700">
                                    <i data-lucide="map-pin" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    Fortaleza - CE
                                </p>
                                <p class="flex items-center text-gray-700">
                                    <i data-lucide="phone" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    (85) 91234-5678
                                </p>
                            </div>
                            <div>
                                <p class="flex items-center text-gray-700">
                                    <i data-lucide="activity" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    Especialidades:
                                </p>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm">UTI</span>
                                    <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm">Cuidados Críticos</span>
                                </div>
                            </div>
                        </div>
                        <button class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded-lg transform transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-opacity-50">
                            Solicitar Orçamento
                        </button>
                    </div>
                </div>
            </div>

            <!-- Company 6 -->
            <div class="bg-white rounded-xl shadow-lg mb-8 overflow-hidden hover:-translate-y-1 hover:shadow-xl transition-all">
                <div class="flex flex-col md:flex-row">
                    <div class="md:w-1/3">
                        <img src="../../imagens/img_plano_de_saude2.jpg" class="w-full h-64 object-cover" alt="Transporte Médico">
                    </div>
                    <div class="md:w-2/3 p-6">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-2xl font-bold text-gray-800">Salto da Saúde</h3>
                            <span class="bg-teal-500 text-white px-3 py-1 rounded-lg text-sm font-medium">Atendimento Rápido</span>
                        </div>
                        <div class="flex items-center mb-3 company-rating">
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5"></i>
                            <span class="text-gray-500 ml-2">(132 avaliações)</span>
                        </div>
                        <p class="text-gray-600 mb-4">Comprometida com o bem-estar, oferece transporte rápido e seguro para emergências médicas.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="flex items-center text-gray-700">
                                    <i data-lucide="map-pin" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    Porto Alegre - RS
                                </p>
                                <p class="flex items-center text-gray-700">
                                    <i data-lucide="phone" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    (51) 92345-6789
                                </p>
                            </div>
                            <div>
                                <p class="flex items-center text-gray-700">
                                    <i data-lucide="activity" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    Especialidades:
                                </p>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm">Emergências</span>
                                    <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm">Suporte Avançado</span>
                                </div>
                            </div>
                        </div>
                        <button class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded-lg transform transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-opacity-50">
                            Solicitar Orçamento
                        </button>
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