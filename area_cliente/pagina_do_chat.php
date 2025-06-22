<?php
require_once '../includes/classe_usuario.php';
require '../includes/conexao_BdAgendamento.php';
use usuario\Usuario;

Usuario::verificarPermissao('cliente');

$clienteId = $_SESSION['usuario']['id'];
$empresaSelecionada = isset($_GET['empresa_id']) ? (int)$_GET['empresa_id'] : null;
$sala = $empresaSelecionada ? "empresa_{$empresaSelecionada}_cliente_{$clienteId}" : null;

$temAgendamento = false;
$empresa = null;

// Verificar se o cliente tem agendamento com a empresa
if ($empresaSelecionada) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM agendamentos WHERE cliente_id = :cliente_id AND empresa_id = :empresa_id");
    $stmt->bindParam(':cliente_id', $clienteId, PDO::PARAM_INT);
    $stmt->bindParam(':empresa_id', $empresaSelecionada, PDO::PARAM_INT);
    $stmt->execute();

    $temAgendamento = $stmt->fetchColumn() > 0;

    // Se tem agendamento, buscar dados da empresa
    if ($temAgendamento) {
        $stmt = $conn->prepare("SELECT nome, email FROM medcar_cadastro_login.empresas WHERE id = :empresa_id");
        $stmt->bindParam(':empresa_id', $empresaSelecionada, PDO::PARAM_INT);
        $stmt->execute();
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Chat com a Empresa</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.socket.io/4.5.0/socket.io.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10">
  <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
    <?php if ($empresaSelecionada && $empresa): ?>
      <div class="mb-4">
        <h2 class="text-xl font-bold text-blue-900"><?= htmlspecialchars($empresa['nome']) ?></h2>
        <p class="text-gray-600"><?= htmlspecialchars($empresa['email']) ?></p>
      </div>

      <div id="chat" class="h-96 overflow-y-auto p-4 space-y-3 bg-gray-50 rounded mb-4"></div>
      <div class="flex gap-2">
        <input id="mensagem" type="text" placeholder="Digite sua mensagem..." class="flex-1 border rounded-full px-4 py-2 shadow-sm focus:outline-none">
        <button onclick="enviarMensagem()" class="bg-blue-800 text-white px-6 py-2 rounded-full hover:bg-blue-900">Enviar</button>
      </div>

      <script>
        const socket = io("http://localhost:3001"); // Altere para seu servidor real
        const sala = "<?= $sala ?>";
        const remetente = "cliente_<?= $clienteId ?>";

        socket.emit("join_room", sala);

        fetch(`/includes/chat_api.php?sala=${sala}`)
          .then(res => res.json())
          .then(mensagens => {
            const chat = document.getElementById("chat");
            chat.innerHTML = '';
            mensagens.forEach(data => {
              const isCliente = data.remetente.includes("cliente");
              const horario = new Date(data.data_envio).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

              chat.innerHTML += `
                <div class="chat-bubble ${isCliente ? 'sent' : 'received'}">
                  <div>
                    <p class="text-sm font-medium">${data.mensagem}</p>
                    <p class="message-time">${isCliente ? 'Você' : 'Empresa'} • ${horario}</p>
                  </div>
                </div>`;
            });
            chat.scrollTop = chat.scrollHeight;
          });

        function enviarMensagem() {
          const input = document.getElementById("mensagem");
          const msg = input.value.trim();
          if (msg === "") return;

          const timestamp = new Date().toISOString();
          const horario = new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

          const chat = document.getElementById("chat");
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

          fetch("/includes/chat_api.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ sala, remetente, mensagem: msg, timestamp })
          });

          input.value = "";
        }

        socket.on("receive_message", (data) => {
          const isCliente = data.sender.includes("cliente");
          const horario = new Date(data.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

          const chat = document.getElementById("chat");
          chat.innerHTML += `
            <div class="chat-bubble ${isCliente ? 'sent' : 'received'}">
              <div>
                <p class="text-sm font-medium">${data.message}</p>
                <p class="message-time">${isCliente ? 'Você' : 'Empresa'} • ${horario}</p>
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
      <style>
        .chat-bubble.sent { text-align: right; }
        .chat-bubble.received { text-align: left; }
        .message-time { font-size: 0.7rem; color: #888; }
      </style>
    <?php else: ?>
      <p class="text-gray-600">Nenhuma empresa selecionada ou você não tem agendamento com essa empresa.</p>
      <a href="batepapo_empresas.php" class="text-blue-700 underline">Voltar</a>
    <?php endif; ?>
  </div>
</body>
</html>
