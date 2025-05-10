<?php
require '../../includes/conexao_BdCadastroLogin.php';
session_start();

// Verificação robusta da sessão
if (!isset($_SESSION['usuario']['id']) || $_SESSION['usuario']['tipo'] !== 'empresa') {
    $_SESSION['erro'] = "Acesso não autorizado. Faça login novamente.";
    header('Location: ../../paginas/cadastro_empresas.php');
    exit();
}

$empresa_id = (int)$_SESSION['usuario']['id'];

// Sanitização dos inputs
$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
$cnpj = filter_input(INPUT_POST, 'cnpj', FILTER_SANITIZE_SPECIAL_CHARS);
$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_SPECIAL_CHARS);
$cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_SPECIAL_CHARS);
$endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_SPECIAL_CHARS);
$cep = filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_SPECIAL_CHARS);

$especialidades = $_POST['especialidades'] ?? [];
$tipos_veiculos = $_POST['tipos_veiculos'] ?? [];

try {
    $conn->beginTransaction();

    // Atualiza dados básicos
    $sql = "UPDATE empresas SET 
            nome = :nome, cnpj = :cnpj, telefone = :telefone, 
            cidade = :cidade, endereco = :endereco, cep = :cep 
            WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':nome' => $nome,
        
        ':cnpj' => $cnpj,
        ':telefone' => $telefone,
        ':cidade' => $cidade,
        ':endereco' => $endereco,
        ':cep' => $cep,
        ':id' => $empresa_id
    ]);

    // Remove e insere especialidades
    $conn->prepare("DELETE FROM empresa_especialidades WHERE empresa_id = :id")
         ->execute([':id' => $empresa_id]);
    
    foreach ($especialidades as $esp) {
        $esp = filter_var($esp, FILTER_SANITIZE_SPECIAL_CHARS);
        $conn->prepare("INSERT INTO empresa_especialidades (empresa_id, especialidade) VALUES (:id, :esp)")
             ->execute([':id' => $empresa_id, ':esp' => $esp]);
    }

    // Remove e insere veículos
    $conn->prepare("DELETE FROM empresa_veiculos WHERE empresa_id = :id")
         ->execute([':id' => $empresa_id]);
    
    foreach ($tipos_veiculos as $veic) {
        $veic = filter_var($veic, FILTER_SANITIZE_SPECIAL_CHARS);
        $conn->prepare("INSERT INTO empresa_veiculos (empresa_id, tipo_veiculo) VALUES (:id, :veic)")
             ->execute([':id' => $empresa_id, ':veic' => $veic]);
    }

    $conn->commit();
    
    // Mensagem de sucesso
    $_SESSION['sucesso'] = 'Cadastro atualizado com sucesso!';
    header('Location: ../../area_empresas/menu_principal.php');
    exit();

} catch (PDOException $e) {
    $conn->rollBack();
    $_SESSION['erro'] = 'Erro ao atualizar cadastro: ' . $e->getMessage();
    header('Location: ../../area_empresas/editar_cadastro_empresa.php');
    exit();
}