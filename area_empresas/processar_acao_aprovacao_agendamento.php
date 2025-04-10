<?php 
require '../includes/classe_usuario.php'; // inclui o arquivo de validação de login
use usuario\Usuario; // usa o namespace usuario\Usuario

// Usuario::verificarPermissao('empresa'); // verifica se o usuário logado é uma empresa

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém os dados enviados pelo formulário
    $agendamento_id = $_POST['agendamento_id'] ?? null;
    $acao = $_POST['acao'] ?? null;

    // Valida os dados mínimos
    if (!$agendamento_id || !$acao) {
        die("Dados insuficientes para processar a ação.");
    }

    // Conexão com o banco de dados
    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "medcar_agendamentos";

    $conn = new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }

    // Define a query e os parâmetros conforme a ação
    if ($acao === 'aprovar') {
        // Atualiza para 'Aprovado'
        $sql = "UPDATE agendamentos SET situacao = 'Agendado' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $agendamento_id);
    } elseif ($acao === 'recusar') {
        // Atualiza para 'Negado' e salva o motivo no campo 'observacoes'
        $motivo = $_POST['motivo'] ?? '';
        $sql = "UPDATE agendamentos SET situacao = 'Cancelado', observacoes = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $motivo, $agendamento_id);
    } else {
        die("Ação inválida.");
    }

    // Executa a atualização e redireciona ou exibe mensagem de erro
    if ($stmt->execute()) {
        header("Location: aprovar_agendamentos.php");
        exit;
    } else {
        echo "Erro ao atualizar o agendamento: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: aprovar_agendamentos.php");
    exit;
}
?>
