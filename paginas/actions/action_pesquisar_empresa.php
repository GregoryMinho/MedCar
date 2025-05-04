<?php

require '../../includes/conexao_BdCadastroLogin.php';

$busca = isset($_GET['busca']) ? $_GET['busca'] : '';
$localizacao = isset($_GET['localizacao']) ? $_GET['localizacao'] : '';
$especialidade = isset($_GET['especialidade']) ? $_GET['especialidade'] : '';
$tipo_veiculo = isset($_GET['tipo_veiculo']) ? $_GET['tipo_veiculo'] : '';
$telefone = isset($_GET['telefone']) ? $_GET['telefone'] : '';
$cep = isset($_GET['cep']) ? $_GET['cep'] : '';

try {
    $sql = "SELECT e.* FROM empresas e";
    $conditions = [];
    $params = [];

    if (!empty($busca)) {
        $conditions[] = "(e.nome LIKE :busca OR e.email LIKE :busca OR e.cnpj LIKE :busca)";
        $params[':busca'] = '%' . $busca . '%';
    }

    if (!empty($localizacao)) {
        $conditions[] = "e.cidade = :localizacao";
        $params[':localizacao'] = $localizacao;
    }

    if (!empty($telefone)) {
        // Remove formatação para buscar no banco de dados
        $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
        $conditions[] = "REPLACE(REPLACE(REPLACE(REPLACE(e.telefone, '(', ''), ')', ''), ' ', ''), '-', '') LIKE :telefone";
        $params[':telefone'] = '%' . $telefone_limpo . '%';
    }

    if (!empty($cep)) {
        // Remove formatação para buscar no banco de dados
        $cep_limpo = preg_replace('/[^0-9]/', '', $cep);
        $conditions[] = "REPLACE(e.cep, '-', '') LIKE :cep";
        $params[':cep'] = '%' . $cep_limpo . '%';
    }

    // Filtrar por especialidade (requer join com a tabela empresa_especialidades)
    if (!empty($especialidade)) {
        $sql .= " INNER JOIN empresa_especialidades ee ON e.id = ee.empresa_id";
        $conditions[] = "ee.especialidade = :especialidade";
        $params[':especialidade'] = $especialidade;
    }

    // Filtrar por tipo de veículo (requer join com a tabela empresa_veiculos)
    if (!empty($tipo_veiculo)) {
        $sql .= " INNER JOIN empresa_veiculos ev ON e.id = ev.empresa_id";
        $conditions[] = "ev.tipo_veiculo = :tipo_veiculo";
        $params[':tipo_veiculo'] = $tipo_veiculo;
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    // Para evitar resultados duplicados ao usar JOIN com especialidades/veículos
    if (!empty($especialidade) || !empty($tipo_veiculo)) {
        $sql .= " GROUP BY e.id";
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Redireciona de volta para a página de pesquisa com os resultados via GET
    $query_string = http_build_query(['resultados' => $resultados] + $_GET);
    header('Location: ../pesquisar_empresa.php?' . $query_string);
    exit();

} catch (PDOException $e) {
    // Log do erro para depuração
    error_log("Erro na pesquisa de empresas: " . $e->getMessage());
    // Exibe uma mensagem de erro amigável para o usuário
    $_SESSION['erro_pesquisa'] = "Ocorreu um erro ao realizar a pesquisa. Por favor, tente novamente mais tarde.";
    header('Location: ../pesquisar_empresa.php');
    exit();
}
?>