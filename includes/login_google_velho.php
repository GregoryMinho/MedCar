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
session_start();
if (isset($payload['email'])) {

    // Consulta o banco de dados para verificar as credenciais    
    $stmt = $conn->prepare("SELECT id, nome, email, cpf, telefone, foto, tipo FROM clientes WHERE email = :email");
    $stmt->bindParam(':email', $payload['email']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // E-mail encontrado, armazena os dados na sessão

        $_SESSION['usuario'] = $result;

        // Atualiza o campo 'foto' no banco de dados com a foto do Google
        $updateQuery = "UPDATE clientes SET foto = :foto WHERE email = :email";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':foto', $payload['picture']);
        $updateStmt->bindParam(':email', $payload['email']);

        if (!$updateStmt->execute()) {
            // Atualização bem-sucedida
            echo "Erro ao atualizar a foto do usuário no banco de dados.";
            exit;
        }

        $conn = null; // Fecha a conexão com o banco de dados
        // Redireciona para a página de cadastro de cliente
        header('Location: ../area_cliente/menu_principal.php');
        exit;
    } else {

        // Exibe um modal para o usuário cadastrar os dados restantes
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Complete seu Cadastro</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        </head>
        <body class="min-h-screen bg-gradient-to-r from-blue-900 to-blue-800 flex items-center justify-center">
            <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">Complete seu Cadastro</h2>
                <p class="text-center text-gray-600 mb-6">Por favor, insira os dados restantes para continuar.</p>
                <form action="./actions/completa_cadastro_cliente.php" method="POST" class="space-y-4">
                    <input type="hidden" name="email" value="' . base64_encode($payload['email']) . '">
                    <input type="hidden" name="nome" value="' . base64_encode($payload['name']) . '">
                    <div>
                        <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
                        <input type="text" id="cpf" name="cpf" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="000.000.000-00" required>
                    </div>
                    <div>
                        <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                        <input type="text" id="telefone" name="telefone" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="(00) 00000-0000" required>
                    </div>
                    <div>
                        <label for="data_nascimento" class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" required>
                    </div>
                    <div>
                        <label for="contato_emergencia" class="block text-sm font-medium text-gray-700">Contato de Emergência</label>
                        <input type="text" id="contato_emergencia" name="contato_emergencia" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="(00) 00000-0000" required>
                    </div>
                    <div>
                        <label for="rua" class="block text-sm font-medium text-gray-700">Rua/Avenida</label>
                        <input type="text" id="rua" name="rua" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: Av. Paulista" required>
                    </div>
                    <div>
                        <label for="numero" class="block text-sm font-medium text-gray-700">Número</label>
                        <input type="text" id="numero" name="numero" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: 123" required>
                    </div>
                    <div>
                        <label for="complemento" class="block text-sm font-medium text-gray-700">Complemento</label>
                        <input type="text" id="complemento" name="complemento" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: Apto 101">
                    </div>
                    <div>
                        <label for="bairro" class="block text-sm font-medium text-gray-700">Bairro</label>
                        <input type="text" id="bairro" name="bairro" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: Bela Vista" required>
                    </div>
                    <div>
                        <label for="cidade" class="block text-sm font-medium text-gray-700">Cidade</label>
                        <input type="text" id="cidade" name="cidade" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: São Paulo" required>
                    </div>
                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                        <input type="text" id="estado" name="estado" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: SP" required>
                    </div>
                    <div>
                        <label for="cep" class="block text-sm font-medium text-gray-700">CEP</label>
                        <input type="text" id="cep" name="cep" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: 01310-100" required>
                    </div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-500 hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                        Continuar
                    </button>
                </form>
            </div>
            <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>
            <script src="../jquery.mask.min.js"></script>            
            <script>' . "
                    // Restrição de data de nascimento
                    const dataNascimentoInput = document.getElementById('data_nascimento');
                    const hoje = new Date();
                    const maxDate = new Date(hoje.getFullYear() - 120, hoje.getMonth(), hoje.getDate()); // ano máximo 120 anos atrás
                    // Definindo a data mínima como a data de hoje
                    const minDate = new Date(hoje.getFullYear(), hoje.getMonth(), hoje.getDate());
                    dataNascimentoInput.max = minDate.toISOString().split('T')[0];
                    dataNascimentoInput.min = maxDate.toISOString().split('T')[0];
                    $('#cpf').mask('000.000.000-00');
                    $('#telefone').mask('(00) 00000-0000');
                    $('#contato_emergencia').mask('(00) 00000-0000');
                    $('#cep').mask('00000-000');" . '
            </script>
        </body>
        </html>';
    }
} else {
    // Token inválido ou expirado, redireciona para a página de login
    header('Location: ../paginas/login_clientes.php'); // redireciona para a página de login com erro
    exit;
}
