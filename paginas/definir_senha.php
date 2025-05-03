<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_incompleto'])) {
    header('Location: login_clientes.php');
    exit;
}

$idUsuario = $_SESSION['usuario_incompleto'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Definir Senha</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-r from-blue-900 to-blue-800 flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-bold mb-4 text-center">Definir Senha</h2>
        <form action="../includes/actions/definir_senha_action.php" method="POST" class="space-y-4">
            <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($idUsuario, ENT_QUOTES, 'UTF-8'); ?>">
            <div>
                <label for="senha" class="block text-sm font-medium text-gray-700">Nova Senha</label>
                <input type="password" id="senha" name="senha" maxlength="20" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="••••••••" minlength="8" required>
            </div>
            <div>
                <label for="confirmar_senha" class="block text-sm font-medium text-gray-700">Confirmar Nova Senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" maxlength="20" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="••••••••" minlength="8" required>
            </div>
            <button type="submit" class="w-full py-2 px-4 bg-teal-500 text-white rounded-md hover:bg-teal-600">Salvar Senha</button>
        </form>
    </div>
</body>
</html>
