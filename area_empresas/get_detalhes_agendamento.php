<?php
require '../includes/conexao_BdAgendamento.php'; // inclui o arquivo de conexão com o banco de dados

$id = $_GET['id'] ?? 0;

$sql = "SELECT 
            a.*, 
            c.nome AS cliente_nome,  -- Nome claro para evitar conflitos
            a.rua_destino,
            a.horario,
            a.tipo_transporte,
            a.situacao
        FROM agendamentos a
        JOIN medcar_cadastro_login.clientes c ON a.cliente_id = c.id  -- Correção do JOIN
        WHERE a.id = :id";

$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id]);
$dados = $stmt->fetch(PDO::FETCH_ASSOC);

if ($dados) {
    echo '<div class="schedule-card-details">';
    echo '<h4 class="mb-4">'.htmlspecialchars($dados['cliente_nome']).'</h4>'; 
    
    echo '<div class="row">';
    echo '<div class="col-md-6">';
    echo '<p><strong>Horário:</strong> '.date('H:i', strtotime($dados['horario'])).'</p>';
    echo '</div>';
    
    echo '<div class="col-md-6">';
    echo '<p><strong>Endereço:</strong> '.htmlspecialchars($dados['rua_destino']).'</p>';
    echo '<p><strong>Tipo de Transporte:</strong> '.htmlspecialchars($dados['tipo_transporte']).'</p>';
    echo '<p><strong>Status:</strong> <span class="badge '.getStatusClass($dados['situacao']).'">'.htmlspecialchars($dados['situacao']).'</span></p>';
    echo '</div>';
    echo '</div>';
    
    echo '</div>';
} else {
    echo '<div class="alert alert-danger">Agendamento não encontrado</div>';
}

function getStatusClass($status) {
    switch($status) {
        case 'Agendado': return 'bg-primary';
        case 'Concluído': return 'bg-success';
        case 'Cancelado': return 'bg-danger';
        default: return 'bg-secondary';
    }
}
?>