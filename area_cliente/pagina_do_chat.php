<?php
require_once '../includes/classe_usuario.php';
require '../includes/conexao_BdAgendamento.php';
use usuario\Usuario;

Usuario::verificarPermissao('cliente');

$clienteId = $_SESSION['usuario']['id'];

// Recebe o ID da empresa via GET
if (!isset($_GET['empresa_id'])) {
    header('Location: batepapo_clientes.php');
    exit;
}
$empresaId = (int)$_GET['empresa_id'];

// Busca dados da empresa
$stmt = $conn->prepare("SELECT id, nome, email FROM medcar_cadastro_login.empresas WHERE id = :id");
$stmt->bindParam(':id', $empresaId, PDO::PARAM_INT);
$stmt->execute();
$empresa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$empresa) {
    echo "Empresa não encontrada.";
    exit;
}

$sala = "empresa_{$empresaId}_cliente_{$clienteId}";
$remetente = "cliente_{$clienteId}";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Chat com <?= htmlspecialchars($empresa['nome']) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.socket.io/4.5.0/socket.io.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    .chat-bubble {
      max-width: 75%;
      padding: 0.75rem;
      border-radius: 1rem;
      margin-bottom: 0.5rem;
      position: relative;
      word-wrap: break-word;
    }
    .chat-bubble.sent {
      background-color: #2563eb;
      color: white;
      margin-left: auto;
      border-bottom-right-radius: 0.25rem;
    }
    .chat-bubble.received {
      background-color: #f3f4f6;
      color: #1f2937;
      margin-right: auto;
      border-bottom-left-radius: 0.25rem;
    }
    .message-time {
      font-size: 0.7rem;
      opacity: 0.8;
      text-align: right;
      margin-top: 0.25rem;
    }
    .chat-bubble.received .message-time {
      color: #6b7280;
    }
    .chat-bubble.sent .message-time {
      color: rgba(255,255,255,0.8);
    }
    .chat-container {
      height: 24rem;
      display: flex;
      flex-direction: column;
    }
    .messages-container {
      flex-grow: 1;
      overflow-y: auto;
      padding: 1rem;
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen py-10">
  <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow">
    <div class="mb-4">
      <a href="batepapo_clientes.php" class="text-blue-700 hover:underline text-sm flex items-center gap-2 mb-2">
        <i class="bi bi-arrow-left"></i> Voltar para lista de empresas
      </a>
      <h2 class="text-2xl font-bold text-blue-900 mb-1">Chat com <?= htmlspecialchars($empresa['nome']) ?></h2>
      <p class="text-sm text-gray-600"><?= htmlspecialchars($empresa['email']) ?></p>
    </div>
    <div class="chat-container">
      <div id="chat" class="messages-container bg-gray-50"></div>
    </div>
    <div class="border-t border-gray-200 p-4 bg-white">
      <div class="flex gap-2">
        <input id="mensagem" type="text" placeholder="Digite sua mensagem..." class="flex-1 border rounded-full px-4 py-2 shadow-sm focus:outline-none">
        <button onclick="enviarMensagem()" class="bg-blue-800 text-white px-6 py-2 rounded-full hover:bg-blue-900 transition">
          <i class="bi bi-send"></i>
        </button>
      </div>
    </div>
  </div>

  <script>
    const socket = io("http://localhost:3001");
    const sala = "<?= $sala ?>";
    const remetente = "<?= $remetente ?>";
    const empresaNome = "<?= htmlspecialchars($empresa['nome']) ?>";

    socket.emit("join_room", sala);

    // Carrega o histórico do chat
    fetch("/includes/chat_api.php?sala=" + sala)
      .then(res => res.json())
      .then(mensagens => {
        const chat = document.getElementById("chat");
        chat.innerHTML = '';
        mensagens.forEach(data => {
          const isCliente = data.remetente === remetente;
          const horario = new Date(data.data_envio).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
          if (isCliente) {
            chat.innerHTML += `
              <div class="chat-bubble sent">
                <div>
                  <p class="text-sm font-medium">${data.mensagem}</p>
                  <p class="message-time">Você • ${horario}</p>
                </div>
              </div>`;
          } else {
            chat.innerHTML += `
              <div class="chat-bubble received">
                <div class="flex items-end gap-2">
                  <div class="bg-gray-200 border-2 border-dashed rounded-xl w-6 h-6"></div>
                  <div>
                    <p class="text-sm font-medium">${data.mensagem}</p>
                    <p class="message-time">${empresaNome} • ${horario}</p>
                  </div>
                </div>
              </div>`;
          }
        });
        chat.scrollTop = chat.scrollHeight;
      });

    function enviarMensagem() {
      const input = document.getElementById("mensagem");
      const msg = input.value.trim();
      if (msg === "") return;
      const timestamp = new Date().toISOString();

      // Mostra mensagem imediatamente
      const chat = document.getElementById("chat");
      const horario = new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      chat.innerHTML += `
        <div class="chat-bubble sent">
          <div>
            <p class="text-sm font-medium">${msg}</p>
            <p class="message-time">Você • ${horario}</p>
          </div>
        </div>`;
      chat.scrollTop = chat.scrollHeight;

      socket.emit("send_message", {
        room: sala,
        message: msg,
        sender: remetente,
        timestamp: timestamp
      });

      input.value = "";
    }

    socket.on("receive_message", (data) => {
      if (data.sender && data.sender.trim() === remetente.trim()) {
        return;
      }
      const horario = new Date(data.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      const chat = document.getElementById("chat");
      chat.innerHTML += `
        <div class="chat-bubble received">
          <div class="flex items-end gap-2">
            <div class="bg-gray-200 border-2 border-dashed rounded-xl w-6 h-6"></div>
            <div>
              <p class="text-sm font-medium">${data.message}</p>
              <p class="message-time">${empresaNome} • ${horario}</p>
            </div>
          </div>
        </div>`;
      chat.scrollTop = chat.scrollHeight;
    });

    document.getElementById("mensagem").addEventListener("keypress", function(e) {
      if (e.key === "Enter") {
        enviarMensagem();
      }
    });
  </script>
</body>
</html>
