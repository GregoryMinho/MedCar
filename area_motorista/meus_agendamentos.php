<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Meus Agendamentos</title>
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
                <a href="#" class="flex items-center gap-3 ps-4 py-3 rounded-lg bg-blue-800 font-semibold">
                    <i class="bi bi-speedometer2 fs-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition font-semibold">
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
                <a href="#" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition mt-8 text-red-400">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                    <span>Sair</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 px-2 md:px-10 py-8">
            <!-- Título -->
            <h1 class="text-3xl font-bold text-blue-900 mb-2 flex items-center gap-2">
                <i data-lucide="calendar-days" class="h-7 w-7 text-blue-800"></i>
                Meus Agendamentos
            </h1>
            <p class="text-gray-500 mb-6">Acompanhe aqui todos os seus agendamentos de transporte e os próximos atendimentos.</p>
            
            <!-- Card de Resumo dos Agendamentos -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-xl shadow-lg p-4 flex flex-col items-center text-center">
                    <i data-lucide="calendar-check-2" class="h-8 w-8 text-green-500 mb-2"></i>
                    <div class="text-sm font-semibold text-gray-500">Agendamentos Hoje</div>
                    <div class="text-2xl font-bold text-blue-900">3</div>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-4 flex flex-col items-center text-center">
                    <i data-lucide="user-check" class="h-8 w-8 text-blue-500 mb-2"></i>
                    <div class="text-sm font-semibold text-gray-500">Transportes Finalizados</div>
                    <div class="text-xl font-bold text-blue-900">18 este mês</div>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-4 flex flex-col items-center text-center">
                    <i data-lucide="clock" class="h-8 w-8 text-yellow-500 mb-2"></i>
                    <div class="text-sm font-semibold text-gray-500">Próximo Agendamento</div>
                    <div class="text-xl font-bold text-blue-900">12/06/2025 - 09:00</div>
                </div>
            </div>

            <!-- Lista de Agendamentos (Tabela) -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                    <i data-lucide="list" class="h-5 w-5 mr-2 text-teal-500"></i>
                    Agenda Detalhada
                </h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horário</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origem</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destino</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr>
                                <td class="px-4 py-2">12/06/2025</td>
                                <td class="px-4 py-2">09:00</td>
                                <td class="px-4 py-2">Maria Oliveira</td>
                                <td class="px-4 py-2">Rua das Flores, 100</td>
                                <td class="px-4 py-2">Hospital São Lucas</td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">
                                        <i class="bi bi-clock-history mr-1"></i> Agendado
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">12/06/2025</td>
                                <td class="px-4 py-2">13:30</td>
                                <td class="px-4 py-2">João Souza</td>
                                <td class="px-4 py-2">Av. Central, 234</td>
                                <td class="px-4 py-2">Clínica Vida</td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">
                                        <i class="bi bi-clock-history mr-1"></i> Agendado
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">11/06/2025</td>
                                <td class="px-4 py-2">15:00</td>
                                <td class="px-4 py-2">Ana Costa</td>
                                <td class="px-4 py-2">Rua Rio Branco, 45</td>
                                <td class="px-4 py-2">Hospital Central</td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">
                                        <i class="bi bi-check2-circle mr-1"></i> Finalizado
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">10/06/2025</td>
                                <td class="px-4 py-2">08:00</td>
                                <td class="px-4 py-2">Pedro Ramos</td>
                                <td class="px-4 py-2">Rua Aurora, 201</td>
                                <td class="px-4 py-2">Laboratório Exame</td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">
                                        <i class="bi bi-check2-circle mr-1"></i> Finalizado
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">09/06/2025</td>
                                <td class="px-4 py-2">10:00</td>
                                <td class="px-4 py-2">Carla Nunes</td>
                                <td class="px-4 py-2">Av. Brasil, 321</td>
                                <td class="px-4 py-2">Clínica Vida</td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-red-100 text-red-800">
                                        <i class="bi bi-x-circle mr-1"></i> Cancelado
                                    </span>
                                </td>
                            </tr>
                            <!-- Adicione mais linhas conforme necessário -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Observação ou botão de exportar -->
            <div class="flex justify-end mt-2">
                <button class="bg-blue-800 hover:bg-blue-900 text-white font-semibold px-6 py-2 rounded-lg shadow transition flex items-center gap-2">
                    <i data-lucide="download"></i>
                    Exportar Agenda
                </button>
            </div>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
