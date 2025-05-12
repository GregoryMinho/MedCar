<?php
require '../../includes/conexao_BdCadastroLogin.php';
require '../../includes/classe_usuario.php';
use usuario\Usuario;

// Verifica se o usuário logado é uma empresa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

Usuario::verificarPermissao('empresa');

$idEmpresa = (int)$_SESSION['usuario']['id'];

// Sanitiza os inputs recebidos
$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_SPECIAL_CHARS);
$endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_SPECIAL_CHARS);
$cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_SPECIAL_CHARS);
$cep = filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_SPECIAL_CHARS);

// Obter arrays de especialidades e tipos de veículos
// Usando isset para verificar se os campos existem no POST
$especialidades = isset($_POST['especialidades']) ? $_POST['especialidades'] : [];
$tiposVeiculos = isset($_POST['tipos_veiculos']) ? $_POST['tipos_veiculos'] : [];

try {
    // Inicia uma transação para garantir que todas as operações sejam concluídas com sucesso
    $conn->beginTransaction();
    
    // Atualiza os dados da empresa no banco de dados
    $stmt = $conn->prepare("UPDATE empresas SET 
        nome = :nome, 
        telefone = :telefone, 
        endereco = :endereco, 
        cidade = :cidade, 
        cep = :cep 
        WHERE id = :id");
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':cidade', $cidade);
    $stmt->bindParam(':cep', $cep);
    $stmt->bindParam(':id', $idEmpresa, PDO::PARAM_INT);
    $stmt->execute();

    // Atualiza especialidades - primeiro remove todas as existentes
    $conn->prepare("DELETE FROM empresa_especialidades WHERE empresa_id = :id")
        ->execute([':id' => $idEmpresa]);
    
    // Insere as novas especialidades selecionadas
    if (!empty($especialidades)) {
        $stmtEsp = $conn->prepare("INSERT INTO empresa_especialidades (empresa_id, especialidade) VALUES (:id, :especialidade)");
        foreach ($especialidades as $especialidade) {
            $stmtEsp->execute([':id' => $idEmpresa, ':especialidade' => $especialidade]);
        }
    }

    // Atualiza tipos de veículos - primeiro remove todos os existentes
    $conn->prepare("DELETE FROM empresa_veiculos WHERE empresa_id = :id")
        ->execute([':id' => $idEmpresa]);
    
    // Insere os novos tipos de veículos selecionados
    if (!empty($tiposVeiculos)) {
        $stmtVeic = $conn->prepare("INSERT INTO empresa_veiculos (empresa_id, tipo_veiculo) VALUES (:id, :tipo_veiculo)");
        foreach ($tiposVeiculos as $tipoVeiculo) {
            $stmtVeic->execute([':id' => $idEmpresa, ':tipo_veiculo' => $tipoVeiculo]);
        }
    }

    // Confirma todas as alterações no banco de dados
    $conn->commit();
    
    // Define mensagem de sucesso
    $_SESSION['sucesso'] = "Perfil atualizado com sucesso! As especialidades e tipos de veículos foram salvos.";
    
    // Redireciona para a página de perfil da empresa
    header('Location: ../perfil_empresa.php');
    exit();
    
} catch (PDOException $e) {
    // Em caso de erro, desfaz todas as alterações
    $conn->rollBack();
    
    $_SESSION['erro'] = "Erro ao atualizar os dados: " . $e->getMessage();
    header('Location: ../perfil_empresa.php');
    exit();
}
