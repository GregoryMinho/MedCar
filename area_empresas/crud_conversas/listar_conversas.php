<?php
require '../../includes/classe_usuario.php';
require '../../includes/conexao_BdConversas.php';

Usuario::verificarPermissao('empresa');

try {
    $stmt = $pdo->prepare("
        SELECT 
            c.id,
            p.nome AS paciente,
            p.foto_url,
            m.mensagem AS ultima_mensagem,
            MAX(m.data_envio) AS ultima_data,
            COUNT(CASE WHEN m.lida = 0 AND m.remetente = 'paciente' THEN 1 END) AS nao_lidas
        FROM medcar_conversas c
        INNER JOIN medcar_pacientes p ON c.paciente_id = p.id
        LEFT JOIN medcar_mensagens m ON c.id = m.conversa_id
        WHERE c.empresa_id = ?
        GROUP BY c.id
        ORDER BY MAX(m.data_envio) DESC
    ");
    $stmt->execute([$_SESSION['usuario']['id']]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao buscar conversas']);
}
?>