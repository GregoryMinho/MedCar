<?php
require_once 'conexao_BdChat.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $empresa_id = isset($_GET['empresa_id']) ? (int)$_GET['empresa_id'] : 0;
    $cliente_id = isset($_GET['cliente_id']) ? (int)$_GET['cliente_id'] : 0;

    if (!$empresa_id || !$cliente_id) {
        http_response_code(400);
        echo json_encode(['erro' => 'IDs obrigatórios ausentes.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM mensagens_chat WHERE empresa_id = :empresa AND cliente_id = :cliente ORDER BY data_envio ASC");
    $stmt->execute([':empresa' => $empresa_id, ':cliente' => $cliente_id]);
    $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($mensagens);
    exit;
}
