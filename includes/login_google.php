<?php
require_once '../vendor/autoload.php';
require_once 'conexao_BdCadastroLogin.php';


session_start();

$client = new Google_Client();
$client->setAuthConfig('../includes/client_secret_162031456903-j67l39klr0m4p0js3cf4pjsl7kleqmp2.apps.googleusercontent.com.json');
$client->setRedirectUri('https://localhost/MedQ-2/includes/login_google.php');
$client->addScope('email');
$client->addScope('profile');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);
        $oauth2 = new         composer require google/apiclient($client);
        $userInfo = $oauth2->userinfo->get();

        $email = $userInfo->email;

        // Consulta no banco usando PDO
        $stmt = $conn->prepare("SELECT * FROM clientes WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = null;

        if ($resultado) {
            $_SESSION['usuario'] = $resultado;
            $_SESSION['usuario']['foto'] = $userInfo->picture;

            // Redireciona para o menu adequado conforme o tipo no session
            if ($_SESSION['tipo_login_google'] == 'cliente') {
                header('Location: ../area_cliente/menu_principal.php');
                exit;
            } elseif ($_SESSION['tipo_login_google'] == 'empresa') {
                header('Location: ../area_empresas/menu_principal.php');
                exit;
            }
        } else {
            // Caso o email não exista no banco, redirecionar para uma página de erro ou cadastro
            header('Location: ../area_cliente/cadastro.php');
            exit;
        }
    } else {
        echo 'Erro ao autenticar com o Google.';
    }
} else {
    $loginUrl = $client->createAuthUrl();
    header('Location: ' . $loginUrl);
    exit;
}
?>
