<?php
session_start();
require '../../includes/conexao_BdCadastroLogin.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['password'];

    try {
        $query = "SELECT * FROM empresas WHERE email = :email";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($empresa) {
            // Versão segura com hash (recomendado)
             if (password_verify($senha, $empresa['senha'])) {
            
            // Versão insegura com texto plano (apenas para teste)
            // if ($senha === $empresa['senha']) { 
                $_SESSION['usuario'] = [
                    'id' => $empresa['id'], // ID correto da empresa
                    'nome' => $empresa['nome'],
                    'email' => $empresa['email'],
                    'tipo' => $empresa['tipo'],
                    'foto' => $empresa['picture']
                ];
                header("Location: /MedQ-2/area_empresas/menu_principal.php");
                exit();
            } else {
                // Senha incorreta
            $_SESSION['login_erro'] = "Senha ou E-mail incorreto.";
            }
        } else {
             // Senha ou email incorreta
             $_SESSION['login_erro'] = "Senha ou E-mail incorreto.";
        }
    } catch (PDOException $e) {
       error_log("Erro de login: " . $e->getMessage());
    }
    
    header("Location: /MedQ-2/paginas/login_empresas.php");
    exit();
} else {
    error_log("Método de requisição inválido.");
    header("Location: /MedQ-2/paginas/login_empresas.php");
    exit();
}
?>