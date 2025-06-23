<?php
require_once '../includes/classe_usuario.php';
require '../includes/conexao_BdAgendamento.php';

use usuario\Usuario;

if (!Usuario::validarSessaoEmpresa()) {
    header('Location: ../paginas/login_empresas.php');
    exit;
}

$empresaId = $_SESSION['usuario']['id'];

$stmt = $conn->prepare("
    SELECT DISTINCT 
        c.id, 
        c.nome, 
        c.email, 
        c.foto
    FROM agendamentos a
    INNER JOIN medcar_cadastro_login.clientes c 
        ON a.cliente_id = c.id
    WHERE 
        a.empresa_id = :empresa_id AND 
        a.situacao NOT IN ('Cancelado', 'Concluido')
");
$stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
if (!$stmt->execute()) {
    error_log("Erro SQL: " . implode(" - ", $stmt->errorInfo()));
}
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$clienteSelecionado = null;
$clienteInfo = null;

if (isset($_GET['cliente_id'])) {
    $clienteId = (int)$_GET['cliente_id'];
    $sala = "empresa_{$empresaId}_cliente_{$clienteId}";
    foreach ($clientes as $cliente) {
        if ($cliente['id'] == $clienteId) {
            $clienteInfo = $cliente;
            $clienteSelecionado = $clienteId;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MedCar - Chat com Cliente</title>
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
    /* Lateral padrão */
    .menu-lateral {
      background: #1a365d;
      color: white;
      min-height: 100vh;
      padding: 0;
      position: sticky;
      top: 0;
      box-shadow: 2px 0 10px rgba(0,0,0,0.05);
      z-index: 2;
    }
    .menu-lateral nav {
      padding: 24px 0;
    }
    .menu-lateral a, .menu-lateral button {
      color: #fff;
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 32px;
      border-radius: 10px;
      margin-bottom: 8px;
      background: none;
      text-decoration: none;
      font-weight: 500;
      transition: background .15s;
    }
    .menu-lateral a.active, .menu-lateral a:hover, .menu-lateral button:hover {
      background: #234372;
      color: #38b2ac;
    }
    .menu-lateral .back-btn {
      color: #38b2ac;
      border: 1px solid #38b2ac;
      margin-top: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
    }
    .menu-lateral .back-btn i {
      margin-right: 6px;
    }
  </style>
</head>
<body class="bg-gray-50 min-h-screen">
  <!-- Navbar -->
  <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow">
    <div class="container mx-auto px-4">
      <div class="flex justify-between items-center h-16">
        <a href="#" class="flex items-center space-x-2 text-xl font-bold">
          <i data-lucide="ambulance" class="h-6 w-6"></i>
          <span>MedCar</span>
        </a>
        <div class="flex items-center space-x-4">
          <span class="flex items-center gap-2"><i data-lucide="user" class="w-5 h-5"></i> Empresa</span>
        </div>
      </div>
    </div>
  </nav>

  <div class="flex pt-20">
    <!-- Menu Lateral padrão -->
    <aside class="hidden md:block w-64 menu-lateral">
      <nav class="flex flex-col space-y-2">
        <a href="dashboard.php">
          <i class="bi bi-graph-up"></i>
          Estatísticas
        </a>
        <a href="agendamentos_pacientes.php">
          <i class="bi bi-calendar-event"></i>
          Agendamentos
        </a>
        <a href="aprovar_agendamentos.php">
          <i class="bi bi-check-circle"></i>
          Aprovar Agendamentos
        </a>
        <a href="gestao_motoristas.php">
          <i class="bi bi-people"></i>
          Motoristas
        </a>
        <a href="gestao_veiculos.php">
          <i class="bi bi-truck"></i>
          Frota
        </a>
        <a href="relatorios_financeiros.php">
          <i class="bi bi-graph-up-arrow"></i>
          Financeiro
        </a>
        <a href="relatorios.php">
          <i class="bi bi-file-earmark-text"></i>
          Relatórios
        </a>
        <a href="avaliacoes.php">
          <i class="bi bi-star"></i>
          Avaliações
        </a>
        <a href="batepapo_clientes.php" class="active">
          <i class="bi bi-chat-dots"></i>
          Bate-Papo
        </a>
        <a href="dashboard.php" class="back-btn">
          <i class="bi bi-arrow-left"></i>
          Voltar
        </a>
      </nav>
    </aside>

    <main class="flex-1 px-4 py-8 md:px-10">
      <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-blue-900 mb-4 flex items-center gap-2">
          <i data-lucide="message-square-text" class="w-6 h-6 text-blue-800"></i>
          Conversas com Clientes
        </h1>

        <?php if (count($clientes) === 0): ?>
          <div class="bg-yellow-100 text-yellow-900 p-4 rounded-lg shadow mb-4">
            Nenhum paciente com agendamento ativo no momento.
          </div>
        <?php endif; ?>

        <?php foreach ($clientes as $cliente): ?>
          <div class="bg-white shadow rounded-lg p-4 mb-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
              <?php if ($cliente['foto']): ?>
                <img src="<?= htmlspecialchars($cliente['foto']) ?>" 
                     alt="Foto de <?= htmlspecialchars($cliente['nome']) ?>"
                     class="w-10 h-10 rounded-full object-cover">
              <?php else: ?>
                <div class="bg-gray-200 border-2 border-dashed rounded-full w-10 h-10"></div>
              <?php endif; ?>
              <div>
                <p class="font-semibold text-blue-900"><?= htmlspecialchars($cliente['nome']) ?></p>
                <p class="text-sm text-gray-600"><?= htmlspecialchars($cliente['email']) ?></p>
              </div>
            </div>
            <a href="?cliente_id=<?= $cliente['id'] ?>" class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded">
              Abrir Chat
            </a>
          </div>
        <?php endforeach; ?>

        <?php if ($clienteSelecionado && $clienteInfo): ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mt-6">
          <div class="bg-gradient-to-r from-blue-700 to-blue-800 p-4 text-white flex items-center gap-4">
            <?php if ($clienteInfo['foto']): ?>
              <img src="<?= htmlspecialchars($clienteInfo['foto']) ?>" 
                   alt="Foto de <?= htmlspecialchars($clienteInfo['nome']) ?>"
                   class="w-10 h-10 rounded-full object-cover">
            <?php else: ?>
              <div class="bg-gray-200 border-2 border-dashed rounded-full w-10 h-10"></div>
            <?php endif; ?>
            <div>
              <h3 class="font-semibold text-lg">Conversa com <?= htmlspecialchars($clienteInfo['nome']) ?></h3>
              <p class="text-sm text-blue-200">ID: #<?= $clienteSelecionado ?></p>
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
    </main>
  </div>

  <script>
    lucide.createIcons();
  </script>

  <?php if ($clienteSelecionado && $clienteInfo): ?>
  <script>
    const socket = io("http://localhost:3001");
    const sala = "<?= $sala ?>";
    const remetente = "empresa_<?= $empresaId ?>";
    const clienteNome = "<?= htmlspecialchars($clienteInfo['nome']) ?>";
    const empresaId = <?= $empresaId ?>;
    const clienteId = <?= $clienteSelecionado ?>;

    socket.emit("join_room", sala);

    // Carrega o histórico do chat (busca por empresa_id e cliente_id)
    fetch(`../includes/chat_api.php?empresa_id=${empresaId}&cliente_id=${clienteId}`)
      .then(res => res.json())
      .then(mensagens => {
        const chat = document.getElementById("chat");
        chat.innerHTML = '';
        mensagens.forEach(data => {
          const isEmpresa = data.remetente === remetente;
          const horario = new Date(data.data_envio).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
          if (isEmpresa) {
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
                    <p class="message-time">${clienteNome} • ${horario}</p>
                  </div>
                </div>
              </div>`;
          }
        });
        chat.scrollTop = chat.scrollHeight;
      });

    // Função para enviar mensagem
    function enviarMensagem() {
      const input = document.getElementById("mensagem");
      const msg = input.value.trim();
      if (msg === "") return;
      const timestamp = new Date().toISOString();

      // Mostra mensagem imediatamente no chat para quem enviou
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

    // Recebe mensagem em tempo real (evita duplicar a própria mensagem)
    socket.on("receive_message", (data) => {
        if (data.sender && data.sender.trim() === remetente.trim()) {
            // Ignora mensagem enviada pelo próprio usuário
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
                <p class="message-time">${clienteNome} • ${horario}</p>
              </div>
            </div>
          </div>`;
        chat.scrollTop = chat.scrollHeight;
    });

    // Permite enviar com Enter
    document.getElementById("mensagem").addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        enviarMensagem();
      }
    });
  </script>
  <?php endif; ?>
</body>
</html>
