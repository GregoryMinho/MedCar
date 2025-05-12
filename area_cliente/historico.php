<?php
require '../includes/classe_usuario.php'; // inclui o arquivo de validação de login
require '../includes/conexao_BdAgendamento.php'; // inclui o arquivo de conexão com o banco de dados

use usuario\Usuario;

Usuario::verificarPermissao('cliente'); // verifica se o usuário logado é um cliente

// Query para buscar os agendamentos do cliente logado
try {
    $sql = "SELECT 
                a.id, 
                a.data_consulta, 
                a.horario, 
                a.rua_origem, 
                a.numero_origem,
                a.complemento_origem, 
                a.cidade_origem, 
                a.cep_origem, 
                a.rua_destino,
                a.numero_destino, 
                a.complemento_destino, 
                a.cidade_destino, 
                a.cep_destino,
                a.condicao_medica,
                a.situacao,
                a.valor,
                a.data_pagamento,
                e.nome AS empresa_nome,
                CASE 
                    WHEN t.id IS NOT NULL THEN 'Sim' 
                    ELSE 'Não' 
                END AS pagamento_realizado
            FROM medcar_agendamentos.agendamentos AS a 
            INNER JOIN medcar_cadastro_login.empresas AS e ON e.id = a.empresa_id
            LEFT JOIN medcar_financeiro.transacoes AS t ON t.agendamento_id = a.id AND t.status = 'Pago'
            WHERE a.cliente_id = :cliente_id 
            ORDER BY a.data_consulta DESC, a.horario DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cliente_id', $_SESSION['usuario']['id'], PDO::PARAM_INT);
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
    <title>MedCar - Histórico de Agendamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-gray-100 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-blue-900 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="menu_principal.php" class="flex items-center space-x-2 text-white hover:text-teal-300 transition cursor-pointer">
                    <i data-lucide="arrow-left" class="h-6 w-6"></i>
                    <span>Voltar</span>
                </a>
            </div>
            <a class="text-xl font-bold" href="menu_principal.php">
                <i data-lucide="ambulance" class="h-6 w-6 inline mr-2"></i>
                MedCar - Histórico
            </a>

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
                        <a href="perfil_cliente.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                            <i data-lucide="user" class="h-4 w-4 inline mr-2"></i>Minha Conta
                        </a>
                        <a href="../paginas/abas_menu_principal/aba_empresas.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                            <i data-lucide="calendar" class="h-4 w-4 inline mr-2"></i>Agendar
                        </a>
                        <a href="" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                            <i data-lucide="clock" class="h-4 w-4 inline mr-2"></i>Meus Agendamentos
                        </a>
                        <div class="border-t border-gray-100"></div>
                        <a href="../includes/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                            <i data-lucide="log-out" class="h-4 w-4 inline mr-2"></i>Sair
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto py-8">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4">Histórico de Agendamentos</h2>

            <!-- Filtros -->
            <div class="mb-9 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="filter-year" class="block text-lg font-medium text-gray-700">Ano</label>
                    <select id="filter-year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos</option>
                        <?php foreach ($anos as $ano): ?>
                            <option value="<?= $ano ?>"><?= $ano ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="filter-month" class="block text-lg font-medium text-gray-700">Mês</label>
                    <select id="filter-month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos</option>
                        <?php
                        $meses = [
                            1 => 'Janeiro',
                            'Fevereiro',
                            'Março',
                            'Abril',
                            'Maio',
                            'Junho',
                            'Julho',
                            'Agosto',
                            'Setembro',
                            'Outubro',
                            'Novembro',
                            'Dezembro'
                        ];
                        foreach ($meses as $num => $nome): ?>
                            <option value="<?= $num ?>"><?= $nome ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="filter-status" class="block text-lg font-medium text-gray-700">Situação</label>
                    <select id="filter-status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todas</option>
                        <option value="Agendado">Agendado</option>
                        <option value="Pendente">Pendente</option>
                        <option value="Concluido">Concluído</option>
                        <option value="Cancelado">Cancelado</option>
                        <option value="Pago">Pago</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end mb-4">
                <button id="clear-filters" class="bg-red-500 text-white font-semibold px-4 py-2 rounded hover:bg-red-700">Limpar Filtros</button>
            </div>
            <div class="border-t border-gray-300 mb-4"></div>
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
                                $mes = date("n", strtotime($agendamento['data_consulta']));
                                
                                if ($agendamento['situacao'] == 'Pago') {
                                    $statusClass = 'bg-blue-500 text-white';
                                } elseif ($agendamento['situacao'] == 'Agendado') {
                                    $statusClass = 'bg-yellow-500 text-black';
                                } elseif ($agendamento['situacao'] == 'Concluido') {
                                    $statusClass = 'bg-green-500 text-white';
                                } elseif ($agendamento['situacao'] == 'Cancelado') {
                                    $statusClass = 'bg-red-500 text-white';
                                } else {
                                    $statusClass = 'bg-gray-500 text-white';
                                }
                            ?>
                                <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 filter-item" data-year="<?= $ano ?>" data-month="<?= $mes ?>" data-status="<?= $agendamento['situacao'] ?>">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h5 class="text-lg font-bold"><?= htmlspecialchars($agendamento['rua_destino']) ?></h5>
                                            <p class="text-sm text-gray-600"><i data-lucide="calendar" class="h-4 w-4 inline mr-1"></i><?= "$dataFormatada - $horarioFormatado" ?></p>
                                            <p class="text-sm text-gray-600"><i data-lucide="map-pin" class="h-4 w-4 inline mr-1"></i><?= htmlspecialchars($agendamento['cidade_destino']) ?></p>
                                            <p class="text-sm text-gray-600"><i data-lucide="building" class="h-4 w-4 inline mr-1"></i><?= htmlspecialchars($agendamento['empresa_nome']) ?></p>
                                            <?php if ($agendamento['valor']): ?>
                                                <p class="text-sm font-medium mt-1">
                                                    <i data-lucide="credit-card" class="h-4 w-4 inline mr-1"></i>
                                                    R$ <?= number_format($agendamento['valor'], 2, ',', '.') ?>
                                                    <?php if ($agendamento['situacao'] == 'Pago'): ?>
                                                        <span class="text-green-600 ml-1">(Pago)</span>
                                                    <?php elseif ($agendamento['situacao'] == 'Agendado'): ?>
                                                        <a href="pagar_agendamento.php?id=<?= $agendamento['id'] ?>" class="text-blue-600 ml-1 hover:underline">Pagar</a>
                                                    <?php endif; ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <span class="px-3 py-1 rounded-full <?= $statusClass ?>"><?= $agendamento['situacao'] ?></span>
                                    </div>
                                    <hr class="my-2">
                                    <button class="text-blue-500 hover:underline" data-modal-target="#detailsModal-<?= $agendamento['id'] ?>">
                                        <i data-lucide="info" class="h-4 w-4 inline mr-1"></i>Detalhes
                                    </button>
                                </div>

                                <!-- Modal -->
                                <div id="detailsModal-<?= $agendamento['id'] ?>" class="hidden fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50">
                                    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl">
                                        <div class="flex justify-between items-center mb-4">
                                            <h5 class="text-xl font-bold">Detalhes do Agendamento</h5>
                                            <button class="text-gray-500 hover:text-gray-700" data-modal-close="#detailsModal-<?= $agendamento['id'] ?>">
                                                <i data-lucide="x" class="h-5 w-5"></i>
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
                                                    <td class="border px-4 py-2"><strong>Empresa:</strong></td>
                                                    <td class="border px-4 py-2"><?= htmlspecialchars($agendamento['empresa_nome']) ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2"><strong>Condição Médica:</strong></td>
                                                    <td class="border px-4 py-2"><?= htmlspecialchars($agendamento['condicao_medica']) ?></td>
                                                </tr>
                                                <tr class="bg-gray-100">
                                                    <td class="border px-4 py-2"><strong>Situação:</strong></td>
                                                    <td class="border px-4 py-2">
                                                        <span class="px-2 py-1 rounded-full <?= $statusClass ?>"><?= $agendamento['situacao'] ?></span>
                                                    </td>
                                                </tr>
                                                <?php if ($agendamento['valor']): ?>
                                                <tr>
                                                    <td class="border px-4 py-2"><strong>Valor:</strong></td>
                                                    <td class="border px-4 py-2">R$ <?= number_format($agendamento['valor'], 2, ',', '.') ?></td>
                                                </tr>
                                                <?php endif; ?>
                                                <?php if ($agendamento['data_pagamento']): ?>
                                                <tr class="bg-gray-100">
                                                    <td class="border px-4 py-2"><strong>Data do Pagamento:</strong></td>
                                                    <td class="border px-4 py-2"><?= date("d/m/Y H:i", strtotime($agendamento['data_pagamento'])) ?></td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                        <div class="mt-4 text-right">
                                            <?php if ($agendamento['situacao'] == 'Agendado' && $agendamento['valor']): ?>
                                                <a href="pagar_agendamento.php?id=<?= $agendamento['id'] ?>" class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded mr-2">
                                                    Pagar Agendamento
                                                </a>
                                            <?php endif; ?>
                                            <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700" data-modal-close="#detailsModal-<?= $agendamento['id'] ?>">Fechar</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="no-results-message text-gray-600 text-center hidden">Nenhum agendamento encontrado para este ano.</p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-600">Nenhum agendamento encontrado.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

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

        // Filtro de pesquisa
        const filterYear = document.getElementById('filter-year');
        const filterMonth = document.getElementById('filter-month');
        const filterStatus = document.getElementById('filter-status');
        const filterItems = document.querySelectorAll('.filter-item');

        function applyFilters() {
            const year = filterYear.value;
            const month = filterMonth.value;
            const status = filterStatus.value;

            document.querySelectorAll('.year-section').forEach(section => {
                const yearItems = section.querySelectorAll('.filter-item');
                let hasVisibleItems = false;

                yearItems.forEach(item => {
                    const itemMonth = item.getAttribute('data-month');
                    const itemStatus = item.getAttribute('data-status');

                    const matchesMonth = !month || itemMonth === month;
                    const matchesStatus = !status || itemStatus === status;

                    if (matchesMonth && matchesStatus) {
                        item.classList.remove('hidden');
                        hasVisibleItems = true;
                    } else {
                        item.classList.add('hidden');
                    }
                });

                // Display "No appointments found" message if no items are visible in the year section
                const noResultsMessage = section.querySelector('.no-results-message');
                if (hasVisibleItems) {
                    noResultsMessage.classList.add('hidden');
                } else {
                    noResultsMessage.classList.remove('hidden');
                }
            });
        }

        filterYear.addEventListener('change', () => {
            const selectedYear = filterYear.value;
            if (selectedYear) {
                document.getElementById('current-year').textContent = selectedYear;
                document.querySelectorAll('.year-section').forEach(section => {
                    section.classList.add('hidden');
                });
                document.getElementById('year-' + selectedYear).classList.remove('hidden');
            } else {
                navigateYear(0); // Voltar ao ano inicial
            }
            applyFilters(); // Aplicar filtros adicionais
        });

        filterMonth.addEventListener('change', applyFilters);
        filterStatus.addEventListener('change', applyFilters);

        // Botão para limpar filtros
        document.getElementById('clear-filters').addEventListener('click', () => {
            filterYear.value = '';
            filterMonth.value = '';
            filterStatus.value = '';
            // Resetar o ano atual para o primeiro ano disponível
            document.getElementById('current-year').textContent = anos[0];            
            document.querySelectorAll('.no-results-message').forEach(message => {
                message.classList.add('hidden');
            });
            // Mostrar todos os anos e itens
            document.querySelectorAll('.year-section').forEach(section => {
                section.classList.add('hidden');
            });
            document.getElementById('year-' + anos[0]).classList.remove('hidden');
            filterItems.forEach(item => {
                item.classList.remove('hidden');
            });
        });
    </script>
</body>

</html>
