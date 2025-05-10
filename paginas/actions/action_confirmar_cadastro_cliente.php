<?php
require '../../includes/conexao_BdCadastroLogin.php';

if (isset($_GET['token']) || isset($_GET['d'])) {
    $cliente_id = $_GET['d'];
    $token = $_GET['token'];

    try {
        $sql = "SELECT token_expiracao FROM clientes WHERE token = :token OR id = :id_cliente";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_cliente', $cliente_id);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $token_expiracao = $result['token_expiracao'];
            if (strtotime($token_expiracao) > time()) {
                // Token válido, atualiza o status
                $sqlUpdate = "UPDATE clientes SET status = '1', token = NULL, token_expiracao = NULL WHERE token = :token AND id = :id_cliente";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                $stmtUpdate->bindParam(':id_cliente', $cliente_id);
                $stmtUpdate->bindParam(':token', $token);
                $stmtUpdate->execute();
                $conn = null; // Fecha a conexão com o banco de dados
                echo "<h2 style='color: green; text-align: center;'>✔ Cadastro confirmado com sucesso!</h2>";
                header("Refresh: 5;  url=../login_clientes.php");
                exit();
            } else {
                // Token expirado
            
                // Exibe mensagem de erro
                echo "<h2 style='color: red; text-align: center;'>✖ Token expirado. Por favor, solicite um novo cadastro.</h2>";
                header("Refresh: 5; url=../cadastro_cliente.php");
                exit();
            }
        } else {
            echo "<h2>Token inválido ou já utilizado.</h2>";
            header("Refresh: 5; url=../cadastro_cliente.php");
            exit();
        }
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
} else {
    echo "<h2>Token não fornecido.</h2>";
    sleep(5); // Espera 5 segundos antes de redirecionar
    header("Location: ../login_clientes.php"); // Redireciona para a página inicial após falha na confirmação
    exit(); // Encerra o script após o redirecionamento
}
