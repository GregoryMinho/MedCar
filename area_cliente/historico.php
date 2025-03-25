<?php
require '../includes/valida_login.php'; // inclui o arquivo de validação de login
require '../includes/conexao_BdAgendamento.php'; // inclui o arquivo de conexão com o banco de dados

verificarPermissao('cliente'); // verifica se o usuario logado é um cliente

$cliente_id = $_SESSION['usuario']['id'];  // ID do cliente logado
// Query para buscar os agendamentos do cliente logado
try {
    $sql = "SELECT * FROM agendamentos WHERE cliente_id = :cliente_id ORDER BY data_consulta DESC, horario DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);
    $stmt->execute();
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Erro: ' . $e->getMessage();
}

$stmt->closeCursor();
$conn = null;

// Agrupar agendamentos por ano
$agendamentosPorAno = [];
foreach ($agendamentos as $agendamento) {
    $ano = date("Y", strtotime($agendamento['data_consulta']));
    if (!isset($agendamentosPorAno[$ano])) {
        $agendamentosPorAno[$ano] = [];
    }
    $agendamentosPorAno[$ano][] = $agendamento;
}
$anos = array_keys($agendamentosPorAno);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedQ - Histórico de Agendamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-blue-900 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a class="text-xl font-bold" href="#">
                <i class="fas fa-calendar-alt mr-2"></i>
                MedQ - Histórico
            </a>
            <div class="flex items-center">
                <span class="mr-3">Histórico de Agendamentos</span>
                <img src="https://source.unsplash.com/random/40x40/?icon" class="rounded-full" alt="Perfil">
                <a href="../includes/logout.php" class="font-medium hover:text-teal-300 transition">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto py-8">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4">Histórico de Agendamentos</h2>
            <?php if (count($agendamentos) > 0): ?>
                <!-- Navegação por Ano -->
                <div class="mb-4 flex justify-between items-center">
                    <button class="bg-gray-600 text-white rounded-lg p-2 hover:underline" onclick="navigateYear(-1)">&larr; Anterior</button>
                    <span id="current-year" class="text-xl font-bold"><?= $anos[0] ?></span>
                    <button class="bg-gray-600 text-white rounded-lg p-2 hover:underline" onclick="navigateYear(1)">Próximo &rarr;</button>
                </div>

                <!-- Agendamentos por Ano -->
                <?php foreach ($agendamentosPorAno as $ano => $agendamentos): ?>
                    <div id="year-<?= $ano ?>" class="year-section <?= $ano !== $anos[0] ? 'hidden' : '' ?>">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach ($agendamentos as $agendamento):
                                $dataFormatada = date("d/m/Y", strtotime($agendamento['data_consulta']));
                                $horarioFormatado = date("H:i", strtotime($agendamento['horario']));
                                if ($agendamento['situacao'] == 'Agendado') {
                                    $statusClass = 'bg-yellow-500 text-black';
                                } elseif ($agendamento['situacao'] == 'Concluido') {
                                    $statusClass = 'bg-green-500 text-white';
                                } elseif ($agendamento['situacao'] == 'Cancelado') {
                                    $statusClass = 'bg-red-500 text-white';
                                } else {
                                    $statusClass = 'bg-gray-500 text-white';
                                }
                            ?>
                                <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h5 class="text-lg font-bold"><?= htmlspecialchars($agendamento['rua_destino']) ?></h5>
                                            <p class="text-sm text-gray-600"><i class="fas fa-calendar-day mr-2"></i><?= "$dataFormatada - $horarioFormatado" ?></p>
                                            <p class="text-sm text-gray-600"><i class="fas fa-map-marker-alt mr-2"></i><?= htmlspecialchars($agendamento['cidade_destino']) ?></p>
                                        </div>
                                        <span class="px-3 py-1 rounded-full <?= $statusClass ?>"><?= $agendamento['situacao'] ?></span>
                                    </div>
                                    <hr class="my-2">
                                    <button class="text-blue-500 hover:underline" data-modal-target="#detailsModal-<?= $agendamento['id'] ?>">
                                        <i class="fas fa-info-circle mr-2"></i>Detalhes
                                    </button>
                                </div>

                                <!-- Modal -->
                                <div id="detailsModal-<?= $agendamento['id'] ?>" class="hidden fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center">
                                    <div class="bg-white rounded-lg shadow-lg p-6 w-1/2">
                                        <div class="flex justify-between items-center mb-4">
                                            <h5 class="text-xl font-bold">Detalhes do Agendamento</h5>
                                            <button class="text-gray-500 hover:text-gray-700" data-modal-close="#detailsModal-<?= $agendamento['id'] ?>">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <table class="min-w-full bg-white">
                                            <tbody>
                                                <tr class="bg-gray-100">
                                                    <td class="border px-4 py-2"><strong>Data:</strong></td>
                                                    <td class="border px-4 py-2"><?= $dataFormatada ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2"><strong>Horário:</strong></td>
                                                    <td class="border px-4 py-2"><?= $horarioFormatado ?></td>
                                                </tr>
                                                <tr class="bg-gray-100">
                                                    <td class="border px-4 py-2"><strong>Origem:</strong></td>
                                                    <td class="border px-4 py-2"><?= htmlspecialchars($agendamento['rua_origem']) ?>, <?= htmlspecialchars($agendamento['cidade_origem']) ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2"><strong>Destino:</strong></td>
                                                    <td class="border px-4 py-2"><?= htmlspecialchars($agendamento['rua_destino']) ?>, <?= htmlspecialchars($agendamento['cidade_destino']) ?></td>
                                                </tr>
                                                <tr class="bg-gray-100">
                                                    <td class="border px-4 py-2"><strong>Condição Médica:</strong></td>
                                                    <td class="border px-4 py-2"><?= htmlspecialchars($agendamento['condicao_medica']) ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2"><strong>Medicamentos:</strong></td>
                                                    <td class="border px-4 py-2"><?= htmlspecialchars($agendamento['medicamentos']) ?></td>
                                                </tr>
                                                <tr class="bg-gray-100">
                                                    <td class="border px-4 py-2"><strong>Alergias:</strong></td>
                                                    <td class="border px-4 py-2"><?= htmlspecialchars($agendamento['alergias']) ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2"><strong>Contato de Emergência:</strong></td>
                                                    <td class="border px-4 py-2"><?= htmlspecialchars($agendamento['contato_emergencia']) ?></td>
                                                </tr>
                                                <tr class="bg-gray-100">
                                                    <td class="border px-4 py-2"><strong>Informações Adicionais:</strong></td>
                                                    <td class="border px-4 py-2"><?= htmlspecialchars($agendamento['informacoes_adicionais']) ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="mt-4 text-right">
                                            <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700" data-modal-close="#detailsModal-<?= $agendamento['id'] ?>">Fechar</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-600">Nenhum agendamento encontrado.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Script para abrir e fechar modais
        document.querySelectorAll('[data-modal-target]').forEach(button => {
            button.addEventListener('click', () => {
                const modal = document.querySelector(button.getAttribute('data-modal-target'));
                modal.classList.remove('hidden');
            });
        });

        document.querySelectorAll('[data-modal-close]').forEach(button => {
            button.addEventListener('click', () => {
                const modal = document.querySelector(button.getAttribute('data-modal-close'));
                modal.classList.add('hidden');
            });
        });

        // Script para navegar entre os anos
        let currentYearIndex = 0;
        const anos = <?= json_encode($anos) ?>;

        function navigateYear(direction) {
            currentYearIndex += direction;
            if (currentYearIndex < 0) {
                currentYearIndex = anos.length - 1;
            } else if (currentYearIndex >= anos.length) {
                currentYearIndex = 0;
            }
            const year = anos[currentYearIndex];
            document.getElementById('current-year').textContent = year;
            document.querySelectorAll('.year-section').forEach(section => {
                section.classList.add('hidden');
            });
            document.getElementById('year-' + year).classList.remove('hidden');
        }
    </script>
</body>

</html>