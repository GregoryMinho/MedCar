<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Cliente</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .mobile-menu {
            transition: transform 0.3s ease-in-out;
            transform: translateX(100%);
        }

        .mobile-menu.open {
            transform: translateX(0);
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-r from-blue-900 to-blue-800">
    <header>
        <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-md">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center space-x-4">
                        <button onclick="window.history.back();" class="flex items-center space-x-2 text-white hover:text-teal-300 transition">
                            <i data-lucide="arrow-left" class="h-6 w-6"></i>
                            <span>Voltar</span>
                        </button>
                    </div>
                    <a href="pagina_inicial.php" class="flex items-center space-x-2 text-xl font-bold">
                        <i data-lucide="ambulance" class="h-6 w-6"></i>
                        <span>MedCar</span>
                    </a>
                    <div class="hidden md:flex space-x-6">
                        <a href="pagina_inicial.php" class="font-medium hover:text-teal-300 transition">Home</a>
                        <a href="pagina_inicial.php#contato" class="font-medium hover:text-teal-300 transition">Contato</a>
                    </div>
                    <button id="mobile-menu-button" aria-expanded="false" aria-controls="mobile-menu" class="md:hidden text-white">
                        <i data-lucide="menu" class="h-6 w-6"></i>
                    </button>
                </div>
            </div>
        </nav>
    </header>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="fixed inset-0 z-50 bg-blue-900 bg-opacity-95 flex flex-col text-white p-6 mobile-menu">
        <div class="flex justify-end">
            <button id="close-menu-button" class="text-white">
                <i data-lucide="x" class="h-6 w-6"></i>
            </button>
        </div>

        <div class="flex flex-col items-center justify-center space-y-8 flex-grow text-xl">
            <a href="pagina_inicial.php" class="font-medium hover:text-teal-300 transition">Home</a>
            <a href="abas_menu_principal/aba_empresas.php" class="font-medium hover:text-teal-300 transition">Empresas</a>
            <a href="pagina_inicial.php#contato" class="font-medium hover:text-teal-300 transition">Contato</a>

        </div>
    </div>

    <!-- Cadastro Section -->
    <section class="pt-32 pb-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-teal-500 text-white p-8 text-center">
                    <h2 class="text-3xl font-bold mb-2">Cadastre-se</h2>
                    <p class="text-xl">Crie sua conta para agendar seus transportes</p>
                </div>
                <div class="flex flex-col md:flex-row">
                    <!-- Cadastro Form -->
                    <div class="w-full md:w-1/2 p-8">
                        <form id="cadastro-form" class="space-y-6" action="actions/action_cadastro_cliente.php" method="POST">
                            <p class="text-m text-red-600">
                                <?php // impremir mensagem de erro, então limpa a variável de sessão
                                if (isset($_SESSION['erro'])) {
                                    echo $_SESSION['erro'];
                                    unset($_SESSION['erro']);
                                }
                                ?>
                            </p>
                            <div>
                                <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                                <input type="text" id="nome" name="nome" maxlength="60" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Seu nome completo" required>
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                                <?php
                                $email = isset($_GET['email']) ? base64_decode($_GET['email']) : '';
                                ?>
                                <input type="email" id="email" name="email" maxlength="50" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="seunome@exemplo.com" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
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
                                <input type="text" id="rua" name="rua" maxlength="30" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: Av. Paulista" required>
                            </div>
                            <div>
                                <label for="numero" class="block text-sm font-medium text-gray-700">Número</label>
                                <input type="text" id="numero" name="numero" maxlength="10" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: 123" required>
                            </div>
                            <div>
                                <label for="complemento" class="block text-sm font-medium text-gray-700">Complemento</label>
                                <input type="text" id="complemento" name="complemento" maxlength="30" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: Apto 101">
                            </div>
                            <div>
                                <label for="bairro" class="block text-sm font-medium text-gray-700">Bairro</label>
                                <input type="text" id="bairro" name="bairro" maxlength="30" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: Bela Vista" required>
                            </div>
                            <div>
                                <label for="cidade" class="block text-sm font-medium text-gray-700">Cidade</label>
                                <input type="text" id="cidade" name="cidade" maxlength="30" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: São Paulo" required>
                            </div>
                            <div>
                                <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                                <input type="text" id="estado" name="estado" maxlength="20" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: SP" required>
                            </div>
                            <div>
                                <label for="cep" class="block text-sm font-medium text-gray-700">CEP</label>
                                <input type="text" id="cep" name="cep" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="Ex: 01310-100" required>
                            </div>
                            <div>
                                <label for="senha" class="block text-sm font-medium text-gray-700">Senha</label>
                                <input type="password" id="senha" name="senha" maxlength="20" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="••••••••" minlength="8" required>
                            </div>
                            <div>
                                <label for="confirmar_senha" class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                                <input type="password" id="confirmar_senha" name="confirmar_senha" maxlength="20" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" placeholder="••••••••" minlength="8" required>
                            </div>
                            <button type="submit" id="submit-button" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-500 hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                                Cadastrar
                            </button>
                        </form>
                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600">
                                Já tem conta?
                                <a href="/MedQ-2/paginas/login_clientes.php" class="font-medium text-teal-500 hover:text-teal-600">Faça login aqui</a>
                            </p>
                        </div>
                    </div>
                    <!-- Benefits -->
                    <div class="w-full md:w-1/2 bg-gray-50 p-8">
                        <h4 class="text-xl font-bold mb-4 text-gray-800">Benefícios de ser Cliente</h4>
                        <ul class="space-y-2 mb-8 border-l-4 border-teal-500 pl-4">
                            <li>Agendamento rápido e seguro</li>
                            <li>Histórico de transportes</li>
                            <li>Cupons de desconto para clientes frequentes</li>
                            <li>Suporte 24 horas</li>
                        </ul>
                       
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Mobile menu functionality
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeMenuButton = document.getElementById('close-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.add('open');
        });

        closeMenuButton.addEventListener('click', () => {
            mobileMenu.classList.remove('open');
        });

        // Form submission
        document.getElementById('cadastro-form').addEventListener('submit', function(e) {
            const submitButton = document.getElementById('submit-button');
            submitButton.disabled = true; // Disable the button
            submitButton.textContent = 'Aguarde...'; // Change button text
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;

            if (senha !== confirmarSenha) {
                alert('As senhas não coincidem!');
                submitButton.disabled = false; // Re-enable the button if validation fails
                submitButton.textContent = 'Cadastrar'; // Reset button text
                e.preventDefault();
                return;
            }
            this.submit();
        });

        // Restrição de data de nascimento
        const dataNascimentoInput = document.getElementById('data_nascimento');
        const hoje = new Date();
        const maxDate = new Date(hoje.getFullYear() - 120, hoje.getMonth(), hoje.getDate()); // ano máximo 120 anos atrás
// Definindo a data mínima como a data de hoje
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