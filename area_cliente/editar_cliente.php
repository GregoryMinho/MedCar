<?php
require '../includes/classe_usuario.php'; // inclui o arquivo de validação de login
require '../includes/conexao_BdCadastroLogin.php';

use usuario\Usuario;
Usuario::verificarPermissao('cliente'); // verifica se o usuário logado é um cliente



$id = $_SESSION['usuario']['id'];
$dados = [];
$sql = $conn->prepare("SELECT * FROM clientes WHERE id = :id");
$sql->bindValue(":id", $id);
$sql->execute();
$conn = null; // Fecha a conexão com o banco de dados

if ($sql->rowCount() > 0) {
    $dados = $sql->fetch(PDO::FETCH_ASSOC);
} else {
    $_SESSION['error'] = "Erro ao buscar os dados do cliente.";
    header("Location:../area_cliente/menu_principal.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Conta</title>
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
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="../area_cliente/menu_principal.php" class="flex items-center space-x-2 text-white hover:text-teal-300 transition">
                        <i data-lucide="arrow-left" class="h-6 w-6"></i>
                        <span>Voltar</span>
                    </a>
                </div>
                <a href="/MedQ-2/paginas/pagina_inicial.php" class="flex items-center space-x-2 text-xl font-bold">
                    <i data-lucide="ambulance" class="h-6 w-6"></i>
                    <span>MedCar</span>
                </a>
                <div class="hidden md:flex space-x-6">
                    <a href="../area_cliente/menu_principal.php" class="font-medium hover:text-teal-300 transition">Home</a>
                    <a href="#" class="font-medium hover:text-teal-300 transition">Contato</a>
                </div>
                <button id="mobile-menu-button" class="md:hidden text-white">
                    <i data-lucide="menu" class="h-6 w-6"></i>
                </button>
            </div>
        </div>
    </nav>

    <div id="mobile-menu" class="fixed inset-0 z-50 bg-blue-900 bg-opacity-95 flex flex-col text-white p-6 mobile-menu">
        <div class="flex justify-end">
            <button id="close-menu-button" class="text-white">
                <i data-lucide="x" class="h-6 w-6"></i>
            </button>
        </div>
        <div class="flex flex-col items-center justify-center space-y-8 flex-grow text-xl">
            <a href="../area_cliente/menu_principal.php" class="font-medium hover:text-teal-300 transition">Home</a>
            <a href="#" class="font-medium hover:text-teal-300 transition">Contato</a>
        </div>
    </div>

    <section class="pt-24 pb-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-teal-500 text-white p-8 text-center">
                    <h2 class="text-3xl font-bold mb-2">Editar Minha Conta</h2>
                </div>
                <div class="p-8">
                    <div class="bg-teal-500 text-red-600 text-center">
                        <h3 class="text-xl font-bold mb-2">Altere somente os campos que deseja alterar</h3>
                    </div>
                    <form id="editar-form" class="space-y-6" action="actions/action_editar_cliente.php" method="POST">
                        <input type="hidden" name="id" id="id" value="<?= $dados['id']; ?>">
                        <div>
                            <label for="nome" class="block text-sm font-medium text-gray-700">Nome</label>
                            <input type="text" id="nome" name="nome" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" value="<?= $dados['nome']; ?>" required>
                        </div>
                        <div>
                            <label for="senha" class="block text-sm font-medium text-gray-700">Senha</label>
                            <input type="text" id="senha" name="senha" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" value="" minlength="8" placeholder="Não altere para manter a senha atual">
                        </div>
                        <div>
                            <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                            <input type="text" id="telefone" name="telefone" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" value="<?= $dados['telefone']; ?>" required>
                        </div>
                        <button type="submit" class="w-1/3 flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-xl font-medium text-white bg-teal-500 hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            Salvar
                        </button>
                    </form>
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
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>
    <script src="../jquery.mask.min.js"></script>
    <script>
        $('#telefone').mask('(00) 00000-0000');
    </script>
</body>

</html>