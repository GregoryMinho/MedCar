<?php
require '../../includes/conexao_BdCadastroLogin.php'; // inclui o arquivo de conexão com o banco de dados
session_start(); // Inicia a sessão

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cnpj = $_POST['cnpj'];
    $telefone = $_POST['telefone'];
    $cidade = $_POST['cidade']; // Inclusão de endereco, cidade (localização)
    $endereco = $_POST['endereco'];
    $cep = $_POST['cep']; // Captura o CEP
    $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT); // Criptografa a senha

    // Recebe arrays de especialidades e veículos
    $especialidades = isset($_POST['especialidades']) ? $_POST['especialidades'] : [];
    $tipos_veiculos = isset($_POST['tipos_veiculos']) ? $_POST['tipos_veiculos'] : [];

    try {
        // Início da transação
        $conn->beginTransaction();

        //  Adiciona o campo cep no INSERT
        $sql = "INSERT INTO empresas (nome, email, cnpj, telefone, senha, cidade, endereco, cep)
                VALUES (:nome, :email, :cnpj, :telefone, :senha, :cidade, :endereco, :cep)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':cnpj', $cnpj);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':cidade', $cidade);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->bindParam(':cep', $cep);
        $stmt->execute();

        // Obtém o ID da empresa recém-inserida
        $empresa_id = $conn->lastInsertId();

        // Inserção das especialidades
        $sqlEspecialidade = "INSERT INTO empresa_especialidades (empresa_id, especialidade) VALUES (:empresa_id, :especialidade)";
        $stmtEsp = $conn->prepare($sqlEspecialidade);
        foreach ($especialidades as $esp) {
            $stmtEsp->execute([
                ':empresa_id' => $empresa_id,
                ':especialidade' => $esp
            ]);
        }
        // Inserção dos tipos de veículos
        $sqlVeiculo = "INSERT INTO empresa_veiculos (empresa_id, tipo_veiculo) VALUES (:empresa_id, :tipo_veiculo)";
        $stmtVeic = $conn->prepare($sqlVeiculo);
        foreach ($tipos_veiculos as $veic) {
            $stmtVeic->execute([
                ':empresa_id' => $empresa_id,
                ':tipo_veiculo' => $veic
            ]);
        }
        // Commit
        $conn->commit();
        
        // Cria a sessão
        $_SESSION['usuario'] = [
            'id' => $empresa_id,
            'nome' => $nome,
            'email' => $email,
            'tipo' => 'empresa'
        ];

        header('Location: ../../area_empresas/menu_principal.php');
        exit();
    } catch (PDOException $e) {
        // Rollback no caso de erro
        $conn->rollBack();
        $_SESSION['erro'] = 'Erro ao cadastrar empresa. Verifique se o e-mail ou CNPJ já estão cadastrados.';
        header('Location: ../../paginas/cadastro_empresas.php');
        exit();
    }
}
