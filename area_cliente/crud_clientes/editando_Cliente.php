<?php
require '../../includes/conexao_BdCadastroLogin.php'; 

$id = $_POST['id'];
$nome = $_POST['nome'];
$senha = $_POST['senha'];
$telefone = $_POST['telefone'];

$sql = $pdo->prepare("UPDATE clientes SET nome = :nome, senha = :senha, telefone = :telefone WHERE id = $id");
$sql->bindValue(':nome', $nome);
$sql->bindValue(':senha', $senha);
$sql->bindValue(':telefone', $telefone);
$sql->execute();

header("Location:../area_cliente/menu_principal.php");
?>