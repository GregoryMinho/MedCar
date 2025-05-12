<?php
session_start();
require '../../includes/conexao_BdCadastroLogin.php'; // Inclui a conexão com o banco de dados
require '../../includes/email/comunicacao/classe_email.php'; // Inclui a classe de envio de e-mail

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['password'];

    // Consulta o banco de dados para verificar as credenciais
    $query = "SELECT id, nome, email, senha, tipo, status FROM clientes WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
       
        if ($cliente['status'] !== '1') { // Verifica se o status está ativo
            
            // Atualiza o token e a data de expiração para o cliente
            $stmt = $conn->prepare("UPDATE clientes SET token = :token, token_expiracao = :token_expiracao WHERE email = :email");
            $token = bin2hex(random_bytes(16)); // gera um token aleatório
            $token_expiracao = date('Y-m-d H:i:s', strtotime('+24 hours')); // Define o token para expirar em 24 horas
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':token_expiracao', $token_expiracao);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $conn = null; // Fecha a conexão com o banco de dados
            // Envia um novo e-mail de confirmação
            $emailSender = new EmailSender();
            $emailSender->setFrom('medcartransportemedico@gmail.com', 'MedCar Transporte Médico');
            $emailSender->addRecipient($cliente['email'], $cliente['nome']);
            $emailSender->setSubject('Confirmação de Cadastro - MedCar');
            $confirmationLink = "http://localhost/MedQ-2/paginas/actions/action_confirmar_cadastro_cliente.php?token=" . $token . "&d=" . $cliente['id'];
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
            $emailSender->send();

            $_SESSION['erro'] = "Sua conta ainda não foi ativada. Um novo e-mail de confirmação foi enviado.";
            header("Location: /MedQ-2/paginas/login_clientes.php");
            exit();
        }

        if (empty($cliente['senha'])) {
            // Redireciona para definir senha se a senha estiver em branco
            $_SESSION['usuario_incompleto'] = $cliente['id'];
            header("Location: ../definir_senha.php");
            exit();
        }

        // Verifica a senha
        if (password_verify($senha, $cliente['senha'])) {
            // Inicia a sessão e armazena as informações do cliente
            $_SESSION['usuario'] = [
                'id' => $cliente['id'],
                'nome' => $cliente['nome'],
                'email' => $cliente['email'],
                'tipo' => $cliente['tipo'] // Define o tipo de usuário como cliente
            ];
            header("Location: /MedQ-2/area_cliente/menu_principal.php");
            exit();
        } else {
            // Senha incorreta
            $_SESSION['login_erro'] = "Senha ou E-mail incorreto.";
        }
    } else {
        // E-mail não encontrado
        $_SESSION['login_erro'] = "Senha ou E-mail incorreto.";
    }
    header("Location: /MedQ-2/paginas/login_clientes.php");
    exit();
} else {
    header("Location: /MedQ-2/paginas/login_clientes.php");
    exit();
}
