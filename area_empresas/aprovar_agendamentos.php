<?php
require '../includes/classe_usuario.php';
use usuario\Usuario;

// Garante que apenas empresas acessem a página
Usuario::verificarPermissao('empresa');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Geração do token CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Verifica se a sessão tem um ID de usuário
if (!isset($_SESSION['usuario']['id'])) {
    die('Erro: Usuário não autenticado.');
}

$empresa_id = $_SESSION['usuario']['id'];

require '../includes/conexao_BdAgendamento.php';

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
                c.nome AS cliente_nome 
            FROM medcar_agendamentos.agendamentos AS a 
            INNER JOIN medcar_cadastro_login.clientes AS c 
                ON c.id = a.cliente_id 
            WHERE a.empresa_id = :empresa_id 
              AND a.situacao = 'Pendente'
            ORDER BY a.data_consulta, a.horario";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
    $stmt->execute();
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro detalhado: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Aprovar Agendamentos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
                    <div class="relative group">
                        <button class="flex items-center space-x-1 font-medium hover:text-teal-300 transition">
                            <i data-lucide="user" class="h-5 w-5"></i>
                            <span>Perfil</span>
                            <i data-lucide="chevron-down" class="h-4 w-4"></i>
                        </button>
                        <div class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 invisible group-hover:visible transition-all duration-300 opacity-0 group-hover:opacity-100 transform group-hover:translate-y-0 translate-y-2">
                            <div class="py-1">
                                <a href="perfil_empresa.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="user" class="h-4 w-4 inline mr-2"></i>Minha Conta
                                </a>
                                <a href="dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="chart-pie" class="h-4 w-4 inline mr-2"></i>Estatísticas
                                </a>
                                <a href="agendamentos_pacientes.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="clock" class="h-4 w-4 inline mr-2"></i>Ver Agendamentos
                                </a>
                                <a href="aprovar_agendamentos.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="clipboard-check" class="h-4 w-4 inline mr-2"></i>Aprovar Agendamentos
                                </a>
                                <a href="gestao_motoristas.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="car" class="h-4 w-4 inline mr-2"></i>Motoristas
                                </a>
                                <a href="relatorios_financeiros.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="chart-no-axes-combined" class="h-4 w-4 inline mr-2"></i>Financeiro
                                </a>
                                <a href="relatorios.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-900">
                                    <i data-lucide="clipboard-list" class="h-4 w-4 inline mr-2"></i>Relatórios
                                </a>
                                <div class="border-t border-gray-300"></div>
                                <a href="../includes/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i data-lucide="log-out" class="h-4 w-4 inline mr-2"></i>Sair
                                </a>
                            </div>
                        </div>
                    </div>
                    <button id="mobile-menu-button" class="md:hidden text-white ml-2">
                        <i data-lucide="menu" class="h-6 w-6"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="fixed inset-0 z-50 bg-blue-900 bg-opacity-95 flex flex-col text-white p-6 mobile-menu">
        <div class="flex justify-end">
            <button id="close-menu-button" class="text-white">
                <i class="bi bi-x-lg h-6 w-6"></i>
            </button>
        </div>
        <div class="flex flex-col items-start space-y-6 flex-grow text-xl ps-4">
            <a href="dashboard.php" class="w-full hover:text-teal-300 transition d-flex align-items-center gap-3">
                <i class="bi bi-grid fs-5"></i>
                Estatísticas
            </a>
            <a href="agendamentos_pacientes.php" class="w-full hover:text-teal-300 transition d-flex align-items-center gap-3">
                <i class="bi bi-calendar-event fs-5"></i>
                Agendamentos
            </a>
            <a href="aprovar_agendamentos.php" class="w-full hover:text-teal-300 transition d-flex align-items-center gap-3">
                <i class="bi bi-calendar-check fs-5"></i>
                Aprovar Agendamentos
            </a>
            <a href="gestao_motoristas.php" class="w-full hover:text-teal-300 transition d-flex align-items-center gap-3">
                <i class="bi bi-people fs-5"></i>
                Motoristas
            </a>
            <a href="gestao_veiculos.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
    <i class="bi bi-truck fs-6"></i>
    <span>Frota</span>
</a>

            <a href="relatorios_financeiros.php" class="w-full hover:text-teal-300 transition d-flex align-items-center gap-3">
                <i class="bi bi-graph-up fs-5"></i>
                Financeiro
            </a>
            <a href="relatorios.php" class="w-full hover:text-teal-300 transition d-flex align-items-center gap-3">
                <i class="bi bi-file-text fs-5"></i>
                Relatórios
            </a>
            <div class="relative">
                <button id="dropdown-button-mobile" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition focus:outline-none">
                    <i data-lucide="settings" class="h-5 w-5"></i>
                    <span>Configurações</span>
                    <i data-lucide="chevron-down" class="h-5 w-5"></i>
                </button>
                <div id="dropdown-menu-mobile" class="absolute hidden bg-white text-blue-900 rounded-lg shadow-lg mt-2 w-48">
                    <a href="perfil_empresa.php" class="block px-4 py-2 hover:bg-gray-100">Editar Cadastro</a>
                    <a href="seguranca.php" class="block px-4 py-2 hover:bg-gray-100">Segurança</a>
                </div>
            </div>
            <a href="avaliacoes.php" class="w-full hover:text-teal-300 transition d-flex align-items-center gap-3">
                <i class="bi bi-star fs-5"></i>
                Avaliações
            </a>
            <a href="../includes/logout.php" class="btn btn-outline-light ms-3">Logout</a>
        </div>
    </div>

    <div class="flex flex-col md:flex-row">
        <!-- Sidebar -->
        <div class="hidden md:block w-64 bg-blue-900 text-white min-h-screen pt-24">
            <nav class="flex flex-col space-y-2 px-4">
                <a href="dashboard.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-grid fs-6"></i>
                    <span>Estatísticas</span>
                </a>
                <a href="agendamentos_pacientes.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-calendar-event fs-6"></i>
                    <span>Agendamentos</span>
                </a>
                <a href="aprovar_agendamentos.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition group">
                    <i class="bi bi-check-circle fs-6 text-white group-hover:text-green-400"></i>
                    <span class="text-white group-hover:text-green-400">Aprovar Agendamentos</span>
                </a>
                <a href="gestao_motoristas.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-people fs-6"></i>
                    <span>Motoristas</span>
                </a>
                <a href="gestao_veiculos.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
    <i class="bi bi-truck fs-6"></i>
    <span>Frota</span>
</a>

                <a href="relatorios_financeiros.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-graph-up fs-6"></i>
                    <span>Financeiro</span>
                </a>
                <a href="PowerBiDashFinanceiro.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-coin"></i>
                    <span>Dashboard Financeiro</span>
                </a>
                <a href="relatorios.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-file-text fs-6"></i>
                    <span>Relatórios</span>
                </a>
                <a href="avaliacoes.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-star fs-6"></i>
                    <span>Avaliações</span>
                </a>
                <a href="batepapo_clientes.php" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-chat-dots fs-6"></i>
                    <span>Bate-Papo com Clientes</span>
                </a>
                <div class="relative">
                    <button id="dropdown-button" class="flex items-center space-x-2 px-4 py-3 rounded-lg text-white hover:bg-blue-800 transition focus:outline-none">
                        <i data-lucide="settings" class="h-5 w-5"></i>
                        <span>Configurações</span>
                        <i data-lucide="chevron-down" class="h-5 w-5"></i>
                    </button>
                    <div id="dropdown-menu" class="absolute hidden bg-white text-blue-900 rounded-lg shadow-lg mt-2 w-48">
                        <a href="editar_cadastro_empresa.php" class="block px-4 py-2 hover:bg-gray-100">Editar Cadastro</a>
                        <a href="seguranca.php" class="block px-4 py-2 hover:bg-gray-100">Segurança</a>
                        <a href="preferencias.php" class="block px-4 py-2 hover:bg-gray-100">Preferências</a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <section class="pt-24 pb-10">
                <div class="container mx-auto px-4">
                    <h1 class="text-3xl font-bold text-blue-900 mb-8">Solicitações Pendentes</h1>

                    <!-- Filtros -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <form method="GET" class="flex flex-wrap gap-4">
                            <input type="text" name="busca" placeholder="Buscar por nome..." 
                                class="px-4 py-2 border rounded-lg flex-grow">
                            <select name="status" class="px-4 py-2 border rounded-lg">
                                <option value="">Todos os status</option>
                                <option value="Pendente">Pendentes</option>
                                <option value="Aprovado">Aprovados</option>
                                <option value="Recusado">Recusados</option>
                            </select>
                            <input type="date" name="data" class="px-4 py-2 border rounded-lg">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                Filtrar
                            </button>
                        </form>
                    </div>

                    <!-- Listagem de Agendamentos -->
                    <?php if (!empty($agendamentos)): ?>
                        <?php foreach ($agendamentos as $row): ?>
                            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h2 class="text-2xl font-semibold text-blue-900">
                                            <?php echo htmlspecialchars($row['cliente_nome']); ?>
                                        </h2>
                                        <p class="text-gray-600 mt-2">
                                            <?php echo date('d/m/Y', strtotime($row['data_consulta'])); ?> 
                                            às <?php echo htmlspecialchars($row['horario']); ?>
                                        </p>
                                    </div>

                                    <!-- Ações -->
                                    <div class="flex gap-3">
                                        <!-- Botão Aprovar com Modal -->
                                        <button onclick="openApproveForm(<?php echo $row['id']; ?>)" 
                                                class="bg-teal-500 text-white px-4 py-2 rounded-lg hover:bg-teal-600 flex items-center">
                                            <i data-lucide="check-circle" class="h-5 w-5 mr-2"></i>Aprovar
                                        </button>

                                        <!-- Botão Recusar -->
                                        <button onclick="openRejectForm(<?php echo $row['id']; ?>)" 
                                                class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 flex items-center">
                                            <i data-lucide="x-circle" class="h-5 w-5 mr-2"></i>Recusar
                                        </button>
                                    </div>
                                </div>

                                <!-- Detalhes Expandíveis -->
                                <div class="mt-4 border-t pt-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Seções de Endereço... (mantidas como no original) -->
                                    </div>
                                </div>

                                <!-- Formulário de Aprovação -->
                                <div id="approveForm-<?php echo $row['id']; ?>" class="hidden mt-4">
                                    <form method="POST" action="processar_acao_aprovacao_agendamento.php">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="agendamento_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="acao" value="aprovar">
                                        
                                        <div class="mb-3">
                                            <label class="block text-gray-700 font-medium mb-2">Valor do Transporte (R$)</label>
                                            <input type="number" name="valor" step="0.01" min="0.01" 
                                                class="w-full px-4 py-2 border rounded-lg" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="block text-gray-700 font-medium mb-2">Observações (opcional)</label>
                                            <textarea name="observacoes" rows="2" 
                                                class="w-full px-4 py-2 border rounded-lg"></textarea>
                                        </div>
                                        
                                        <div class="flex justify-end gap-3">
                                            <button type="button" onclick="closeApproveForm(<?php echo $row['id']; ?>)" 
                                                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                                                Cancelar
                                            </button>
                                            <button type="submit" 
                                                    class="bg-teal-500 text-white px-4 py-2 rounded-lg hover:bg-teal-600">
                                                Confirmar Aprovação
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Formulário de Rejeição -->
                                <div id="rejectForm-<?php echo $row['id']; ?>" class="hidden mt-4">
                                    <form method="POST" action="processar_acao_aprovacao_agendamento.php">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="agendamento_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="acao" value="recusar">
                                        
                                        <div class="mb-3">
                                            <label class="block text-gray-700 font-medium mb-2">Motivo da Recusa</label>
                                            <textarea name="motivo" rows="3" 
                                                class="w-full px-4 py-2 border rounded-lg" required></textarea>
                                        </div>
                                        
                                        <div class="flex justify-end gap-3">
                                            <button type="button" onclick="closeRejectForm(<?php echo $row['id']; ?>)" 
                                                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                                                Cancelar
                                            </button>
                                            <button type="submit" 
                                                    class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                                                Confirmar Recusa
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-600">Nenhum agendamento pendente encontrado.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Mobile menu functionality
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeMenuButton = document.getElementById('close-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.add('open');
            document.body.style.overflow = 'hidden';
        });

        closeMenuButton.addEventListener('click', () => {
            mobileMenu.classList.remove('open');
            document.body.style.overflow = '';
        });

        // Dropdown menus
        const dropdownButton = document.getElementById('dropdown-button');
        const dropdownMenu = document.getElementById('dropdown-menu');
        const dropdownButtonMobile = document.getElementById('dropdown-button-mobile');
        const dropdownMenuMobile = document.getElementById('dropdown-menu-mobile');

        if (dropdownButton && dropdownMenu) {
            dropdownButton.addEventListener('click', () => {
                dropdownMenu.classList.toggle('hidden');
            });
        }

        if (dropdownButtonMobile && dropdownMenuMobile) {
            dropdownButtonMobile.addEventListener('click', () => {
                dropdownMenuMobile.classList.toggle('hidden');
            });
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', (event) => {
            if (dropdownButton && !dropdownButton.contains(event.target)) {
                dropdownMenu.classList.add('hidden');
            }
            if (dropdownButtonMobile && !dropdownButtonMobile.contains(event.target)) {
                dropdownMenuMobile.classList.add('hidden');
            }
        });

        function openApproveForm(id) {
            const form = document.getElementById(`approveForm-${id}`);
            form.classList.remove('hidden');
            form.scrollIntoView({ behavior: 'smooth' });
        }

        function closeApproveForm(id) {
            document.getElementById(`approveForm-${id}`).classList.add('hidden');
        }

        function openRejectForm(id) {
            const form = document.getElementById(`rejectForm-${id}`);
            form.classList.remove('hidden');
            form.scrollIntoView({ behavior: 'smooth' });
        }

        function closeRejectForm(id) {
            document.getElementById(`rejectForm-${id}`).classList.add('hidden');
        }
    </script>
</body>
</html>
<?php 
// Fecha a conexão de maneira correta para PDO
$conn = null;
?>
