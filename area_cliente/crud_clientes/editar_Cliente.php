<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Conta</title>
</head>

<body>
    <h1>Editar Minha Conta</h1>
    <?php
    require '../../includes/conexao_BdCadastroLogin.php';
    $id = $_REQUEST["id"];
    $dados = [];
    $sql = $pdo->prepare("SELECT * FROM clientes WHERE id = :id");
    $sql->bindValue(":id", $id);
    $sql->execute();

    if ($sql->rowCount() > 0) {
        $dados = $sql->fetch(PDO::FETCH_ASSOC);
    } else {
        header("Location:../area_cliente/menu_principal.php");
        exit;
    }
    ?>
    <form action="editandoCliente.php" method="POST">
        <input type="hidden" name="id" id="id" value="<?= $dados['id']; ?>">
        <label for="nome">
            Nome <input type="text" name="nome" value="<?= $dados['nome']; ?>">
        </label>
        <label for="senha">
            Senha <input type="password" name="senha" value="<?= $dados['senha']; ?>">
        </label>
        <label for="telefone">
            Telefone <input type="text" name="telefone" value="<?= $dados['telefone']; ?>">
        </label>
        <button type="submit">Salvar</button>
    </form>
</body>
</html>