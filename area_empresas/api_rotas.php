<?php
// api_rotas.php

// Configura os cabeçalhos para permitir CORS e retornar JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permite requisições de qualquer origem (ajuste em produção)

/**
 * Obtém a rota do serviço OSRM
 */
function getRoute($start, $end) {
    // Constrói a URL para a API do OSRM (serviço público)
    $osrm_url = "http://router.project-osrm.org/route/v1/driving/$start;$end?overview=full";
    
    // Configura as opções da requisição
    $options = [
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: MapRouteAPI/1.0\r\n",
            'timeout' => 10 // Timeout de 10 segundos
        ]
    ];
    
    $context = stream_context_create($options);
    
    // Faz a requisição com supressão de erros (@) e tratamento manual
    $response = @file_get_contents($osrm_url, false, $context);
    
    return $response ? json_decode($response, true) : null;
}

/**
 * Valida o formato das coordenadas
 */
function validateCoordinates($coord) {
    return preg_match('/^-?\d{1,3}\.\d+,-?\d{1,3}\.\d+$/', $coord);
}

/**
 * Função principal que processa a requisição
 */
try {
    // Verifica o método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Método não permitido', 405);
    }
    
    // Verifica se os parâmetros foram fornecidos
    if (!isset($_GET['start']) || !isset($_GET['end'])) {
        throw new Exception('Parâmetros "start" e "end" são obrigatórios', 400);
    }
    
    // Obtém e sanitiza as coordenadas
    $start = trim($_GET['start']);
    $end = trim($_GET['end']);
    
    // Valida o formato das coordenadas
    if (!validateCoordinates($start) || !validateCoordinates($end)) {
        throw new Exception('Formato de coordenadas inválido. Use "latitude,longitude" (ex: -23.5505,-46.6333)', 400);
    }
    
    // Obtém a rota do serviço OSRM
    $route = getRoute($start, $end);
    
    // Verifica se a rota foi calculada com sucesso
    if (!$route || !isset($route['code']) || $route['code'] !== 'Ok') {
        throw new Exception('Não foi possível calcular a rota. Serviço pode estar indisponível.', 500);
    }
    
    // Prepara a resposta de sucesso
    $response = [
        'status' => 'success',
        'distance' => $route['routes'][0]['distance'], // em metros
        'duration' => $route['routes'][0]['duration'], // em segundos
        'geometry' => $route['routes'][0]['geometry'], // polilinha codificada
        'start_point' => $route['waypoints'][0]['location'], // [lat, lng]
        'end_point' => $route['waypoints'][1]['location']     // [lat, lng]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    // Tratamento de erros
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'status' => 'error',
        'error' => $e->getMessage(),
        'details' => (isset($route) && $route) ? $route : null
    ]);
}
?>