<?php
session_start();
require '../../includes/conexao_BdCadastroLogin.php'; // Inclui a conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['password'];

    // Consulta o banco de dados para verificar as credenciais
    $query = "SELECT * FROM empresas WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($empresa) {
        // Verifica a senha
        if (password_verify($senha, $empresa['senha'])) {
            // Inicia a sessão e armazena as informações da empresa
            $_SESSION['usuario'] = [
                'id' => $empresa['id'],
                'nome' => $empresa['nome'],
                'email' => $empresa['email'],
                'tipo' => $empresa['tipo'], // Define o tipo de usuário como empresa
                'foto' => $empresa['picture'] // Adiciona a foto de perfil da empresa tipo link 
            ];
            header("Location: /MedQ-2/area_empresas/menu_principal.php");
            exit();
        } else {
            // Senha incorreta
            $_SESSION['login_erro'] = "Senha ou E-mail incorreto.";
        }
    } else {
        // E-mail não encontrado
        $_SESSION['login_erro'] = "Senha ou E-mail incorreto.";
    }
    header("Location: /MedQ-2/paginas/login_empresas.php");
    exit();
} else {
    header("Location: /MedQ-2/paginas/login_empresas.php");
    exit();
}
