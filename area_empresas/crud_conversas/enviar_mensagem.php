<?php
require '../../includes/classe_usuario.php';
require '../../includes/conexao_BdConversas.php';

Usuario::verificarPermissao('empresa');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO medcar_mensagens 
            (conversa_id, remetente, mensagem)
            VALUES (?, 'empresa', ?)
        ");
        $stmt->execute([$dados['conversa_id'], $dados['mensagem']]);
        
        echo json_encode(['status' => 'sucesso']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao enviar mensagem']);
    }
}
?>