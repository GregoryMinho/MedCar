<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Documentos do Motorista</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://unpkg.com/lucide@latest"></script>
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
                    <button class="flex items-center space-x-2 font-medium hover:text-teal-300 transition">
                        <i data-lucide="user" class="h-5 w-5"></i>
                        <span>Carlos Silva</span>
                    </button>
                    <button class="relative">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-xs rounded-full px-1.5 text-white">2</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar + Main -->
    <div class="flex pt-20">
        <!-- Sidebar -->
        <div class="hidden md:block w-64 bg-blue-900 text-white min-h-screen pt-10">
            <nav class="flex flex-col space-y-2 px-4">
                <a href="#" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-speedometer2 fs-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-calendar-event fs-5"></i>
                    <span>Meus Agendamentos</span>
                </a>
                <a href="#" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-person fs-5"></i>
                    <span>Meu Perfil</span>
                </a>
                <a href="#" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-currency-dollar fs-5"></i>
                    <span>Pagamentos</span>
                </a>
                <a href="#" class="flex items-center gap-3 ps-4 py-3 rounded-lg bg-blue-800 font-semibold">
                    <i class="bi bi-folder2-open fs-5"></i>
                    <span>Meus Documentos</span>
                </a>
                <a href="#" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition mt-8 text-red-400">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                    <span>Sair</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 px-2 md:px-10 py-8">
            <!-- Suporte rápido -->
            <div class="flex justify-end mb-2">
                <button onclick="alert('Abrindo suporte MedCar...')" class="bg-blue-800 hover:bg-blue-900 text-white px-5 py-2 rounded-lg shadow transition flex items-center gap-2">
                    <i data-lucide="help-circle" class="w-5 h-5"></i> Suporte Documentação
                </button>
            </div>

            <!-- Título -->
            <h1 class="text-3xl font-bold text-blue-900 mb-2 flex items-center gap-2">
                <i data-lucide="folder-check" class="h-7 w-7 text-blue-800"></i>
                Documentos Obrigatórios
            </h1>
            <p class="text-gray-500 mb-6">Envie ou atualize seus documentos para manter seu cadastro ativo. Acompanhe status, validade e pendências.</p>
            
            <!-- Cards status resumido -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-xl shadow-lg p-4 flex flex-col items-center text-center">
                    <i data-lucide="check-circle" class="h-8 w-8 text-green-600 mb-2"></i>
                    <div class="text-sm font-semibold text-gray-500">Aprovados</div>
                    <div class="text-2xl font-bold text-blue-900">4</div>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-4 flex flex-col items-center text-center">
                    <i data-lucide="alert-triangle" class="h-8 w-8 text-yellow-500 mb-2"></i>
                    <div class="text-sm font-semibold text-gray-500">Pendentes</div>
                    <div class="text-2xl font-bold text-blue-900">2</div>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-4 flex flex-col items-center text-center">
                    <i data-lucide="x-circle" class="h-8 w-8 text-red-500 mb-2"></i>
                    <div class="text-sm font-semibold text-gray-500">Reprovados</div>
                    <div class="text-2xl font-bold text-blue-900">0</div>
                </div>
            </div>

            <!-- Tabela documentos -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                    <i data-lucide="file-text" class="h-5 w-5 mr-2 text-teal-500"></i>
                    Documentos do Motorista
                </h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Validade</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Último Envio</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Histórico</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <!-- CNH -->
                            <tr>
                                <td class="px-4 py-2 font-semibold text-blue-900 flex items-center gap-2">
                                    <i class="bi bi-card-heading"></i> CNH (Carteira de Motorista)
                                </td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">
                                        <i class="bi bi-check-circle mr-1"></i> Aprovado
                                    </span>
                                </td>
                                <td class="px-4 py-2 flex items-center gap-1">
                                    12/06/2026
                                </td>
                                <td class="px-4 py-2">12/05/2025</td>
                                <td class="px-4 py-2 flex gap-2">
                                    <label class="cursor-pointer bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded flex items-center gap-1 text-xs">
                                        <i data-lucide="upload" class="w-4 h-4"></i> Atualizar
                                        <input type="file" class="hidden" />
                                    </label>
                                    <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded flex items-center gap-1 text-xs">
                                        <i data-lucide="eye" class="w-4 h-4"></i> Visualizar
                                    </button>
                                </td>
                                <td class="px-4 py-2">
                                    <button onclick="showHistory('CNH')" class="bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded text-xs flex items-center gap-1">
                                        <i data-lucide="history" class="w-4 h-4"></i> Histórico
                                    </button>
                                </td>
                            </tr>
                            <!-- CRLV -->
                            <tr>
                                <td class="px-4 py-2 font-semibold text-blue-900 flex items-center gap-2">
                                    <i class="bi bi-car-front-fill"></i> CRLV (Doc. Veículo)
                                </td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">
                                        <i class="bi bi-clock-history mr-1"></i> Pendente
                                    </span>
                                </td>
                                <td class="px-4 py-2">--</td>
                                <td class="px-4 py-2">-</td>
                                <td class="px-4 py-2 flex gap-2">
                                    <label class="cursor-pointer bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded flex items-center gap-1 text-xs">
                                        <i data-lucide="upload" class="w-4 h-4"></i> Enviar
                                        <input type="file" class="hidden" />
                                    </label>
                                </td>
                                <td class="px-4 py-2">
                                    <button onclick="showHistory('CRLV')" class="bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded text-xs flex items-center gap-1">
                                        <i data-lucide="history" class="w-4 h-4"></i> Histórico
                                    </button>
                                </td>
                            </tr>
                            <!-- Seguro do veículo -->
                            <tr>
                                <td class="px-4 py-2 font-semibold text-blue-900 flex items-center gap-2">
                                    <i class="bi bi-shield-check"></i> Seguro do Veículo
                                </td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">
                                        <i class="bi bi-exclamation-circle mr-1"></i> Vencendo
                                    </span>
                                </td>
                                <td class="px-4 py-2 flex items-center gap-1">
                                    28/06/2024
                                    <span class="ml-2 text-xs text-red-600 font-semibold animate-pulse flex items-center gap-1">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i> Vence em breve
                                    </span>
                                </td>
                                <td class="px-4 py-2">10/06/2024</td>
                                <td class="px-4 py-2 flex gap-2">
                                    <label class="cursor-pointer bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded flex items-center gap-1 text-xs">
                                        <i data-lucide="upload" class="w-4 h-4"></i> Renovar
                                        <input type="file" class="hidden" />
                                    </label>
                                    <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded flex items-center gap-1 text-xs">
                                        <i data-lucide="eye" class="w-4 h-4"></i> Visualizar
                                    </button>
                                </td>
                                <td class="px-4 py-2">
                                    <button onclick="showHistory('Seguro')" class="bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded text-xs flex items-center gap-1">
                                        <i data-lucide="history" class="w-4 h-4"></i> Histórico
                                    </button>
                                </td>
                            </tr>
                            <!-- Curso de transporte -->
                            <tr>
                                <td class="px-4 py-2 font-semibold text-blue-900 flex items-center gap-2">
                                    <i class="bi bi-mortarboard"></i> Certificado Curso Transporte
                                </td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">
                                        <i class="bi bi-check-circle mr-1"></i> Aprovado
                                    </span>
                                </td>
                                <td class="px-4 py-2">25/04/2027</td>
                                <td class="px-4 py-2">13/04/2025</td>
                                <td class="px-4 py-2 flex gap-2">
                                    <label class="cursor-pointer bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded flex items-center gap-1 text-xs">
                                        <i data-lucide="upload" class="w-4 h-4"></i> Atualizar
                                        <input type="file" class="hidden" />
                                    </label>
                                    <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded flex items-center gap-1 text-xs">
                                        <i data-lucide="eye" class="w-4 h-4"></i> Visualizar
                                    </button>
                                </td>
                                <td class="px-4 py-2">
                                    <button onclick="showHistory('Curso')" class="bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded text-xs flex items-center gap-1">
                                        <i data-lucide="history" class="w-4 h-4"></i> Histórico
                                    </button>
                                </td>
                            </tr>
                            <!-- Foto do motorista -->
                            <tr>
                                <td class="px-4 py-2 font-semibold text-blue-900 flex items-center gap-2">
                                    <i class="bi bi-person-badge"></i> Foto do Motorista
                                </td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">
                                        <i class="bi bi-check-circle mr-1"></i> Aprovado
                                    </span>
                                </td>
                                <td class="px-4 py-2">--</td>
                                <td class="px-4 py-2">10/03/2025</td>
                                <td class="px-4 py-2 flex gap-2">
                                    <label class="cursor-pointer bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded flex items-center gap-1 text-xs">
                                        <i data-lucide="upload" class="w-4 h-4"></i> Atualizar
                                        <input type="file" class="hidden" />
                                    </label>
                                    <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded flex items-center gap-1 text-xs">
                                        <i data-lucide="eye" class="w-4 h-4"></i> Visualizar
                                    </button>
                                </td>
                                <td class="px-4 py-2">
                                    <button onclick="showHistory('Foto')" class="bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded text-xs flex items-center gap-1">
                                        <i data-lucide="history" class="w-4 h-4"></i> Histórico
                                    </button>
                                </td>
                            </tr>
                            <!-- Antecedentes criminais -->
                            <tr>
                                <td class="px-4 py-2 font-semibold text-blue-900 flex items-center gap-2">
                                    <i class="bi bi-shield-exclamation"></i> Certidão Antecedentes
                                </td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">
                                        <i class="bi bi-clock-history mr-1"></i> Pendente
                                    </span>
                                </td>
                                <td class="px-4 py-2">--</td>
                                <td class="px-4 py-2">-</td>
                                <td class="px-4 py-2 flex gap-2">
                                    <label class="cursor-pointer bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded flex items-center gap-1 text-xs">
                                        <i data-lucide="upload" class="w-4 h-4"></i> Enviar
