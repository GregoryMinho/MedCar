<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Empresa</title>
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
                        <a href="/MedQ-2/area_cliente/menu_principal.php" class="font-medium hover:text-teal-300 transition">Home</a>
                        <a href="/MedQ-2/paginas/abas_menu_principal/aba_empresas.php" class="font-medium hover:text-teal-300 transition">Empresas</a>
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
                    <h2 class="text-3xl font-bold mb-2">Cadastro de Empresa</h2>
                    <p class="text-xl">Crie sua conta corporativa</p>
                </div>
                <div class="flex flex-col md:flex-row">
                    <!-- Cadastro Form -->
                    <div class="w-full md:w-1/2 p-8">
                        <form action="actions/action_cadastro_empresa.php" method="POST" id="cadastro-form" class="space-y-6">
                            <div>
                                <label for="nome" class="block text-sm font-medium text-gray-700">Nome da Empresa</label>
                                <input type="text" id="nome" name="nome" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Nome da empresa" required>
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">E-mail Corporativo</label>
                                <input type="email" id="email" name="email" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="empresa@exemplo.com" required>
                            </div>
                            <div>
                                <label for="cnpj" class="block text-sm font-medium text-gray-700">CNPJ</label>
                                <input type="text" id="cnpj" name="cnpj" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="00.000.000/0000-00" required>
                            </div>
                            <div>
                                <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                                <input type="text" id="telefone" name="telefone" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="(00) 00000-0000" required>
                            </div>
                            

                                     <!-- adicionei esses campos aqui -->
                           

                            <!-- add o  campo cep que não tinha --> 
                            <div>
                                <label for="cep" class="block text-sm font-medium text-gray-700">CEP</label>
                                <input type="text" id="cep" name="cep" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="00000-000" required>
                            </div>
                            
                            <!-- Localização -->
                            <div>
                                <label for="localizacao" class="block text-sm font-medium text-gray-700">Localização</label>
                                <select name="localizacao" id="localizacao" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" required>
                                    <option value="">Selecione sua localização</option>
                                    <option value="Salvador">Salvador - BA</option>
                                    <option value="Lauro de Freitas">Lauro de Freitas - BA</option>
                                    <option value="Camaçari">Camaçari - BA</option>
                                    <option value="Candeias">Candeias - BA</option>
                                </select>
                            </div>
                            
                            <!-- Especialidades (como select) -->
                            <div>
                                <label for="especialidade" class="block text-sm font-medium text-gray-700">Especialidade Principal</label>
                                <select name="especialidade" id="especialidade" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" required>
                                    <option value="">Selecione a especialidade principal</option>
                                    <option value="UTI">UTI Móvel</option>
                                    <option value="Cardíaco">Cardíaco</option>
                                    <option value="Cadeirantes">Cadeirantes</option>
                                    <option value="Idosos">Idosos</option>
                                    <option value="Fisioterapia">Fisioterapia</option>
                                </select>
                            </div>
                            
                            <!-- Tipos de Veículos (como select) -->
                            <div>
                                <label for="tipo_veiculo" class="block text-sm font-medium text-gray-700">Tipo de Veículo Principal</label>
                                <select name="tipo_veiculo" id="tipo_veiculo" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" required>
                                    <option value="">Selecione o tipo de veículo principal</option>
                                    <option value="UTI">Ambulância UTI</option>
                                    <option value="Leito">Ambulância Leito</option>
                                    <option value="Adaptado">Veículo Adaptado</option>
                                    <option value="Suporte Básico">Veículo de Suporte Básico</option>
                                </select>
                            </div>
                            
                            <!-- Disponibilidade (como checkboxes) -->
                             <P>Disponibilidade</P>
                            <div class="mt-2 space-y-2">
                                <div class="flex items-center">
                                    <input id="horas24" name="horas24" type="checkbox" value="1" class="h-4 w-4 text-teal-500 focus:ring-teal-500 border-gray-300 rounded">
                                    <label for="horas24" class="ml-2 block text-sm text-gray-700">Atendimento 24 Horas</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="plantao" name="plantao" type="checkbox" value="1" class="h-4 w-4 text-teal-500 focus:ring-teal-500 border-gray-300 rounded">
                                    <label for="plantao" class="ml-2 block text-sm text-gray-700">Plantão Fixo (Ex: 12h, 18h, etc.)</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="agendamento" name="agendamento" type="checkbox" value="1" class="h-4 w-4 text-teal-500 focus:ring-teal-500 border-gray-300 rounded">
                                    <label for="agendamento" class="ml-2 block text-sm text-gray-700">Atendimento com Agendamento</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="finais_semana" name="finais_semana" type="checkbox" value="1" class="h-4 w-4 text-teal-500 focus:ring-teal-500 border-gray-300 rounded">
                                    <label for="finais_semana" class="ml-2 block text-sm text-gray-700">Disponível em Finais de Semana</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="feriados" name="feriados" type="checkbox" value="1" class="h-4 w-4 text-teal-500 focus:ring-teal-500 border-gray-300 rounded">
                                    <label for="feriados" class="ml-2 block text-sm text-gray-700">Disponível em Feriados</label>
                                </div>
                            </div>
                            
                            <div>
                                <label for="senha" class="block text-sm font-medium text-gray-700">Senha</label>
                                <input type="password" id="senha" name="senha" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="••••••••"  minlength="8" required>
                            </div>
                            <div>
                                <label for="confirmar_senha" class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                                <input type="password" id="confirmar_senha" name="confirmar_senha" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="••••••••" minlength="8" required>
                            </div>
                            <p class="text-m text-red-600">
                                <?php // imprimir mensagem de erro, então limpa a variável de sessão
                                    if (isset($_SESSION['erro'])) {
                                        echo $_SESSION['erro'];
                                        unset($_SESSION['erro']);
                                    }
                                ?>
                            </p>
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-500 hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                                Cadastrar
                            </button>
                        </form>
                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600">
                                Já tem conta?
                                <a href="/MedQ-2/paginas/login_empresas.php" class="font-medium text-teal-500 hover:text-teal-600">Faça login aqui</a>
                            </p>
                        </div>
                    </div>
                    <!-- Benefits -->
                    <div class="w-full md:w-1/2 bg-gray-50 p-8">
                        <h4 class="text-xl font-bold mb-4 text-gray-800">Benefícios para Empresas</h4>
                        <ul class="space-y-2 mb-8 border-l-4 border-teal-500 pl-4">
                            <li>Gestão completa de transportes</li>
                            <li>Dashboard personalizado</li>
                            <li>Relatórios detalhados</li>
                            <li>Benefícios exclusivos para empresas parceiras</li>
                            <li>Suporte 24 horas</li>
                        </ul>
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
        $('#cnpj').mask('00.000.000/0000-00');
        $('#telefone').mask('(00) 00000-0000');
        $('#cep').mask('00000-000');
        
        // isso aqui pode ser opcional ou não, mas isso vai buscar endereço via api quando cep for preenchido 
        // obs: ainda deve ser testada.
        $('#cep').blur(function() {
            var cep = $(this).val().replace(/\D/g, '');
            if (cep.length === 8) {
                $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function(data) {
                    if (!data.erro) {
                        // aqui você pode preencher automaticamente outros campos de endereço se quiser
                        console.log(data); // ver os dados retornados
                    }
                });
            }
        });
    </script>
</body>

</html>