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
        
        #sugestoes-cidades div {
            transition: background-color 0.2s;
        }
        #sugestoes-cidades div:hover {
            background-color: #f3f4f6;
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
                    <div class="w-full md:w-1/2 p-8 relative">
                        <form action="actions/action_cadastro_empresa.php" method="POST" id="cadastro-form" class="space-y-6">
                            <!-- Nome da Empresa -->
                            <div>
                                <div class="flex items-center mb-1">
                                    <i data-lucide="building-2" class="h-4 w-4 mr-2 text-teal-500"></i>
                                    <label for="nome" class="block text-sm font-medium text-gray-700">Nome da Empresa</label>
                                </div>
                                <input type="text" id="nome" name="nome" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Nome da empresa" required>
                            </div>

                            <!-- E-mail Corporativo -->
                            <div>
                                <div class="flex items-center mb-1">
                                    <i data-lucide="mail" class="h-4 w-4 mr-2 text-teal-500"></i>
                                    <label for="email" class="block text-sm font-medium text-gray-700">E-mail Corporativo</label>
                                </div>
                                <input type="email" id="email" name="email" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="empresa@exemplo.com" required>
                            </div>

                            <!-- CNPJ -->
                            <div>
                                <div class="flex items-center mb-1">
                                    <i data-lucide="id-card" class="h-4 w-4 mr-2 text-teal-500"></i>
                                    <label for="cnpj" class="block text-sm font-medium text-gray-700">CNPJ</label>
                                </div>
                                <input type="text" id="cnpj" name="cnpj" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="00.000.000/0000-00" required>
                            </div>

                            <!-- Telefone -->
                            <div>
                                <div class="flex items-center mb-1">
                                    <i data-lucide="phone" class="h-4 w-4 mr-2 text-teal-500"></i>
                                    <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                                </div>
                                <input type="text" id="telefone" name="telefone" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="(00) 00000-0000" required>
                            </div>

                            <!-- CEP -->
                            <div>
                                <div class="flex items-center mb-1">
                                    <i data-lucide="map-pin" class="h-4 w-4 mr-2 text-teal-500"></i>
                                    <label for="cep" class="block text-sm font-medium text-gray-700">CEP</label>
                                </div>
                                <input type="text" id="cep" name="cep" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="00000-000" required>
                            </div>
<!-- Endereço -->
<div>
    <div class="flex items-center mb-1 mt-4">
    <i data-lucide="home" class="h-4 w-4 mr-2 text-teal-500"></i>
        <label for="endereco" class="block text-sm font-medium text-gray-700">Endereço</label>
    </div>
    <input type="text" id="endereco" name="endereco" required class="mt-1 block w-full border border-gray-300 rounded-md p-2"placeholder="Digite o endereço">
</div>
                            <!-- Localização -->
                            <div class="relative">
                            <i data-lucide="map" class="h-4 w-4 mr-2 text-teal-500"></i>
    <label for="localizacao" class="block text-sm font-medium text-gray-700">Cidade</label>
    <input type="text" id="localizacao" name="cidade" autocomplete="off" required
           class="mt-1 block w-full border border-gray-300 rounded-md p-2"placeholder="Digite a cidade">
    <div id="sugestoes-cidades" class="border border-gray-300 rounded-md bg-white mt-1 hidden absolute z-10 w-full"></div>
</div>

                            <!-- Especialidades -->
                            <div>
                                <div class="flex items-center mb-1">
                                    <i data-lucide="stethoscope" class="h-4 w-4 mr-2 text-teal-500"></i>
                                    <label class="block text-sm font-medium text-gray-700">Especialidades</label>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input id="especialidade_cardiaco" name="especialidades[]" type="checkbox" value="Cardíaco" class="h-4 w-4 text-teal-500 focus:ring-teal-500 border-gray-300 rounded">
                                        <label for="especialidade_cardiaco" class="ml-2 block text-sm text-gray-700">
                                            <span class="font-medium">Cardíaco</span>
                                            <p class="text-xs text-gray-500">Monitoramento cardíaco especializado</p>
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="especialidade_cadeirantes" name="especialidades[]" type="checkbox" value="Cadeirantes" class="h-4 w-4 text-teal-500 focus:ring-teal-500 border-gray-300 rounded">
                                        <label for="especialidade_cadeirantes" class="ml-2 block text-sm text-gray-700">
                                            <span class="font-medium">Cadeirantes</span>
                                            <p class="text-xs text-gray-500">Veículos adaptados para mobilidade reduzida</p>
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="especialidade_idosos" name="especialidades[]" type="checkbox" value="Idosos" class="h-4 w-4 text-teal-500 focus:ring-teal-500 border-gray-300 rounded">
                                        <label for="especialidade_idosos" class="ml-2 block text-sm text-gray-700">
                                            <span class="font-medium">Idosos</span>
                                            <p class="text-xs text-gray-500">Atendimento especializado para a terceira idade</p>
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="especialidade_fisioterapia" name="especialidades[]" type="checkbox" value="Fisioterapia" class="h-4 w-4 text-teal-500 focus:ring-teal-500 border-gray-300 rounded">
                                        <label for="especialidade_fisioterapia" class="ml-2 block text-sm text-gray-700">
                                            <span class="font-medium">Fisioterapia</span>
                                            <p class="text-xs text-gray-500">Transporte para sessões de fisioterapia</p>
                                        </label>
                                    </div>
                                </div>
                            </div>

                             <!-- Tipos de Veículos -->
<div>
    <div class="flex items-center mb-1">
        <i data-lucide="ambulance" class="h-4 w-4 mr-2 text-teal-500"></i>
        <label class="block text-sm font-medium text-gray-700">Tipos de Veículos Disponíveis</label>
    </div>
    <div class="space-y-2">
        <div class="flex items-center">
            <input id="veiculo_padrao" name="tipos_veiculos[]" type="checkbox" value="Padrão" class="h-4 w-4 text-teal-500 focus:ring-teal-500 border-gray-300 rounded">
            <label for="veiculo_padrao" class="ml-2 block text-sm text-gray-700">
                <span class="font-medium">Veículo Padrão</span>
                <p class="text-xs text-gray-500">Para pacientes que podem se sentar durante o transporte</p>
            </label>
        </div>
        <div class="flex items-center">
            <input id="veiculo_cadeira" name="tipos_veiculos[]" type="checkbox" value="Cadeira de Rodas" class="h-4 w-4 text-teal-500 focus:ring-teal-500 border-gray-300 rounded">
            <label for="veiculo_cadeira" class="ml-2 block text-sm text-gray-700">
                <span class="font-medium">Adaptado para Cadeira de Rodas</span>
                <p class="text-xs text-gray-500">Veículo com elevador e fixação para cadeira de rodas</p>
            </label>
        </div>
        <div class="flex items-center">
            <input id="veiculo_maca" name="tipos_veiculos[]" type="checkbox" value="Maca" class="h-4 w-4 text-teal-500 focus:ring-teal-500 border-gray-300 rounded">
            <label for="veiculo_maca" class="ml-2 block text-sm text-gray-700">
                <span class="font-medium">Transporte com Maca</span>
                <p class="text-xs text-gray-500">Para pacientes que precisam permanecer deitados</p>
            </label>
        </div>
        <div class="flex items-center">
            <input id="veiculo_van" name="tipos_veiculos[]" type="checkbox" value="Van Adaptada" class="h-4 w-4 text-teal-500 focus:ring-teal-500 border-gray-300 rounded">
            <label for="veiculo_van" class="ml-2 block text-sm text-gray-700">
                <span class="font-medium">Van Adaptada</span>
                <p class="text-xs text-gray-500">Veículo amplo com adaptações para transporte de pacientes</p>
            </label>
        </div>
        <div class="flex items-center">
            <input id="veiculo_carro" name="tipos_veiculos[]" type="checkbox" value="Carro Comum" class="h-4 w-4 text-teal-500 focus:ring-teal-500 border-gray-300 rounded">
            <label for="veiculo_carro" class="ml-2 block text-sm text-gray-700">
                <span class="font-medium">Carro Comum</span>
                <p class="text-xs text-gray-500">Veículo padrão para transporte de pacientes ambulatoriais</p>
            </label>
        </div>
    </div>
</div>

                            <!-- Senha -->
                            <div>
                                <div class="flex items-center mb-1">
                                    <i data-lucide="lock" class="h-4 w-4 mr-2 text-teal-500"></i>
                                    <label for="senha" class="block text-sm font-medium text-gray-700">Senha</label>
                                </div>
                                <input type="password" id="senha" name="senha" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="••••••••" minlength="8" required>
                            </div>

                            <!-- Confirmar Senha -->
                            <div>
                                <div class="flex items-center mb-1">
                                    <i data-lucide="lock-keyhole" class="h-4 w-4 mr-2 text-teal-500"></i>
                                    <label for="confirmar_senha" class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                                </div>
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
        // Máscaras para os campos
        $('#cnpj').mask('00.000.000/0000-00');
        $('#telefone').mask('(00) 00000-0000');
        $('#cep').mask('00000-000');
        
        // buscar endereço via api quando cep for preenchido
        $('#cep').blur(function() {
    var cep = $(this).val().replace(/\D/g, '');

    if (cep.length === 8) {
        $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function(data) {
            if (!data.erro) {
                // Preenche os campos com os dados do endereço
                $('#endereco').val(data.logradouro);  // Rua
            } else {
                alert("CEP não encontrado.");
            }
        });
    }
});


        // funcao para buscar cidades via api ibge
        async function buscarCidades(termo) {
            try {
                const response = await fetch(`https://servicodados.ibge.gov.br/api/v1/localidades/municipios`);
                const cidades = await response.json();
                
                return cidades
                    .filter(cidade => cidade.nome.toLowerCase().includes(termo.toLowerCase()))
                    .map(cidade => ({
                        nome: `${cidade.nome} - ${cidade.microrregiao.mesorregiao.UF.sigla}`,
                        uf: cidade.microrregiao.mesorregiao.UF.sigla
                    }));
            } catch (error) {
                console.error('Erro ao buscar cidades:', error);
                return [];
            }
        }

        // elementos do dom para autocomplete de cidades
        const inputLocalizacao = document.getElementById('localizacao');
        const sugestoesContainer = document.getElementById('sugestoes-cidades');

        // evento de input para autocomplete
        inputLocalizacao.addEventListener('input', async function(e) {
            const termo = e.target.value.trim();
            
            if (termo.length < 3) {
                sugestoesContainer.classList.add('hidden');
                return;
            }
            
            const cidades = await buscarCidades(termo);
            
            if (cidades.length > 0) {
                sugestoesContainer.innerHTML = cidades.map(cidade => `
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer" 
                         data-value="${cidade.nome}">
                        ${cidade.nome}
                    </div>
                `).join('');
                
                sugestoesContainer.classList.remove('hidden');
            } else {
                sugestoesContainer.classList.add('hidden');
            }
        });

        // evento para selecionar uma cidade
        sugestoesContainer.addEventListener('click', function(e) {
            if (e.target.dataset.value) {
                inputLocalizacao.value = e.target.dataset.value;
                sugestoesContainer.classList.add('hidden');
            }
        });

        // esconder sugestoes ao clicar fora
        document.addEventListener('click', function(e) {
            if (!inputLocalizacao.contains(e.target) && !sugestoesContainer.contains(e.target)) {
                sugestoesContainer.classList.add('hidden');
            }
        });

        // validacao ao submeter o formulario
        document.getElementById('cadastro-form').addEventListener('submit', function(e) {
            const cidade = inputLocalizacao.value.trim();
            if (!cidade || !cidade.match(/^[A-Za-zÀ-ÿ\s]+ - [A-Z]{2}$/)) {
                alert('Por favor, selecione uma cidade válida no formato "Cidade - UF"');
                e.preventDefault();
                inputLocalizacao.focus();
            }
        });
    </script>
</body>
</html>