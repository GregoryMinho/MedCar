<?php
require '../../includes/conexao_BdCadastroLogin.php';
require '../../vendor/autoload.php';

use Google\Client as GoogleClient;

session_start();

// Defina o fuso horário (substitua pelo seu fuso horário correto)
date_default_timezone_set('America/Sao_Paulo'); // Ajuste para o fuso horário correto

// Carrega o arquivo client_secret.json
$clientSecretPath = '../../client_secret.json';
$clientSecretData = json_decode(file_get_contents($clientSecretPath), true);

// Configurações do Google OAuth 2.0 usando os dados do client_secret.json
$client = new GoogleClient([
    'client_id' => $clientSecretData['web']['client_id'],
    'client_secret' => $clientSecretData['web']['client_secret'],
    'redirect_uri' => $clientSecretData['web']['redirect_uris'][0],
    'access_type' => 'offline',
    'scope' => ['email', 'profile'],
    'prompt' => 'consent'
]);

if (isset($_GET['code'])) {
    // Troca o código de autorização pelo token de acesso
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if ($token['access_token']) {
        $client->setAccessToken($token['access_token']);
    } else {
        // Token inválido ou expirado, redireciona para a página de login
        header('Location: ../login_clientes.php'); // redireciona para a página de login com erro
        exit;
    }

    // Obtém as informações do usuário
    $payload = $client->verifyIdToken($token['id_token']);

    if (isset($payload['email'])) {
        $email = $payload['email'];
        $nome = $payload['name'];
        $foto = $payload['picture'];

        // Consulta o banco de dados para verificar se o usuário existe
        $stmt = $conn->prepare("SELECT id, nome, email, cpf, telefone, foto, tipo FROM clientes WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Atualiza a foto do usuário
            $updateQuery = "UPDATE clientes SET foto = :foto WHERE email = :email";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bindParam(':foto', $foto);
            $updateStmt->bindParam(':email', $email);
            $updateStmt->execute();

            $result['foto'] = $foto; // Atualiza a foto no array de resultado
            // Usuário existe, faz o login
            $_SESSION['usuario'] = $result;

            header('Location: ../../area_cliente/menu_principal.php');
            exit;
        } else {
            // Usuário não existe, mostra o formulário para completar o cadastro
            header('Location: ../../paginas/completa_cadastro_cliente.php?email=' . urlencode($email) . '&nome=' . urlencode($nome) . '&foto=' . urlencode($foto));
            exit;
        }
    } else {
        // Token inválido ou expirado, redireciona para a página de login
        header('Location: ../login_clientes.php'); // redireciona para a página de login com erro
        exit;
    }
} else {
    // Se não houver código, redireciona para a página de login
    header('Location: ../login_clientes.php'); // redireciona para a página de login com erro
    exit;
}
