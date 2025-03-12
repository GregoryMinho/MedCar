<?php
require '../../includes/valida_login.php'; // Inclui o arquivo de validação de login
verificarPermissao('CLIENTE'); // Verifica se o usuário logado é um cliente

require '../../includes/conexao_BdAgendamento.php'; // Inclui o arquivo de conexão com o banco de dados

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtém os dados do formulário
    $cliente_id = $_SESSION['usuario']['id'];
    $empresa_id = $_POST['empresa_id'];
    $data_consulta = $_POST['data_consulta'];
    $horario = $_POST['horario'];
    $destino = $_POST['destino'];
    $observacoes = $_POST['observacoes'];

    // Prepara a consulta SQL para inserir os dados na tabela agendamentos_registros
    $sql = "INSERT INTO agendamentos_registros (cliente_id, empresa_id, data_consulta, horario, destino, observacoes, status) VALUES (?, ?, ?, ?, ?, ?, 'Agendado')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissss", $cliente_id, $empresa_id, $data_consulta, $horario, $destino, $observacoes);

    // Executa a consulta e verifica se foi bem-sucedida
    if ($stmt->execute()) {
        // Redireciona para uma página de sucesso ou exibe uma mensagem de sucesso
        header("Location: ../agendamento_sucesso.php");
        exit();
    } else {
        // Exibe uma mensagem de erro
        echo "Erro ao agendar transporte: " . $stmt->error;
    }

    // Fecha a declaração e a conexão
    $stmt->close();
    $conn->close();
} else {
    // Redireciona para a página de agendamento se o formulário não foi enviado
    header("Location: ../agendamento_cliente.php");
    exit();
}
?>