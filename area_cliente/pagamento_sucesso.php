<?php
require '../includes/conexao_BdAgendamento.php';
require '../includes/classe_usuario.php';

use usuario\Usuario;

// Verifica se o usuário está logado e é um cliente
Usuario::verificarPermissao('cliente');

// Verifica se o ID do agendamento está presente
$agendamento_id = $_GET['agendamento_id'] ?? null;

// Busca informações do agendamento
$agendamento = null;
if ($agendamento_id) {
    $query = "SELECT a.*, e.nome as empresa_nome 
              FROM agendamentos a 
              INNER JOIN medcar_cadastro_login.empresas e ON a.empresa_id = e.id 
              WHERE a.id = :id AND a.cliente_id = :cliente_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $agendamento_id, PDO::PARAM_INT);
    $stmt->bindParam(':cliente_id', $_SESSION['usuario']['id'], PDO::PARAM_INT);
    $stmt->execute();
    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Pagamento Realizado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="menu_principal.php" class="flex items-center space-x-2 text-white hover:text-teal-300 transition cursor-pointer">
                        <i data-lucide="arrow-left" class="h-6 w-6"></i>
                        <span class="hidden sm:inline">Voltar</span>
                    </a>
                </div>
                <a href="menu_principal.php" class="flex items-center space-x-2 text-xl font-bold">
                    <i data-lucide="ambulance" class="h-6 w-6"></i>
                    <span>MedCar</span>
                </a>
                <div class="hidden md:flex space-x-6">
                    <a href="historico.php" class="font-medium hover:text-teal-300 transition">Histórico</a>
                    <a href="../includes/logout.php" class="font-medium hover:text-teal-300 transition">Sair</a>
                </div>
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-white hover:text-teal-300">
                        <i data-lucide="menu" class="h-6 w-6"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Menu mobile -->
        <div id="mobile-menu" class="hidden bg-blue-800 md:hidden">
            <div class="container mx-auto px-4 py-2">
                <a href="historico.php" class="block py-2 text-white hover:text-teal-300">Histórico</a>
                <a href="../includes/logout.php" class="block py-2 text-white hover:text-teal-300">Sair</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <section class="pt-24 pb-10">
        <div class="container mx-auto px-4 flex justify-center">
            <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 max-w-md w-full">
                <div class="flex flex-col items-center text-center">
                    <div class="bg-green-100 p-3 rounded-full mb-4">
                        <i data-lucide="check-circle" class="h-12 w-12 text-green-600"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-green-700 mb-2">Pagamento Realizado com Sucesso!</h1>
                    
                    <?php if ($agendamento): ?>
                    <div class="bg-gray-50 p-4 rounded-lg w-full mb-4">
                        <h2 class="font-semibold text-gray-700 mb-2">Detalhes do Agendamento</h2>
                        <div class="text-left text-sm">
                            <p><span class="font-medium">Data:</span> <?= date("d/m/Y", strtotime($agendamento['data_consulta'])) ?></p>
                            <p><span class="font-medium">Horário:</span> <?= date("H:i", strtotime($agendamento['horario'])) ?></p>
                            <p><span class="font-medium">Destino:</span> <?= htmlspecialchars($agendamento['cidade_destino']) ?></p>
                            <p><span class="font-medium">Empresa:</span> <?= htmlspecialchars($agendamento['empresa_nome']) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <p class="text-gray-600 mb-6">
                        Seu agendamento foi confirmado e está pronto para uso. Você pode acompanhar o status do seu agendamento no histórico.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 w-full">
                        <a href="historico.php" class="bg-blue-900 hover:bg-blue-800 text-white font-medium py-2 px-4 rounded-lg transition text-center flex-1 flex items-center justify-center">
                            <i data-lucide="clock" class="h-5 w-5 mr-2"></i>
                            Ver Histórico
                        </a>
                        <a href="menu_principal.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg transition text-center flex-1 flex items-center justify-center">
                            <i data-lucide="home" class="h-5 w-5 mr-2"></i>
                            Voltar ao Menu
                        </a>
                    </div>
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
                    <p class="text-blue-200">&copy; <?= date('Y') ?> MedCar. Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
        
        // Redirecionar para o histórico após 5 segundos
        setTimeout(function() {
            window.location.href = 'historico.php';
        }, 5000);
    </script>
</body>

</html>
