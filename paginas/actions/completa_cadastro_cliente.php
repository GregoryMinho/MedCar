<?php
require '../../includes/conexao_BdCadastroLogin.php'; // Inclui a conexão com o banco de dados
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $foto = base64_decode($_POST['foto']); // decodifica link da imagem base64
    $email = base64_decode($_POST['email']);
    $nome = base64_decode($_POST['nome']);
    $nome = trim($nome); // Remove espaços em branco do início e do fim
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $data_nascimento = $_POST['data_nascimento'];
    $contato_emergencia = $_POST['contato_emergencia'];
    $rua = $_POST['rua'];
    $numero = $_POST['numero'];
    $complemento = $_POST['complemento'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $cep = $_POST['cep'];

    try {

        $query = "INSERT INTO clientes (email, nome, cpf, telefone, foto, data_nascimento, contato_emergencia) 
                  VALUES (:email, :nome, :cpf, :telefone, :data_nascimento, :contato_emergencia)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':foto', $foto); // Assuming you have a variable $foto for the photo
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':data_nascimento', $data_nascimento);
        $stmt->bindParam(':contato_emergencia', $contato_emergencia);

        if ($stmt->execute()) {
            $lastId = $conn->lastInsertId();

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
                $query = "SELECT id, nome, email, telefone, tipo FROM clientes WHERE id = :id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':id', $lastId);
                $stmt->execute();

                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['usuario'] = $result;

                header('Location: ../../area_cliente/menu_principal.php');
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao salvar o endereço.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar os dados.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método inválido.']);
}
