<?php
require '../../includes/conexao_BdCadastroLogin.php';
require '../../vendor/autoload.php';

use Google\Client as GoogleClient;

session_start();

// Defina o fuso horário (substitua pelo seu fuso horário correto)
date_default_timezone_set('America/Sao_Paulo');

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
    $client->setAccessToken($token['access_token']);

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
                    <form action="completa_cadastro_cliente.php" method="POST" class="space-y-4">
                        <input type="hidden" name="email" value="' . base64_encode($email) . '">
                        <input type="hidden" name="nome" value="' . base64_encode($nome) . '">
                        <input type="hidden" name="foto" value="' . base64_encode($foto) . '">
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
        header('Location: ../login_clientes.php'); // redireciona para a página de login com erro
        exit;
    }
} else {
    // Se não houver código, redireciona para a página de login
    header('Location: ../login_clientes.php'); // redireciona para a página de login com erro
    exit;
}
