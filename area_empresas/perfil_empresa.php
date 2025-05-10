<?php
require '../includes/conexao_BdCadastroLogin.php';
require '../includes/classe_usuario.php';

use usuario\Usuario;

Usuario::verificarPermissao('empresa'); // Verifica se o usuário logado é uma empresa

if (!isset($_SESSION)) {
    session_start();
}
$idEmpresa = $_SESSION['usuario']['id'];

// Busca os dados da empresa
try {
    $stmt = $conn->prepare("SELECT nome, cnpj, telefone, endereco, cidade, cep FROM empresas WHERE id = :id");
    $stmt->bindParam(':id', $idEmpresa, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $_SESSION['empresa'] = $result;
    }
} catch (PDOException $e) {
    echo "Erro ao buscar informações da empresa: " . $e->getMessage();
}

$conn = null; // Fecha a conexão com o banco de dados

// Verifica se há mensagens de sucesso ou erro na sessão
$mensagemSucesso = $_SESSION['sucesso'] ?? null;
$mensagemErro = $_SESSION['erro'] ?? null;
// Limpa as mensagens após exibição
unset($_SESSION['sucesso'], $_SESSION['erro']);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Perfil Empresa</title>
    <link rel="stylesheet" href="style/style_perfil_empresa.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="min-h-screen bg-gray-50">
    <?php if ($mensagemSucesso || $mensagemErro): ?>
        <div id="modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-96">
                <h2 class="text-lg font-bold mb-4 <?= $mensagemSucesso ? 'text-green-600' : 'text-red-600' ?>">
                    <?= $mensagemSucesso ? 'Sucesso' : 'Erro' ?>
                </h2>
                <p class="text-gray-700 mb-4">
                    <?= htmlspecialchars($mensagemSucesso ?? $mensagemErro) ?>
                </p>
                <button id="close-modal" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg">
                    Fechar
                </button>
            </div>
        </div>
    <?php endif; ?>
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="menu_principal.php" class="flex items-center space-x-2 text-white hover:text-teal-300 transition">
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
                                <a href="" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="user" class="h-4 w-4 inline mr-2"></i>Minha Conta
                                </a>
                                <a href="../paginas/abas_menu_principal/aba_empresas.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="calendar" class="h-4 w-4 inline mr-2"></i>Agendar
                                </a>
                                <a href="historico.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="clock" class="h-4 w-4 inline mr-2"></i>Meus Agendamentos
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
        </div>
    </nav>

    <!-- Header Section -->
    <section class="pt-24 pb-10 bg-gradient-to-r from-blue-900 to-blue-800 text-white">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">Perfil da Empresa</h1>
            <p class="text-xl text-blue-100">
                Visualize e gerencie as informações da sua empresa.
            </p>
        </div>
    </section>

    <!-- Profile Section -->
    <section class="py-10">
        <div class="container mx-auto px-4">
            <div class="space-y-6">
                <!-- Profile Header -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                        <div class="flex-1 text-center md:text-left">
                            <h2 class="text-2xl font-bold text-blue-900"><?= $_SESSION['empresa']['nome'] ?></h2>
                            <p class="text-gray-600 flex items-center justify-center md:justify-start mt-1">
                                <i data-lucide="phone" class="h-4 w-4 mr-2 text-teal-500"></i>
                                <?= $_SESSION['empresa']['telefone'] ?>
                            </p>
                            <p class="text-gray-600 flex items-center justify-center md:justify-start mt-1">
                                <i data-lucide="map-pin" class="h-4 w-4 mr-2 text-teal-500"></i>
                                <?= $_SESSION['empresa']['endereco'] ?>, <?= $_SESSION['empresa']['cidade'] ?> - CEP: <?= $_SESSION['empresa']['cep'] ?>
                            </p>
                        </div>

                        <div class="flex space-x-2">
                            <button id="edit-profile-btn" class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-2 px-4 rounded-lg transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-opacity-50 flex items-center">
                                <i data-lucide="edit" class="h-4 w-4 mr-2"></i>
                                Editar Perfil
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Profile Tabs -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <!-- Tab Buttons -->
                    <div class="grid grid-cols-2 gap-2 mb-6">
                        <button class="tab-button active flex items-center justify-center py-2 px-4 rounded-lg font-medium transition-colors" data-tab="company">
                            <i data-lucide="briefcase" class="h-4 w-4 mr-2"></i>
                            Dados da Empresa
                        </button>
                        <button class="tab-button flex items-center justify-center py-2 px-4 rounded-lg font-medium transition-colors" data-tab="address">
                            <i data-lucide="map-pin" class="h-4 w-4 mr-2"></i>
                            Endereço
                        </button>
                    </div>

                    <!-- Tab Contents -->
                    <!-- Company Information Tab -->
                    <div id="company-tab" class="tab-content active">
                        <h3 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                            <i data-lucide="briefcase" class="h-5 w-5 mr-2 text-teal-500"></i>
                            Informações da Empresa
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Nome da Empresa</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['empresa']['nome'] ?>
                                </p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">CNPJ</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['empresa']['cnpj'] ?>
                                </p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Telefone</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['empresa']['telefone'] ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Address Tab -->
                    <div id="address-tab" class="tab-content">
                        <h3 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                            <i data-lucide="map-pin" class="h-5 w-5 mr-2 text-teal-500"></i>
                            Endereço
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Endereço</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['empresa']['endereco'] ?>
                                </p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Cidade</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['empresa']['cidade'] ?>
                                </p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">CEP</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['empresa']['cep'] ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Edit Profile Modal -->
    <div id="edit-profile-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-blue-900">Editar Perfil</h3>
                <button id="close-modal-btn" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>

            <form action="crud_perfil/action_editar_perfil.php" id="edit-profile-form" method="POST">
                <div class="space-y-6">
                    <div>
                        <h3 class="text-xl font-bold text-red-600">Altere somente o que desejar alterar</h3>
                        <h4 class="text-lg font-semibold text-blue-900 mb-3">Informações da Empresa</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="nome" class="block text-gray-700 font-medium mb-1">Nome da Empresa</label>
                                <input type="text" id="nome" name="nome" value="<?= $_SESSION['empresa']['nome'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="telefone" class="block text-gray-700 font-medium mb-1">Telefone</label>
                                <input type="tel" id="telefone" name="telefone" value="<?= $_SESSION['empresa']['telefone'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold text-blue-900 mb-3">Endereço</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="endereco" class="block text-gray-700 font-medium mb-1">Endereço</label>
                                <input type="text" id="endereco" name="endereco" value="<?= $_SESSION['empresa']['endereco'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="cidade" class="block text-gray-700 font-medium mb-1">Cidade</label>
                                <input type="text" id="cidade" name="cidade" value="<?= $_SESSION['empresa']['cidade'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="cep" class="block text-gray-700 font-medium mb-1">CEP</label>
                                <input type="text" id="cep" name="cep" value="<?= $_SESSION['empresa']['cep'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancel-edit-btn" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition">Salvar Alterações</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));

                // Add active class to clicked button and corresponding content
                button.classList.add('active');
                const tabId = button.getAttribute('data-tab');
                document.getElementById(`${tabId}-tab`).classList.add('active');
            });
        });

        // Edit profile modal functionality
        const editProfileBtn = document.getElementById('edit-profile-btn');
        const editProfileModal = document.getElementById('edit-profile-modal');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const cancelEditBtn = document.getElementById('cancel-edit-btn');

        editProfileBtn.addEventListener('click', () => {
            editProfileModal.classList.remove('hidden');
        });

        closeModalBtn.addEventListener('click', () => {
            editProfileModal.classList.add('hidden');
        });

        cancelEditBtn.addEventListener('click', () => {
            editProfileModal.classList.add('hidden');
        });

        // Close modal when clicking outside
        editProfileModal.addEventListener('click', (e) => {
            if (e.target === editProfileModal) {
                editProfileModal.classList.add('hidden');
            }
        });
    </script>
</body>

</html>