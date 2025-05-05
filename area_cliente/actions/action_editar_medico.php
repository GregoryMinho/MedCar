<?php
require '../../includes/conexao_BdCadastroLogin.php'; // Inclui a conexão com o banco de dados
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idCliente = $_SESSION['usuario']['id'];
    $alergias = filter_input(INPUT_POST, 'allergies', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $doencasCronicas = filter_input(INPUT_POST, 'chronic_conditions', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $remediosRecorrentes = filter_input(INPUT_POST, 'medications', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    try {
        // Verifica se o cliente já possui informações médicas
        $queryCheck = "SELECT id FROM detalhe_medico WHERE id_cliente = :id_cliente";
        $stmtCheck = $conn->prepare($queryCheck);
        $stmtCheck->bindParam(':id_cliente', $idCliente, PDO::PARAM_INT);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            // Atualiza as informações médicas existentes
            $queryUpdate = "UPDATE detalhe_medico 
                            SET alergias = :alergias, doencas_cronicas = :doencas_cronicas, remedio_recorrente = :remedios_recorrentes 
                            WHERE id_cliente = :id_cliente";
            $stmtUpdate = $conn->prepare($queryUpdate);
            $stmtUpdate->bindParam(':alergias', $alergias);
            $stmtUpdate->bindParam(':doencas_cronicas', $doencasCronicas);
            $stmtUpdate->bindParam(':remedios_recorrentes', $remediosRecorrentes);
            $stmtUpdate->bindParam(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmtUpdate->execute();
        } else {
            // Insere novas informações médicas
            $queryInsert = "INSERT INTO detalhe_medico (id_cliente, alergias, doencas_cronicas, remedio_recorrente) 
                            VALUES (:id_cliente, :alergias, :doencas_cronicas, :remedios_recorrentes)";
            $stmtInsert = $conn->prepare($queryInsert);
            $stmtInsert->bindParam(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmtInsert->bindParam(':alergias', $alergias);
            $stmtInsert->bindParam(':doencas_cronicas', $doencasCronicas);
            $stmtInsert->bindParam(':remedios_recorrentes', $remediosRecorrentes);
            $stmtInsert->execute();
        }

        $_SESSION['sucesso'] = 'Informações médicas atualizadas com sucesso.';
        header('Location: ../perfil_cliente.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['erro'] = 'Erro ao atualizar informações médicas: ' . $e->getMessage();
        header('Location: ../perfil_cliente.php');
        exit;
    }
} else {
    $_SESSION['erro'] = 'Método inválido.';
    header('Location: ../perfil_cliente.php');
    exit;
}
