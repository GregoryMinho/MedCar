<?php
session_start();
require '../includes/conexao_BdAgendamento.php';

$data = $_GET['data'] ?? date('Y-m-d');
$empresa_id = $_SESSION['usuario']['id'] ?? die(json_encode(['error' => 'Sessão inválida']));

$sql = "SELECT 
            a.id, 
            a.cliente_id, 
            c.nome,
            DATE_FORMAT(CONCAT(a.data_consulta, ' ', a.horario), '%Y-%m-%d %H:%i') AS data_completa
        FROM medcar_agendamentos.agendamentos a
        JOIN medcar_cadastro_login.clientes c ON a.cliente_id = c.id
        WHERE DATE(CONVERT_TZ(CONCAT(a.data_consulta, ' ', a.horario), '+00:00', '+03:00')) = ?
        AND a.empresa_id = ?";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute([$data, $empresa_id]);
    
    echo '<div class="list-group">';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<div class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex align-items-center gap-2">
                    <span style="cursor:pointer; color:#0d6efd;" 
                          onclick="showAppointmentDetails('.$row['id'].')">
                        '.htmlspecialchars($row['nome']).'
                    </span>
                    <a href="mapa.php?agendamento_id='.$row['id'].'" 
                       class="btn btn-link p-0" 
                       title="Ver Rota">
                        <i class="fas fa-map-marker-alt text-primary"></i>
                    </a>
                </div>
                <small class="text-muted">'.date('H:i', strtotime($row['data_completa'])).'</small>
              </div>';
    }
    echo '</div>';

} catch (PDOException $e) {
    error_log("Erro get_agendamentos: ".$e->getMessage());
    echo '<div class="alert alert-danger">Erro ao carregar agenda: '.$e->getMessage().'</div>';
}