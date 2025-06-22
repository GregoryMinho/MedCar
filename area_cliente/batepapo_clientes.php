<?php
require_once '../includes/classe_usuario.php';
require '../includes/conexao_BdAgendamento.php';
use usuario\Usuario;

Usuario::verificarPermissao('cliente');

$clienteId = $_SESSION['usuario']['id'];

$stmt = $conn->prepare("
    SELECT DISTINCT e.id, e.nome, e.email
    FROM medcar_agendamentos.agendamentos a
    INNER JOIN medcar_cadastro_login.empresas e ON a.empresa_id = e.id
    WHERE a.cliente_id = :cliente_id
");
$stmt->bindParam(':cliente_id', $clienteId, PDO::PARAM_INT);
$stmt->execute();
$empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$empresaSelecionada = null;
$empresaInfo = null;

if (isset($_GET['empresa_id'])) {
    $empresaId = (int)$_GET['empresa_id'];
    foreach ($empresas as $empresa) {
        if ($empresa['id'] == $empresaId) {
            $empresaInfo = $empresa;
            $empresaSelecionada = $empresaId;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Chat com Empresa</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.socket.io/4.5.0/socket.io.min.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
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
    <h2 class="text-2xl font-bold text-blue-900 mb-4">Empresas com agendamentos</h2>
    <?php if (count($empresas) > 0): ?>
      <ul class="space-y-4">
        <?php foreach ($empresas as $empresa): ?>
          <li class="border p-4 rounded shadow hover:bg-blue-50">
            <div class="flex justify-between items-center">
              <div>
                <p class="text-lg font-semibold"><?= htmlspecialchars($empresa['nome']) ?></p>
                <p class="text-sm text-gray-600"><?= htmlspecialchars($empresa['email']) ?></p>
              </div>
              <a href="?empresa_id=<?= $empresa['id'] ?>" class="bg-blue-800 text-white px-4 py-2 rounded hover:bg-blue-900">
                Iniciar Chat
              </a>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p class="text-gray-600">Você ainda não tem agendamentos com nenhuma empresa.</p>
    <?php endif; ?>

    <?php if ($empresaSelecionada && $empresaInfo): ?>
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden mt-6">
        <div class="bg-gradient-to-r from-blue-700 to-blue-800 p-4 text-white flex items-center gap-4">
          <div>
            <h3 class="font-semibold text-lg">Conversa com <?= htmlspecialchars($empresaInfo['nome']) ?></h3>
            <p class="text-sm text-blue-200">ID: #<?= $empresaSelecionada ?></p>
          </div>
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
    <?php endif; ?>
  </div>
  <script>
    lucide.createIcons();
  </script>
  <?php if ($empresaSelecionada && $empresaInfo): ?>
  <script>
    const socket = io("http://localhost:3001");
    const sala = "empresa_<?= $empresaSelecionada ?>_cliente_<?= $clienteId ?>";
    const remetente = "cliente_<?= $clienteId ?>";
    const empresaNome = "<?= htmlspecialchars($empresaInfo['nome']) ?>";
    const empresaId = <?= $empresaSelecionada ?>;
    const clienteId = <?= $clienteId ?>;

    socket.emit("join_room", sala);

    // Carrega o histórico do chat (busca por empresa_id e cliente_id)
    fetch(`../includes/chat_api.php?empresa_id=${empresaId}&cliente_id=${clienteId}`)
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
        timestamp: timestamp,
        empresa_id: empresaId,
        cliente_id: clienteId
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

    document.getElementById("mensagem").addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        enviarMensagem();
      }
    });
  </script>
  <?php endif; ?>
</body>
</html>
