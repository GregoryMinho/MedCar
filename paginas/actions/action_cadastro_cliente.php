<?php
require '../../includes/conexao_BdCadastroLogin.php'; // inclui o arquivo de conexão com o banco de dados
session_start(); // Inicia a sessão

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT); // Criptografa a senha
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
        $sql = "INSERT INTO clientes (nome, email, cpf, telefone, senha, data_nascimento, contato_emergencia) VALUES (:nome, :email, :cpf, :telefone, :senha, :data_nascimento, :contato_emergencia)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':data_nascimento', $data_nascimento);
        $stmt->bindParam(':contato_emergencia', $contato_emergencia);
        $stmt->execute();

        // Obtém o ID do cliente recém-inserido
        $cliente_id = $conn->lastInsertId();

        $sqlEndereco = "INSERT INTO enderecos_clientes (id_cliente, rua, numero, complemento, bairro, cidade, estado, cep) VALUES (:id_cliente, :rua, :numero, :complemento, :bairro, :cidade, :estado, :cep)";
        $stmtEndereco = $conn->prepare($sqlEndereco);
        $stmtEndereco->bindParam(':id_cliente', $cliente_id);
        $stmtEndereco->bindParam(':rua', $rua);
        $stmtEndereco->bindParam(':numero', $numero);
        $stmtEndereco->bindParam(':complemento', $complemento);
        $stmtEndereco->bindParam(':bairro', $bairro);
        $stmtEndereco->bindParam(':cidade', $cidade);
        $stmtEndereco->bindParam(':estado', $estado);
        $stmtEndereco->bindParam(':cep', $cep);
        $stmtEndereco->execute();

        // Define o tipo de usuário na sessão
        $_SESSION['usuario'] = [
            'id' => $cliente_id,
            'nome' => $nome,
            'email' => $email,
            'telefone' => $telefone,
            'tipo' => 'cliente' // Define o tipo de usuário como cliente
        ];
        $conn = null; // Fecha a conexão com o banco de dados
        // Redireciona para a página principal do cliente
        
        header('Location: ../../area_cliente/menu_principal.php');
        exit();
    } catch (PDOException $e) {
        echo 'Erro: ' . $e->getMessage();
        $_SESSION['erro'] = 'E-mail ou CPF já cadastrado.';
        header('Location: ../../paginas/cadastro_cliente.php');
        exit();
    }
}
