<?php

require 'conexao_BdCadastroLogin.php'; // Inclui a conexão com o banco de dados
// autoload do composer
require __DIR__ . '../../vendor/autoload.php';

use Google\Client as GoogleClient;

//verifica os campos obrigatórios do login com google
if (!isset($_POST['credential']) || !isset($_POST['g_csrf_token'])) {
    header('Location: ../paginas/login_clientes.php'); // redireciona para a página de login com erro
    exit;
}

$cookie = $_COOKIE['g_csrf_token'] ?? null; // pega o cookie de csrf

// verifica o valor do cookei e do post para o csrf
if ($cookie !== $_POST['g_csrf_token']) {
    header('Location: ../paginas/login_clientes.php'); // redireciona para a página de login com erro
    exit;
}

// instancia cliente google
$client = new GoogleClient(['client_id' => '162031456903-j67l39klr0m4p0js3cf4pjsl7kleqmp2.apps.googleusercontent.com']);  // Especifica o CLIENT_ID do aplicativo que acessa o backend
// obtem os dados do usuario com base no jwt
$payload = $client->verifyIdToken($_POST['credential']);

//verifica os dados do payload
if (isset($payload['email'])) {
    // Consulta o banco de dados para verificar as credenciais
    $query = "SELECT * FROM clientes WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $payload['email']);
    $stmt->execute();
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    session_start(); // Inicia a sessão se ainda não estiver iniciada
    if ($cliente) {
        // Inicia a sessão e armazena as informações do cliente
        $_SESSION['usuario'] = [
            'id' => $cliente['id'],
            'nome' => $payload['name'],
            'email' => $cliente['email'],
            'tipo' => $cliente['tipo'], // Define o tipo de usuário como cliente
            'foto' => $payload['picture'] // Adiciona a foto de perfil do usuário google
        ];
        header("Location: /MedQ-2/area_cliente/menu_principal.php");
        exit();
    } else {
        // E-mail não encontrado, redireciona para a página de login com erro
        $_SESSION['erro'] = "Conta não encontrada. Faça o cadastro.";
        // Criptografa o e-mail antes de passar pela URL
        $encryptedEmail = urlencode(base64_encode($payload['email']));
        // Redireciona para a página de cadastro de cliente com o e-mail criptografado
        header('Location: ../paginas/cadastro_cliente.php?email=' . $encryptedEmail);
    }
}
