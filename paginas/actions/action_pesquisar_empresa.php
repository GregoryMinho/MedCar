<?php
session_start();
require '../../includes/conexao_BdCadastroLogin.php';

// Sanitização dos parâmetros restantes
$localizacao = filter_input(INPUT_GET, 'localizacao', FILTER_SANITIZE_STRING);
$especialidade = filter_input(INPUT_GET, 'especialidade', FILTER_SANITIZE_STRING);
$tipo_veiculo = filter_input(INPUT_GET, 'tipo_veiculo', FILTER_SANITIZE_STRING);

try {
    $sql = "SELECT 
    e.id,
    e.nome,
    e.cnpj,
    e.cidade,
    e.endereco,
    e.email,
    e.telefone,
    e.cep,
    GROUP_CONCAT(DISTINCT esp.especialidade SEPARATOR ', ') AS especialidades,
    GROUP_CONCAT(DISTINCT vei.tipo_veiculo SEPARATOR ', ') AS tipos_veiculos
FROM empresas e
LEFT JOIN empresa_especialidades esp ON e.id = esp.empresa_id
LEFT JOIN empresa_veiculos vei ON e.id = vei.empresa_id";

    $conditions = [];
    $params = [];

    // Filtro por Localização
    if (!empty($localizacao)) {
        $partes = explode('-', $localizacao);
        $cidade = trim($partes[0]);
        $uf = isset($partes[1]) ? trim($partes[1]) : null;
        
        $conditions[] = "e.cidade LIKE :cidade";
        $params[':cidade'] = "%$cidade%";
        
         
    }

    // Filtro por Especialidade
    if (!empty($especialidade)) {
        $conditions[] = "esp.especialidade = :especialidade";
        $params[':especialidade'] = $especialidade;
    }

    // Filtro por Tipo de Veículo
    if (!empty($tipo_veiculo)) {
        $conditions[] = "vei.tipo_veiculo = :tipo_veiculo";
        $params[':tipo_veiculo'] = $tipo_veiculo;
    }

    // Montagem da query final
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " GROUP BY e.id";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Redirecionamento com resultados
    header('Location: ../pesquisar_empresa.php?' . http_build_query([
        'resultados' => $resultados,
        'localizacao' => $localizacao,
        'especialidade' => $especialidade,
        'tipo_veiculo' => $tipo_veiculo
    ]));
    exit();

} catch (PDOException $e) {
    $_SESSION['erro_pesquisa'] = "Erro ao pesquisar: " . $e->getMessage();
    header('Location: ../pesquisar_empresa.php');
    exit();
}