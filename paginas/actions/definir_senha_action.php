<?php
require '../conexao_BdCadastroLogin.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idUsuario = $_POST['id_usuario'];
    $senha = $_POST['senha'];
    $confirmarSenha = $_POST['confirmar_senha'];

    if ($senha !== $confirmarSenha) {
        $_SESSION['erro'] = 'As senhas não coincidem.';
        header('Location: ../../paginas/definir_senha.php');
        exit;
    }

    try {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $query = "UPDATE clientes SET senha = :senha WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':senha', $senhaHash);
        $stmt->bindParam(':id', $idUsuario);

        if ($stmt->execute()) {
            unset($_SESSION['usuario_incompleto']);
            header('Location: ../../paginas/login_clientes.php');
            exit;
        } else {
            $_SESSION['erro'] = 'Erro ao salvar a senha.';
            header('Location: ../../paginas/definir_senha.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['erro'] = 'Erro no banco de dados: ' . $e->getMessage();
        header('Location: ../../paginas/definir_senha.php');
        exit;
    }
} else {
    header('Location: ../../paginas/login_clientes.php');
    exit;
}
