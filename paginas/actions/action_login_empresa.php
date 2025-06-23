<?php
session_start();
require_once '../../includes/conexao_BdCadastroLogin.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['password'];

    try {
        $query = "SELECT * FROM empresas WHERE email = :email";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
         $conn = null; // Fecha a conexão com o banco de dados
         
        if ($empresa) {
             if (password_verify($senha, $empresa['senha'])) {           
           
                $_SESSION['usuario'] = [
                    'id' => $empresa['id'], 
                    'nome' => $empresa['nome'],
                    'email' => $empresa['email'],
                    'tipo' => $empresa['tipo'],
                    'foto' => $empresa['picture']
                ];
                header("Location: /MedCar/area_empresas/menu_principal.php");
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
    
    header("Location: /MedCar/paginas/login_empresas.php");
    exit();
} else {
    error_log("Método de requisição inválido.");
    header("Location: /MedCar/paginas/login_empresas.php");
    exit();
}
?>