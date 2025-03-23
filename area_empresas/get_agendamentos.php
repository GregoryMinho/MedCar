<?php
require '../includes/conexao_BdAgendamento.php'; // inclui o arquivo de conexão com o banco de dados

$data = $_GET['data'] ?? date('Y-m-d');

$sql = "SELECT a.id, c.nome 
        FROM agendamentos a
        JOIN medcar_cadastro_login.clientes c ON a.cliente_id = c.id
        WHERE a.data_consulta = :data";

$stmt = $pdo->prepare($sql);
$stmt->execute([':data' => $data]);

echo '<div class="list-group">';
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo '<a href="#" class="list-group-item list-group-item-action" 
          onclick="showAppointmentDetails('.$row['id'].')">
          '.htmlspecialchars($row['nome']).'
          </a>';
}
echo '</div>';