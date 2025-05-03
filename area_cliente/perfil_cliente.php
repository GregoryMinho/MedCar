<?php
require '../includes/conexao_BdCadastroLogin.php';
require '../includes/classe_usuario.php';

use usuario\Usuario;

Usuario::verificarPermissao('cliente'); // verifica se o usuário logado é um cliente

if (!isset($_SESSION)) {
    session_start();
}
$idUser = $_SESSION['usuario']['id'];

// busca os dados do cliente e do endereço
$stmt = $conn->prepare("SELECT
 c.nome, c.email, c.cpf, c.telefone, c.foto,
  c.data_nascimento, c.contato_emergencia, e.rua,
   e.numero, e.complemento, e.bairro, e.cidade, e.estado, e.cep
  FROM clientes c
  INNER JOIN enderecos_clientes e ON c.id = e.id_cliente
  WHERE c.id = :id");
$stmt->bindParam(':id', $idUser, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
if ($result) {
    $_SESSION['usuario'] = array_merge($_SESSION['usuario'], $result);
} else {
    echo "Erro ao buscar informações do usuário." . $idUser;
}

// busca os dados médicos do cliente
$stmt = $conn->prepare("SELECT alergias, doencas_cronicas, remedio_recorrente FROM detalhe_medico WHERE id_cliente = :id_cliente");
$stmt->bindParam(':id_cliente', $idUser, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
if ($result) {
    $_SESSION['usuario'] = $result;
} else {
    echo "Erro ao buscar informações médicas do usuário." . $idUser;
}

$conn = null; // Fecha a conexão com o banco de dados

echo '<pre>';
var_dump($_SESSION['usuario']);
echo '</pre>';

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
    <title>MedCar - Minha Conta</title>
    <link rel="stylesheet" href="style/style_perfil_cliente.css">
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
            <h1 class="text-3xl md:text-4xl font-bold mb-4">Minha Conta</h1>
            <p class="text-xl text-blue-100">
                Visualize e gerencie suas informações pessoais e preferências.
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
                        <div class="relative">
                            <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-200 border-4 border-white shadow-lg">
                                <img src="<?= $_SESSION['usuario']['foto'] ?>" alt="Foto de Perfil" class="w-full h-full object-cover">
                            </div>
                        </div>

                        <div class="flex-1 text-center md:text-left">
                            <h2 class="text-2xl font-bold text-blue-900"><?= $_SESSION['usuario']['nome'] ?></h2>
                            <p class="text-gray-600 flex items-center justify-center md:justify-start mt-1">
                                <i data-lucide="mail" class="h-4 w-4 mr-2 text-teal-500"></i>
                                <?= $_SESSION['usuario']['email'] ?>
                            </p>
                            <p class="text-gray-600 flex items-center justify-center md:justify-start mt-1">
                                <i data-lucide="phone" class="h-4 w-4 mr-2 text-teal-500"></i>
                                <?= $_SESSION['usuario']['telefone'] ?>
                            </p>
                            <p class="text-gray-600 flex items-center justify-center md:justify-start mt-1">
                                <i data-lucide="calendar" class="h-4 w-4 mr-2 text-teal-500"></i>
                                <?= $_SESSION['usuario']['data_nascimento'] ?? ''; ?>
                            </p>
                        </div>

                        <div class="flex space-x-2">
                            <button id="edit-profile-btn" class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-2 px-4 rounded-lg transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-opacity-50 flex items-center">
                                <i data-lucide="edit" class="h-4 w-4 mr-2"></i>
                                Editar Perfil
                            </button>
                            <button id="edit-medical-btn" class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-2 px-4 rounded-lg transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-opacity-50 flex items-center">
                                <i data-lucide="activity" class="h-4 w-4 mr-2"></i>
                                Editar Informações Médicas
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Profile Tabs -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <!-- Tab Buttons -->
                    <div class="grid grid-cols-3 gap-2 mb-6">
                        <button class="tab-button active flex items-center justify-center py-2 px-4 rounded-lg font-medium transition-colors" data-tab="personal">
                            <i data-lucide="user" class="h-4 w-4 mr-2"></i>
                            Dados Pessoais
                        </button>
                        <button class="tab-button flex items-center justify-center py-2 px-4 rounded-lg font-medium transition-colors" data-tab="address">
                            <i data-lucide="map-pin" class="h-4 w-4 mr-2"></i>
                            Endereço
                        </button>
                        <button class="tab-button flex items-center justify-center py-2 px-4 rounded-lg font-medium transition-colors" data-tab="medical">
                            <i data-lucide="activity" class="h-4 w-4 mr-2"></i>
                            Dados Médicos
                        </button>
                    </div>

                    <!-- Tab Contents -->
                    <!-- Personal Information Tab -->
                    <div id="personal-tab" class="tab-content active">
                        <h3 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                            <i data-lucide="user" class="h-5 w-5 mr-2 text-teal-500"></i>
                            Informações Pessoais
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Nome Completo</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['usuario']['nome'] ?>
                                </p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">CPF</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['usuario']['cpf'] ?>
                                </p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Data de Nascimento</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['usuario']['data_nascimento'] ?? ''; ?>
                                </p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">E-mail</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['usuario']['email'] ?>
                                </p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Telefone</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['usuario']['telefone'] ?>
                                </p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Contato de Emergência</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['usuario']['contato_emergencia'] ?? ''; ?>
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

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Rua/Avenida</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['usuario']['rua'] ?? ''; ?>
                                </p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Número</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['usuario']['numero'] ?? ''; ?>
                                </p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Complemento</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['usuario']['complemento'] ?? ''; ?>
                                </p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Bairro</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['usuario']['bairro'] ?? ''; ?>
                                </p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Cidade</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['usuario']['cidade'] ?? ''; ?>
                                </p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Estado</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['usuario']['estado'] ?? ''; ?>
                                </p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">CEP</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['usuario']['cep'] ?? ''; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Information Tab -->
                    <div id="medical-tab" class="tab-content">
                        <h3 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                            <i data-lucide="activity" class="h-5 w-5 mr-2 text-teal-500"></i>
                            Informações Médicas
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Alergias</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['usuario']['alergias'] ?? ''; ?>
                                </p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Condições Crônicas</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['usuario']['doencas_cronicas'] ?? ''; ?>
                                </p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Medicamentos em Uso</label>
                                <p class="text-gray-800 border border-gray-200 rounded-lg px-4 py-2 bg-gray-50">
                                    <?= $_SESSION['usuario']['remedio_recorrente'] ?? ''; ?>
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

            <form action="actions/action_editar_perfil.php" id="edit-profile-form" method="POST">
                <div class="space-y-6">
                    <div>
                        <h3 class="text-xl font-bold text-red-600">Altere somente o que desejar alterar</h3>
                        <h4 class="text-lg font-semibold text-blue-900 mb-3">Informações Pessoais</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-gray-700 font-medium mb-1">Nome Completo</label>
                                <input type="text" id="name" name="name" value="<?= $_SESSION['usuario']['nome'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="cpf" class="block text-gray-700 font-medium mb-1">CPF</label>
                                <input type="text" id="cpf" name="cpf" value="<?= $_SESSION['usuario']['cpf'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="birth_date" class="block text-gray-700 font-medium mb-1">Data de Nascimento</label>
                                <input type="date" id="birth_date" name="birth_date" value="<?= $_SESSION['usuario']['data_nascimento'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="email" class="block text-gray-700 font-medium mb-1">E-mail</label>
                                <input type="email" id="email" name="email" value="<?= $_SESSION['usuario']['email'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="phone" class="block text-gray-700 font-medium mb-1">Telefone</label>
                                <input type="tel" id="phone" name="phone" value="<?= $_SESSION['usuario']['telefone'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="emergency_contact" class="block text-gray-700 font-medium mb-1">Contato de Emergência</label>
                                <input type="text" id="emergency_contact" name="emergency_contact" value="<?= $_SESSION['usuario']['contato_emergencia'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold text-blue-900 mb-3">Endereço</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="street" class="block text-gray-700 font-medium mb-1">Rua/Avenida</label>
                                <input type="text" id="street" name="street" value="<?= $_SESSION['usuario']['rua'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="number" class="block text-gray-700 font-medium mb-1">Número</label>
                                <input type="text" id="number" name="number" value="<?= $_SESSION['usuario']['numero'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="complement" class="block text-gray-700 font-medium mb-1">Complemento</label>
                                <input type="text" id="complement" name="complement" value="<?= $_SESSION['usuario']['complemento'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="neighborhood" class="block text-gray-700 font-medium mb-1">Bairro</label>
                                <input type="text" id="neighborhood" name="neighborhood" value="<?= $_SESSION['usuario']['bairro'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="city" class="block text-gray-700 font-medium mb-1">Cidade</label>
                                <input type="text" id="city" name="city" value="<?= $_SESSION['usuario']['cidade'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="state" class="block text-gray-700 font-medium mb-1">Estado</label>
                                <input type="text" id="state" name="state" value="<?= $_SESSION['usuario']['estado'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="zipcode" class="block text-gray-700 font-medium mb-1">CEP</label>
                                <input type="text" id="zipcode" name="zipcode" value="<?= $_SESSION['usuario']['cep'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
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

    <!-- Edit Medical Information Modal -->
    <div id="edit-medical-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-blue-900">Editar Informações Médicas</h3>
                <button id="close-medical-modal-btn" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>

            <form action="actions/action_editar_medico.php" id="edit-medical-form">
                <div class="space-y-6">
                    <div>
                        <h4 class="text-lg font-semibold text-blue-900 mb-3">Informações Médicas</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="allergies" class="block text-gray-700 font-medium mb-1">Alergias a medicamentos</label>
                                <input type="text" id="allergies" name="allergies" value="<?= $_SESSION['usuario']['alergias'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="chronic_conditions" class="block text-gray-700 font-medium mb-1">Condições Crônicas</label>
                                <input type="text" id="chronic_conditions" name="chronic_conditions" value="<?= $_SESSION['usuario']['doencas_cronicas'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="medications" class="block text-gray-700 font-medium mb-1">Medicamentos em Uso</label>
                                <input type="text" id="medications" name="medications" value="<?= $_SESSION['usuario']['remedio_recorrente'] ?? '' ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancel-medical-edit-btn" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition">Salvar Alterações</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
        const editProfileForm = document.getElementById('edit-profile-form');

        editProfileBtn.addEventListener('click', () => {
            editProfileModal.classList.remove('hidden');
        });

        closeModalBtn.addEventListener('click', () => {
            editProfileModal.classList.add('hidden');
        });

        cancelEditBtn.addEventListener('click', () => {
            editProfileModal.classList.add('hidden');
        });

        editProfileForm.addEventListener('submit', (e) => {
            e.preventDefault();
            // Here you would typically send the form data to your backend
            // For now, we'll just close the modal
            editProfileModal.classList.add('hidden');

            // Show a success message
            alert('Perfil atualizado com sucesso!');
        });

        // Close modal when clicking outside
        editProfileModal.addEventListener('click', (e) => {
            if (e.target === editProfileModal) {
                editProfileModal.classList.add('hidden');
            }
        });

        // Medical information modal functionality
        const editMedicalBtn = document.getElementById('edit-medical-btn');
        const editMedicalModal = document.getElementById('edit-medical-modal');
        const closeMedicalModalBtn = document.getElementById('close-medical-modal-btn');
        const cancelMedicalEditBtn = document.getElementById('cancel-medical-edit-btn');
        const editMedicalForm = document.getElementById('edit-medical-form');

        if (editMedicalBtn) {
            editMedicalBtn.addEventListener('click', () => {
                editMedicalModal.classList.remove('hidden');
            });
        }

        closeMedicalModalBtn.addEventListener('click', () => {
            editMedicalModal.classList.add('hidden');
        });

        cancelMedicalEditBtn.addEventListener('click', () => {
            editMedicalModal.classList.add('hidden');
        });

        editMedicalForm.addEventListener('submit', (e) => {
            e.preventDefault();
            // Here you would typically send the form data to your backend
            // For now, we'll just close the modal
            editMedicalModal.classList.add('hidden');

            // Show a success message
            alert('Informações médicas atualizadas com sucesso!');
        });

        // Close modal when clicking outside
        editMedicalModal.addEventListener('click', (e) => {
            if (e.target === editMedicalModal) {
                editMedicalModal.classList.add('hidden');
            }
        });
    </script>
    <script>

// Fecha o modal de avisos ao clicar no botão "Fechar"
const closeModalButton = document.getElementById('close-modal');
const modal = document.getElementById('modal');

if (closeModalButton) {
    closeModalButton.addEventListener('click', () => {
        modal.style.display = 'none';
    });
}

    </script>
</body>

</html>