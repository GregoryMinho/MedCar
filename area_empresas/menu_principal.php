<?php
require '../includes/valida_login.php'; // inclui o arquivo de validação de login
session_start(); // Inicia a sessão

verificarPermissao('empresa'); // verifica se o usuário logado é uma empresa
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Área da Empresa</title>
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

        .vehicle-status {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-available {
            background: #10b981;
        }

        .status-in-use {
            background: #f59e0b;
        }

        .status-maintenance {
            background: #ef4444;
        }

        .schedule-timeline {
            border-left: 3px solid #38b2ac;
            padding-left: 20px;
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

                <div class="flex items-center space-x-4">
                    <div class="text-white mr-3">Bem-vindo, nome da empresa</div>
                    <img src="https://source.unsplash.com/random/40x40/?logo" class="rounded-full h-8 w-8" alt="Logo">
                    <button id="mobile-menu-button" class="md:hidden text-white ml-2">
                        <i data-lucide="menu" class="h-6 w-6"></i>
                    </button>
                </div>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: var(--primary-color);">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-ambulance me-2"></i>
                MedCar
            </a>
            <div class="d-flex align-items-center">
                <div class="text-white me-3">Bem-vindo, nome da empresa</div>
                <img src="https://source.unsplash.com/random/40x40/?logo" class="rounded-circle" alt="Logo">
                <a href="../includes/logout.php" class="btn btn-outline-light ms-3">Logout</a>
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
            <a href="dashboard.php" class="font-medium hover:text-teal-300 transition flex items-center">
                <i data-lucide="layout-dashboard" class="h-5 w-5 mr-2"></i>
                Dashboard
            </a>
            <a href="agendamentos_pacientes.php" class="font-medium hover:text-teal-300 transition flex items-center">
                <i data-lucide="calendar" class="h-5 w-5 mr-2"></i>
                Agendamentos
            </a>
            <a href="gestao_motoristas.php" class="font-medium hover:text-teal-300 transition flex items-center">
                <i data-lucide="users" class="h-5 w-5 mr-2"></i>
                Motoristas
            </a>
            <a href="relatorios_financeiros.php" class="font-medium hover:text-teal-300 transition flex items-center">
                <i data-lucide="bar-chart-2" class="h-5 w-5 mr-2"></i>
                Financeiro
            </a>
            <a href="relatorios.php" class="font-medium hover:text-teal-300 transition flex items-center">
                <i data-lucide="file-text" class="h-5 w-5 mr-2"></i>
                Relatórios
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex flex-col md:flex-row">
        <!-- Sidebar -->
        <div class="hidden md:block w-64 bg-blue-900 text-white min-h-screen pt-24 px-4">
            <nav class="flex flex-col space-y-2">
                <a href="dashboard.php" class="flex items-center space-x-2 px-4 py-3 rounded-lg bg-blue-800 text-white hover:bg-blue-700 transition">
                    <i data-lucide="layout-dashboard" class="h-5 w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="agendamentos_pacientes.php" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
                    <i data-lucide="calendar" class="h-5 w-5"></i>
                    <span>Agendamentos</span>
                </a>
                <a href="gestao_motoristas.php" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
                    <i data-lucide="users" class="h-5 w-5"></i>
                    <span>Motoristas</span>
                </a>
                <a href="relatorios_financeiros.php" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
                    <i data-lucide="bar-chart-2" class="h-5 w-5"></i>
                    <span>Financeiro</span>
                </a>
                <a href="relatorios.php" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition">
                    <i data-lucide="file-text" class="h-5 w-5"></i>
                    <span>Relatórios</span>
                </a>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1">
            <!-- Header Section -->
            <section class="pt-24 pb-16 bg-gradient-to-r from-blue-900 to-blue-800 text-white">
                <div class="container mx-auto px-4">
                    <h1 class="text-3xl md:text-4xl font-bold mb-6">Relatório</h1>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <!-- Serviços Hoje -->
                        <div class="dashboard-card relative overflow-hidden bg-white text-blue-900 rounded-xl shadow-lg p-4 text-center">
                            <div class="mb-2">
                                <i data-lucide="calendar-check" class="h-8 w-8 mx-auto text-teal-500"></i>
                            </div>
                            <h5 class="text-sm font-semibold mb-1">Serviços Hoje</h5>
                            <p class="text-2xl font-bold">18</p>
                        </div>

                        <!-- Faturamento -->
                        <div class="dashboard-card relative overflow-hidden bg-white text-blue-900 rounded-xl shadow-lg p-4 text-center">
                            <div class="mb-2">
                                <i data-lucide="dollar-sign" class="h-8 w-8 mx-auto text-teal-500"></i>
                            </div>
                            <h5 class="text-sm font-semibold mb-1">Faturamento</h5>
                            <p class="text-xl font-bold">R$ 12.540,00</p>
                        </div>

                        <!-- Avaliação -->
                        <div class="dashboard-card relative overflow-hidden bg-white text-blue-900 rounded-xl shadow-lg p-4 text-center">
                            <div class="mb-2">
                                <i data-lucide="star" class="h-8 w-8 mx-auto text-yellow-500"></i>
                            </div>
                            <h5 class="text-sm font-semibold mb-1">Avaliação</h5>
                            <p class="text-xl font-bold">4.8 <i data-lucide="star" class="h-4 w-4 inline"></i></p>
                        </div>

                        <!-- Pendências -->
                        <div class="dashboard-card relative overflow-hidden bg-amber-50 text-blue-900 rounded-xl shadow-lg p-4 text-center">
                            <div class="mb-2">
                                <i data-lucide="alert-triangle" class="h-8 w-8 mx-auto text-amber-500"></i>
                            </div>
                            <h5 class="text-sm font-semibold mb-1">Pendências</h5>
                            <p class="text-xl font-bold">3</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main Sections -->
            <div class="container mx-auto px-4 py-8">
                <!-- Agendamentos -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h4 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                        <i data-lucide="calendar-days" class="h-5 w-5 mr-2 text-teal-500"></i>
                        Agenda de Hoje
                    </h4>
                    <div class="schedule-timeline mt-3">
                        <div class="mb-4">
                            <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                                <div>
                                    <h5 class="font-semibold text-blue-900">Paciente: João Silva</h5>
                                    <p class="text-gray-600 text-sm">Hospital Santa Maria - 09:30</p>
                                </div>
                                <div class="mt-2 md:mt-0">
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        Em transporte
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- Mais agendamentos... -->
                    </div>
                </div>

                <!-- Gestão de Frota e Motoristas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Gestão de Frota -->
                    <div class="bg-white rounded-xl shadow-lg p-6 h-full">
                        <h4 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                            <i data-lucide="ambulance" class="h-5 w-5 mr-2 text-teal-500"></i>
                            Gestão de Frota
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Veículo
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Última Manutenção
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            AMB-1234
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            <div class="vehicle-status status-available"></div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            15/08/2024
                                        </td>
                                    </tr>
                                    <!-- Mais veículos... -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Motoristas -->
                    <div class="bg-white rounded-xl shadow-lg p-6 h-full">
                        <h4 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                            <i data-lucide="users" class="h-5 w-5 mr-2 text-teal-500"></i>
                            Motoristas
                        </h4>
                        <div class="space-y-3">
                            <div class="border rounded-lg p-4 flex flex-col md:flex-row md:justify-between md:items-center">
                                <div>
                                    <h5 class="font-semibold text-blue-900">Carlos Silva</h5>
                                    <p class="text-gray-600 text-sm">Disponível</p>
                                </div>
                                <div class="mt-2 md:mt-0">
                                    <button class="bg-blue-100 hover:bg-blue-200 text-blue-800 text-sm font-medium py-1 px-3 rounded-lg transition-all hover:scale-105">
                                        Detalhes
                                    </button>
                                </div>
                            </div>
                            <!-- Mais motoristas... -->
                        </div>
                    </div>
                </div>

                <!-- Financeiro -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h4 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                        <i data-lucide="bar-chart-2" class="h-5 w-5 mr-2 text-teal-500"></i>
                        Desempenho Financeiro
                    </h4>
                    <div class="mt-3 h-64">
                        <!-- Gráfico (implementar com biblioteca) -->
                        <div class="bg-gray-50 text-center p-5 rounded-lg flex items-center justify-center h-full">
                            <p class="text-gray-500">Gráfico de Desempenho</p>
                        </div>
                    </div>
                </div>
            </div>
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