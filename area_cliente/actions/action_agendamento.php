<?php
require '../../includes/valida_login.php'; // inclui o arquivo de validação de login
require '../../includes/conexao_BdAgendamento.php'; // inclui o arquivo de conexão com o banco de dados

verificarPermissao('CLIENTE'); // verifica se o usuario logado é um cliente

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtém os dados do formulário
    $data_consulta = $_POST['data_consulta'];
    $horario = $_POST['horario_selecionado'];
    $rua_origem = $_POST['pickup_street'];
    $numero_origem = $_POST['pickup_number'];
    $complemento_origem = $_POST['pickup_complement'];
    $cidade_origem = $_POST['pickup_city'];
    $cep_origem = $_POST['pickup_zipcode'];
    $rua_destino = $_POST['dest_street'];
    $numero_destino = $_POST['dest_number'];
    $complemento_destino = $_POST['dest_complement'];
    $cidade_destino = $_POST['dest_city'];
    $cep_destino = $_POST['dest_zipcode'];
    $condicao_medica = $_POST['medical_condition'];
    $precisa_oxigenio = isset($_POST['need_oxygen']) ? 1 : 0;
    $precisa_assistencia = isset($_POST['need_assistance']) ? 1 : 0;
    $precisa_monitor = isset($_POST['need_monitor']) ? 1 : 0;
    $medicamentos = $_POST['medications'];
    $alergias = $_POST['allergies'];
    $contato_emergencia = $_POST['emergency_contact'];
    $informacoes_adicionais = $_POST['additional_info'];
    $acompanhante = $_POST['companion'];
    $tipo_transporte = $_POST['hidden_transport_type'];
    

    // Obtém o ID do cliente e da empresa 
    $empresa_id = $_POST['empresa_id']; // O id vem da pagina de consulta de empresas
    $cliente_id = $_SESSION['usuario']['id'];

    // Exibe os valores das variáveis para depuração
    var_dump(
        $data_consulta,
        $horario,
        $rua_origem,
        $numero_origem,
        $complemento_origem,
        $cidade_origem,
        $cep_origem,
        $rua_destino,
        $numero_destino,
        $complemento_destino,
        $cidade_destino,
        $cep_destino,
        $condicao_medica,
        $precisa_oxigenio,
        $precisa_assistencia,
        $precisa_monitor,
        $medicamentos,
        $alergias,
        $contato_emergencia,
        $informacoes_adicionais,
        $acompanhante,
        $tipo_transporte,
        $empresa_id,
        $cliente_id
    );

    try {
        // Insere os dados na tabela agendamentos
        $sql = "INSERT INTO agendamentos (cliente_id, empresa_id, data_consulta, horario, rua_origem, numero_origem, complemento_origem, cidade_origem, cep_origem, rua_destino, numero_destino, complemento_destino, cidade_destino, cep_destino, condicao_medica, precisa_oxigenio, precisa_assistencia, precisa_monitor, medicamentos, alergias, contato_emergencia, informacoes_adicionais, acompanhante, tipo_transporte)
         VALUES (:cliente_id, :empresa_id, :data_consulta, :horario, :rua_origem, :numero_origem, :complemento_origem, :cidade_origem, :cep_origem, :rua_destino, :numero_destino, :complemento_destino, :cidade_destino, :cep_destino, :condicao_medica, :precisa_oxigenio, :precisa_assistencia, :precisa_monitor, :medicamentos, :alergias, :contato_emergencia, :informacoes_adicionais, :acompanhante, :tipo_transporte)";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':cliente_id', $cliente_id, PDO::PARAM_INT);
        $stmt->bindValue(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->bindValue(':data_consulta', $data_consulta, PDO::PARAM_STR);
        $stmt->bindValue(':horario', $horario, PDO::PARAM_STR);
        $stmt->bindValue(':rua_origem', $rua_origem, PDO::PARAM_STR);
        $stmt->bindValue(':numero_origem', $numero_origem, PDO::PARAM_STR);
        $stmt->bindValue(':complemento_origem', $complemento_origem, PDO::PARAM_STR);
        $stmt->bindValue(':cidade_origem', $cidade_origem, PDO::PARAM_STR);
        $stmt->bindValue(':cep_origem', $cep_origem, PDO::PARAM_STR);
        $stmt->bindValue(':rua_destino', $rua_destino, PDO::PARAM_STR);
        $stmt->bindValue(':numero_destino', $numero_destino, PDO::PARAM_STR);
        $stmt->bindValue(':complemento_destino', $complemento_destino, PDO::PARAM_STR);
        $stmt->bindValue(':cidade_destino', $cidade_destino, PDO::PARAM_STR);
        $stmt->bindValue(':cep_destino', $cep_destino, PDO::PARAM_STR);
        $stmt->bindValue(':condicao_medica', $condicao_medica, PDO::PARAM_STR);
        $stmt->bindValue(':precisa_oxigenio', $precisa_oxigenio, PDO::PARAM_INT);
        $stmt->bindValue(':precisa_assistencia', $precisa_assistencia, PDO::PARAM_INT);
        $stmt->bindValue(':precisa_monitor', $precisa_monitor, PDO::PARAM_INT);
        $stmt->bindValue(':medicamentos', $medicamentos, PDO::PARAM_STR);
        $stmt->bindValue(':alergias', $alergias, PDO::PARAM_STR);
        $stmt->bindValue(':contato_emergencia', $contato_emergencia, PDO::PARAM_STR);
        $stmt->bindValue(':informacoes_adicionais', $informacoes_adicionais, PDO::PARAM_STR);
        $stmt->bindValue(':acompanhante', $acompanhante, PDO::PARAM_INT);
        $stmt->bindValue(':tipo_transporte', $tipo_transporte, PDO::PARAM_STR);
     
        if ($stmt->execute()) {
            echo "Agendamento realizado com sucesso!";
        } else {
            echo "Erro ao realizar agendamento: " . $stmt->errorInfo()[2];
        }
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }

    $conn = null;
}
