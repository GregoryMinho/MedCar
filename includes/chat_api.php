<?php
require_once 'conexao_BdChat.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['sala'], $data['remetente'], $data['mensagem'], $data['timestamp'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'Campos obrigatórios ausentes.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("
            INSERT INTO mensagens_chat (sala, remetente, mensagem, data_envio) 
            VALUES (:sala, :remetente, :mensagem, :data_envio)
        ");
        $stmt->execute([
            ':sala' => $data['sala'],
            ':remetente' => $data['remetente'],
            ':mensagem' => $data['mensagem'],
            ':data_envio' => $data['timestamp']
        ]);

        http_response_code(200);
        echo json_encode(['status' => 'ok']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao salvar mensagem', 'detalhes' => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sala = $_GET['sala'] ?? '';

    if (empty($sala)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Sala não especificada.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("SELECT * FROM mensagens_chat WHERE sala = :sala ORDER BY data_envio ASC");
        $stmt->execute([':sala' => $sala]);
        $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($mensagens);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao buscar mensagens', 'detalhes' => $e->getMessage()]);
    }
    exit;
}

// Requisição com método não permitido
http_response_code(405);
echo json_encode(['erro' => 'Método não permitido.']);
exit;
