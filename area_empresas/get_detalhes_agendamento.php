<?php
$host = 'localhost';
$dbname = 'agendamentos_medcar';
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

$sql = "SELECT a.*, p.*, t.nome AS transportadora, t.telefone AS tel_transportadora
        FROM agendamentos a
        JOIN pacientes p ON a.paciente_id = p.id
        JOIN transportadoras t ON a.transportadora_id = t.id
        WHERE a.id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$agendamento) die("Agendamento não encontrado");

echo '
<div class="schedule-card-details">
    <button class="btn btn-secondary mb-3" onclick="backToDaySchedule()">
        <i class="fas fa-arrow-left me-2"></i>Voltar
    </button>
    
    <div class="row">
        <div class="col-md-6">
            <h4>Detalhes do Agendamento</h4>
            <div class="mb-3">
                <span class="status-badge status-'.htmlspecialchars($agendamento['status']).'">
                    '.ucfirst(htmlspecialchars($agendamento['status'])).'
                </span>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Data/Hora:</label>
                <p>'.date('d/m/Y H:i', strtotime($agendamento['data_hora'])).'</p>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Destino:</label>
                <p>'.htmlspecialchars($agendamento['destino']).'</p>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Tipo:</label>
                <p>'.ucfirst(htmlspecialchars($agendamento['tipo'])).'</p>
            </div>
        </div>
        
        <div class="col-md-6">
            <h4>Informações do Paciente</h4>
            <div class="mb-3">
                <label class="form-label">Nome:</label>
                <p>'.htmlspecialchars($agendamento['nome']).'</p>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Data de Nascimento:</label>
                <p>'.date('d/m/Y', strtotime($agendamento['data_nascimento'])).'</p>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Telefone:</label>
                <p>'.htmlspecialchars($agendamento['telefone']).'</p>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Endereço:</label>
                <p>'.htmlspecialchars($agendamento['endereco']).'</p>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <h4>Transportadora</h4>
        <div class="mb-3">
            <label class="form-label">Nome:</label>
            <p>'.htmlspecialchars($agendamento['transportadora']).'</p>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Telefone:</label>
            <p>'.htmlspecialchars($agendamento['tel_transportadora']).'</p>
        </div>
    </div>
</div>';
?>