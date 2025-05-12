<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Confirmação de Cadastro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-screen bg-white flex flex-col">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a class="flex items-center space-x-2 text-xl font-bold">
                    <i data-lucide="ambulance" class="h-6 w-6"></i>
                    <span>MedCar</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Confirmation Section -->
    <section class="flex-grow flex items-center justify-center pt-32 pb-20">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="mb-6">
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
                                    ?>
                                    <div class="bg-teal-100 p-4 rounded-full inline-block mb-4">
                                        <i data-lucide="check-circle" class="h-16 w-16 text-teal-600"></i>
                                    </div>
                                    <h2 class="text-3xl font-bold text-blue-900 mb-4">Cadastro Confirmado!</h2>
                                    <p class="text-xl text-gray-600 mb-6">
                                        Seu cadastro foi confirmado com sucesso. Você será redirecionado para a página de login em instantes.
                                    </p>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
                                        <div id="progress-bar" class="bg-teal-500 h-2 rounded-full" style="width: 0%"></div>
                                    </div>
                                    <?php
                                    header("Refresh: 5;  url=../login_clientes.php");
                                } else {
                                    // Token expirado
                                    ?>
                                    <div class="bg-red-100 p-4 rounded-full inline-block mb-4">
                                        <i data-lucide="x-circle" class="h-16 w-16 text-red-600"></i>
                                    </div>
                                    <h2 class="text-3xl font-bold text-blue-900 mb-4">Token Expirado</h2>
                                    <p class="text-xl text-gray-600 mb-6">
                                        O token de confirmação expirou. Por favor, solicite um novo cadastro.
                                    </p>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
                                        <div id="progress-bar" class="bg-red-500 h-2 rounded-full" style="width: 0%"></div>
                                    </div>
                                    <?php
                                    header("Refresh: 5; url=../cadastro_cliente.php");
                                }
                            } else {
                                ?>
                                <div class="bg-red-100 p-4 rounded-full inline-block mb-4">
                                    <i data-lucide="alert-triangle" class="h-16 w-16 text-red-600"></i>
                                </div>
                                <h2 class="text-3xl font-bold text-blue-900 mb-4">Token Inválido</h2>
                                <p class="text-xl text-gray-600 mb-6">
                                    O token de confirmação é inválido ou já foi utilizado. Por favor, verifique o link ou solicite um novo cadastro.
                                </p>
                                <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
                                    <div id="progress-bar" class="bg-red-500 h-2 rounded-full" style="width: 0%"></div>
                                </div>
                                <?php
                                header("Refresh: 5; url=../cadastro_cliente.php");
                            }
                        } catch (PDOException $e) {
                            ?>
                            <div class="bg-red-100 p-4 rounded-full inline-block mb-4">
                                <i data-lucide="alert-circle" class="h-16 w-16 text-red-600"></i>
                            </div>
                            <h2 class="text-3xl font-bold text-blue-900 mb-4">Erro no Servidor</h2>
                            <p class="text-xl text-gray-600 mb-6">
                                Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.
                            </p>
                            <p class="text-sm text-gray-500">Erro: <?php echo $e->getMessage(); ?></p>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="bg-yellow-100 p-4 rounded-full inline-block mb-4">
                            <i data-lucide="help-circle" class="h-16 w-16 text-yellow-600"></i>
                        </div>
                        <h2 class="text-3xl font-bold text-blue-900 mb-4">Token Não Fornecido</h2>
                        <p class="text-xl text-gray-600 mb-6">
                            Nenhum token de confirmação foi fornecido. Por favor, verifique o link enviado ao seu e-mail.
                        </p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
                            <div id="progress-bar" class="bg-yellow-500 h-2 rounded-full" style="width: 0%"></div>
                        </div>
                        <?php
                        header("Refresh: 5; url=../login_clientes.php");
                    }
                    ?>
                </div>
                
                <a href="../login_clientes.php" class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-3 px-8 rounded-lg transition-all hover:scale-105 inline-block">
                    Ir para Login
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-blue-900 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-2 text-xl font-bold mb-4 md:mb-0">
                    <i data-lucide="ambulance" class="h-6 w-6"></i>
                    <span>MedCar</span>
                </div>
                
                <div class="flex space-x-4">
                    <a href="#" class="text-white hover:text-teal-300 transition">
                        <i data-lucide="facebook" class="h-5 w-5"></i>
                    </a>
                    <a href="#" class="text-white hover:text-teal-300 transition">
                        <i data-lucide="instagram" class="h-5 w-5"></i>
                    </a>
                    <a href="#" class="text-white hover:text-teal-300 transition">
                        <i data-lucide="twitter" class="h-5 w-5"></i>
                    </a>
                    <a href="#" class="text-white hover:text-teal-300 transition">
                        <i data-lucide="linkedin" class="h-5 w-5"></i>
                    </a>
                </div>
                
                <div class="mt-4 md:mt-0 text-blue-300 text-sm">
                    &copy; 2023 MedCar. Todos os direitos reservados.
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Mobile menu functionality
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeMenuButton = document.getElementById('close-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.remove('hidden');
        });

        closeMenuButton.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
        });

        // Progress bar animation
        const progressBar = document.getElementById('progress-bar');
        let width = 0;
        const interval = setInterval(() => {
            if (width >= 100) {
                clearInterval(interval);
            } else {
                width += 2;
                progressBar.style.width = width + '%';
            }
        }, 100);
    </script>
</body>
</html>