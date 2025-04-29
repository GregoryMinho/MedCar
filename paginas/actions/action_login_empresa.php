<?php
session_start();
require '../../includes/conexao_loginDasEmpresas.php';  // Arquivo de conexão com o banco

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['password'];

    try {
        // Consulta preparada para buscar o usuário
        $stmt = $pdo->prepare("SELECT * FROM empresas_transporte WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($empresa && password_verify($senha, $empresa['senha_hash'])) {
            // Autenticação bem-sucedida
            $_SESSION['logado'] = true;
            $_SESSION['usuario'] = [
                'id' => $empresa['id'],
                'email' => $empresa['email'],
                'nome' => $empresa['nome_empresa'],
                'tipo' => 'empresa'
            ];
            
            header('Location: ../../area_empresas/menu_principal.php');
            exit();
        } else {
            $_SESSION['login_erro'] = "Credenciais inválidas!";
            header('Location: ../../paginas/login_empresas.php');
            exit();
        }
    } catch (PDOException $e) {
        error_log("Erro de login: " . $e->getMessage());
        $_SESSION['login_erro'] = "Erro ao processar login. Tente novamente.";
        header('Location: ../../paginas/login_empresas.php');
        exit();
    }
} else {
    header('Location: ../../paginas/login_empresas.php');
    exit();
}