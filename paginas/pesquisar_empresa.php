<?php
session_start();
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="style/style_pagina_pesquisa_empresa.css">
</head>
<body class="min-h-screen bg-gray-50 font-sans">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 header-gradient text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="" class="flex items-center space-x-2 text-xl font-bold">
                    <i data-lucide="ambulance" class="h-6 w-6 text-teal-300"></i>
                    <span>MedCar</span>
                </a>
                <div class="hidden md:flex space-x-6">
                    <a href="/MedCar/area_cliente/menu_principal.php" class="font-medium hover:text-teal-300 transition duration-300">Home</a>
                </div>
                <button id="mobile-menu-button" class="md:hidden text-white focus:outline-none">
                    <i data-lucide="menu" class="h-6 w-6"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="fixed inset-0 z-50 bg-blue-900 bg-opacity-95 flex flex-col text-white p-6 mobile-menu">
        <div class="flex justify-end">
            <button id="close-menu-button" class="text-white focus:outline-none">
                <i data-lucide="x" class="h-6 w-6"></i>
            </button>
        </div>
        <div class="flex flex-col items-center justify-center space-y-8 flex-grow text-xl">
            <a href="/MedQ-2/area_cliente/menu_principal.php" class="font-medium hover:text-teal-300 transition duration-300">Home</a>
        </div>
    </div>

    <!-- Hero Section -->
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

    <!-- Main Content -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <!-- Search Section -->
            <div class="bg-white rounded-xl shadow-md p-8 mb-8">
                <form action="actions/action_pesquisar_empresa.php" method="GET">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Filtros de Pesquisa</h2>
                        <?php if(isset($_GET['resultados'])): ?>
                            <div class="text-teal-600 font-medium">
                                <?php echo count($_GET['resultados']); ?> resultados encontrados
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <!-- Localização -->
                        <div class="relative">
                            <div class="flex items-center mb-2">
                                <i data-lucide="map-pin" class="h-4 w-4 mr-2 text-teal-500"></i>
                                <label for="localizacao" class="block text-sm font-medium text-gray-700">Localização</label>
                            </div>
                            <input type="text" id="localizacao" name="localizacao" autocomplete="off"
                                value="<?php echo htmlspecialchars($_GET['localizacao'] ?? ''); ?>"
                                class="mt-1 block w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent shadow-sm" 
                                placeholder="Digite a cidade">
                            <div id="sugestoes-cidades" class="border border-gray-200 rounded-lg bg-white mt-1 hidden absolute z-10 w-full shadow-lg max-h-60 overflow-auto"></div>
                        </div>

                        <!-- Especialidade -->
                        <div>
                            <div class="flex items-center mb-2">
                                <i data-lucide="heart-pulse" class="h-4 w-4 mr-2 text-teal-500"></i>
                                <label class="block text-sm font-medium text-gray-700">Especialidade</label>
                            </div>
                            <select name="especialidade" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent shadow-sm">
                                <option value="">Todas as especialidades</option>
                                <option value="Cardíaco" <?= ($_GET['especialidade'] ?? '') === 'Cardíaco' ? 'selected' : '' ?>>Cardíaco</option>
                                <option value="Cadeirantes" <?= ($_GET['especialidade'] ?? '') === 'Cadeirantes' ? 'selected' : '' ?>>Cadeirantes</option>
                                <option value="Idosos" <?= ($_GET['especialidade'] ?? '') === 'Idosos' ? 'selected' : '' ?>>Idosos</option>
                                <option value="Fisioterapia" <?= ($_GET['especialidade'] ?? '') === 'Fisioterapia' ? 'selected' : '' ?>>Fisioterapia</option>
                            </select>
                        </div>

                        <!-- Tipo de Veículo -->
                        <div>
                            <div class="flex items-center mb-2">
                                <i data-lucide="car" class="h-4 w-4 mr-2 text-teal-500"></i>
                                <label class="block text-sm font-medium text-gray-700">Tipo de Veículo</label>
                            </div>
                            <select name="tipo_veiculo" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent shadow-sm">
                                <option value="">Todos os veículos</option>
                                <option value="Padrão" <?= ($_GET['tipo_veiculo'] ?? '') === 'Padrão' ? 'selected' : '' ?>>Veículo Padrão</option>
                                <option value="Cadeira de Rodas" <?= ($_GET['tipo_veiculo'] ?? '') === 'Cadeira de Rodas' ? 'selected' : '' ?>>Adaptado para Cadeira de Rodas</option>
                                <option value="Maca" <?= ($_GET['tipo_veiculo'] ?? '') === 'Maca' ? 'selected' : '' ?>>Transporte com Maca</option>
                                <option value="Van Adaptada" <?= ($_GET['tipo_veiculo'] ?? '') === 'Van Adaptada' ? 'selected' : '' ?>>Van Adaptada</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 mt-8">
                        <a href="pesquisar_empresa.php" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 btn-secondary text-center">
                            Limpar Filtros
                        </a>
                        <button type="submit" class="px-6 py-3 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-300 btn-primary text-center">
                            <i data-lucide="search" class="inline mr-2"></i>
                            Pesquisar Empresas
                        </button>
                    </div>
                </form>
            </div>

            <!-- Results Section -->
            <div id="resultados-pesquisa" class="space-y-6">
                <?php if(isset($_SESSION['erro_pesquisa'])): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i data-lucide="alert-triangle" class="h-5 w-5 text-red-500"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Erro na Pesquisa</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p><?php echo $_SESSION['erro_pesquisa']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php unset($_SESSION['erro_pesquisa']); ?>
                <?php endif; ?>
                <?php if(isset($_GET['resultados']) && is_array($_GET['resultados'])): ?>
    <?php if(count($_GET['resultados']) > 0): ?>
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Resultados da Pesquisa</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach($_GET['resultados'] as $empresa): ?>
                <div class="result-card bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-start mb-4">
                        <div class="bg-teal-100 p-3 rounded-full mr-4">
                       <!-- add as imgns das empresas so que elas geram imagens aleatórias  não fixa! mas ta bom. --> <img 
            src="https://picsum.photos/100/100?random=<?php echo $empresa['id']; ?>" 
            alt="Imagem da empresa" 
            class="h-12 w-12 rounded-full object-cover"
            onerror="this.src='https://via.placeholder.com/100?text=Empresa'" 
        >
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($empresa['nome']); ?></h3>
                            <p class="text-gray-600 text-sm">CNPJ: <?php echo htmlspecialchars($empresa['cnpj']); ?></p>
                        </div>
                    </div>

                    <div class="space-y-2 mb-4">
                        <?php if(!empty($empresa['endereco'])): ?>
                            <div class="flex items-center text-gray-700">
                                <i data-lucide="home" class="h-4 w-4 mr-2 text-teal-500"></i>
                                <span><?php echo htmlspecialchars($empresa['endereco']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($empresa['cep'])): ?>
                            <div class="flex items-center text-gray-700">
                                <i data-lucide="map-pin" class="h-4 w-4 mr-2 text-teal-500"></i>
                                <span>CEP: <?php echo htmlspecialchars($empresa['cep']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($empresa['email'])): ?>
                            <div class="flex items-center text-gray-700">
                                <i data-lucide="mail" class="h-4 w-4 mr-2 text-teal-500"></i>
                                <span>Email: <?php echo htmlspecialchars($empresa['email']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($empresa['telefone'])): ?>
                            <div class="flex items-center text-gray-700">
                                <i data-lucide="phone" class="h-4 w-4 mr-2 text-teal-500"></i>
                                <span>Telefone: <?php echo htmlspecialchars($empresa['telefone']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($empresa['especialidades']) && $empresa['especialidades'] != 'Não informado'): ?>
                            <div class="flex items-center text-gray-700">
                                <i data-lucide="heart-pulse" class="h-4 w-4 mr-2 text-teal-500"></i>
                                <span>Especialidades: <?php echo htmlspecialchars($empresa['especialidades']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($empresa['tipos_veiculos']) && $empresa['tipos_veiculos'] != 'Não informado'): ?>
                            <div class="flex items-center text-gray-700">
                                <i data-lucide="car" class="h-4 w-4 mr-2 text-teal-500"></i>
                                <span>Veículos: <?php echo htmlspecialchars($empresa['tipos_veiculos']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-4">
                        <a href="/MedCar/area_cliente/agendamento_cliente.php?empresa_id=<?php echo urlencode($empresa['id']); ?>" 
                           class="flex items-center justify-center bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded-lg transform transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-opacity-50">
                            <i data-lucide="calendar-plus" class="mr-2"></i>
                            Solicitar Agendamento
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <i data-lucide="search-x" class="h-12 w-12 text-gray-400 mx-auto mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Nenhuma empresa encontrada</h3>
            <p class="text-gray-500">Tente ajustar seus critérios de pesquisa</p>
        </div>
    <?php endif; ?>
<?php endif; ?>
                
            </div>
        </div>
    </section>

    <script>
        // Inicializa ícones
        lucide.createIcons();

        // Menu mobile
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeMenuButton = document.getElementById('close-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton && closeMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => mobileMenu.classList.add('open'));
            closeMenuButton.addEventListener('click', () => mobileMenu.classList.remove('open'));
        }

        // Autocomplete para cidades
        async function buscarCidades(termo) {
            if (termo.length < 3) return [];
            
            try {
                const sugestoesContainer = document.getElementById('sugestoes-cidades');
                sugestoesContainer.innerHTML = '<div class="p-4 text-center text-gray-500">Buscando cidades...</div>';
                sugestoesContainer.classList.remove('hidden');

                const response = await fetch(`https://servicodados.ibge.gov.br/api/v1/localidades/municipios`);
                
                if (!response.ok) throw new Error('Falha ao buscar cidades');
                
                const cidades = await response.json();
                return cidades
                    .filter(cidade => cidade.nome.toLowerCase().startsWith(termo.toLowerCase()))
                    .slice(0, 20)
                    .map(cidade => ({
                        nome: `${cidade.nome} - ${cidade.microrregiao.mesorregiao.UF.sigla}`,
                        uf: cidade.microrregiao.mesorregiao.UF.sigla,
                        nomeCompleto: cidade.nome
                    }));

            } catch (error) {
                console.error('Erro ao buscar cidades:', error);
                return [];
            }
        }

        // Elementos do DOM para autocomplete
        const inputLocalizacao = document.getElementById('localizacao');
        const sugestoesContainer = document.getElementById('sugestoes-cidades');

        // Evento de input com debounce
        let timeoutId;
        inputLocalizacao.addEventListener('input', async function(e) {
            clearTimeout(timeoutId);
            const termo = e.target.value.trim();
            
            timeoutId = setTimeout(async () => {
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
                    sugestoesContainer.innerHTML = '<div class="p-4 text-center text-gray-500">Nenhuma cidade encontrada</div>';
                    sugestoesContainer.classList.remove('hidden');
                }
            }, 300);
        });

        // Selecionar cidade
        sugestoesContainer.addEventListener('click', function(e) {
            const target = e.target.closest('[data-value]');
            if (target && target.dataset.value) {
                inputLocalizacao.value = target.dataset.value;
                sugestoesContainer.classList.add('hidden');
            }
        });

        // Fechar sugestões ao clicar fora
        document.addEventListener('click', function(e) {
            if (!inputLocalizacao.contains(e.target) && !sugestoesContainer.contains(e.target)) {
                sugestoesContainer.classList.add('hidden');
            }
        });

        // Rolagem para resultados
        <?php if(isset($_GET['resultados']) && count($_GET['resultados']) > 0): ?>
            window.location.hash = 'resultados-pesquisa';
        <?php endif; ?>
    </script>
</body>
</html>