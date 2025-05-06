<?php // essa pagina é acessada apos o usuario selecionar a empresa para agendar
require '../includes/classe_usuario.php'; // inclui o arquivo de validação de login
require '../includes/conexao_BdCadastroLogin.php'; // inclui o arquivo de conexão com o banco de dados

use usuario\Usuario;

Usuario::verificarPermissao('cliente'); // verifica se o usuário logado é um cliente

// pegar o id da empresa selecionada por sessão
// $empresa_id = $_SESSION['empresa_id'];

$empresa_id = 1;    //////// temporario////////////////////////
$user_id = $_SESSION['usuario']['id']; // pega o id do cliente logado


$stmt = $conn->prepare("SELECT e.*, c.contato_emergencia FROM enderecos_clientes e JOIN clientes c WHERE e.id_cliente = :id AND e.id_cliente = c.id");
$stmt->bindParam(':id', $user_id);
$stmt->execute();
$endereco_cliente = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Agendar Transporte</title>
    <link rel="stylesheet" href="style/style_agendamento_cliente.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        function updateTransportType() {
            // Obtém o valor do rádio selecionado
            const selectedTransportType = document.querySelector('input[name="transport_type"]:checked').value;
            // Atualiza o valor do input oculto
            document.getElementById('hidden_transport_type').value = selectedTransportType;
        }
    </script>
</head>

<body class="min-h-screen bg-gray-50">

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a onclick="window.history.back();" class="flex items-center space-x-2 text-white hover:text-teal-300 transition">
                        <i data-lucide="arrow-left" class="h-6 w-6"></i>
                        <span>Voltar</span>
                    </a>
                </div>
                <a href="menu_principal.php" class="flex items-center space-x-2 text-xl font-bold">
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
                                <a href="menu_principal.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="panels-top-left" class="h-4 w-4 inline mr-2"></i>Menu Principal
                                </a>
                                <a href="perfil_cliente.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="user" class="h-4 w-4 inline mr-2"></i>Minha Conta
                                </a>
                                <a href="../paginas/abas_menu_principal/aba_empresas.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="calendar" class="h-4 w-4 inline mr-2"></i>Agendamentos
                                </a>
                                <a href="historico.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="clock" class="h-4 w-4 inline mr-2"></i>Histórico
                                </a>
                                <div class="border-t border-gray-300"></div>
                                <a href="../includes/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i data-lucide="log-out" class="h-4 w-4 inline mr-2"></i>Sair
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </nav>
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

                            <div method="POST" class="space-y-4">
                                <div class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:border-teal-500 cursor-pointer transition">
                                    <input type="radio" id="standard" name="transport_type" value="Padrão" class="h-5 w-5 text-teal-500 focus:ring-teal-500" checked onclick="updateTransportType()">
                                    <label for="standard" class="flex-1 cursor-pointer">
                                        <span class="font-medium text-blue-900 block">Veículo Padrão</span>
                                        <span class="text-sm text-gray-500">Para pacientes que podem se sentar durante o transporte</span>
                                    </label>
                                </div>

                                <div class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:border-teal-500 cursor-pointer transition">
                                    <input type="radio" id="wheelchair" name="transport_type" value="Cadeirante" class="h-5 w-5 text-teal-500 focus:ring-teal-500" onclick="updateTransportType()">
                                    <label for="wheelchair" class="flex-1 cursor-pointer">
                                        <span class="font-medium text-blue-900 block">Adaptado para Cadeira de Rodas</span>
                                        <span class="text-sm text-gray-500">Veículo com elevador e fixação para cadeira de rodas</span>
                                    </label>
                                </div>

                                <div class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:border-teal-500 cursor-pointer transition">
                                    <input type="radio" id="stretcher" name="transport_type" value="Maca" class="h-5 w-5 text-teal-500 focus:ring-teal-500" onclick="updateTransportType()">
                                    <label for="stretcher" class="flex-1 cursor-pointer">
                                        <span class="font-medium text-blue-900 block">Transporte com Maca</span>
                                        <span class="text-sm text-gray-500">Para pacientes que precisam permanecer deitados</span>
                                    </label>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Form -->
                    <form action="actions/action_agendamento.php" method="POST" class="md:w-1/2">
                        <input type="hidden" id="empresa_id" name="empresa_id" value="<?php echo $empresa_id; ?>">
                        <input type="hidden" id="horario_selecionado" name="horario_selecionado" value="14:00">
                        <input type="hidden" id="data_consulta" name="data_consulta">
                        <input type="hidden" id="hidden_transport_type" name="hidden_transport_type" value="Padrão">
                        <div>
                            <div class="form-card bg-white rounded-xl shadow-md p-6 mb-6">
                                <h2 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                                    <i data-lucide="map-pin" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    Endereço de Origem
                                </h2>
                                <div class="flex justify-end">
                                    <button type="button" id="toggle-address" class="bg-orange-500 hover:bg-orange-700 font-semibold text-white font-medium py-2 px-4 rounded-lg transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-opacity-50">
                                        Usar endereço salvo
                                    </button>
                                </div>
                                <!-- Pickup Address -->
                                <div class="mb-6">

                                    <div class="space-y-4">
                                        <div>
                                            <label for="pickup_street" class="block text-gray-700 font-medium mb-1">Rua/Avenida</label>
                                            <input type="text" id="pickup_street" name="pickup_street" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: Av. Paulista">
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label for="pickup_number" class="block text-gray-700 font-medium mb-1">Número</label>
                                                <input type="text" id="pickup_number" name="pickup_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: 1000">
                                            </div>
                                            <div>
                                                <label for="pickup_complement" class="block text-gray-700 font-medium mb-1">Complemento</label>
                                                <input type="text" id="pickup_complement" name="pickup_complement" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: Apto 101">
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label for="pickup_city" class="block text-gray-700 font-medium mb-1">Cidade</label>
                                                <input type="text" id="pickup_city" name="pickup_city" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: São Paulo">
                                            </div>
                                            <div>
                                                <label for="pickup_zipcode" class="block text-gray-700 font-medium mb-1">CEP</label>
                                                <input type="text" id="pickup_zipcode" name="pickup_zipcode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: 01310-100">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t border-gray-300 m-7"></div>
                            <!-- Destination Address -->
                            <div>
                                <h2 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                                    <i data-lucide="map-pin" class="h-5 w-5 mr-2 text-teal-500"></i>
                                    Endereço Destino
                                </h2>
                                <div class="space-y-4">
                                    <div>
                                        <label for="dest_street" class="block text-gray-700 font-medium mb-1">Rua/Avenida</label>
                                        <input type="text" id="dest_street" name="dest_street" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: Rua Vergueiro">
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="dest_number" class="block text-gray-700 font-medium mb-1">Número</label>
                                            <input type="text" id="dest_number" name="dest_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: 2000">
                                        </div>
                                        <div>
                                            <label for="dest_complement" class="block text-gray-700 font-medium mb-1">Complemento</label>
                                            <input type="text" id="dest_complement" name="dest_complement" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: Hospital, Sala 202">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="dest_city" class="block text-gray-700 font-medium mb-1">Cidade</label>
                                            <input type="text" id="dest_city" name="dest_city" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: São Paulo">
                                        </div>
                                        <div>
                                            <label for="dest_zipcode" class="block text-gray-700 font-medium mb-1">CEP</label>
                                            <input type="text" id="dest_zipcode" name="dest_zipcode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Ex: 04101-300">
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
                                    <textarea id="medical_condition" name="medical_condition" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Descreva sua condição médica e necessidades específicas durante o transporte..."></textarea>
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Necessidades Especiais</label>
                                    <div class="space-y-2">
                                        <div class="flex items-center">
                                            <input type="checkbox" id="need_oxygen" name="need_oxygen" class="h-4 w-4 text-teal-500 focus:ring-teal-500 mr-2">
                                            <label for="need_oxygen" class="text-gray-700">Necessita de oxigênio</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="checkbox" id="need_assistance" name="need_assistance" class="h-4 w-4 text-teal-500 focus:ring-teal-500 mr-2">
                                            <label for="need_assistance" class="text-gray-700">Necessita de assistência para locomoção</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="checkbox" id="need_monitor" name="need_monitor" class="h-4 w-4 text-teal-500 focus:ring-teal-500 mr-2">
                                            <label for="need_monitor" class="text-gray-700">Necessita de monitoramento de sinais vitais</label>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="medications" class="block text-gray-700 font-medium mb-1">Medicamentos em uso</label>
                                    <input type="text" id="medications" name="medications" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Liste os medicamentos que você utiliza regularmente">
                                </div>

                                <div>
                                    <label for="allergies" class="block text-gray-700 font-medium mb-1">Alergias</label>
                                    <input type="text" id="allergies" name="allergies" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Liste suas alergias, se houver">
                                </div>

                                <div>
                                    <label for="emergency_contact" class="block text-gray-700 font-medium mb-1">Contato de Emergência</label>
                                    <input type="text" id="emergency_contact" value="<?= $endereco_cliente['contato_emergencia'] ?? '' ?>" name="emergency_contact" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Telefone de um contato de emergência">
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
                                    <textarea id="additional_info" name="additional_info" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Informações adicionais que possam ser relevantes para o transporte. Motivo da viagem."></textarea>
                                </div>

                                <div>
                                    <label for="companion" class="block text-gray-700 font-medium mb-1">Acompanhante</label>
                                    <select id="companion" name="companion" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                        <option value="" disabled>Selecione uma opção</option>
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
                </form>
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
    <script src="script/script_agendamento.js"></script> <!-- script do calendario -->
    <script>
        // alerta para mensagem se o agendamento foi realizado com sucesso ou não
        document.addEventListener('DOMContentLoaded', function() {
            var cadastrado = <?php echo json_encode($_SESSION['cadastrado']); ?> ?? null;

            if (cadastrado == 1) {
                alert('AGENDADO COM SUCESSO! (*^_^*) \n\nAguardando confirmação da empresa.');
            } else if (cadastrado == 0) {
                alert('ERRO AO AGENDAR! (T_T) \n\nPor favor, tente novamente.');
            }

            <?php $_SESSION['cadastrado'] = null ?>;

        });
    </script>
    <script>
        // Função para mostar ou ocultar o endereço salvo
        let isUsingAddress = false; // Variável para controlar o estado do endereço
        const rua = "<?= $endereco_cliente['rua'] ?? '' ?>";
        const numero = "<?= $endereco_cliente['numero'] ?? '' ?>";
        const complemento = "<?= $endereco_cliente['complemento'] ?? '' ?>";
        const cidade = "<?= $endereco_cliente['cidade'] ?? '' ?>";
        const cep = "<?= $endereco_cliente['cep'] ?? '' ?>";

        document.getElementById('toggle-address').addEventListener('click', function() {
            if (!isUsingAddress) {
                document.getElementById('pickup_street').value = rua;
                document.getElementById('pickup_number').value = numero;
                document.getElementById('pickup_complement').value = complemento;
                document.getElementById('pickup_city').value = cidade;
                document.getElementById('pickup_zipcode').value = cep;
                isUsingAddress = true; // Atualiza o estado para "usando endereço"
            } else {
                document.getElementById('pickup_street').value = '';
                document.getElementById('pickup_number').value = '';
                document.getElementById('pickup_complement').value = '';
                document.getElementById('pickup_city').value = '';
                document.getElementById('pickup_zipcode').value = '';
                isUsingAddress = false; // Atualiza o estado para "não usando endereço"
            }
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>
    <script src="../jquery.mask.min.js"></script>
    <script>
        $('#emergency_contact').mask('(00) 00000-0000');
    </script>
</body>

</html>