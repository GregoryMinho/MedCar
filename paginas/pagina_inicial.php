<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCar - Transporte Médico Não Emergencial</title>
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
        .feature-card {
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .feature-card::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(56, 178, 172, 0.1));
            transform: rotate(45deg);
            transition: all 0.5s;
        }
        .feature-card:hover::before {
            animation: shine 1.5s;
        }
        @keyframes shine {
            0% { transform: rotate(45deg) translate(-50%, -50%); }
            100% { transform: rotate(45deg) translate(100%, 100%); }
        }
        .testimonial-card {
            transition: all 0.3s ease;
        }
        .testimonial-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="min-h-screen bg-white">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                    <i data-lucide="ambulance" class="h-6 w-6"></i>
                    <span>MedCar</span>
                </a>
                
    <!--seria interessante por essas partes como um menu lateral enves de como esta, seria legal ou nem ? -->
                <div class="flex items-center space-x-6">
                    <a href="#" class="font-medium hover:text-teal-300 transition">Home</a>
                    <a href="#funcionalidades" class="font-medium hover:text-teal-300 transition">Funcionalidades</a>
                    <a href="#vantagens" class="font-medium hover:text-teal-300 transition">Vantagens</a>
                    <a href="#contato" class="font-medium hover:text-teal-300 transition">Contato</a>
                    <a href="/MedQ-2/paginas/abas_menu_principal/aba_entrar.php" class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded-lg transition-all hover:scale-105 hidden md:block">
                        Entrar
                    </a> <!-- conectado a aba entrar, depois ver de botar a aba de cadastros -->
                    <button id="mobile-menu-button" class="md:hidden text-white ml-2">
                        <i data-lucide="menu" class="h-6 w-6"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="fixed inset-0 z-50 bg-blue-900 bg-opacity-95 flex flex-col text-white p-6 mobile-menu">
        <div class="flex justify-end">
            <button id="close-menu-button" class="text-white">
                <i data-lucide="x" class="h-6 w-6"></i>
            </button>
        </div>
        
        <div class="flex flex-col items-center justify-center space-y-8 flex-grow text-xl">
            <a href="#" class="font-medium hover:text-teal-300 transition">Home</a>
            <a href="#funcionalidades" class="font-medium hover:text-teal-300 transition">Funcionalidades</a>
            <a href="#vantagens" class="font-medium hover:text-teal-300 transition">Vantagens</a>
            <a href="#contato" class="font-medium hover:text-teal-300 transition">Contato</a>
            <a href="/MedQ-2/paginas/abas_menu_principal/aba_entrar.php" class="bg-teal-500 hover:bg-teal-600 text-white px-6 py-3 rounded-lg mt-4 transition-all hover:scale-105">
                Entrar
            </a>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 bg-gradient-to-r from-blue-900 to-blue-800 text-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-10 md:mb-0">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">
                        Transporte Médico Não Emergencial Simplificado
                    </h1>
                    <p class="text-xl mb-8 text-blue-100">
                        Conectamos pacientes a empresas de transporte médico de forma rápida, segura e eficiente.
                    </p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
    <!-- Revisar essa parte pois ficou redundante ter a msm coisa, botar o cadastro aqui? -->
                        <a href="/MedQ-2/paginas/abas_menu_principal/aba_entrar.php" class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-3 px-8 rounded-lg transition-all hover:scale-105 text-center">
                            Começar Agora
                        </a>
                        <a href="#funcionalidades" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-medium py-3 px-8 rounded-lg transition-all hover:scale-105 text-center">
                            Saiba Mais
                        </a>
                    </div>
                </div>
                <div class="md:w-1/2 flex justify-center">
                    <img src="https://source.unsplash.com/random/600x400/?ambulance,medical" alt="Transporte Médico" class="rounded-lg shadow-2xl max-w-full h-auto" />
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="p-6">
                    <p class="text-4xl font-bold text-blue-900 mb-2">500+</p>
                    <p class="text-gray-600">Empresas Cadastradas</p>
                </div>
                <div class="p-6">
                    <p class="text-4xl font-bold text-blue-900 mb-2">10.000+</p>
                    <p class="text-gray-600">Pacientes Atendidos</p>
                </div>
                <div class="p-6">
                    <p class="text-4xl font-bold text-blue-900 mb-2">25.000+</p>
                    <p class="text-gray-600">Transportes Realizados</p>
                </div>
                <div class="p-6">
                    <p class="text-4xl font-bold text-blue-900 mb-2">98%</p>
                    <p class="text-gray-600">Satisfação dos Clientes</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="funcionalidades" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-blue-900 mb-4">Funcionalidades da Plataforma</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Nossa plataforma oferece diversas funcionalidades para facilitar o transporte médico não emergencial.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="feature-card relative overflow-hidden bg-white rounded-xl shadow-lg p-8 text-center">
                    <div class="mb-4">
                        <i data-lucide="calendar" class="h-12 w-12 mx-auto text-teal-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-blue-900 mb-3">Agendamento Simplificado</h3>
                    <p class="text-gray-600">
                        Agende transportes médicos com antecedência, escolhendo data, horário e tipo de veículo necessário.
                    </p>
                </div>

                <div class="feature-card relative overflow-hidden bg-white rounded-xl shadow-lg p-8 text-center">
                    <div class="mb-4">
                        <i data-lucide="map" class="h-12 w-12 mx-auto text-teal-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-blue-900 mb-3">Rastreamento em Tempo Real</h3>
                    <p class="text-gray-600">
                        Acompanhe o trajeto do veículo em tempo real, com estimativa precisa de chegada.
                    </p>
                </div>

                <div class="feature-card relative overflow-hidden bg-white rounded-xl shadow-lg p-8 text-center">
                    <div class="mb-4">
                        <i data-lucide="credit-card" class="h-12 w-12 mx-auto text-teal-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-blue-900 mb-3">Pagamento Seguro</h3>
                    <p class="text-gray-600">
                        Realize pagamentos de forma segura diretamente pela plataforma, com múltiplas opções disponíveis.
                    </p>
                </div>

                <div class="feature-card relative overflow-hidden bg-white rounded-xl shadow-lg p-8 text-center">
                    <div class="mb-4">
                        <i data-lucide="star" class="h-12 w-12 mx-auto text-teal-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-blue-900 mb-3">Avaliações e Comentários</h3>
                    <p class="text-gray-600">
                        Avalie o serviço após cada transporte e consulte avaliações de outros pacientes para escolher a melhor empresa.
                    </p>
                </div>

                <div class="feature-card relative overflow-hidden bg-white rounded-xl shadow-lg p-8 text-center">
                    <div class="mb-4">
                        <i data-lucide="file-text" class="h-12 w-12 mx-auto text-teal-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-blue-900 mb-3">Histórico Médico</h3>
                    <p class="text-gray-600">
                        Mantenha informações médicas relevantes para o transporte, garantindo atendimento adequado às suas necessidades.
                    </p>
                </div>

                <div class="feature-card relative overflow-hidden bg-white rounded-xl shadow-lg p-8 text-center">
                    <div class="mb-4">
                        <i data-lucide="bell" class="h-12 w-12 mx-auto text-teal-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-blue-900 mb-3">Notificações Automáticas</h3>
                    <p class="text-gray-600">
                        Receba lembretes de agendamentos e notificações sobre a chegada do veículo por e-mail ou SMS.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="vantagens" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <!-- Benefits for Patients -->
            <div class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-blue-900 mb-4">Vantagens para Pacientes</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Descubra como a MedCar facilita o transporte médico para pacientes.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="flex items-start space-x-4">
                        <div class="bg-teal-100 p-3 rounded-full">
                            <i data-lucide="check-circle" class="h-6 w-6 text-teal-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-blue-900 mb-2">Acesso a Diversas Empresas</h3>
                            <p class="text-gray-600">
                                Compare preços, avaliações e disponibilidade entre diversas empresas de transporte médico em um só lugar.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="bg-teal-100 p-3 rounded-full">
                            <i data-lucide="check-circle" class="h-6 w-6 text-teal-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-blue-900 mb-2">Agendamento Flexível</h3>
                            <p class="text-gray-600">
                                Agende transportes com antecedência ou para o mesmo dia, de acordo com sua necessidade.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="bg-teal-100 p-3 rounded-full">
                            <i data-lucide="check-circle" class="h-6 w-6 text-teal-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-blue-900 mb-2">Veículos Adaptados</h3>
                            <p class="text-gray-600">
                                Encontre veículos adaptados para suas necessidades específicas, como cadeiras de rodas ou macas.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="bg-teal-100 p-3 rounded-full">
                            <i data-lucide="check-circle" class="h-6 w-6 text-teal-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-blue-900 mb-2">Segurança e Confiabilidade</h3>
                            <p class="text-gray-600">
                                Todas as empresas são verificadas e avaliadas constantemente, garantindo um serviço de qualidade.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Benefits for Companies -->
            <div>
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-blue-900 mb-4">Vantagens para Empresas</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Veja como sua empresa de transporte médico pode crescer com a MedCar.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="flex items-start space-x-4">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i data-lucide="trending-up" class="h-6 w-6 text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-blue-900 mb-2">Aumento de Visibilidade</h3>
                            <p class="text-gray-600">
                                Sua empresa ganha visibilidade para milhares de pacientes que buscam serviços de transporte médico.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i data-lucide="calendar" class="h-6 w-6 text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-blue-900 mb-2">Gestão de Agendamentos</h3>
                            <p class="text-gray-600">
                                Sistema completo para gerenciar agendamentos, rotas e disponibilidade de veículos.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i data-lucide="dollar-sign" class="h-6 w-6 text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-blue-900 mb-2">Redução de Custos Operacionais</h3>
                            <p class="text-gray-600">
                                Diminua custos com marketing e atendimento ao cliente, focando no que realmente importa: o transporte.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i data-lucide="bar-chart-2" class="h-6 w-6 text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-blue-900 mb-2">Relatórios e Análises</h3>
                            <p class="text-gray-600">
                                Acesse relatórios detalhados sobre o desempenho da sua empresa e identifique oportunidades de melhoria.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-blue-900 mb-4">O Que Nossos Usuários Dizem</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Veja os depoimentos de pacientes e empresas que utilizam nossa plataforma.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="testimonial-card bg-white rounded-xl shadow-lg p-8">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-500 flex">
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6 italic">
                        "A MedCar facilitou muito minha vida. Agora consigo agendar transportes para minhas consultas médicas com facilidade e segurança. O motorista sempre chega no horário e o veículo é muito confortável."
                    </p>
                    <div class="flex items-center">
                        <img src="https://source.unsplash.com/random/100x100/?portrait,woman" alt="Cliente" class="w-12 h-12 rounded-full mr-4" />
                        <div>
                            <h4 class="font-bold text-blue-900">Maria Silva</h4>
                            <p class="text-gray-500 text-sm">Paciente</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="testimonial-card bg-white rounded-xl shadow-lg p-8">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-500 flex">
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6 italic">
                        "Desde que cadastramos nossa empresa na MedCar, nosso número de clientes aumentou significativamente. O sistema de gestão é muito intuitivo e nos ajuda a organizar melhor nossa frota e agendamentos."
                    </p>
                    <div class="flex items-center">
                        <img src="https://source.unsplash.com/random/100x100/?portrait,man" alt="Empresa" class="w-12 h-12 rounded-full mr-4" />
                        <div>
                            <h4 class="font-bold text-blue-900">Carlos Oliveira</h4>
                            <p class="text-gray-500 text-sm">Diretor de Empresa</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="testimonial-card bg-white rounded-xl shadow-lg p-8">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-500 flex">
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            <i data-lucide="star-half" class="h-5 w-5 fill-current"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6 italic">
                        "Como médico, recomendo a MedCar para meus pacientes que precisam de transporte. O serviço é confiável e os pacientes chegam às consultas no horário, sem estresse. Isso melhora muito a experiência de atendimento."
                    </p>
                    <div class="flex items-center">
                        <img src="https://source.unsplash.com/random/100x100/?doctor" alt="Médico" class="w-12 h-12 rounded-full mr-4" />
                        <div>
                            <h4 class="font-bold text-blue-900">Dr. Paulo Santos</h4>
                            <p class="text-gray-500 text-sm">Cardiologista</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-blue-900 to-blue-800 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Pronto para Simplificar o Transporte Médico?</h2>
            <p class="text-xl mb-8 max-w-3xl mx-auto">
                Junte-se a milhares de pacientes e empresas que já utilizam a MedCar para facilitar o transporte médico não emergencial.
            </p>
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="aba_entrar.php" class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-3 px-8 rounded-lg transition-all hover:scale-105">
                    Cadastre-se Gratuitamente
                </a>
                <a href="#contato" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-medium py-3 px-8 rounded-lg transition-all hover:scale-105">
                    Fale Conosco
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contato" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-blue-900 mb-4">Entre em Contato</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Tem dúvidas ou sugestões? Nossa equipe está pronta para ajudar.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div>
                    <form class="space-y-6">
                        <div>
                            <label for="name" class="block text-gray-700 font-medium mb-2">Nome</label>
                            <input type="text" id="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Seu nome completo">
                        </div>
                        <div>
                            <label for="email" class="block text-gray-700 font-medium mb-2">E-mail</label>
                            <input type="email" id="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="seu@email.com">
                        </div>
                        <div>
                            <label for="subject" class="block text-gray-700 font-medium mb-2">Assunto</label>
                            <select id="subject" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="">Selecione um assunto</option>
                                <option value="info">Informações Gerais</option>
                                <option value="support">Suporte Técnico</option>
                                <option value="partnership">Parcerias</option>
                                <option value="other">Outro</option>
                            </select>
                        </div>
                        <div>
                            <label for="message" class="block text-gray-700 font-medium mb-2">Mensagem</label>
                            <textarea id="message" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Digite sua mensagem aqui..."></textarea>
                        </div>
                        <button type="submit" class="bg-teal-500 hover:bg-teal-600 text-white font-medium py-3 px-6 rounded-lg transition-all hover:scale-105">
                            Enviar Mensagem
                        </button>
                    </form>
                </div>

                <div class="flex flex-col justify-center">
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-blue-900 mb-4">Informações de Contato</h3>
                        <div class="flex items-start space-x-4 mb-4">
                            <div class="bg-teal-100 p-3 rounded-full">
                                <i data-lucide="map-pin" class="h-6 w-6 text-teal-600"></i>
                            </div>
                            <div>
                                <p class="text-gray-700">Av. Paulista, 1000 - Bela Vista</p>
                                <p class="text-gray-700">São Paulo - SP, 01310-100</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4 mb-4">
                            <div class="bg-teal-100 p-3 rounded-full">
                                <i data-lucide="phone" class="h-6 w-6 text-teal-600"></i>
                            </div>
                            <div>
                                <p class="text-gray-700">(11) 3456-7890</p>
                                <p class="text-gray-700">(11) 98765-4321</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="bg-teal-100 p-3 rounded-full">
                                <i data-lucide="mail" class="h-6 w-6 text-teal-600"></i>
                            </div>
                            <div>
                                <p class="text-gray-700">contato@medcar.com.br</p>
                                <p class="text-gray-700">suporte@medcar.com.br</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xl font-bold text-blue-900 mb-4">Horário de Atendimento</h3>
                        <p class="text-gray-700 mb-2">Segunda a Sexta: 8h às 18h</p>
                        <p class="text-gray-700">Sábado: 9h às 13h</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-blue-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <a href="#" class="flex items-center space-x-2 text-xl font-bold mb-4">
                        <i data-lucide="ambulance" class="h-6 w-6"></i>
                        <span>MedCar</span>
                    </a>
                    <p class="text-blue-200 mb-4">
                        Conectando pacientes e empresas de transporte médico não emergencial desde 2020.
                    </p>
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
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4">Links Rápidos</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-blue-200 hover:text-teal-300 transition">Home</a></li>
                        <li><a href="#funcionalidades" class="text-blue-200 hover:text-teal-300 transition">Funcionalidades</a></li>
                        <li><a href="#vantagens" class="text-blue-200 hover:text-teal-300 transition">Vantagens</a></li>
                        <li><a href="#contato" class="text-blue-200 hover:text-teal-300 transition">Contato</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4">Para Empresas</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-blue-200 hover:text-teal-300 transition">Cadastro de Empresa</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-teal-300 transition">Planos e Preços</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-teal-300 transition">Termos para Empresas</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-teal-300 transition">Suporte Técnico</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4">Para Pacientes</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-blue-200 hover:text-teal-300 transition">Como Funciona</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-teal-300 transition">Perguntas Frequentes</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-teal-300 transition">Política de Privacidade</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-teal-300 transition">Termos de Uso</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-blue-800 mt-8 pt-8 text-center text-blue-300">
                <p>&copy; 2023 MedCar. Todos os direitos reservados.</p>
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
            mobileMenu.classList.add('open');
        });

        closeMenuButton.addEventListener('click', () => {
            mobileMenu.classList.remove('open');
        });
    </script>
</body>
</html>