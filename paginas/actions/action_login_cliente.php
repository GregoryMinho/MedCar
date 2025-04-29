<?php
session_start();
require '../../includes/conexao_loginDasEmpresas.php'; // Inclui a conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['password'];

    // Consulta o banco de dados para verificar as credenciais
    $query = "SELECT * FROM clientes WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        // Verifica a senha
        if (password_verify($senha, $cliente['senha'])) {
            // Inicia a sessão e armazena as informações do cliente
            $_SESSION['usuario'] = [
                'id' => $cliente['id'],
                'nome' => $cliente['nome'],
                'email' => $cliente['email'],
                'tipo' => $cliente['tipo'] // Define o tipo de usuário como cliente
            ];
            header("Location: /MedQ-2/area_cliente/menu_principal.php");
            exit();
        } else {
            // Senha incorreta
            $_SESSION['login_erro'] = "Senha ou E-mail incorreto.";
        }
    } else {
        // E-mail não encontrado
        $_SESSION['login_erro'] = "Senha ou E-mail incorreto.";
    }
    header("Location: /MedQ-2/paginas/login_clientes.php");
    exit();
} else {
    header("Location: /MedQ-2/paginas/login_clientes.php");
    exit();
}
?>
