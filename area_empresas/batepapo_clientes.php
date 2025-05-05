<?php
session_start();
require '../includes/classe_usuario.php';
use usuario\Usuario;

// Verify user permission and authentication
Usuario::verificarPermissao('empresa');


if (!isset($_SESSION['empresa_id'])) {
   header('Location: login_empresa.php');
    exit();
}

require '../includes/conexao_BdConversas.php';

// Initialize variables
$mensagens = [];
$conversaAtual = null;
$conversas = [];

// Fetch all conversations for the current company
try {
    $stmt = $pdo->prepare("
        SELECT 
            c.id,
            c.assunto,
            c.data_abertura,
            p.nome AS paciente,
            p.foto_url,
            (SELECT mensagem FROM mensagens WHERE conversa_id = c.id ORDER BY data_envio DESC LIMIT 1) AS ultima_mensagem,
            (SELECT data_envio FROM mensagens WHERE conversa_id = c.id ORDER BY data_envio DESC LIMIT 1) AS ultima_data,
            (SELECT COUNT(*) FROM mensagens WHERE conversa_id = c.id AND remetente = 'paciente' AND lida = 0) AS nao_lidas
        FROM conversas c
        INNER JOIN pacientes p ON c.paciente_id = p.id
        WHERE c.empresa_id = ?
        ORDER BY ultima_data DESC
    ");
    
    $stmt->execute([$_SESSION['empresa_id']]);
    $conversas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Error fetching conversations: " . $e->getMessage());
    $conversas = []; // Ensure empty array on error
}

// Handle specific conversation if requested
if (isset($_GET['conversa'])) {
    try {
        $conversa_id = filter_input(INPUT_GET, 'conversa', FILTER_VALIDATE_INT);
        
        if (!$conversa_id) {
            throw new Exception("ID de conversa inválido");
        }

        // Verify conversation belongs to this company
        $stmt = $pdo->prepare("
            SELECT c.*, p.nome AS paciente, p.foto_url 
            FROM conversas c
            INNER JOIN pacientes p ON c.paciente_id = p.id
            WHERE c.id = ? AND c.empresa_id = ?
        ");
        $stmt->execute([$conversa_id, $_SESSION['empresa_id']]);
        $conversaAtual = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$conversaAtual) {
            throw new Exception("Conversa não encontrada ou acesso não autorizado");
        }

        // Fetch messages
        $stmt = $pdo->prepare("
            SELECT * 
            FROM mensagens 
            WHERE conversa_id = ?
            ORDER BY data_envio ASC
        ");
        $stmt->execute([$conversa_id]);
        $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mark patient messages as read
        $stmt = $pdo->prepare("
            UPDATE mensagens 
            SET lida = 1 
            WHERE conversa_id = ? AND remetente = 'paciente' AND lida = 0
        ");
        $stmt->execute([$conversa_id]);

    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        // Show error to user but continue loading page
        $error_message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Central de Mensagens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2a4f7e;
            --accent-color: #38b2ac;
            --chat-bg: #f0f4f8;
        }
        .chat-container {
            height: calc(100vh - 120px);
            background: var(--chat-bg);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .conversation-list {
            border-right: 2px solid #e9ecef;
            overflow-y: auto;
        }
        .chat-messages {
            overflow-y: auto;
            background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAAOklEQVRoge3BMQEAAADCoPVPbQlPoAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABeA8XKAAFZcBBuAAAAAElFTkSuQmCC');
        }
        .message-bubble {
            max-width: 70%;
            border-radius: 15px;
            padding: 12px 15px;
            margin: 8px 0;
            position: relative;
        }
        .received {
            background: white;
            align-self: flex-start;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .sent {
            background: var(--accent-color);
            color: white;
            align-self: flex-end;
        }
        .unread-badge {
            background: #dc3545;
            color: white;
            font-size: 0.8em;
            min-width: 20px;
            height: 20px;
            border-radius: 10px;
        }
        .conversation-item:hover {
            background: #f8f9fa;
            cursor: pointer;
        }
        .active-conversation {
            background: var(--chat-bg);
            border-left: 4px solid var(--accent-color);
        }
        .message-input {
            border-radius: 25px;
            padding: 12px 20px;
            border: 2px solid #e9ecef;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: var(--primary-color)">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-comments me-2"></i>
                MedCar - Central de Mensagens
            </a>
            <div class="ms-auto">
                <a href="logout.php" class="btn btn-outline-light">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <!-- Error Alert -->
    <?php if (!empty($error_message)): ?>
    <div class="container mt-3">
        <div class="alert alert-danger">
            <?= htmlspecialchars($error_message) ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="container-fluid py-4">
        <div class="row chat-container mx-2">
            <!-- Conversation List -->
            <div class="col-md-4 conversation-list p-0">
                <div class="p-3 border-bottom">
                    <input type="text" class="form-control" placeholder="Pesquisar conversas...">
                </div>
                <?php foreach ($conversas as $conversa): ?>
                <a href="?conversa=<?= $conversa['id'] ?>" class="text-decoration-none">
                    <div class="conversation-item p-3 border-bottom <?= ($_GET['conversa'] ?? null) == $conversa['id'] ? 'active-conversation' : '' ?>">
                        <div class="d-flex align-items-center">
                            <img src="<?= htmlspecialchars($conversa['foto_url']) ?>" class="rounded-circle me-3" width="50" height="50" onerror="this.src='default_profile.png'">
                            <div class="flex-grow-1">
                                <h6 class="mb-0 text-dark"><?= htmlspecialchars($conversa['paciente']) ?></h6>
                                <small class="text-muted"><?= htmlspecialchars(substr($conversa['ultima_mensagem'] ?? '', 0, 30)) ?>...</small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block"><?= date('H:i', strtotime($conversa['ultima_data'])) ?></small>
                                <?php if (($conversa['nao_lidas'] ?? 0) > 0): ?>
                                <span class="unread-badge d-inline-flex align-items-center justify-content-center">
                                    <?= $conversa['nao_lidas'] ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- Chat Area -->
            <div class="col-md-8 p-0 d-flex flex-column">
                <?php if (isset($_GET['conversa']) && $conversaAtual): ?>
                <!-- Chat Header -->
                <div class="p-3 border-bottom bg-white">
                    <div class="d-flex align-items-center">
                        <img src="<?= htmlspecialchars($conversaAtual['foto_url']) ?>" class="rounded-circle me-3" width="45" height="45" onerror="this.src='default_profile.png'">
                        <div>
                            <h5 class="mb-0"><?= htmlspecialchars($conversaAtual['paciente']) ?></h5>
                            <small class="text-muted">Assunto: <?= htmlspecialchars($conversaAtual['assunto']) ?></small>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="flex-grow-1 chat-messages p-3" id="messages-container">
                    <?php foreach ($mensagens as $msg): ?>
                    <div class="d-flex <?= $msg['remetente'] === 'empresa' ? 'justify-content-end' : 'justify-content-start' ?>">
                        <div class="message-bubble <?= $msg['remetente'] === 'empresa' ? 'sent' : 'received' ?>">
                            <?= nl2br(htmlspecialchars($msg['mensagem'])) ?>
                            <small class="d-block text-end mt-1" style="opacity: 0.7;">
                                <?= date('H:i', strtotime($msg['data_envio'])) ?>
                                <?php if ($msg['remetente'] === 'empresa'): ?>
                                <i class="fas fa-check ms-2 <?= $msg['lida'] ? 'text-primary' : 'text-muted' ?>"></i>
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Message Input -->
                <form method="POST" action="enviar_mensagem.php" class="p-3 border-top bg-white">
                    <input type="hidden" name="conversa_id" value="<?= htmlspecialchars($_GET['conversa']) ?>">
                    <div class="input-group">
                        <input type="text" name="mensagem" class="form-control message-input" 
                               placeholder="Digite sua mensagem..." required>
                        <button type="submit" class="btn btn-primary ms-2">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
                <?php else: ?>
                <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                    <div class="text-center text-muted">
                        <i class="fas fa-comments fa-3x mb-3"></i>
                        <h4>Selecione uma conversa para começar</h4>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-scroll to bottom
        const messagesContainer = document.getElementById('messages-container');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Auto-refresh every 30 seconds
        setInterval(() => {
            if (window.location.search.includes('conversa')) {
                window.location.reload();
            }
        }, 30000);

        // Error handling for profile images
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('img').forEach(img => {
                img.onerror = function() {
                    this.src = 'default_profile.png';
                };
            });
        });
    </script>
</body>
</html>