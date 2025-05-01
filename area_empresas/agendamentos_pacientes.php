<?php
session_start(); // <--- ADICIONAR NO TOPO

require '../includes/conexao_BdAgendamento.php';
require '../includes/classe_usuario.php';

use usuario\Usuario;

// --- VERIFICA SE O USUÁRIO ESTÁ LOGADO ---
if (empty($_SESSION['usuario']) || !isset($_SESSION['usuario']['id'])) {
    header('Location: ../paginas/login_empresas.php');
    exit();
}


$empresa_id = $_SESSION['usuario']['id'];



//Usuario::verificarPermissao('empresa'); // verifica se o usuário logado é uma empresa

 //  as consultas sql precisam inluir o id da empresa na sessão, use pdo
 /*$_SESSION['usuario'] = [
    'id' => 1,
    'tipo' => 'cliente',
    'nome' => 'Transportadora João Silva',
];*/


// Função para gerar o calendário
function gerarCalendario($mes, $ano, $agendamentos) {
    $mes = str_pad($mes, 2, '0', STR_PAD_LEFT); // Garante 2 dígitos
    $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
    $primeiro_dia = date('w', strtotime("$ano-$mes-01"));
    
    $calendario = '<div class="calendar-container">';
    
    // Cabeçalho
    $calendario .= '<div class="calendar-header-grid">';
    foreach (['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'] as $dia) {
        $calendario .= '<div class="calendar-header">'.$dia.'</div>';
    }
    $calendario .= '</div>';
    
    // Dias
    $calendario .= '<div class="calendar-days-grid">';
    
    // Dias vazios
    for ($i = 0; $i < $primeiro_dia; $i++) {
        $calendario .= '<div class="calendar-day empty"></div>';
    }
    
    // Preenche dias
    for ($dia = 1; $dia <= $dias_mes; $dia++) {
        $dataCalendario = "$ano-$mes-" . str_pad($dia, 2, '0', STR_PAD_LEFT);
        
        $eventos = array_filter($agendamentos, function($a) use ($dataCalendario) {
            // Cria um objeto DateTime com o fuso horário de São Paulo
            $dataAgendamento = new DateTime($a['data_convertida'], new DateTimeZone('America/Sao_Paulo'));
            return $dataAgendamento->format('Y-m-d') === $dataCalendario;
        });
    
        $calendario .= '<div class="calendar-day' . (count($eventos) ? ' has-event' : '') . '" 
                          onclick="showScheduleDetails(\'' . $dataCalendario . '\')">
                          <div class="day-number">' . $dia . '</div>
                          ' . (count($eventos) ? '<div class="event-dot"></div>' : '') . '
                        </div>';
    }

    
    // Completa grid
    $total_cells = $primeiro_dia + $dias_mes;
    $remaining = (7 - ($total_cells % 7)) % 7;
    for ($i = 0; $i < $remaining; $i++) {
        $calendario .= '<div class="calendar-day empty"></div>';
    }
    
    $calendario .= '</div></div>';
    return $calendario;
}
$filtros = [
    'status' => $_GET['status'] ?? 'all',
    'mes' => $_GET['mes'] ?? date('Y-m'),
    'tipo' => $_GET['tipo'] ?? 'all'
];

// Define o intervalo do mês (início e fim)
$inicio_mes = $filtros['mes'] . '-01'; // Primeiro dia do mês
$fim_mes = date('Y-m-t', strtotime($inicio_mes)); // Último dia do mês

// Buscar agendamentos
// Buscar agendamentos (ATUALIZE ESTE TRECHO)
$sql = "SELECT 
            a.*, 
            CONVERT_TZ(a.data_consulta, '+00:00', '+03:00') AS data_convertida, 
            c.nome 
        FROM medcar_agendamentos.agendamentos a
        JOIN medcar_cadastro_login.clientes c ON a.cliente_id = c.id
        WHERE a.empresa_id = :empresa_id 
        AND DATE(CONVERT_TZ(a.data_consulta, '+00:00', '+03:00')) BETWEEN :inicio_mes AND :fim_mes";

$params = [
    ':inicio_mes' => $inicio_mes,
    ':fim_mes' => $fim_mes,
    ':empresa_id' => $_SESSION['usuario']['id'] ?? null 
];

if ($filtros['status'] != 'all') {
    $sql .= " AND situacao = :status";
    $params[':status'] = $filtros['status'];
}

if ($filtros['tipo'] != 'all') {
    $sql .= " AND tipo_transporte = :tipo";
    $params[':tipo'] = $filtros['tipo'];
}

$stmt = $conn->prepare($sql);
$stmt->execute($params); // Linha 84
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Processar datas para o calendário
$dataFiltro = explode('-', $filtros['mes']);
$ano = $dataFiltro[0];
$mes = $dataFiltro[1];
$calendario = gerarCalendario($mes, $ano, $agendamentos);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Agendamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style/style_agendamentos_pacientes.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: var(--primary-color);">
        <div class="container">
            <a class="navbar-brand" href="/MedQ-2/area_empresas/menu_principal.php">
                <i class="fas fa-ambulance me-2"></i>
                MedCar Transportes
            </a>
            <div class="d-flex align-items-center">
                <div class="text-white me-3">Transportadora Saúde Total</div>
                <img src="https://source.unsplash.com/random/40x40/?icon" class="rounded-circle" alt="Perfil">
            </div>
        </div>
    </nav>

    <div class="schedule-dashboard">
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-3 p-4" style="background: var(--secondary-color); color: white; min-height: 100vh;">
                    <h5 class="mb-4"><i class="fas fa-filter me-2"></i>Filtros</h5>
                    <form method="GET">
                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="all" <?= $filtros['status'] == 'all' ? 'selected' : '' ?>>Todos</option>
                                <option value="Pendente" <?= $filtros['status'] == 'Pendente' ? 'selected' : '' ?>>Pendentes</option>
                                <option value="Agendado" <?= $filtros['status'] == 'Agendado' ? 'selected' : '' ?>>Agendados</option>
                                <option value="Concluído" <?= $filtros['status'] == 'Concluído' ? 'selected' : '' ?>>Concluídos</option>
                                <option value="Cancelado" <?= $filtros['status'] == 'Cancelado' ? 'selected' : '' ?>>Cancelados</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Período</label>
                            <input type="month" class="form-control" name="mes" value="<?= $filtros['mes'] ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Tipo de Serviço</label>
                            <select class="form-select" name="tipo">
                                <option value="all" <?= $filtros['tipo'] == 'all' ? 'selected' : '' ?>>Todos</option>
                                <option value="rotina" <?= $filtros['tipo'] == 'rotina' ? 'selected' : '' ?>>Rotina</option>
                                <option value="exame" <?= $filtros['tipo'] == 'exame' ? 'selected' : '' ?>>Exames</option>
                                <option value="emergencia" <?= $filtros['tipo'] == 'emergencia' ? 'selected' : '' ?>>Emergência</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-schedule w-100 mb-3">
                            <i class="fas fa-sync me-2"></i>Aplicar Filtros
                        </button>
                    </form>

                    <a href="novo_agendamento.php" class="btn btn-light w-100">
                        <i class="fas fa-plus me-2"></i>Novo Agendamento
                    </a>
                </div>

                <!-- Conteúdo Principal -->
                <div class="col-md-9 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3><i class="fas fa-calendar-alt me-2"></i>Agendamentos</h3>
                        <div class="d-flex gap-2">
                            <input type="month" class="form-control" value="<?= $filtros['mes'] ?>" 
                                   onchange="window.location.href = '?mes=' + this.value">
                        </div>
                    </div>

                    <!-- Calendário -->
                    <?= $calendario ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Agendamentos -->
    <div class="modal fade" id="scheduleModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agendamentos - <span id="modalSelectedDate"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalAgendamentosList">
                    <!-- Lista de pacientes será carregada aqui -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
          let currentDate = null;
    // Armazena a instância do modal globalmente
    const scheduleModal = new bootstrap.Modal(document.getElementById('scheduleModal'));
    let isShowingDetails = false; // Adiciona um flag para controlar o estado

    function showScheduleDetails(data) {
        currentDate = data;
        isShowingDetails = false; // Resetamos o flag ao carregar a lista
        
        fetch(`get_agendamentos.php?data=${data}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('modalSelectedDate').textContent = formatarData(data);
                document.getElementById('modalAgendamentosList').innerHTML = html;
                scheduleModal.show();
            });
    }

    function showAppointmentDetails(id) {
        isShowingDetails = true; // Marcamos que estamos visualizando detalhes
        
        fetch(`get_detalhes_agendamento.php?id=${id}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('modalAgendamentosList').innerHTML = `
                    <button onclick="backToList()" class="btn btn-secondary mb-3">
                        <i class="fas fa-arrow-left me-2"></i>Voltar para Lista
                    </button>
                    ${html}
                `;
            });
    }

    function backToList() {
        if(currentDate && !isShowingDetails) {
            showScheduleDetails(currentDate);
        } else {
            // Força o fechamento do modal se estiver em estado inconsistente
            scheduleModal.hide();
        }
    }

    // Adiciona evento para limpar o estado quando o modal é fechado
    document.getElementById('scheduleModal').addEventListener('hidden.bs.modal', function() {
        currentDate = null;
        isShowingDetails = false;
    });

        // Função auxiliar para formatar data
        function formatarData(dataString) {
    const [ano, mes, dia] = dataString.split('-');
    const data = new Date(ano, mes - 1, dia); // Mês é 0-based no JavaScript
    const d = String(data.getDate()).padStart(2, '0');
    const m = String(data.getMonth() + 1).padStart(2, '0');
    return `${d}/${m}/${data.getFullYear()}`;
}
    </script>
</body>
</html>