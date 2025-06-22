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
  <style>
    .chat-bubble {
      max-width: 75%;
      padding: 0.75rem;
      border-radius: 1rem;
      margin-bottom: 0.5rem;
      position: relative;
      word-wrap: break-word;
      animation: fadeIn 0.3s ease-in-out;
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
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .message-input:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3);
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen py-10">
  <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
    <?php if ($empresaSelecionada && $empresa): ?>
      <div class="mb-4">
        <h2 class="text-xl font-bold text-blue-900"><?= htmlspecialchars($empresa['nome']) ?></h2>
        <p class="text-gray-600"><?= htmlspecialchars($empresa['email']) ?></p>
      </div>

      <div class="chat-container">
        <div id="chat" class="messages-container bg-gray-50 rounded mb-4"></div>
      </div>
      <div class="flex gap-2">
        <input id="mensagem" type="text" placeholder="Digite sua mensagem..." 
               class="flex-1 border rounded-full px-4 py-2 shadow-sm focus:outline-none message-input">
        <button id="btn-enviar" class="bg-blue-800 text-white px-6 py-2 rounded-full hover:bg-blue-900">Enviar</button>
      </div>

      <script>
        // Verifica se já existe uma conexão socket antes de criar uma nova
        if (typeof window.socketConnection === 'undefined') {
          window.socketConnection = io("http://localhost:3001");
        }
        const socket = window.socketConnection;
        
        const sala = "<?= $sala ?>";
        const remetente = "cliente_<?= $clienteId ?>";

        console.log("Conectando ao socket com ID:", socket.id);
        console.log("Sala:", sala);
        console.log("Remetente:", remetente);

        socket.emit("join_room", sala);

        // Carrega histórico do chat
        fetch(`/includes/chat_api.php?sala=${sala}`)
          .then(res => res.json())
          .then(mensagens => {
            const chat = document.getElementById("chat");
            chat.innerHTML = '';
            mensagens.forEach(data => {
              const isCliente = data.remetente.includes("cliente");
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
                    <div>
                      <p class="text-sm font-medium">${data.mensagem}</p>
                      <p class="message-time">Empresa • ${horario}</p>
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
          const horario = new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

          const chat = document.getElementById("chat");
          
          // Adiciona a mensagem enviada pelo usuário (UI otimista)
          chat.innerHTML += `
            <div class="chat-bubble sent">
              <div>
                <p class="text-sm font-medium">${msg}</p>
                <p class="message-time">Você • ${horario}</p>
              </div>
            </div>`;
          
          chat.scrollTop = chat.scrollHeight;

          // Envia a mensagem via socket.io
          socket.emit("send_message", {
            room: sala,
            message: msg,
            sender: remetente,
            timestamp: timestamp
          });

          // Salva a mensagem no banco de dados
          fetch("/includes/chat_api.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ 
              sala, 
              remetente, 
              mensagem: msg, 
              timestamp 
            })
          });

          input.value = "";
        }

        // Evento para receber mensagens
        socket.on("receive_message", (data) => {
          console.log("Mensagem recebida via socket:", data);
          console.log("Remetente local:", remetente);
          console.log("Remetente mensagem:", data.sender);
          
          // Verificação robusta para evitar mensagens duplicadas
          if (data.sender && remetente && data.sender.trim() === remetente.trim()) {
            console.log("Ignorando mensagem do próprio usuário");
            return;
          }

          const horario = new Date(data.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
          const chat = document.getElementById("chat");
          
          chat.innerHTML += `
            <div class="chat-bubble received">
              <div>
                <p class="text-sm font-medium">${data.message}</p>
                <p class="message-time">Empresa • ${horario}</p>
              </div>
            </div>`;
          
          chat.scrollTop = chat.scrollHeight;
        });

        // Event listeners
        document.getElementById("btn-enviar").addEventListener("click", enviarMensagem);
        
        document.getElementById("mensagem").addEventListener("keypress", function (e) {
          if (e.key === "Enter") {
            enviarMensagem();
          }
        });

        // Limpeza ao sair da página para evitar múltiplas conexões
        window.addEventListener('beforeunload', () => {
          if (socket) {
            socket.disconnect();
            console.log("Socket desconectado");
          }
        });
      </script>
    <?php else: ?>
      <p class="text-gray-600">Nenhuma empresa selecionada ou você não tem agendamento com essa empresa.</p>
      <a href="batepapo_empresas.php" class="text-blue-700 underline">Voltar</a>
    <?php endif; ?>
  </div>
</body>
</html>