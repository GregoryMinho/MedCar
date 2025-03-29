<?php
require '../../includes/conexao_BdCadastroLogin.php'; // inclui o arquivo de conexão com o banco de dados
session_start(); // Inicia a sessão

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cnpj = $_POST['cnpj'];
    $telefone = $_POST['telefone'];
    $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT); // Criptografa a senha

    try {
        $sql = "INSERT INTO empresas (nome, email, cnpj, telefone, senha) VALUES (:nome, :email, :cnpj, :telefone, :senha)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':cnpj', $cnpj);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':senha', $senha);
        $stmt->execute();

        // Obtém o ID da empresa recém-inserida
        $empresa_id = $conn->lastInsertId();
        
        // Define o tipo de usuário na sessão
        $_SESSION['usuario'] = [
            'id' => $empresa_id,
            'nome' => $nome,
            'email' => $email,
            'tipo' => 'empresa' // Define o tipo de usuário como empresa
        ];

        header('Location: ../../area_empresas/menu_principal.php');
        exit();
    } catch (PDOException $e) {
        echo 'Erro: ' . $e->getMessage();
        $_SESSION['erro'] = 'E-mail ou CNPJ já cadastrado.';
        header('Location: ../../paginas/cadastro_empresas.php');
        exit();
    }
}
