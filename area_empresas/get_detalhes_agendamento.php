<?php
$host = 'localhost';
$dbname = 'medcar_agendamentos';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Não foi possível conectar ao banco de dados: " . $e->getMessage());
}

$id = $_GET['id'] ?? null;

if(!$id) die("Agendamento não especificado");

$sql = "SELECT a.*, u.nome AS cliente 
        FROM agendamentos a
        JOIN usuarios u ON a.cliente_id = u.id
        WHERE a.id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$agendamento) die("Agendamento não encontrado");

// Monta endereço de destino
$destino = implode(', ', array_filter([
    $agendamento['rua_destino'],
    $agendamento['numero_destino'],
    $agendamento['complemento_destino'],
    $agendamento['cidade_destino']
]));

echo '
<div class="schedule-card-details">
    <button class="btn btn-secondary mb-3" onclick="backToDaySchedule()">
        <i class="fas fa-arrow-left me-2"></i>Voltar
    </button>
    
    <div class="row">
        <div class="col-md-6">
            <h4>Detalhes do Agendamento</h4>
            <div class="mb-3">
                <span class="status-badge status-'.htmlspecialchars($agendamento['situacao']).'">
                    '.ucfirst(htmlspecialchars($agendamento['situacao'])).'
                </span>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Data/Hora:</label>
                <p>'.
                    date('d/m/Y', strtotime($agendamento['data_consulta'])).
                    ' às '.
                    htmlspecialchars($agendamento['horario']).
                '</p>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Destino:</label>
                <p>'.htmlspecialchars($destino).'</p>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Tipo de Transporte:</label>
                <p>'.ucfirst(htmlspecialchars($agendamento['tipo_transporte'])).'</p>
            </div>
        </div>
        
        <div class="col-md-6">
            <h4>Informações do Cliente</h4>
            <div class="mb-3">
                <label class="form-label">Nome:</label>
                <p>'.htmlspecialchars($agendamento['cliente']).'</p>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Condição Médica:</label>
                <p>'.htmlspecialchars($agendamento['condicao_medica'] ?? 'N/A').'</p>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Medicamentos:</label>
                <p>'.htmlspecialchars($agendamento['medicamentos'] ?? 'Nenhum').'</p>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <h4>Detalhes Adicionais</h4>
        <div class="mb-3">
            <label class="form-label">Acompanhante:</label>
            <p>'.($agendamento['acompanhante'] ? 'Sim' : 'Não').'</p>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Contato de Emergência:</label>
            <p>'.htmlspecialchars($agendamento['contato_emergencia'] ?? 'N/A').'</p>
        </div>
    </div>
</div>';
?>