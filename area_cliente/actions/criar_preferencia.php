<?php

use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Exceptions\MPApiException;

require_once '../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeImmutable(dirname(__DIR__));
$dotenv->load();

MercadoPagoConfig::setAccessToken(getenv("MERCADO_PAGO_ACCESS_TOKEN"));

$preferenceClient = new PreferenceClient();

// Receba os dados do agendamento via POST
$request_data = json_decode(file_get_contents('php://input'), true);

if (!isset($request_data['agendamento_id']) || !isset($request_data['valor'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados do agendamento incompletos']);
    exit;
}

$agendamento_id = $request_data['agendamento_id'];
$valor = (float)$request_data['valor'];
$descricao = $request_data['descricao'] ?? "Agendamento MedCar #{$agendamento_id}";
$cliente_nome = $request_data['cliente_nome'] ?? "Cliente";
$cliente_email = $request_data['cliente_email'] ?? "cliente@example.com";

$preference_data = [
    "items" => [
        [
            "title" => $descricao,
            "quantity" => 1,
            "unit_price" => $valor,
        ],
    ],
    "payer" => [
        "name" => $cliente_nome,
        "email" => $cliente_email,
    ],
    "back_urls" => [
        "success" => "http://localhost/medcar/pagamento_sucesso.php?agendamento_id={$agendamento_id}",
        "failure" => "http://localhost/medcar/pagemento_erro.php?agendamento_id={$agendamento_id}",
        "pending" => "http://localhost/medcar/pagemento_pendente.php?agendamento_id={$agendamento_id}",
    ],
    "auto_return" => "approved",
    "external_reference" => strval($agendamento_id),
];

try {
    $preference = $preferenceClient->create($preference_data);

    // Retorna o ID da preferência
    echo json_encode([
        'success' => true,
        'preference_id' => $preference->id
    ]);

} catch (MPApiException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao criar preferência: ' . $e->getMessage()]);
}