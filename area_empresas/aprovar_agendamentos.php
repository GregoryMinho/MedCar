<?php 
require '../includes/valida_login.php';

// Conexão com o banco de dados de agendamentos
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "medcar_agendamentos";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Consulta com JOIN para trazer o nome do cliente a partir do banco de dados medcar_cadastro_login
$sql = "SELECT a.*, c.nome AS cliente_nome 
        FROM agendamentos AS a 
        INNER JOIN medcar_cadastro_login.clientes AS c 
            ON c.id = a.cliente_id 
        WHERE a.situacao = 'Pendente' 
        ORDER BY a.data_consulta, a.horario";
$result = $conn->query($sql);
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
            
            <!-- Filtros (exemplo estático; implemente a lógica se necessário) -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex flex-wrap gap-4">
                    <input type="text" placeholder="Buscar por nome..." class="px-4 py-2 border rounded-lg flex-grow">
                    <select class="px-4 py-2 border rounded-lg">
                        <option>Todos os status</option>
                        <option>Pendentes</option>
                        <option>Aprovados</option>
                        <option>Recusados</option>
                    </select>
                    <input type="date" class="px-4 py-2 border rounded-lg">
                </div>
            </div>

            <!-- Lista de Agendamentos -->
            <div class="space-y-4">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): 
                        // Formata a data para dd/mm/aaaa e o horário para HH:MM
                        $dataConsulta = date("d/m/Y", strtotime($row['data_consulta']));
                        $horario = substr($row['horario'], 0, 5);
                    ?>
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                                <!-- Informações do Agendamento -->
                                <div class="mb-4 md:mb-0">
                                    <h3 class="text-lg font-bold text-blue-900">
                                        Cliente: <?php echo htmlspecialchars($row['cliente_nome']); ?>
                                    </h3>
                                    <p class="text-gray-600"><?php echo $dataConsulta . " - " . $horario; ?></p>
                                    <p class="text-sm text-gray-500">Transporte: <?php echo htmlspecialchars($row['tipo_transporte']); ?></p>
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-sm">
                                        <?php echo htmlspecialchars($row['situacao']); ?>
                                    </span>
                                </div>

                                <!-- Ações -->
                                <div class="flex gap-3">
                                    <!-- Botão Aprovar -->
                                    <form method="POST" action="processar_acao_aprovacao_agendamento.php">
                                        <input type="hidden" name="agendamento_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="acao" value="aprovar">
                                        <button type="submit" class="bg-teal-500 text-white px-4 py-2 rounded-lg hover:bg-teal-600 flex items-center">
                                            <i data-lucide="check-circle" class="h-5 w-5 mr-2"></i>Aprovar
                                        </button>
                                    </form>

                                    <!-- Botão Recusar -->
                                    <button onclick="openRejectForm(<?php echo $row['id']; ?>)" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 flex items-center">
                                        <i data-lucide="x-circle" class="h-5 w-5 mr-2"></i>Recusar
                                    </button>
                                </div>
                            </div>

                            <!-- Detalhes Expandíveis -->
                            <div class="mt-4 border-t pt-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="font-semibold text-blue-900">Endereço de Origem</h4>
                                        <p>
                                            <?php 
                                                echo htmlspecialchars($row['rua_origem']) . ", " . htmlspecialchars($row['numero_origem']);
                                                if (!empty($row['complemento_origem'])) {
                                                    echo " - " . htmlspecialchars($row['complemento_origem']);
                                                }
                                                echo "<br>" . htmlspecialchars($row['cidade_origem']) . " - CEP: " . htmlspecialchars($row['cep_origem']);
                                            ?>
                                        </p>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-blue-900">Endereço de Destino</h4>
                                        <p>
                                            <?php 
                                                echo htmlspecialchars($row['rua_destino']) . ", " . htmlspecialchars($row['numero_destino']);
                                                if (!empty($row['complemento_destino'])) {
                                                    echo " - " . htmlspecialchars($row['complemento_destino']);
                                                }
                                                echo "<br>" . htmlspecialchars($row['cidade_destino']) . " - CEP: " . htmlspecialchars($row['cep_destino']);
                                            ?>
                                        </p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <h4 class="font-semibold text-blue-900">Observações Médicas</h4>
                                        <p class="text-gray-600">
                                            <?php 
                                                echo (!empty($row['condicao_medica'])) ? htmlspecialchars($row['condicao_medica']) : "Sem observações";
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Formulário de Rejeição (Oculto por padrão) -->
                            <div id="rejectForm-<?php echo $row['id']; ?>" class="hidden mt-4">
                                <form method="POST" action="processar_acao_aprovacao_agendamento.php">
                                    <input type="hidden" name="agendamento_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="acao" value="recusar">
                                    
                                    <div class="mb-3">
                                        <label class="block text-gray-700 font-medium mb-2">Motivo da Recusa</label>
                                        <textarea name="motivo" rows="3" class="w-full px-4 py-2 border rounded-lg" required></textarea>
                                    </div>
                                    
                                    <div class="flex justify-end gap-3">
                                        <button type="button" onclick="closeRejectForm(<?php echo $row['id']; ?>)" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                                            Cancelar
                                        </button>
                                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                                            Confirmar Recusa
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-gray-600">Nenhum agendamento pendente encontrado.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
   
    <script>
        // Inicializa os ícones do Lucide
        lucide.createIcons();

        // Funções para exibir/ocultar o formulário de recusa
        function openRejectForm(id) {
            document.getElementById('rejectForm-' + id).classList.remove('hidden');
        }
        function closeRejectForm(id) {
            document.getElementById('rejectForm-' + id).classList.add('hidden');
        }
    </script>
</body>
</html>

<?php 
$conn->close();
?>
