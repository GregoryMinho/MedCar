<?php
require '../../includes/conexao_BdCadastroLogin.php';
session_start(); // Inicia a sessão se ainda não estiver iniciada

$id = $_SESSION['usuario']['id']; // Obtém o ID do usuário logado
$nome = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$telefone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$rua = filter_input(INPUT_POST, 'street', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$numero = filter_input(INPUT_POST, 'number', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$complemento = filter_input(INPUT_POST, 'complement', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$bairro = filter_input(INPUT_POST, 'neighborhood', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$cidade = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$estado = filter_input(INPUT_POST, 'state', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$cep = filter_input(INPUT_POST, 'zipcode', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$data_nascimento = filter_input(INPUT_POST, 'birth_date', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$contato_emergencia = filter_input(INPUT_POST, 'emergency_contact', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Atualiza os campos, exceto a senha
try {
    $sql = $conn->prepare("UPDATE clientes SET nome = :nome, cpf = :cpf, telefone = :telefone, data_nascimento = :data_nascimento, contato_emergencia = :contato_emergencia WHERE id = :id");
    $sql->bindValue(':id', $id);
    $sql->bindValue(':nome', $nome);
    $sql->bindValue(':cpf', $cpf);
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


    // Verifica se a atualização foi bem-sucedida
    if ($sqlEndereco->rowCount() > 0 || $sql->rowCount() > 0) {
        $_SESSION['sucesso'] = "Dados atualizados com sucesso!";
    } else {
        $_SESSION['erro'] = "Nenhuma alteração foi realizada.";
    }
    $conn = null; // Fecha a conexão com o banco de dados
    // Redireciona para a página de perfil do cliente
    header("Location:../perfil_cliente.php");
    exit;
} catch (PDOException $e) {
    $_SESSION['erro'] = "Erro ao atualizar os dados. :( <br> Detalhes: " . $e->getMessage();
    header("Location:../perfil_cliente.php");
    exit;
}
