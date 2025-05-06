<?php
require '../includes/conexao_BdAgendamento.php';
require '../includes/classe_usuario.php';

use usuario\Usuario;

Usuario::verificarPermissao('cliente'); // Verifica se o usuário logado é um cliente

$usuario_id = $_SESSION['usuario']['id'];

// Consulta agendamentos com situação "agendado"
$query = "SELECT id, data_consulta, horario, valor FROM agendamentos WHERE cliente_id = :id AND situacao = 'agendado'";
$stmt = $conn->prepare($query);
$stmt->bindParam(":id", $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Pagar Agendamentos</title>
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
                    <a onclick="window.history.back();" class="flex items-center space-x-2 text-white hover:text-teal-300 transition">
                        <i data-lucide="arrow-left" class="h-6 w-6"></i>
                        <span>Voltar</span>
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
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <section class="pt-24 pb-10">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
                <h1 class="text-2xl font-bold text-blue-900 mb-6">Pagar Agendamentos</h1>

                <?php if (count($agendamentos) > 0): ?>
                    <table class="min-w-full bg-white border border-gray-300 rounded-lg overflow-hidden">
                        <thead class="bg-blue-900 text-white">
                            <tr>
                                <th class="py-3 px-4 text-left">Data</th>
                                <th class="py-3 px-4 text-left">Horário</th>
                                <th class="py-3 px-4 text-left">Valor</th>
                                <th class="py-3 px-4 text-center">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($agendamentos as $agendamento): ?>
                                <tr class="border-b">
                                    <td class="py-3 px-4 text-gray-700"><?= date("d/m/Y", strtotime($agendamento['data_consulta'])) ?></td>
                                    <td class="py-3 px-4 text-gray-700"><?= date("H:i", strtotime($agendamento['horario'])) ?></td>
                                    <td class="py-3 px-4 text-gray-700">R$ <?= number_format($agendamento['valor'], 2, ',', '.') ?></td>
                                    <td class="py-3 px-4 text-center">
                                        <form action="processar_pagamento.php" method="POST">
                                            <input type="hidden" name="agendamento_id" value="<?= $agendamento['id'] ?>">
                                            <button type="submit" class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-2 px-4 rounded-lg transition-all hover:scale-105">
                                                Pagar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-gray-600 text-center">Nenhum agendamento pendente de pagamento encontrado.</p>
                <?php endif; ?>
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
                    <p class="text-blue-200">&copy; 2023 MedCar. Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>

</html>