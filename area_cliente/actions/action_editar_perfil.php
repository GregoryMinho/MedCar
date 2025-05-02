<?php
require '../../includes/conexao_BdCadastroLogin.php'; 
session_start(); // Inicia a sessão se ainda não estiver iniciada


$id = $_POST['id'];
$nome = $_POST['nome'];
$telefone = $_POST['telefone'];
$rua = $_POST['rua'];
$numero = $_POST['numero'];
$complemento = $_POST['complemento'];
$bairro = $_POST['bairro'];
$cidade = $_POST['cidade'];
$estado = $_POST['estado'];
$cep = $_POST['cep'];
$data_nascimento = $_POST['data_nascimento'];
$contato_emergencia = $_POST['contato_emergencia'];

// Atualiza os campos, exceto a senha
$sql = $conn->prepare("UPDATE clientes SET nome = :nome, telefone = :telefone, data_nascimento = :data_nascimento, contato_emergencia = :contato_emergencia WHERE id = :id");
$sql->bindValue(':id', $id);
$sql->bindValue(':nome', $nome);
$sql->bindValue(':telefone', $telefone);
$sql->bindValue(':data_nascimento', $data_nascimento);
$sql->bindValue(':contato_emergencia', $contato_emergencia);
$sql->execute();

$sqlEndereco = $conn->prepare("UPDATE enderecos_clientes SET rua = :rua, numero = :numero, complemento = :complemento, bairro = :bairro, cidade = :cidade, estado = :estado, cep = :cep WHERE id_cliente = :id_cliente");
$sqlEndereco->bindValue(':rua', $rua);
$sqlEndereco->bindValue(':numero', $numero);
$sqlEndereco->bindValue(':complemento', $complemento);
$sqlEndereco->bindValue(':bairro', $bairro);
$sqlEndereco->bindValue(':cidade', $cidade);
$sqlEndereco->bindValue(':estado', $estado);
$sqlEndereco->bindValue(':cep', $cep);
$sqlEndereco->bindValue(':id_cliente', $id);
$sqlEndereco->execute();

$conn = null; // Fecha a conexão com o banco de dados

// Verifica se a atualização foi bem-sucedida
if ($sql->rowCount() > 0 && $sqlEndereco->rowCount() > 0) {
    $_SESSION['sucesso'] = "Dados atualizados com sucesso!";
} else {
    $_SESSION['erro'] = "Erro ao atualizar os dados. :(";
}

// Redireciona para a página de menu principal
header("Location:../menu_principal.php");
?>