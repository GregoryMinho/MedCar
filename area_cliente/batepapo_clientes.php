<?php
require_once '../includes/classe_usuario.php';
require '../includes/conexao_BdAgendamento.php';
use usuario\Usuario;

Usuario::verificarPermissao('cliente');

$clienteId = $_SESSION['usuario']['id'];

// Buscar todas as empresas com agendamento do cliente
$stmt = $conn->prepare("
    SELECT DISTINCT e.id, e.nome, e.email
    FROM medcar_agendamentos.agendamentos a
    INNER JOIN medcar_cadastro_login.empresas e ON a.empresa_id = e.id
    WHERE a.cliente_id = :cliente_id
");
$stmt->bindParam(':cliente_id', $clienteId, PDO::PARAM_INT);
$stmt->execute();
$empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Empresas com Agendamento</title>
  <script src="https://cdn.tailwindcss.com"></script>
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
              <a href="pagina_do_chat.php?empresa_id=<?= $empresa['id'] ?>" class="bg-blue-800 text-white px-4 py-2 rounded hover:bg-blue-900">
                Iniciar Chat
              </a>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p class="text-gray-600">Você ainda não tem agendamentos com nenhuma empresa.</p>
    <?php endif; ?>
  </div>
</body>
</html>
