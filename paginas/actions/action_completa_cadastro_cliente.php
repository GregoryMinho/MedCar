<?php
require '../../includes/conexao_BdCadastroLogin.php'; // Inclui a conexão com o banco de dados
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $foto = filter_var($_POST['foto'], FILTER_SANITIZE_URL); // Sanitiza o link da imagem
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $nome = filter_var($_POST['nome'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $cpf = filter_var($_POST['cpf'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $telefone = filter_var($_POST['telefone'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $data_nascimento = filter_var($_POST['data_nascimento'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contato_emergencia = filter_var($_POST['contato_emergencia'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $rua = filter_var($_POST['rua'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $numero = filter_var($_POST['numero'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $complemento = filter_var($_POST['complemento'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $bairro = filter_var($_POST['bairro'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $cidade = filter_var($_POST['cidade'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $estado = filter_var($_POST['estado'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $cep = filter_var($_POST['cep'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    try {
        // Insere os dados do cliente na tabela clientes
        $query = "INSERT INTO clientes (email, nome, cpf, telefone, foto, data_nascimento, contato_emergencia, status) 
                  VALUES (:email, :nome, :cpf, :telefone, :foto, :data_nascimento, :contato_emergencia, '1')";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':foto', $foto);
        $stmt->bindParam(':data_nascimento', $data_nascimento);
        $stmt->bindParam(':contato_emergencia', $contato_emergencia);

        if ($stmt->execute()) {
            $lastId = $conn->lastInsertId();

            // Insere os dados do endereço na tabela enderecos_clientes
            $queryEndereco = "INSERT INTO enderecos_clientes (id_cliente, rua, numero, complemento, bairro, cidade, estado, cep) 
                              VALUES (:id_cliente, :rua, :numero, :complemento, :bairro, :cidade, :estado, :cep)";
            $stmtEndereco = $conn->prepare($queryEndereco);
            $stmtEndereco->bindParam(':id_cliente', $lastId);
            $stmtEndereco->bindParam(':rua', $rua);
            $stmtEndereco->bindParam(':numero', $numero);
            $stmtEndereco->bindParam(':complemento', $complemento);
            $stmtEndereco->bindParam(':bairro', $bairro);
            $stmtEndereco->bindParam(':cidade', $cidade);
            $stmtEndereco->bindParam(':estado', $estado);
            $stmtEndereco->bindParam(':cep', $cep);

            if ($stmtEndereco->execute()) {
                // Recupera os dados do cliente para a sessão
                $query = "SELECT id, nome, email, telefone, tipo FROM clientes WHERE id = :id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':id', $lastId);
                $stmt->execute();

                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['usuario'] = $result;

                header('Location: ../../area_cliente/menu_principal.php');
                exit;
            } else {
                $_SESSION['erro'] = 'Erro ao salvar o endereço.';
                header('Location: ../../paginas/completa_cadastro_cliente.php');
                exit;
            }
        } else {
            $_SESSION['erro'] = 'Erro ao salvar os dados do cliente.';
            header('Location: ../../paginas/completa_cadastro_cliente.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['erro'] = 'Erro no banco de dados: ' . $e->getMessage();
        header('Location: ../../paginas/completa_cadastro_cliente.php');
        exit;
    }
} else {
    $_SESSION['erro'] = 'Método inválido.';
    header('Location: ../../paginas/completa_cadastro_cliente.php');
    exit;
}
