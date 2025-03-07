<?php
// Inclui a conexão
include 'conexao.php';

// Função para buscar todos os registros
function buscarPacientes() {
    global $conn;

    // Consulta SQL para buscar todos os registros
    $sql = "SELECT * FROM pacientes_registros";
    $result = $conn->query($sql);

    // Verifica se há registros
    if ($result->num_rows > 0) {
        // Cria um array para armazenar os registros
        $registros = [];

        // Loop para armazenar os resultados em um array
        while($row = $result->fetch_assoc()) {
            $registros[] = $row;
        }

        // Retorna os registros
        return $registros;
    } else {
        // Retorna um array vazio se não houver resultados
        return [];
    }
}

// Chama a função para obter os dados
$pacientes = buscarPacientes();

// Exemplo de como usar os dados retornados
// Por exemplo, você pode imprimir ou processar os dados em outra parte do seu código.
if (count($pacientes) > 0) {
    // Pode realizar qualquer ação com os dados aqui, como exibir em uma página
    // Exemplo: var_dump($pacientes);
} else {
    // Caso não haja resultados, nada será exibido, mas pode-se processar
    // Exemplo: echo "Nenhum paciente encontrado.";
}

// Fecha a conexão
$conn = null; // Fecha a conexão manualmente (opcional)
?>
