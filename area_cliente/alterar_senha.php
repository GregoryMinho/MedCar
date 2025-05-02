<?php
require '../includes/conexao_BdCadastroLogin.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $nova_senha = password_hash($_POST['nova_senha'], PASSWORD_BCRYPT);

    try {
        $sql = $conn->prepare("UPDATE clientes SET senha = :nova_senha WHERE email = :email");
        $sql->bindParam(':nova_senha', $nova_senha);
        $sql->bindParam(':email', $email);
        $sql->execute();

        $_SESSION['sucesso'] = "Senha alterada com sucesso!";
        header('Location: login_clientes.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['erro'] = "Erro ao alterar a senha.";
        header('Location: alterar_senha.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="max-w-md w-full bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-blue-900 mb-4">Alterar Senha</h2>
        <form method="POST">
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                <input type="email" id="email" name="email" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" required>
            </div>
            <div class="mb-4">
                <label for="nova_senha" class="block text-sm font-medium text-gray-700">Nova Senha</label>
                <input type="password" id="nova_senha" name="nova_senha" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" minlength="8" required>
            </div>
            <button type="submit" class="w-full py-2 px-4 bg-teal-500 text-white rounded-md hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-500">Alterar Senha</button>
        </form>
    </div>
</body>
</html>
