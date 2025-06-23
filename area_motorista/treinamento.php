<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Treinamento do Motorista</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-screen bg-gray-50">

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="#" class="flex items-center space-x-2 text-xl font-bold">
                    <i data-lucide="ambulance" class="h-6 w-6"></i>
                    <span>MedCar</span>
                </a>
                <div class="flex items-center space-x-6">
                    <button class="flex items-center space-x-2 font-medium hover:text-teal-300 transition">
                        <i data-lucide="user" class="h-5 w-5"></i>
                        <span>Carlos Silva</span>
                    </button>
                    <button class="relative">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-xs rounded-full px-1.5 text-white">2</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar + Main -->
    <div class="flex pt-20">
        <!-- Sidebar -->
        <div class="hidden md:block w-64 bg-blue-900 text-white min-h-screen pt-10">
            <nav class="flex flex-col space-y-2 px-4">
                <a href="#" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-speedometer2 fs-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-calendar-event fs-5"></i>
                    <span>Meus Agendamentos</span>
                </a>
                <a href="#" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-person fs-5"></i>
                    <span>Meu Perfil</span>
                </a>
                <a href="#" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <i class="bi bi-currency-dollar fs-5"></i>
                    <span>Pagamentos</span>
                </a>
                <a href="#" class="flex items-center gap-3 ps-4 py-3 rounded-lg bg-blue-800 font-semibold">
                    <i class="bi bi-mortarboard fs-5"></i>
                    <span>Treinamento</span>
                </a>
                <a href="#" class="flex items-center gap-3 ps-4 py-3 rounded-lg hover:bg-blue-800 transition mt-8 text-red-400">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                    <span>Sair</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 px-2 md:px-10 py-8">
            <!-- Título -->
            <h1 class="text-3xl font-bold text-blue-900 mb-2 flex items-center gap-2">
                <i data-lucide="mortarboard" class="h-7 w-7 text-blue-800"></i>
                Treinamento do Motorista
            </h1>
            <p class="text-gray-500 mb-6">Capacite-se com os conteúdos obrigatórios de transporte seguro e boas práticas. Sua participação é registrada para a empresa.</p>
            
            <!-- Progresso do treinamento -->
            <div class="bg-white rounded-xl shadow-lg p-4 mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center gap-4">
                    <i data-lucide="badge-check" class="h-9 w-9 text-teal-600"></i>
                    <div>
                        <div class="font-semibold text-gray-700">Progresso do Treinamento Obrigatório</div>
                        <div class="flex items-center gap-3 mt-1">
                            <div class="w-48 bg-gray-200 rounded-full h-3">
                                <div class="bg-teal-600 h-3 rounded-full" style="width: 70%"></div>
                            </div>
                            <span class="text-xs text-teal-700 font-bold">70% concluído</span>
                        </div>
                    </div>
                </div>
                <button id="btn-concluir" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold px-6 py-2 rounded-lg shadow transition flex items-center gap-2">
                    <i data-lucide="check-circle"></i>
                    Marcar como concluído
                </button>
            </div>
            
            <!-- Materiais de Treinamento -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <!-- Videoaula 1 -->
                <div class="bg-white rounded-xl shadow-lg p-4 flex flex-col items-center text-center">
                    <div class="w-full aspect-video mb-3 rounded-lg overflow-hidden bg-gray-200 flex items-center justify-center">
                        <!-- Simulação de vídeo incorporado -->
                        <iframe class="w-full h-full" src="https://www.youtube.com/embed/8hY4dIuQhQw" title="Treinamento de Transporte Seguro" allowfullscreen></iframe>
                    </div>
                    <div class="font-bold text-blue-900 mb-1">Transporte Seguro de Pacientes</div>
                    <div class="text-gray-600 text-sm mb-2">Entenda normas, procedimentos e o papel do motorista em situações de risco.</div>
                    <a href="#" class="text-teal-600 text-sm flex items-center gap-1 hover:underline mt-1">
                        <i class="bi bi-file-earmark-arrow-down"></i>
                        Baixar Material PDF
                    </a>
                </div>
                <!-- Videoaula 2 -->
                <div class="bg-white rounded-xl shadow-lg p-4 flex flex-col items-center text-center">
                    <div class="w-full aspect-video mb-3 rounded-lg overflow-hidden bg-gray-200 flex items-center justify-center">
                        <iframe class="w-full h-full" src="https://www.youtube.com/embed/I43L9QG4pCw" title="Boas Práticas para Motoristas" allowfullscreen></iframe>
                    </div>
                    <div class="font-bold text-blue-900 mb-1">Boas Práticas no Atendimento</div>
                    <div class="text-gray-600 text-sm mb-2">Dicas de postura, empatia, comunicação e abordagem humanizada ao paciente.</div>
                    <a href="#" class="text-teal-600 text-sm flex items-center gap-1 hover:underline mt-1">
                        <i class="bi bi-file-earmark-arrow-down"></i>
                        Baixar Checklist
                    </a>
                </div>
                <!-- Tutorial sistema -->
                <div class="bg-white rounded-xl shadow-lg p-4 flex flex-col items-center text-center">
                    <div class="w-full aspect-video mb-3 rounded-lg overflow-hidden bg-gray-200 flex items-center justify-center">
                        <iframe class="w-full h-full" src="https://www.youtube.com/embed/gZnIbeI0GPc" title="Como usar o Sistema MedCar" allowfullscreen></iframe>
                    </div>
                    <div class="font-bold text-blue-900 mb-1">Uso do Sistema MedCar</div>
                    <div class="text-gray-600 text-sm mb-2">Aprenda a utilizar o aplicativo e acompanhar agendamentos, confirmações e relatórios.</div>
                    <a href="#" class="text-teal-600 text-sm flex items-center gap-1 hover:underline mt-1">
                        <i class="bi bi-file-earmark-arrow-down"></i>
                        Baixar Manual Digital
                    </a>
                </div>
            </div>

            <!-- FAQ Interativo -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                    <i data-lucide="help-circle" class="h-5 w-5 mr-2 text-teal-500"></i>
                    Dúvidas Frequentes (FAQ)
                </h2>
                <div class="space-y-4">
                    <details class="group">
                        <summary class="cursor-pointer flex items-center font-semibold text-blue-800 group-open:text-teal-700">
                            <i class="bi bi-question-circle mr-2"></i> O treinamento é obrigatório?
                        </summary>
                        <div class="ml-7 mt-2 text-gray-600">
                            Sim. Todo motorista MedCar deve concluir o treinamento obrigatório para atuar nos serviços da plataforma.
                        </div>
                    </details>
                    <details class="group">
                        <summary class="cursor-pointer flex items-center font-semibold text-blue-800 group-open:text-teal-700">
                            <i class="bi bi-question-circle mr-2"></i> Como registro minha conclusão?
                        </summary>
                        <div class="ml-7 mt-2 text-gray-600">
                            Clique no botão "Marcar como concluído". O sistema irá registrar e enviar ao RH da empresa.
                        </div>
                    </details>
                    <details class="group">
                        <summary class="cursor-pointer flex items-center font-semibold text-blue-800 group-open:text-teal-700">
                            <i class="bi bi-question-circle mr-2"></i> Onde acessar o manual do motorista?
                        </summary>
                        <div class="ml-7 mt-2 text-gray-600">
                            Todos os materiais estão disponíveis nesta página, incluindo o manual digital para download.
                        </div>
                    </details>
                    <details class="group">
                        <summary class="cursor-pointer flex items-center font-semibold text-blue-800 group-open:text-teal-700">
                            <i class="bi bi-question-circle mr-2"></i> Como tirar outras dúvidas?
                        </summary>
                        <div class="ml-7 mt-2 text-gray-600">
                            Você pode enviar dúvidas para o suporte MedCar ou pedir ajuda ao seu gestor direto pelo sistema.
                        </div>
                    </details>
                </div>
            </div>

            <!-- Registro de Conclusão -->
            <div id="concluido-msg" class="hidden bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded relative mb-4 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                Treinamento concluído e registrado com sucesso! Você já pode atuar normalmente.
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Simulação: Marcar como concluído
        document.getElementById("btn-concluir").addEventListener("click", function(){
            document.getElementById("concluido-msg").classList.remove("hidden");
            window.scrollTo({top:document.body.scrollHeight, behavior:"smooth"});
            this.disabled = true;
            this.classList.add("bg-teal-400", "cursor-not-allowed");
            this.innerHTML = '<i data-lucide="check-circle"></i> Treinamento Concluído';
            lucide.createIcons();
        });
    </script>
</body>
</html>
