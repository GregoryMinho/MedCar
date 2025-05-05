<?php
require '../../includes/classe_usuario.php';
require '../../includes/conexao_BdConversas.php';

Usuario::verificarPermissao('empresa');

if (isset($_GET['conversa_id'])) {
    try {
        // Marcar mensagens como lidas
        $stmt = $pdo->prepare("
            UPDATE medcar_mensagens 
            SET lida = 1 
            WHERE conversa_id = ? AND remetente = 'paciente'
        ");
        $stmt->execute([$_GET['conversa_id']]);

        // Buscar mensagens
        $stmt = $pdo->prepare("
            SELECT * FROM medcar_mensagens 
            WHERE conversa_id = ?
            ORDER BY data_envio ASC
        ");
        $stmt->execute([$_GET['conversa_id']]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao carregar mensagens']);
    }
}
?>