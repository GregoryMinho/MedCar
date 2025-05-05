<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete seu Cadastro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="min-h-screen bg-gradient-to-r from-blue-900 to-blue-800 flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">Complete seu Cadastro</h2>
        <p class="text-center text-gray-600 mb-6">Por favor, insira os dados restantes para continuar.</p>
        <form action="actions/action_completa_cadastro_cliente.php" method="POST" class="space-y-4">
            <?php
            $email = isset($_GET['email']) ? urldecode($_GET['email']) : '';
            $nome = isset($_GET['nome']) ? urldecode($_GET['nome']) : '';
            $foto = isset($_GET['foto']) ? urldecode($_GET['foto']) : '';
            ?>
            <input type="hidden" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="nome" value="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="foto" value="<?= htmlspecialchars($foto, ENT_QUOTES, 'UTF-8') ?>">
            <div>
                <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
                <input type="text" id="cpf" name="cpf" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="000.000.000-00" required>
            </div>
            <div>
                <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                <input type="text" id="telefone" name="telefone" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="(00) 00000-0000" required>
            </div>
            <div>
                <label for="data_nascimento" class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                <input type="date" id="data_nascimento" name="data_nascimento" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" required>
            </div>
            <div>
                <label for="contato_emergencia" class="block text-sm font-medium text-gray-700">Contato de Emergência</label>
                <input type="text" id="contato_emergencia" name="contato_emergencia" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="(00) 00000-0000" required>
            </div>
            <div>
                <label for="rua" class="block text-sm font-medium text-gray-700">Rua/Avenida</label>
                <input type="text" id="rua" name="rua" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: Av. Paulista" required>
            </div>
            <div>
                <label for="numero" class="block text-sm font-medium text-gray-700">Número</label>
                <input type="text" id="numero" name="numero" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: 123" required>
            </div>
            <div>
                <label for="complemento" class="block text-sm font-medium text-gray-700">Complemento</label>
                <input type="text" id="complemento" name="complemento" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: Apto 101">
            </div>
            <div>
                <label for="bairro" class="block text-sm font-medium text-gray-700">Bairro</label>
                <input type="text" id="bairro" name="bairro" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: Bela Vista" required>
            </div>
            <div>
                <label for="cidade" class="block text-sm font-medium text-gray-700">Cidade</label>
                <input type="text" id="cidade" name="cidade" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: São Paulo" required>
            </div>
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                <input type="text" id="estado" name="estado" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: SP" required>
            </div>
            <div>
                <label for="cep" class="block text-sm font-medium text-gray-700">CEP</label>
                <input type="text" id="cep" name="cep" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: 01310-100" required>
            </div>
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-500 hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                Continuar
            </button>
        </form>
    </div>

    <script>
        // Restrição de data de nascimento
        const dataNascimentoInput = document.getElementById('data_nascimento');
        const hoje = new Date();
        const maxDate = new Date(hoje.getFullYear() - 120, hoje.getMonth(), hoje.getDate()); // ano máximo 120 anos atrás
        const minDate = new Date(hoje.getFullYear(), hoje.getMonth(), hoje.getDate());
        dataNascimentoInput.max = minDate.toISOString().split('T')[0];
        dataNascimentoInput.min = maxDate.toISOString().split('T')[0];
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>

    <script src="../jquery.mask.min.js"></script>

    <script>
        $('#cpf').mask('000.000.000-00');
        $('#telefone').mask('(00) 00000-0000');
        $('#contato_emergencia').mask('(00) 00000-0000');
        $('#cep').mask('00000-000');
    </script>
</body>

</html>