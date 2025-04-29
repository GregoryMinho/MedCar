<?php
require '../conexao_BdCadastroLogin.php'; // Inclui a conexão com o banco de dados
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = base64_decode($_POST['email']);
    $nome = base64_decode($_POST['nome']);
    $nome = trim($nome); // Remove espaços em branco do início e do fim
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];

    if (empty($email) || empty($cpf) || empty($telefone)) {
        echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios.']);
        exit;
    }

    try {
        $query = "INSERT INTO clientes (email, nome, cpf, telefone) VALUES (:email, :nome, :cpf, :telefone)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':telefone', $telefone);

        if ($stmt->execute()) {
            // Armazena os dados do usuário na sessão

            $lastId = $conn->lastInsertId(); // Retorna o ID do último registro inserido

            // Buscar o registro completo com base no ID
            $query = "SELECT id, nome, email, cpf, telefone, tipo FROM clientes WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $lastId);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $_SESSION['usuario'] = $result; // Armazena os dados do usuário na sessão

            header('Location: ../../area_cliente/menu_principal.php'); // Redireciona para a página de menu principal
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar os dados.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método inválido.']);
}
