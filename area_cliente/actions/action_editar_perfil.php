<?php
require '../../includes/conexao_BdCadastroLogin.php'; 
session_start(); // Inicia a sessão se ainda não estiver iniciada

$id = $_SESSION['usuario']['id']; // Obtém o ID do usuário logado
$nome = $_POST['name'];
$telefone = $_POST['phone'];
$rua = $_POST['street'];
$numero = $_POST['number'];
$complemento = $_POST['complement'];
$bairro = $_POST['neighborhood'];
$cidade = $_POST['city'];
$estado = $_POST['state'];
$cep = $_POST['zipcode'];
$data_nascimento = $_POST['birth_date'];
$contato_emergencia = $_POST['emergency_contact'];

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
if ($sql->rowCount() > 0 || $sqlEndereco->rowCount() > 0) {
    $_SESSION['sucesso'] = "Dados atualizados com sucesso!";
} else {
    $_SESSION['erro'] = "Erro ao atualizar os dados. :(";
}

// Redireciona para a página de perfil do cliente
header("Location:../perfil_cliente.php");
?>