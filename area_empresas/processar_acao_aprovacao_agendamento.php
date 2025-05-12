<?php
require '../includes/conexao_BdAgendamento.php';
require '../includes/classe_usuario.php';

use usuario\Usuario;

// Verifica se o usuário está logado e é uma empresa
Usuario::verificarPermissao('empresa');

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: aprovar_agendamentos.php');
    exit;
}

// Verifica o token CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: aprovar_agendamentos.php?erro=csrf');
    exit;
}

// Verifica se os parâmetros necessários estão presentes
if (!isset($_POST['agendamento_id']) || !isset($_POST['acao'])) {
    header('Location: aprovar_agendamentos.php?erro=parametros');
    exit;
}

$agendamento_id = $_POST['agendamento_id'];
$acao = $_POST['acao'];
$empresa_id = $_SESSION['usuario']['id'];

// Verifica se o agendamento pertence à empresa
$query = "SELECT * FROM agendamentos WHERE id = :id AND empresa_id = :empresa_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $agendamento_id, PDO::PARAM_INT);
$stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
$stmt->execute();
$agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$agendamento) {
    header('Location: aprovar_agendamentos.php?erro=agendamento');
    exit;
}

// Processa a ação
if ($acao === 'aprovar') {
    // Verifica se o valor foi informado
    if (!isset($_POST['valor']) || empty($_POST['valor'])) {
        header('Location: aprovar_agendamentos.php?erro=valor');
        exit;
    }

    $valor = floatval($_POST['valor']);
    $observacoes = $_POST['observacoes'] ?? '';

    // Atualiza o agendamento
    $query = "UPDATE agendamentos SET situacao = 'Agendado', valor = :valor, observacoes = :observacoes WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
    $stmt->bindParam(':observacoes', $observacoes, PDO::PARAM_STR);
    $stmt->bindParam(':id', $agendamento_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redireciona para a página de agendamentos
    header('Location: aprovar_agendamentos.php?sucesso=aprovado');
} elseif ($acao === 'recusar') {
    // Verifica se o motivo foi informado
    if (!isset($_POST['motivo']) || empty($_POST['motivo'])) {
        header('Location: aprovar_agendamentos.php?erro=motivo');
        exit;
    }

    $motivo = $_POST['motivo'];

    // Atualiza o agendamento
    $query = "UPDATE agendamentos SET situacao = 'Cancelado', observacoes = :observacoes, data_cancelamento = NOW() WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':observacoes', $motivo, PDO::PARAM_STR);
    $stmt->bindParam(':id', $agendamento_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redireciona para a página de agendamentos
    header('Location: aprovar_agendamentos.php?sucesso=recusado');
} else {
    // Ação inválida
    header('Location: aprovar_agendamentos.php?erro=acao');
}
