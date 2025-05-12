<?php
require_once '../vendor/autoload.php';
require_once '../includes/conexao_BdAgendamento.php';

// Carregar variáveis de ambiente
try {
    $dotenv = Dotenv\Dotenv::createUnsafeImmutable(dirname(__FILE__));
    $dotenv->load();
} catch (Exception $e) {
    // Continuar mesmo se o .env não for encontrado
}

// Configurar o Mercado Pago
$access_token = getenv('MERCADO_PAGO_ACCESS_TOKEN');

// Verificar se o token de acesso está disponível
if (!$access_token) {
    http_response_code(500);
    echo json_encode(['error' => 'Token de acesso do Mercado Pago não configurado']);
    exit;
}

// Configurar o SDK do Mercado Pago
MercadoPago\SDK::setAccessToken($access_token);

// Função para registrar logs
function logWebhook($message) {
    $log_file = __DIR__ . '/webhook_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

// Receber a notificação
$input_data = file_get_contents('php://input');
$notification = json_decode($input_data, true);

// Registrar a notificação recebida
logWebhook("Notificação recebida: " . $input_data);

// Verificar se é uma notificação válida
if (!isset($notification['action']) || !isset($notification['data'])) {
    logWebhook("Notificação inválida");
    http_response_code(400);
    echo json_encode(['error' => 'Notificação inválida']);
    exit;
}

// Processar apenas notificações de pagamento
if ($notification['action'] === 'payment.created' || $notification['action'] === 'payment.updated') {
    $payment_id = $notification['data']['id'];
    
    logWebhook("Processando pagamento ID: $payment_id");
    
    try {
        // Buscar informações do pagamento
        $payment = MercadoPago\Payment::find_by_id($payment_id);
        
        // Verificar se o pagamento foi encontrado
        if (!$payment) {
            logWebhook("Pagamento não encontrado: $payment_id");
            http_response_code(404);
            echo json_encode(['error' => 'Pagamento não encontrado']);
            exit;
        }
        
        // Registrar detalhes do pagamento
        logWebhook("Status do pagamento: " . $payment->status);
        logWebhook("Referência externa: " . $payment->external_reference);
        
        // Processar apenas pagamentos aprovados
        if ($payment->status === 'approved') {
            $agendamento_id = $payment->external_reference;
            
            // Atualizar o status do agendamento
            $query = "UPDATE agendamentos SET 
                      situacao = 'Pago', 
                      data_pagamento = NOW(), 
                      mercadopago_payment_id = :payment_id 
                      WHERE id = :agendamento_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':payment_id', $payment_id);
            $stmt->bindParam(':agendamento_id', $agendamento_id);
            $result = $stmt->execute();
            
            if ($result) {
                logWebhook("Agendamento $agendamento_id atualizado com sucesso para status Pago");
            } else {
                logWebhook("Erro ao atualizar agendamento $agendamento_id: " . print_r($stmt->errorInfo(), true));
            }
        } else {
            logWebhook("Pagamento não aprovado. Status: " . $payment->status);
        }
        
        // Responder com sucesso
        http_response_code(200);
        echo json_encode(['success' => true]);
        
    } catch (Exception $e) {
        logWebhook("Erro ao processar pagamento: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao processar pagamento: ' . $e->getMessage()]);
    }
} else {
    logWebhook("Ação não suportada: " . $notification['action']);
    http_response_code(200); // Aceitar, mas não processar
    echo json_encode(['message' => 'Ação não suportada']);
}
