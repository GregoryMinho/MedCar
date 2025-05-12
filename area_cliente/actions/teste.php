<?php
// Arquivo de teste para verificar se a API está funcionando
// Incluir o Composer
require '../../vendor/autoload.php';

// Chamar o método para carregar as variáveis de ambiente
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(dirname(__DIR__));
$dotenv->load();

header('Content-Type: application/json');

echo json_encode([
    'status' => 'success',
    'message' => 'API funcionando corretamente',
    'timestamp' => date('Y-m-d H:i:s'),
    'preferenceId' =>  getenv('MERCADO_PAGO_PUBLIC_KEY') // ID de preferência de teste
]);

