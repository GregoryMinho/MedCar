<?php
require '../../includes/conexao_BdCadastroLogin.php'; 
session_start(); // Inicia a sessão se ainda não estiver iniciada


$id = $_POST['id'];
$nome = $_POST['nome'];
$senha = $_POST['senha'];
$telefone = $_POST['telefone'];

if (!empty($senha)) {
    // Atualiza todos os campos, incluindo a senha
   $senha = password_hash($senha, PASSWORD_DEFAULT); // Criptografa a senha
    $sql = $conn->prepare("UPDATE clientes SET nome = :nome, senha = :senha, telefone = :telefone WHERE id = :id");
    $sql->bindValue(':senha', $senha);
} else {
    // Atualiza apenas os campos nome e telefone
    $sql = $conn->prepare("UPDATE clientes SET nome = :nome, telefone = :telefone WHERE id = :id");
}

$sql->bindValue(':id', $id);
$sql->bindValue(':nome', $nome);
$sql->bindValue(':telefone', $telefone);
$sql->execute();
$conn = null; // Fecha a conexão com o banco de dados

// Verifica se a atualização foi bem-sucedida
if ($sql->rowCount() > 0) {
    $_SESSION['sucesso'] = "Dados atualizados com sucesso!";
} else {
    $_SESSION['erro'] = "Erro ao atualizar os dados.";
}

// Redireciona para a página de menu principal
header("Location:../menu_principal.php");
?>