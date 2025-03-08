<?php // essa pagina é acessa apos o usuario selecionar a empresa 
require '../includes/valida_login.php'; // inclui o arquivo de validação de login

verificarPermissao('CLIENTE'); // verifica se o usuario logado é um cliente

// logica para pegar dados basicos da empresa para enviar a solicitação de agendamento
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Agendar Transporte</title>
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
        .calendar-day {
            transition: all 0.2s ease;
        }
        .calendar-day:hover:not(.calendar-day-disabled) {
            background-color: rgba(56, 178, 172, 0.1);
            transform: scale(1.05);
        }
        .calendar-day-selected {
            background-color: rgba(56, 178, 172, 0.2);
            border: 2px solid #38b2ac;
            color: #38b2ac;
            font-weight: bold;
        }
        .calendar-day-disabled {
            color: #cbd5e0;
            cursor: not-allowed;
        }
        .form-card {
            transition: all 0.3s ease;
        }
        .form-card:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .group:hover .group-hover\:visible {
            visibility: visible;
        }
        .group:hover .group-hover\:opacity-100 {
            opacity: 1;
        }
        .group:hover .group-hover\:translate-y-0 {
            transform: translateY(0);
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="index.html" class="flex items-center space-x-2 text-xl font-bold">
                    <i data-lucide="ambulance" class="h-6 w-6"></i>
                    <span>MedCar</span>
                </a>
            
                <div class="flex items-center space-x-6">
                    <div class="relative group">
                        <button class="flex items-center space-x-1 font-medium hover:text-teal-300 transition">
                            <i data-lucide="user" class="h-5 w-5"></i>
                            <span>Perfil</span>
                            <i data-lucide="chevron-down" class="h-4 w-4"></i>
                        </button>
                        <div class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 invisible group-hover:visible transition-all duration-300 opacity-0 group-hover:opacity-100 transform group-hover:translate-y-0 translate-y-2">
                            <div class="py-1">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="user" class="h-4 w-4 inline mr-2"></i>Minha Conta
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="calendar" class="h-4 w-4 inline mr-2"></i>Agendamentos
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="settings" class="h-4 w-4 inline mr-2"></i>Configurações
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i data-lucide="log-out" class="h-4 w-4 inline mr-2"></i>Sair
                                </a>
                            </div>
                        </div>
                    </div>
                    <a href="index.html#funcionalidades" class="font-medium hover:text-teal-300 transition">Funcionalidades</a>
                    <a href="index.html#vantagens" class="font-medium hover:text-teal-300 transition">Vantagens</a>
                    <a href="index.html#contato" class="font-medium hover:text-teal-300 transition">Contato</a>
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
            <div class="flex flex-col items-center space-y-4">
                <span class="font-medium text-teal-300">Perfil</span>
                <div class="flex flex-col items-center space-y-3 text-base">
                    <a href="#" class="font-medium hover:text-teal-300 transition flex items-center">
                        <i data-lucide="user" class="h-5 w-5 mr-2"></i>Minha Conta
                    </a>
                    <a href="#" class="font-medium hover:text-teal-300 transition flex items-center">
                        <i data-lucide="calendar" class="h-5 w-5 mr-2"></i>Agendamentos
                    </a>
                    <a href="#" class="font-medium hover:text-teal-300 transition flex items-center">
                        <i data-lucide="settings" class="h-5 w-5 mr-2"></i>Configurações
                    </a>
                    <a href="#" class="font-medium text-red-400 hover:text-red-300 transition flex items-center">
                        <i data-lucide="log-out" class="h-5 w-5 mr-2"></i>Sair
                    </a>
                </div>
            </div>
            <a href="index.html#funcionalidades" class="font-medium hover:text-teal-300 transition">Funcionalidades</a>
            <a href="index.html#vantagens" class="font-medium hover:text-teal-300 transition">Vantagens</a>
            <a href="index.html#contato" class="font-medium hover:text-teal-300 transition">Contato</a>
        </div>
    </div>

    <!-- Header Section -->
    <section class="pt-24 pb-10 bg-gradient-to-r from-blue-900 to-blue-800 text-white">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">Agendar Transporte Médico</h1>
            <p class="text-xl text-blue-100">
                Preencha o formulário abaixo para agendar seu transporte médico não emergencial.
            </p>
        </div>
    </section>

    <!-- Scheduling Form Section -->
    <section class="py-10">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:space-x-8">
                    <!-- Left Column - Calendar -->
                    <div class="md:w-1/2 mb-8 md:mb-0">
                        <div class="form-card bg-white rounded-xl shadow-md p-6 mb-6">
                            <h2 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                                <i data-lucide="calendar" class="h-5 w-5 mr-2 text-teal-500"></i>
                                Selecione a Data
                            </h2>
                            
                            <!-- Month Navigation -->
                            <div class="flex justify-between items-center mb-4">
                                <button id="prev-month" class="p-2 rounded-full hover:bg-gray-100 transition">
                                    <i data-lucide="chevron-left" class="h-5 w-5 text-blue-900"></i>
                                </button>
                                <div class="flex items-center">
                                    <select id="month-select" class="text-lg font-semibold text-blue-900 bg-transparent border-none focus:outline-none focus:ring-0 cursor-pointer">
                                        <option value="0">Janeiro</option>
                                        <option value="1">Fevereiro</option>
                                        <option value="2">Março</option>
                                        <option value="3">Abril</option>
                                        <option value="4">Maio</option>
                                        <option value="5">Junho</option>
                                        <option value="6">Julho</option>
                                        <option value="7">Agosto</option>
                                        <option value="8">Setembro</option>
                                        <option value="9">Outubro</option>
                                        <option value="10">Novembro</option>
                                        <option value="11">Dezembro</option>
                                    </select>
                                    <select id="year-select" class="text-lg font-semibold text-blue-900 bg-transparent border-none focus:outline-none focus:ring-0 ml-2 cursor-pointer">
                                        <!-- Anos serão adicionados via JavaScript -->
                                    </select>
                                </div>
                                <button id="next-month" class="p-2 rounded-full hover:bg-gray-100 transition">
                                    <i data-lucide="chevron-right" class="h-5 w-5 text-blue-900"></i>
                                </button>
                            </div>
                            
                            <!-- Calendar -->
                            <div class="mb-4">
                                <!-- Days of Week -->
                                <div class="grid grid-cols-7 gap-1 mb-2">
                                    <div class="text-center font-medium text-gray-500 text-sm">Dom</div>
                                    <div class="text-center font-medium text-gray-500 text-sm">Seg</div>
                                    <div class="text-center font-medium text-gray-500 text-sm">Ter</div>
                                    <div class="text-center font-medium text-gray-500 text-sm">Qua</div>
                                    <div class="text-center font-medium text-gray-500 text-sm">Qui</div>
                                    <div class="text-center font-medium text-gray-500 text-sm">Sex</div>
                                    <div class="text-center font-medium text-gray-500 text-sm">Sáb</div>
                                </div>
                                
                                <!-- Calendar Days -->
                                <div id="calendar-days" class="grid grid-cols-7 gap-1">
                                    <!-- Os dias do calendário serão gerados via JavaScript -->
                                </div>
                            </div>
                            
                            <!-- Time Selection -->
                            <div>
                                <h3 class="text-lg font-semibold text-blue-900 mb-3">Horário</h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                    <div class="time-slot border border-gray-300 rounded-lg p-2 text-center cursor-pointer hover:bg-teal-50 hover:border-teal-500 transition">08:00</div>
                                    <div class="time-slot border border-gray-300 rounded-lg p-2 text-center cursor-pointer hover:bg-teal-50 hover:border-teal-500 transition">09:00</div>
                                    <div class="time-slot border border-gray-300 rounded-lg p-2 text-center cursor-pointer hover:bg-teal-50 hover:border-teal-500 transition">10:00</div>
                                    <div class="time-slot border border-gray-300 rounded-lg p-2 text-center cursor-pointer hover:bg-teal-50 hover:border-teal-500 transition">11:00</div>
                                    <div class="time-slot border border-gray-300 rounded-lg p-2 text-center cursor-pointer hover:bg-teal-50 hover:border-teal-500 transition">13:00</div>
                                    <div class="time-slot border border-teal-500 bg-teal-50 rounded-lg p-2 text-center cursor-pointer">14:00</div>
                                    <div class="time-slot border border-gray-300 rounded-lg p-2 text-center cursor-pointer hover:bg-teal-50 hover:border-teal-500 transition">15:00</div>
                                    <div class="time-slot border border-gray-300 rounded-lg p-2 text-center cursor-pointer hover:bg-teal-50 hover:border-teal-500 transition">16:00</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Transport Type -->
                        <div class="form-card bg-white rounded-xl shadow-md p-6">
                            <h2 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                                <i data-lucide="truck" class="h-5 w-5 mr-2 text-teal-500"></i>
                                Tipo de Transporte
                            </h2>
                            
                            <div class="space-y-4">
                                <div class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:border-teal-500 cursor-pointer transition">
                                    <input type="radio" id="standard" name="transport_type" class="h-5 w-5 text-teal-500 focus:ring-teal-500" checked>
                                    <label for="standard" class="flex-1 cursor-pointer">
                                        <span class="font-medium text-blue-900 block">Veículo Padrão</span>
                                        <span class="text-sm text-gray-500">Para pacientes que podem se sentar durante o transporte</span>
                                    </label>
                                </div>
                                
                                <div class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:border-teal-500 cursor-pointer transition">
                                    <input type="radio" id="wheelchair" name="transport_type" class="h-5 w-5 text-teal-500 focus:ring-teal-500">
                                    <label for="wheelchair" class="flex-1 cursor-pointer">
                                        <span class="font-medium text-blue-900 block">Adaptado para Cadeira de Rodas</span>
                                        <span class="text-sm text-gray-500">Veículo com elevador e fixação para cadeira de rodas</span>
                                    </label>
                                </div>
                                
                                <div class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:border-teal-500 cursor-pointer transition">
                                    <input type="radio" id="stretcher" name="transport_type" class="h-5 w-5 text-teal-500 focus:ring-teal-500">
                                    <label for="stretcher" class="flex-1 cursor-pointer">
                                        <span class="font-medium text-blue-900 block">Transporte com Maca</span>
                                        <span class="text-sm text-gray-500">Para pacientes que precisam permanecer deitados</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column - Form -->
                    <div class="md:w-1/2">
                        <div class="form-card bg-white rounded-xl shadow-md p-6 mb-6">
                            <h2 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                                <i data-lucide="map-pin" class="h-5 w-5 mr-2 text-teal-500"></i>
                                Endereços
                            </h2>
                            
                            <!-- Pickup Address -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-blue-900 mb-3">Endereço de Origem</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="pickup_street" class="block text-gray-700 font-medium mb-1">Rua/Avenida</label>
                                        <input type="text" id="pickup_street" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: Av. Paulista">
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="pickup_number" class="block text-gray-700 font-medium mb-1">Número</label>
                                            <input type="text" id="pickup_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: 1000">
                                        </div>
                                        <div>
                                            <label for="pickup_complement" class="block text-gray-700 font-medium mb-1">Complemento</label>
                                            <input type="text" id="pickup_complement" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: Apto 101">
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="pickup_city" class="block text-gray-700 font-medium mb-1">Cidade</label>
                                            <input type="text" id="pickup_city" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: São Paulo">
                                        </div>
                                        <div>
                                            <label for="pickup_zipcode" class="block text-gray-700 font-medium mb-1">CEP</label>
                                            <input type="text" id="pickup_zipcode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: 01310-100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Destination Address -->
                            <div>
                                <h3 class="text-lg font-semibold text-blue-900 mb-3">Endereço de Destino</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="dest_street" class="block text-gray-700 font-medium mb-1">Rua/Avenida</label>
                                        <input type="text" id="dest_street" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: Rua Vergueiro">
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="dest_number" class="block text-gray-700 font-medium mb-1">Número</label>
                                            <input type="text" id="dest_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: 2000">
                                        </div>
                                        <div>
                                            <label for="dest_complement" class="block text-gray-700 font-medium mb-1">Complemento</label>
                                            <input type="text" id="dest_complement" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: Hospital, Sala 202">
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="dest_city" class="block text-gray-700 font-medium mb-1">Cidade</label>
                                            <input type="text" id="dest_city" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: São Paulo">
                                        </div>
                                        <div>
                                            <label for="dest_zipcode" class="block text-gray-700 font-medium mb-1">CEP</label>
                                            <input type="text" id="dest_zipcode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: 04101-300">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Medical Conditions -->
                        <div class="form-card bg-white rounded-xl shadow-md p-6 mb-6">
                            <h2 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                                <i data-lucide="activity" class="h-5 w-5 mr-2 text-teal-500"></i>
                                Condições Médicas
                            </h2>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="medical_condition" class="block text-gray-700 font-medium mb-1">Descreva sua condição médica</label>
                                    <textarea id="medical_condition" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Descreva sua condição médica e necessidades específicas durante o transporte..."></textarea>
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Necessidades Especiais</label>
                                    <div class="space-y-2">
                                        <div class="flex items-center">
                                            <input type="checkbox" id="need_oxygen" class="h-4 w-4 text-teal-500 focus:ring-teal-500 mr-2">
                                            <label for="need_oxygen" class="text-gray-700">Necessita de oxigênio</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="checkbox" id="need_assistance" class="h-4 w-4 text-teal-500 focus:ring-teal-500 mr-2">
                                            <label for="need_assistance" class="text-gray-700">Necessita de assistência para locomoção</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="checkbox" id="need_monitor" class="h-4 w-4 text-teal-500 focus:ring-teal-500 mr-2">
                                            <label for="need_monitor" class="text-gray-700">Necessita de monitoramento de sinais vitais</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="medications" class="block text-gray-700 font-medium mb-1">Medicamentos em uso</label>
                                    <input type="text" id="medications" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Liste os medicamentos que você utiliza regularmente">
                                </div>
                                
                                <div>
                                    <label for="allergies" class="block text-gray-700 font-medium mb-1">Alergias</label>
                                    <input type="text" id="allergies" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Liste suas alergias, se houver">
                                </div>
                                
                                <div>
                                    <label for="emergency_contact" class="block text-gray-700 font-medium mb-1">Contato de Emergência</label>
                                    <input type="text" id="emergency_contact" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Nome e telefone de um contato de emergência">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Information -->
                        <div class="form-card bg-white rounded-xl shadow-md p-6 mb-6">
                            <h2 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                                <i data-lucide="info" class="h-5 w-5 mr-2 text-teal-500"></i>
                                Informações Adicionais
                            </h2>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="additional_info" class="block text-gray-700 font-medium mb-1">Observações</label>
                                    <textarea id="additional_info" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Informações adicionais que possam ser relevantes para o transporte. Motivo da viagem."></textarea>
                                </div>
                                
                                <div>
                                    <label for="companion" class="block text-gray-700 font-medium mb-1">Acompanhante</label>
                                    <select id="companion" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                        <option value="">Selecione uma opção</option>
                                        <option value="0">Não preciso de acompanhante</option>
                                        <option value="1" selected>1 acompanhante</option>
                                        <option value="2">2 acompanhantes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-3 px-8 rounded-lg transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-opacity-50">
                                Confirmar Agendamento
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-blue-900 text-white py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <a href="#" class="flex items-center space-x-2 text-xl font-bold">
                        <i data-lucide="ambulance" class="h-6 w-6"></i>
                        <span>MedCar</span>
                    </a>
                </div>
                
                <div class="text-center md:text-right">
                    <p class="text-blue-200">&copy; 2023 MedCar. Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

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

        // Calendar functionality
        const currentDate = new Date();
        let currentMonth = currentDate.getMonth();
        let currentYear = currentDate.getFullYear();
        let selectedDate = null;

        const monthSelect = document.getElementById('month-select');
        const yearSelect = document.getElementById('year-select');
        const prevMonthBtn = document.getElementById('prev-month');
        const nextMonthBtn = document.getElementById('next-month');
        const calendarDaysContainer = document.getElementById('calendar-days');

        // Populate year select
        const startYear = currentYear;
        const endYear = currentYear + 5;
        for (let year = startYear; year <= endYear; year++) {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            yearSelect.appendChild(option);
        }

        // Set default values for month and year selects
        monthSelect.value = currentMonth;
        yearSelect.value = currentYear;

        // Generate calendar
        function generateCalendar(month, year) {
            calendarDaysContainer.innerHTML = '';
            
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startingDay = firstDay.getDay(); // 0 = Sunday, 1 = Monday, etc.
            
            // Previous month days
            const prevMonthLastDay = new Date(year, month, 0).getDate();
            for (let i = startingDay - 1; i >= 0; i--) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day calendar-day-disabled h-12 flex items-center justify-center rounded-lg';
                dayElement.textContent = prevMonthLastDay - i;
                calendarDaysContainer.appendChild(dayElement);
            }
            
            // Current month days
            const today = new Date();
            const isCurrentMonth = today.getMonth() === month && today.getFullYear() === year;
            
            for (let i = 1; i <= daysInMonth; i++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day h-12 flex items-center justify-center rounded-lg cursor-pointer';
                
                // Check if this is today
                if (isCurrentMonth && i === today.getDate()) {
                    dayElement.classList.add('bg-blue-100');
                }
                
                // Check if this is the selected date
                if (selectedDate && selectedDate.getDate() === i && 
                    selectedDate.getMonth() === month && 
                    selectedDate.getFullYear() === year) {
                    dayElement.classList.add('calendar-day-selected');
                }
                
                dayElement.textContent = i;
                
                // Add click event
                dayElement.addEventListener('click', () => {
                    // Remove selected class from all days
                    document.querySelectorAll('.calendar-day-selected').forEach(el => {
                        el.classList.remove('calendar-day-selected');
                    });
                    
                    // Add selected class to clicked day
                    dayElement.classList.add('calendar-day-selected');
                    
                    // Update selected date
                    selectedDate = new Date(year, month, i);
                });
                
                calendarDaysContainer.appendChild(dayElement);
            }
            
            // Next month days
            const totalCells = 42; // 6 rows of 7 days
            const remainingCells = totalCells - (startingDay + daysInMonth);
            
            for (let i = 1; i <= remainingCells; i++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day calendar-day-disabled h-12 flex items-center justify-center rounded-lg';
                dayElement.textContent = i;
                calendarDaysContainer.appendChild(dayElement);
            }
        }

        // Event listeners for month navigation
        prevMonthBtn.addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            monthSelect.value = currentMonth;
            yearSelect.value = currentYear;
            generateCalendar(currentMonth, currentYear);
        });

        nextMonthBtn.addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            monthSelect.value = currentMonth;
            yearSelect.value = currentYear;
            generateCalendar(currentMonth, currentYear);
        });

        // Event listeners for month and year selects
        monthSelect.addEventListener('change', () => {
            currentMonth = parseInt(monthSelect.value);
            generateCalendar(currentMonth, currentYear);
        });

        yearSelect.addEventListener('change', () => {
            currentYear = parseInt(yearSelect.value);
            generateCalendar(currentMonth, currentYear);
        });

        // Initialize calendar
        generateCalendar(currentMonth, currentYear);

        // Time slot selection
        const timeSlots = document.querySelectorAll('.time-slot');
        timeSlots.forEach(slot => {
            slot.addEventListener('click', () => {
                // Remove selected class from all slots
                timeSlots.forEach(s => {
                    s.classList.remove('bg-teal-50');
                    s.classList.remove('border-teal-500');
                    s.classList.add('border-gray-300');
                });
                // Add selected class to clicked slot
                slot.classList.remove('border-gray-300');
                slot.classList.add('bg-teal-50');
                slot.classList.add('border-teal-500');
            });
        });
    </script>
</body>
</html>