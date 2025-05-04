<?php
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Pesquisar Empresas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
     <link rel="stylesheet" href="style/style_pagina_pesquisa_empresa.css">
</head>
<body class="min-h-screen bg-gray-50">
    <nav class="fixed top-0 left-0 right-0 z-50 header-gradient text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="#" class="flex items-center space-x-2 text-xl font-bold">
                    <i data-lucide="ambulance" class="h-6 w-6 text-teal-300"></i>
                    <span>MedCar</span>
                </a>

                <div class="hidden md:flex space-x-6">
                    <a href="/MedQ-2/area_cliente/menu_principal.php" class="font-medium hover:text-teal-300 transition duration-300">Home</a>
                    <a href="aba_entrar.php" class="font-medium hover:text-teal-300 transition duration-300">Entrar</a>
                </div>

                <button id="mobile-menu-button" class="md:hidden text-white focus:outline-none">
                    <i data-lucide="menu" class="h-6 w-6"></i>
                </button>
            </div>
        </div>
    </nav>

    <div id="mobile-menu" class="fixed inset-0 z-50 bg-blue-900 bg-opacity-95 flex flex-col text-white p-6 mobile-menu">
        <div class="flex justify-end">
            <button id="close-menu-button" class="text-white focus:outline-none">
                <i data-lucide="x" class="h-6 w-6"></i>
            </button>
        </div>

        <div class="flex flex-col items-center justify-center space-y-8 flex-grow text-xl">
            <a href="#" class="font-medium hover:text-teal-300 transition duration-300">Home</a>
            <a href="aba_entrar.php" class="font-medium hover:text-teal-300 transition duration-300">Entrar</a>
            <a href="#" class="font-medium hover:text-teal-300 transition duration-300">Empresas</a>
        </div>
    </div>

    <section class="pt-32 pb-20 header-gradient text-white">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                Pesquisar Empresas
            </h1>
            <p class="text-xl opacity-90 max-w-2xl mx-auto">
                Encontre a melhor empresa para seu transporte médico
            </p>
        </div>
    </section>

    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="search-section p-8 mb-8">
                <form action="actions/action_pesquisar_empresa.php" method="GET">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Pesquisar Empresas</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="relative">
                            <div class="flex items-center mb-2">
                                <i data-lucide="map-pin" class="h-4 w-4 mr-2 text-teal-500"></i>
                                <label for="localizacao" class="block text-sm font-medium text-gray-700">Localização</label>
                            </div>
                            <input type="text" id="localizacao" name="localizacao" autocomplete="off"
                                class="mt-1 block w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent shadow-sm" 
                                placeholder="Digite a cidade">
                            <div id="sugestoes-cidades" class="border border-gray-200 rounded-lg bg-white mt-1 hidden absolute z-10 w-full shadow-lg max-h-60 overflow-auto"></div>
                        </div>

                        <div>
                            <div class="flex items-center mb-2">
                                <i data-lucide="heart-pulse" class="h-4 w-4 mr-2 text-teal-500"></i>
                                <label class="block text-sm font-medium text-gray-700">Especialidade</label>
                            </div>
                            <select name="especialidade" id="specialty-filter" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent shadow-sm">
                                <option value="">Todas as especialidades</option>
                                <option value="Cardíaco" <?php echo ($_GET['especialidade'] ?? '') == 'Cardíaco' ? 'selected' : ''; ?>>Cardíaco</option>
                                <option value="Cadeirantes" <?php echo ($_GET['especialidade'] ?? '') == 'Cadeirantes' ? 'selected' : ''; ?>>Cadeirantes</option>
                                <option value="Idosos" <?php echo ($_GET['especialidade'] ?? '') == 'Idosos' ? 'selected' : ''; ?>>Idosos</option>
                                <option value="Fisioterapia" <?php echo ($_GET['especialidade'] ?? '') == 'Fisioterapia' ? 'selected' : ''; ?>>Fisioterapia</option>
                            </select>
                        </div>

                        <div>
                            <div class="flex items-center mb-2">
                                <i data-lucide="car" class="h-4 w-4 mr-2 text-teal-500"></i>
                                <label class="block text-sm font-medium text-gray-700">Tipo de Veículo</label>
                            </div>
                            <select name="tipo_veiculo" id="vehicle-filter" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent shadow-sm">
                                <option value="">Todos os veículos</option>
                                <option value="Padrão" <?php echo ($_GET['tipo_veiculo'] ?? '') == 'Padrão' ? 'selected' : ''; ?>>Veículo Padrão</option>
                                <option value="Cadeira de Rodas" <?php echo ($_GET['tipo_veiculo'] ?? '') == 'Cadeira de Rodas' ? 'selected' : ''; ?>>Adaptado para Cadeira de Rodas</option>
                                <option value="Maca" <?php echo ($_GET['tipo_veiculo'] ?? '') == 'Maca' ? 'selected' : ''; ?>>Transporte com Maca</option>
                                <option value="Van Adaptada" <?php echo ($_GET['tipo_veiculo'] ?? '') == 'Van Adaptada' ? 'selected' : ''; ?>>Van Adaptada</option>
                                <option value="Carro Comum" <?php echo ($_GET['tipo_veiculo'] ?? '') == 'Carro Comum' ? 'selected' : ''; ?>>Carro Comum</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 mt-8">
                        <a href="pesquisar_empresa.php" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 btn-secondary text-center">
                            Limpar Filtros
                        </a>
                        <button type="submit" class="px-6 py-3 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-300 btn-primary text-center">
                            Pesquisar Empresas
                        </button>
                    </div>
                </form>
            </div>

            <div id="resultados-pesquisa" class="space-y-6">
    <?php
    if (isset($_GET['resultados']) && is_array($_GET['resultados'])) {
        if (count($_GET['resultados']) > 0) {
            echo "<h2 class='text-2xl font-bold text-gray-800 mb-6'>Resultados da Pesquisa</h2>";
            echo "<div class='grid grid-cols-1 md:grid-cols-2 gap-6'>";
            
            foreach ($_GET['resultados'] as $empresa) {
                echo "<div class='result-card bg-white rounded-lg shadow-md p-6'>";
                echo "<div class='flex items-start mb-4'>";
                echo "<div class='bg-teal-100 p-3 rounded-full mr-4'>";
                echo "<i data-lucide='building' class='h-6 w-6 text-teal-600'></i>";
                echo "</div>";
                echo "<div class='flex-1'>";
                echo "<h3 class='text-xl font-semibold text-gray-800'>" . htmlspecialchars($empresa['nome']) . "</h3>";
                echo "<p class='text-gray-600 text-sm'>CNPJ: " . htmlspecialchars($empresa['cnpj']) . "</p>";
                echo "</div>";
                echo "</div>";

                echo "<div class='space-y-2 mb-4'>";
                echo "<div class='flex items-center text-gray-700'>";
                echo "<i data-lucide='map-pin' class='h-4 w-4 mr-2 text-teal-500'></i>";
                echo "<span>" . htmlspecialchars($empresa['cidade']) . ", " . htmlspecialchars($empresa['endereco']) . "</span>";
                echo "</div>";
                
                // Exibindo telefone se disponível
                if (!empty($empresa['telefone'])) {
                    echo "<div class='flex items-center text-gray-700'>";
                    echo "<i data-lucide='phone' class='h-4 w-4 mr-2 text-teal-500'></i>";
                    echo "<span>Telefone: " . htmlspecialchars($empresa['telefone']) . "</span>";
                    echo "</div>";
                }
                
                // Exibindo CEP se disponível
                if (!empty($empresa['cep'])) {
                    echo "<div class='flex items-center text-gray-700'>";
                    echo "<i data-lucide='map' class='h-4 w-4 mr-2 text-teal-500'></i>";
                    echo "<span>CEP: " . htmlspecialchars($empresa['cep']) . "</span>";
                    echo "</div>";
                }
                
                // Adicionando informações de especialidade e veículo (se disponíveis)
                if (!empty($empresa['especialidade'])) {
                    echo "<div class='flex items-center text-gray-700'>";
                    echo "<i data-lucide='heart-pulse' class='h-4 w-4 mr-2 text-teal-500'></i>";
                    echo "<span>Especialidade: " . htmlspecialchars($empresa['especialidade']) . "</span>";
                    echo "</div>";
                }
                
                if (!empty($empresa['tipo_veiculo'])) {
                    echo "<div class='flex items-center text-gray-700'>";
                    echo "<i data-lucide='car' class='h-4 w-4 mr-2 text-teal-500'></i>";
                    echo "<span>Veículo: " . htmlspecialchars($empresa['tipo_veiculo']) . "</span>";
                    echo "</div>";
                }
                
                echo "</div>";
                
                // Botão de Agendamento
                echo "<div class='mt-4'>";
                echo "<a href='/MedQ-2/area_cliente/agendamento_cliente.php?empresa_id=" . urlencode($empresa['id']) . "' class='bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded-lg transform transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-opacity-50 inline-block text-center w-full sm:w-auto'>";
                echo "Solicitar Agendamento";
                echo "</a>";
                echo "</div>";
                
                echo "</div>";
            }
            
            echo "</div>";
        } else {
            echo "<div class='bg-white rounded-lg shadow-md p-8 text-center'>";
            echo "<i data-lucide='search-x' class='h-12 w-12 text-gray-400 mx-auto mb-4'></i>";
            echo "<h3 class='text-xl font-semibold text-gray-700 mb-2'>Nenhuma empresa encontrada</h3>";
            echo "<p class='text-gray-500'>Tente ajustar seus critérios de pesquisa</p>";
            echo "</div>";
        }
    }
    ?>
</div>
        </div>
    </section>

    <script>
    lucide.createIcons();

    // Máscaras para os campos
    $(document).ready(function(){
        $('#telefone').mask('(00) 00000-0000');
        $('#cep').mask('00000-000');
    });

    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const closeMenuButton = document.getElementById('close-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuButton && closeMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', () => mobileMenu.classList.add('open'));
        closeMenuButton.addEventListener('click', () => mobileMenu.classList.remove('open'));
    }

    // Funcao para buscar cidades via api ibge
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

    // Elementos do dom para autocomplete de cidades
    const inputLocalizacao = document.getElementById('localizacao');
    const sugestoesContainer = document.getElementById('sugestoes-cidades');

    // Evento de input para autocomplete
    inputLocalizacao.addEventListener('input', async function(e) {
        const termo = e.target.value.trim();

        if (termo.length < 3) {
            sugestoesContainer.classList.add('hidden');
            return;
        }

        const cidades = await buscarCidades(termo);

        if (cidades.length > 0) {
            sugestoesContainer.innerHTML = cidades.map(cidade => `
                <div class="px-4 py-3 hover:bg-teal-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition duration-200"
                     data-value="${cidade.nome}">
                    <div class="font-medium">${cidade.nome.split(' - ')[0]}</div>
                    <div class="text-xs text-gray-500">${cidade.uf}</div>
                </div>
            `).join('');

            sugestoesContainer.classList.remove('hidden');
        } else {
            sugestoesContainer.classList.add('hidden');
        }
    });

    // Evento para selecionar uma cidade
    sugestoesContainer.addEventListener('click', function(e) {
        const target = e.target.closest('[data-value]');
        if (target && target.dataset.value) {
            inputLocalizacao.value = target.dataset.value;
            sugestoesContainer.classList.add('hidden');
        }
    });

    // Esconder sugestoes ao clicar fora
    document.addEventListener('click', function(e) {
        if (!inputLocalizacao.contains(e.target) && !sugestoesContainer.contains(e.target)) {
            sugestoesContainer.classList.add('hidden');
        }
    });
    </script>
</body>
</html>