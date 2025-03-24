<?php
require '../../includes/conexao_BdCadastroLogin.php'; // inclui o arquivo de conexão com o banco de dados
session_start(); // Inicia a sessão

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT); // Criptografa a senha

    try {
        $sql = "INSERT INTO clientes (nome, email, cpf, telefone, senha) VALUES (:nome, :email, :cpf, :telefone, :senha)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':senha', $senha);
        $stmt->execute();

        // Obtém o ID do cliente recém-inserido
        $cliente_id = $conn->lastInsertId();
        // Define o tipo de usuário na sessão
        $_SESSION['usuario'] = [
            'id' => $cliente_id,
            'nome' => $nome,
            'email' => $email,
            'tipo' => 'cliente' // Define o tipo de usuário como cliente
        ];

        header('Location: ../../area_cliente/menu_principal.php');
        exit();
    } catch (PDOException $e) {
        echo 'Erro: ' . $e->getMessage();
        $_SESSION['erro'] = 'E-mail ou CPF já cadastrado.';
        header('Location: ../../paginas/cadastro_cliente.php');
        exit();
    }
}
