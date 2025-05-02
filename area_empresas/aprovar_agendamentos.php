// Arquivo: agendamentos_pacientes.php
<?php
require '../includes/classe_usuario.php';
use usuario\Usuario;
// Usuario::verificarPermissao('empresa'); // Verifica se o usuário tem permissão de empresa

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$empresa_id = $_SESSION['usuario']['id'];

require '../includes/conexao_BdAgendamento.php';

try {
    // Query Corrigida (Sem comentários HTML)
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
            ORDER BY a.data_consulta, a.horario"; // <-- Formatado corretamente

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
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="index.php" class="flex items-center space-x-2 text-xl font-bold">
                    <i data-lucide="ambulance" class="h-6 w-6"></i>
                    <span>MedCar</span>
                </a>
                <div class="flex items-center space-x-6">
                    <a href="agendamentos.php" class="font-medium hover:text-teal-300 transition">Agendamentos</a>
                    <a href="aprovar_agendamentos.php" class="font-medium hover:text-teal-300 transition">Aprovar Solicitações</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
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
                                <!-- Botão Aprovar -->
                                <form method="POST" action="processar_acao_aprovacao_agendamento.php">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="agendamento_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="acao" value="aprovar">
                                    <button type="submit" 
                                            onclick="return confirm('Tem certeza que deseja aprovar este agendamento?')"
                                            class="bg-teal-500 text-white px-4 py-2 rounded-lg hover:bg-teal-600 flex items-center">
                                        <i data-lucide="check-circle" class="h-5 w-5 mr-2"></i>Aprovar
                                    </button>
                                </form>

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

    <script>
        lucide.createIcons();

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