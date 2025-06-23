<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Perfil do Motorista</title>
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
                <a href="#" class="flex items-center gap-3 ps-4 py-3 rounded-lg bg-blue-800 font-semibold">
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
                <i data-lucide="user-check" class="h-7 w-7 text-blue-800"></i>
                Meu Perfil
            </h1>
            <p class="text-gray-500 mb-6">Gerencie seus dados, documentos e acompanhe o status do seu cadastro na MedCar.</p>

            <!-- Status do Cadastro -->
            <div class="mb-8 flex items-center gap-4">
                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold bg-yellow-100 text-yellow-700 shadow">
                    <i class="bi bi-hourglass-split mr-2"></i>
                    Cadastro em Análise
                </span>
                <!-- Troque por bg-green-100/text-green-700 e ícone check-circle quando aprovado -->
                <span class="text-gray-400 text-sm">Última atualização: 05/06/2025</span>
            </div>

            <!-- Dados do Motorista -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold text-blue-900 mb-4 flex items-center gap-2">
                    <i data-lucide="id-card" class="h-5 w-5 text-teal-600"></i>
                    Dados Pessoais
                </h2>
                <form class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nome Completo</label>
                        <input type="text" class="w-full border rounded-lg px-3 py-2 text-gray-800 focus:ring-blue-800 focus:border-blue-800" value="Carlos Silva">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Data de Nascimento</label>
                        <input type="date" class="w-full border rounded-lg px-3 py-2 text-gray-800 focus:ring-blue-800 focus:border-blue-800" value="1982-11-23">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Celular</label>
                        <input type="tel" class="w-full border rounded-lg px-3 py-2 text-gray-800 focus:ring-blue-800 focus:border-blue-800" value="(11) 98765-4321">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">E-mail</label>
                        <input type="email" class="w-full border rounded-lg px-3 py-2 text-gray-800 focus:ring-blue-800 focus:border-blue-800" value="carlos.silva@email.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">CPF</label>
                        <input type="text" class="w-full border rounded-lg px-3 py-2 text-gray-400 bg-gray-100" value="123.456.789-10" disabled>
                    </div>
                </form>
                <div class="flex justify-end mt-6">
                    <button class="bg-blue-800 hover:bg-blue-900 text-white font-semibold px-6 py-2 rounded-lg shadow transition flex items-center gap-2">
                        <i data-lucide="save"></i>
                        Atualizar Dados
                    </button>
                </div>
            </div>

            <!-- Dados da CNH -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold text-blue-900 mb-4 flex items-center gap-2">
                    <i data-lucide="clipboard-signature" class="h-5 w-5 text-orange-600"></i>
                    CNH do Motorista
                </h2>
                <form class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Número da CNH</label>
                        <input type="text" class="w-full border rounded-lg px-3 py-2 text-gray-800 focus:ring-blue-800 focus:border-blue-800" value="98765432100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Categoria</label>
                        <select class="w-full border rounded-lg px-3 py-2 text-gray-800 focus:ring-blue-800 focus:border-blue-800">
                            <option selected>B</option>
                            <option>A</option>
                            <option>C</option>
                            <option>D</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Validade</label>
                        <input type="date" class="w-full border rounded-lg px-3 py-2 text-gray-800 focus:ring-blue-800 focus:border-blue-800" value="2029-03-22">
                    </div>
                </form>
                <div class="flex justify-end mt-6">
                    <button class="bg-blue-800 hover:bg-blue-900 text-white font-semibold px-6 py-2 rounded-lg shadow transition flex items-center gap-2">
                        <i data-lucide="save"></i>
                        Atualizar CNH
                    </button>
                </div>
            </div>

            <!-- Dados do Veículo -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold text-blue-900 mb-4 flex items-center gap-2">
                    <i data-lucide="car" class="h-5 w-5 text-green-600"></i>
                    Dados do Veículo
                </h2>
                <form class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Modelo</label>
                        <input type="text" class="w-full border rounded-lg px-3 py-2 text-gray-800 focus:ring-blue-800 focus:border-blue-800" value="Spin Premier">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Placa</label>
                        <input type="text" class="w-full border rounded-lg px-3 py-2 text-gray-800 focus:ring-blue-800 focus:border-blue-800" value="ABC-1234">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Ano</label>
                        <input type="number" class="w-full border rounded-lg px-3 py-2 text-gray-800 focus:ring-blue-800 focus:border-blue-800" value="2022">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Cor</label>
                        <input type="text" class="w-full border rounded-lg px-3 py-2 text-gray-800 focus:ring-blue-800 focus:border-blue-800" value="Branco">
                    </div>
                </form>
                <div class="flex justify-end mt-6">
                    <button class="bg-blue-800 hover:bg-blue-900 text-white font-semibold px-6 py-2 rounded-lg shadow transition flex items-center gap-2">
                        <i data-lucide="save"></i>
                        Atualizar Veículo
                    </button>
                </div>
            </div>

            <!-- Upload de Documentos Obrigatórios -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold text-blue-900 mb-4 flex items-center gap-2">
                    <i data-lucide="file-up" class="h-5 w-5 text-purple-600"></i>
                    Documentos Obrigatórios
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- CNH -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Foto da CNH (Frente e Verso)</label>
                        <div class="flex items-center gap-4">
                            <input type="file" id="doc-cnh" class="hidden">
                            <label for="doc-cnh" class="flex items-center gap-2 bg-blue-100 hover:bg-blue-200 text-blue-800 font-semibold px-4 py-2 rounded-lg cursor-pointer transition">
                                <i class="bi bi-cloud-arrow-up fs-5"></i> Escolher arquivo
                            </label>
                            <span class="text-xs text-gray-400">Nenhum arquivo selecionado</span>
                        </div>
                    </div>
                    <!-- Documento do veículo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Documento do Veículo (CRLV)</label>
                        <div class="flex items-center gap-4">
                            <input type="file" id="doc-crlv" class="hidden">
                            <label for="doc-crlv" class="flex items-center gap-2 bg-blue-100 hover:bg-blue-200 text-blue-800 font-semibold px-4 py-2 rounded-lg cursor-pointer transition">
                                <i class="bi bi-cloud-arrow-up fs-5"></i> Escolher arquivo
                            </label>
                            <span class="text-xs text-gray-400">Nenhum arquivo selecionado</span>
                        </div>
                    </div>
                    <!-- Comprovante de residência -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Comprovante de Residência</label>
                        <div class="flex items-center gap-4">
                            <input type="file" id="doc-residencia" class="hidden">
                            <label for="doc-residencia" class="flex items-center gap-2 bg-blue-100 hover:bg-blue-200 text-blue-800 font-semibold px-4 py-2 rounded-lg cursor-pointer transition">
                                <i class="bi bi-cloud-arrow-up fs-5"></i> Escolher arquivo
                            </label>
                            <span class="text-xs text-gray-400">Nenhum arquivo selecionado</span>
                        </div>
                    </div>
                    <!-- Foto do veículo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Foto do Veículo</label>
                        <div class="flex items-center gap-4">
                            <input type="file" id="doc-foto-veiculo" class="hidden">
                            <label for="doc-foto-veiculo" class="flex items-center gap-2 bg-blue-100 hover:bg-blue-200 text-blue-800 font-semibold px-4 py-2 rounded-lg cursor-pointer transition">
                                <i class="bi bi-cloud-arrow-up fs-5"></i> Escolher arquivo
                            </label>
                            <span class="text-xs text-gray-400">Nenhum arquivo selecionado</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button class="bg-blue-800 hover:bg-blue-900 text-white font-semibold px-6 py-2 rounded-lg shadow transition flex items-center gap-2">
                        <i data-lucide="upload-cloud"></i>
                        Enviar Documentos
                    </button>
                </div>
                <div class="mt-2 text-xs text-gray-500">Formatos aceitos: PDF, JPG, PNG. Tamanho máximo: 5MB por arquivo.</div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Exibir nome do arquivo selecionado
        const fileInputs = [
            { input: 'doc-cnh', label: null },
            { input: 'doc-crlv', label: null },
            { input: 'doc-residencia', label: null },
            { input: 'doc-foto-veiculo', label: null }
        ];

        fileInputs.forEach(({ input }, idx) => {
            const inputEl = document.getElementById(input);
            const labelEl = inputEl.nextElementSibling.nextElementSibling;
            fileInputs[idx].label = labelEl;
            inputEl.addEventListener('change', function () {
                const fileName = inputEl.files.length ? inputEl.files[0].name : "Nenhum arquivo selecionado";
                labelEl.textContent = fileName;
            });
        });
    </script>
</body>
</html>
