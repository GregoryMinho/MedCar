<?php
require '../../includes/conexao_BdCadastroLogin.php'; // inclui o arquivo de conexão com o banco de dados
session_start(); // Inicia a sessão

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $senha = password_hash(filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_FULL_SPECIAL_CHARS), PASSWORD_BCRYPT); // Criptografa a senha
    $data_nascimento = filter_input(INPUT_POST, 'data_nascimento', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contato_emergencia = filter_input(INPUT_POST, 'contato_emergencia', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $rua = filter_input(INPUT_POST, 'rua', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $numero = filter_input(INPUT_POST, 'numero', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $complemento = filter_input(INPUT_POST, 'complemento', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $bairro = filter_input(INPUT_POST, 'bairro', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $cep = filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    try {
        // Verifica se o e-mail já está cadastrado  
        $sql = "SELECT * FROM clientes WHERE email = :email OR cpf = :cpf";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $_SESSION['erro'] = 'E-mail ou CPF já cadastrado.';
            header('Location: ../../paginas/cadastro_cliente.php');
            exit();
        }
        // inserir os dados do cliente na tabela clientes
        $token_expiracao = date('Y-m-d H:i:s', strtotime('+24 hours')); // Define o token para expirar em 24 horas
        $sql = "INSERT INTO clientes (nome, email, cpf, telefone, senha, data_nascimento, contato_emergencia, token, token_expiracao) VALUES (:nome, :email, :cpf, :telefone, :senha, :data_nascimento, :contato_emergencia, :token, :token_expiracao)";
        $stmt = $conn->prepare($sql);
        $token = bin2hex(random_bytes(16)); // Generate a random token
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':data_nascimento', $data_nascimento);
        $stmt->bindParam(':contato_emergencia', $contato_emergencia);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':token_expiracao', $token_expiracao);
        $stmt->execute();

        // Obtém o ID do cliente recém-inserido
        $cliente_id = $conn->lastInsertId();

        // insere os dados do endereço na tabela enderecos_clientes
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

        // envia o e-mail de confirmação
        require_once '../../includes/email/comunicacao/classe_email.php';
        $emailSender = new EmailSender();
        $emailSender->setFrom('medcartransportemedico@gmail.com', 'MedCar Transporte Médico');
        $emailSender->addRecipient($email, $nome);
        $emailSender->setSubject('Confirmação de Cadastro - MedCar');
        $confirmationLink = "http://localhost/MedQ-2/paginas/actions/action_confirmar_cadastro_cliente.php?token=" . $token . "&d=" . $cliente_id;
        $emailBody = '
<html>
    <body style="font-family: Arial, sans-serif; background: linear-gradient(to right, #1e3a8a, #1e40af); color: white; text-align: center; padding: 20px;">
        <div style="max-width: 600px; margin: 0 auto; background: white; color: #1e3a8a; border-radius: 10px; overflow: hidden;">
            <div style="background: #14b8a6; padding: 20px; text-align: center;">
                <h1 style="margin: 0; font-size: 24px; color: white;">Bem-vindo(a) ao MedCar, ' . $nome . '!</h1>
                <p style="margin: 10px 0 0; font-size: 18px; color: white;">Confirme seu cadastro para começar a usar nossos serviços</p>
            </div>
            <div style="padding: 20px; text-align: left;">
                <p style="font-size: 16px; line-height: 1.5; color: #333;">
                    Obrigado por se cadastrar na MedCar! Por favor, confirme seu cadastro clicando no botão abaixo:
                </p>
                <div style="text-align: center; margin: 20px 0;">
                    <a href="' . $confirmationLink . '" style="display: inline-block; padding: 10px 20px; background-color: #14b8a6; color: white; text-decoration: none; border-radius: 5px; font-size: 16px;">
                        Confirmar Cadastro
                    </a>
                </div>
                <p style="font-size: 14px; color: #555; text-align: center;">
                    Se você não se cadastrou, ignore este e-mail.
                </p>
            </div>
            <div style="background: #f3f4f6; padding: 10px; font-size: 12px; color: #555; text-align: center;">
                <p style="margin: 0;">MedCar - Transporte Médico Não Emergencial</p>
                <p style="margin: 0;">© 2023 MedCar. Todos os direitos reservados.</p>
            </div>
        </div>
    </body>
</html>
';
        $emailSender->setBody($emailBody, true);
        //envia o e-mail de confirmação
        if ($emailSender->send()) {

            $_SESSION['sucesso'] = 'Cadastro realizado com sucesso! Verifique seu e-mail para confirmar o cadastro. Procure na caixa de spam.';
            header('Location: ../login_clientes.php');
            exit();
        } else {
            // Se o envio falhar, exclui o cliente do banco de dados
            $smt = " DELETE FROM clientes WHERE id = :id_cliente";
            $stmt = $conn->prepare($smt);
            $stmt->bindParam(':id_cliente', $cliente_id);
            $stmt->execute();
            $_SESSION['erro'] = $emailSender->send();
            header('Location: ../../paginas/cadastro_cliente.php');
            exit();
        }
    } catch (PDOException $e) {
        echo 'Erro: ' . $e->getMessage();
        $_SESSION['erro'] = 'E-mail ou CPF já cadastrado.';
        header('Location: ../../paginas/cadastro_cliente.php');
        exit();
    }
}
