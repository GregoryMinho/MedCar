<?php
require '../includes/conexao_BdAgendamento.php';
require '../includes/classe_usuario.php';

use usuario\Usuario;

// Verifica se o usuário está logado e é uma empresa
Usuario::verificarPermissao('empresa');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o método é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: aprovar_agendamentos.php');
    exit;
}

// Verifica token CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: aprovar_agendamentos.php?erro=csrf');
    exit;
}

if (!isset($_POST['agendamento_id']) || !isset($_POST['acao'])) {
    header('Location: aprovar_agendamentos.php?erro=parametros');
    exit;
}

$agendamento_id = $_POST['agendamento_id'];
$acao = $_POST['acao'];
$empresa_id = $_SESSION['usuario']['id'];

// Verifica se o agendamento pertence à empresa
$query = "SELECT * FROM medcar_agendamentos.agendamentos WHERE id = :id AND empresa_id = :empresa_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $agendamento_id, PDO::PARAM_INT);
$stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
$stmt->execute();
$agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$agendamento) {
    header('Location: aprovar_agendamentos.php?erro=agendamento');
    exit;
}

if ($acao === 'aprovar') {
    if (!isset($_POST['valor']) || empty($_POST['valor'])) {
        header('Location: aprovar_agendamentos.php?erro=valor');
        exit;
    }

    $valor = floatval($_POST['valor']);
    $observacoes = $_POST['observacoes'] ?? '';

    // ✅ CORRIGIDO: Atualiza na tabela correta
    $query = "UPDATE medcar_agendamentos.agendamentos 
              SET situacao = 'Agendado', valor = :valor, observacoes = :observacoes 
              WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':valor', $valor);
    $stmt->bindParam(':observacoes', $observacoes);
    $stmt->bindParam(':id', $agendamento_id);
    $stmt->execute();

    header('Location: aprovar_agendamentos.php?sucesso=aprovado');
    exit;
} elseif ($acao === 'recusar') {
    if (!isset($_POST['motivo']) || empty($_POST['motivo'])) {
        header('Location: aprovar_agendamentos.php?erro=motivo');
        exit;
    }

    $motivo = $_POST['motivo'];

    // ✅ CORRIGIDO: Atualiza na tabela correta
    $query = "UPDATE medcar_agendamentos.agendamentos 
              SET situacao = 'Cancelado', observacoes = :observacoes, data_cancelamento = NOW() 
              WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':observacoes', $motivo);
    $stmt->bindParam(':id', $agendamento_id);
    $stmt->execute();

    header('Location: aprovar_agendamentos.php?sucesso=recusado');
    exit;
} else {
    header('Location: aprovar_agendamentos.php?erro=acao');
    exit;
}
