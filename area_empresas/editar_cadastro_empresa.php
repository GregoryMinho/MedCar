<?php
session_start();
require '../includes/conexao_BdCadastroLogin.php';

if (!isset($_SESSION['usuario']['id']) || $_SESSION['usuario']['tipo'] !== 'empresa') {
    header('Location: ../paginas/cadastro_empresas.php');
    exit();
}

$empresa_id = (int)$_SESSION['usuario']['id'];

$stmt = $conn->prepare("SELECT * FROM empresas WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $empresa_id]);
$empresa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$empresa) {
    header('Location: ../paginas/cadastro_empresas.php');
    exit();
}

$stmtEsp = $conn->prepare("SELECT especialidade FROM empresa_especialidades WHERE empresa_id = :id");
$stmtEsp->execute([':id' => $empresa_id]);
$especialidadesSelecionadas = $stmtEsp->fetchAll(PDO::FETCH_COLUMN);

$stmtVeic = $conn->prepare("SELECT tipo_veiculo FROM empresa_veiculos WHERE empresa_id = :id");
$stmtVeic->execute([':id' => $empresa_id]);
$veiculosSelecionados = $stmtVeic->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cadastro da Empresa</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        <a href="/MedQ-2/area_cliente/menu_principal.php" class="font-medium hover:text-teal-300 transition">Home</a>
        <a href="/MedQ-2/paginas/abas_menu_principal/aba_empresas.php" class="font-medium hover:text-teal-300 transition">Empresas</a>
        <a href="#" class="font-medium hover:text-teal-300 transition">Contato</a>
    </div>
</div>

<main class="pt-20 px-4 pb-10">
    
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Cabeçalho estilizado adicionado -->
        <div class="bg-teal-500 text-white p-8 text-center">
            <h1 class="text-3xl font-bold mb-2">Editar Cadastro da Empresa</h1>
            <p class="text-xl">Atualize suas informações corporativas</p>
        </div>
        <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <?php if (isset($_SESSION['sucesso'])): ?>
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
                <?= $_SESSION['sucesso']; ?>
                <?php unset($_SESSION['sucesso']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['erro'])): ?>
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
                <?= $_SESSION['erro']; ?>
                <?php unset($_SESSION['erro']); ?>
            </div>
        <?php endif; ?>


        <form action="../paginas/actions/action_editar_cadastro_empresa.php" method="POST" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="flex items-center mb-1">
                        <i data-lucide="building-2" class="h-4 w-4 mr-2 text-teal-500"></i>
                        <label class="block text-sm font-medium text-gray-700">Nome da Empresa</label>
                    </div>
                    <input type="text" name="nome" value="<?= htmlspecialchars($empresa['nome']) ?>" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" required>
                </div>

                <div>
                    <div class="flex items-center mb-1">
                        <i data-lucide="mail" class="h-4 w-4 mr-2 text-teal-500"></i>
                        <label class="block text-sm font-medium text-gray-700">E-mail Corporativo</label>
                    </div>
                    <input type="email" name="email" value="<?= htmlspecialchars($empresa['email']) ?>" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" required>
                </div>

                <div>
                    <div class="flex items-center mb-1">
                        <i data-lucide="id-card" class="h-4 w-4 mr-2 text-teal-500"></i>
                        <label class="block text-sm font-medium text-gray-700">CNPJ</label>
                    </div>
                    <input type="text" name="cnpj" value="<?= htmlspecialchars($empresa['cnpj']) ?>" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" required>
                </div>

                <div>
                    <div class="flex items-center mb-1">
                        <i data-lucide="phone" class="h-4 w-4 mr-2 text-teal-500"></i>
                        <label class="block text-sm font-medium text-gray-700">Telefone</label>
                    </div>
                    <input type="text" name="telefone" value="<?= htmlspecialchars($empresa['telefone']) ?>" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" required>
                </div>

                <div>
                    <div class="flex items-center mb-1">
                        <i data-lucide="map-pin" class="h-4 w-4 mr-2 text-teal-500"></i>
                        <label class="block text-sm font-medium text-gray-700">CEP</label>
                    </div>
                    <input type="text" name="cep" value="<?= htmlspecialchars($empresa['cep']) ?>" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" required>
                </div>

                <div>
                    <div class="flex items-center mb-1">
                        <i data-lucide="home" class="h-4 w-4 mr-2 text-teal-500"></i>
                        <label class="block text-sm font-medium text-gray-700">Endereço</label>
                    </div>
                    <input type="text" name="endereco" value="<?= htmlspecialchars($empresa['endereco']) ?>" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" required>
                </div>

                <div>
                    <div class="flex items-center mb-1">
                        <i data-lucide="map" class="h-4 w-4 mr-2 text-teal-500"></i>
                        <label class="block text-sm font-medium text-gray-700">Cidade</label>
                    </div>
                    <input type="text" name="cidade" value="<?= htmlspecialchars($empresa['cidade']) ?>" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" required>
                </div>
            </div>

            <!-- ESPECIALIDADES -->
            <div class="mt-6">
                <div class="flex items-center mb-4">
                    <i data-lucide="stethoscope" class="h-4 w-4 mr-2 text-teal-500"></i>
                    <label class="block text-lg font-medium text-gray-700">Especialidades</label>
                </div>
                <div class="space-y-3">
                    <?php
                    $especialidades = [
                        'Cardíaco' => ['heart', 'Monitoramento cardíaco especializado'],
                        'Cadeirantes' => ['wheelchair', 'Veículos adaptados para mobilidade reduzida'],
                        'Idosos' => ['user', 'Atendimento especializado para a terceira idade'],
                        'Fisioterapia' => ['activity', 'Transporte para sessões de fisioterapia']
                    ];

                    foreach ($especialidades as $nome => $dados) {
                        $checked = in_array($nome, $especialidadesSelecionadas) ? 'checked' : '';
                        $id = 'esp_' . strtolower(str_replace(' ', '_', $nome));
                        echo <<<HTML
                        <div class="flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <div class="flex items-center h-5">
                                <input id="$id" name="especialidades[]" type="checkbox" value="$nome" class="h-4 w-4 text-teal-500 focus:ring-teal-500 border-gray-300 rounded" $checked>
                            </div>
                            <div class="ml-3">
                                <div class="flex items-center">
                                    <i data-lucide="{$dados[0]}" class="h-4 w-4 mr-2 text-teal-500"></i>
                                    <label for="$id" class="block text-sm font-medium text-gray-700">$nome</label>
                                </div>
                                <p class="text-xs text-gray-500 ml-6">{$dados[1]}</p>
                            </div>
                        </div>
                        HTML;
                    }
                    ?>
                </div>
            </div>

            <!-- TIPOS DE VEÍCULOS -->
            <div class="mt-8">
                <div class="flex items-center mb-4">
                    <i data-lucide="ambulance" class="h-4 w-4 mr-2 text-teal-500"></i>
                    <label class="block text-lg font-medium text-gray-700">Tipos de Veículos Disponíveis</label>
                </div>
                <div class="space-y-3">
                    <?php
                    $veiculos = [
                        'Padrão' => ['car', 'Para pacientes que podem se sentar durante o transporte'],
                        'Cadeira de Rodas' => ['wheelchair', 'Veículo com elevador e fixação para cadeira de rodas'],
                        'Maca' => ['bed', 'Para pacientes que precisam permanecer deitados'],
                        'Van Adaptada' => ['bus', 'Veículo amplo com adaptações para transporte de pacientes'],
                        'Carro Comum' => ['car-front', 'Veículo padrão para transporte de pacientes ambulatoriais']
                    ];

                    foreach ($veiculos as $nome => $dados) {
                        $checked = in_array($nome, $veiculosSelecionados) ? 'checked' : '';
                        $id = 'veic_' . strtolower(str_replace(' ', '_', $nome));
                        echo <<<HTML
                        <div class="flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <div class="flex items-center h-5">
                                <input id="$id" name="tipos_veiculos[]" type="checkbox" value="$nome" class="h-4 w-4 text-teal-500 focus:ring-teal-500 border-gray-300 rounded" $checked>
                            </div>
                            <div class="ml-3">
                                <div class="flex items-center">
                                    <i data-lucide="{$dados[0]}" class="h-4 w-4 mr-2 text-teal-500"></i>
                                    <label for="$id" class="block text-sm font-medium text-gray-700">$nome</label>
                                </div>
                                <p class="text-xs text-gray-500 ml-6">{$dados[1]}</p>
                            </div>
                        </div>
                        HTML;
                    }
                    ?>
                </div>
            </div>

            <div class="flex justify-end mt-8">
                <button type="submit" class="bg-teal-600 text-white px-6 py-3 rounded-md hover:bg-teal-700 transition flex items-center">
                    <i data-lucide="save" class="h-4 w-4 mr-2"></i>
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</main>

<script>
    lucide.createIcons();

    document.getElementById("mobile-menu-button").addEventListener("click", () => {
        document.getElementById("mobile-menu").classList.add("open");
    });

    document.getElementById("close-menu-button").addEventListener("click", () => {
        document.getElementById("mobile-menu").classList.remove("open");
    });
</script>

</body>
</html>