<?php
require '../../includes/conexao_BdCadastroLogin.php';
require '../../includes/classe_usuario.php';
use usuario\Usuario;

// Verifica se o usuário logado é uma empresa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

Usuario::verificarPermissao('empresa');


$idEmpresa = (int)$_SESSION['usuario']['id'];

// Sanitiza os inputs recebidos
$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_SPECIAL_CHARS);
$endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_SPECIAL_CHARS);
$cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_SPECIAL_CHARS);
$cep = filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_SPECIAL_CHARS);

try {
    // Atualiza os dados da empresa no banco de dados
    $stmt = $conn->prepare("UPDATE empresas SET 
        nome = :nome, 
        telefone = :telefone, 
        endereco = :endereco, 
        cidade = :cidade, 
        cep = :cep 
        WHERE id = :id");
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':cidade', $cidade);
    $stmt->bindParam(':cep', $cep);
    $stmt->bindParam(':id', $idEmpresa, PDO::PARAM_INT);
    $stmt->execute();

    // Verifica se houve alterações
    if ($stmt->rowCount() > 0) {
        $_SESSION['sucesso'] = "Dados atualizados com sucesso!";
    } else {
        $_SESSION['erro'] = "Nenhuma alteração foi realizada.";
    }

    // Redireciona para a página de perfil da empresa
    header('Location: ../perfil_empresa.php');
    exit();
} catch (PDOException $e) {
    $_SESSION['erro'] = "Erro ao atualizar os dados: " . $e->getMessage();
    header('Location: ../perfil_empresa.php');
    exit();
}
