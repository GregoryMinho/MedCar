<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>MedCar - Mapeamento de Rotas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Dependências do Mapa -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.min.js"></script>

    <!-- Tailwind CSS e Lucide -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style/style_mapa.css">
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Sidebar -->
    <div class="fixed top-0 left-0 h-full w-64 bg-blue-900 text-white flex flex-col items-center pt-10 z-40">
        <img src="https://source.unsplash.com/random/100x100/?logo"
            class="rounded-full mb-3 border-4 border-white shadow-lg"
            alt="Logo"
            width="100"
            height="100">
        <h4 class="text-xl font-bold mb-8">Transportadora MedCar</h4>
        <nav class="flex flex-col w-full space-y-2 px-4">
            <a class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-800 transition" href="dashboard.php">
                <i data-lucide="layout-dashboard" class="h-5 w-5 mr-2"></i> Dashboard
            </a>
            <a class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-800 transition" href="gestao_veiculos.php">
                <i data-lucide="truck" class="h-5 w-5 mr-2"></i> Frota
            </a>
            <a class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-800 transition" href="gestao_motoristas.php">
                <i data-lucide="users" class="h-5 w-5 mr-2"></i> Motoristas
            </a>
            <a class="flex items-center px-4 py-3 rounded-lg bg-blue-800" href="#">
                <i data-lucide="map" class="h-5 w-5 mr-2"></i> Mapeamento
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="ml-0 md:ml-64 min-h-screen flex flex-col">
        <!-- Header -->
        <div class="flex justify-between items-center px-6 pt-8 pb-4 bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-md">
            <h2 class="text-2xl font-bold flex items-center">
                <i data-lucide="route" class="h-6 w-6 mr-2"></i>
                Planejamento de Rotas
            </h2>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <small class="text-gray-200">Última atualização:</small><br>
                    <span class="text-teal-300 font-semibold"><?= date('d/m/Y H:i') ?></span>
                </div>
                <img src="https://source.unsplash.com/random/40x40/?user"
                    class="rounded-full border-2 border-white shadow"
                    alt="Perfil">
            </div>
        </div>

        <!-- Formulário e Mapa -->
        <div class="flex-1 flex flex-col items-center justify-center px-4 py-8">
            <div class="w-full max-w-3xl bg-white rounded-xl shadow-lg p-6">
                <form class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6" onsubmit="event.preventDefault();calcularRota();">
                    <div class="md:col-span-2">
                        <input type="text"
                            id="origem"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Endereço de origem">
                    </div>
                    <div class="md:col-span-2">
                        <input type="text"
                            id="destino"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Endereço de destino">
                    </div>
                    <div>
                        <button type="submit"
                            class="w-full flex items-center justify-center bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2 px-4 rounded-lg transition">
                            <i data-lucide="route" class="h-5 w-5 mr-2"></i>Calcular
                        </button>
                    </div>
                </form>
                <div id="map" class="w-full h-96 rounded-lg border border-gray-200"></div>
            </div>
        </div>
    </div>

    <script>
        // Inicializa Lucide icons
        lucide.createIcons();

        let map = L.map('map').setView([-23.55, -46.63], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let rota;

        function geocodificar(endereco, callback) {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(endereco)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        const latlng = [parseFloat(data[0].lat), parseFloat(data[0].lon)];
                        callback(latlng);
                    } else {
                        alert("Endereço não encontrado: " + endereco);
                    }
                });
        }

        function getParametroURL(nome) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(nome);
        }

        window.onload = () => {
            const origemParam = getParametroURL('rua_origem');
            const destinoParam = getParametroURL('rua_destino');

            if (origemParam && destinoParam) {
                document.getElementById('origem').value = origemParam;
                document.getElementById('destino').value = destinoParam;

                // Espera o mapa carregar antes de calcular
                setTimeout(() => {
                    calcularRota();
                }, 500);
            }
        };

        // Função para calcular a rota e exibir no mapa
        function calcularRota() {
            const origem = document.getElementById('origem').value;
            const destino = document.getElementById('destino').value;

            // Exibe mensagem de erro e destaca campos
            if (!origem || !destino) {
                document.getElementById('origem').classList.add('border-red-500');
                document.getElementById('destino').classList.add('border-red-500');

                // remove a borda vermelha ao focar nos campos
                document.getElementById('origem').addEventListener('focus', function() {
                    this.classList.remove('border-red-500');
                });
                document.getElementById('destino').addEventListener('focus', function() {
                    this.classList.remove('border-red-500');
                });
                return;
            }

            geocodificar(origem, (origemCoord) => {
                geocodificar(destino, (destinoCoord) => {
                    if (rota) {
                        map.removeControl(rota);
                    }
                    rota = L.Routing.control({
                        waypoints: [
                            L.latLng(origemCoord[0], origemCoord[1]),
                            L.latLng(destinoCoord[0], destinoCoord[1])
                        ],
                        routeWhileDragging: false,
                        lineOptions: {
                            styles: [{
                                color: '#1a365d',
                                weight: 5
                            }]
                        }
                    }).addTo(map);
                });
            });
        }
    </script>
</body>

</html>