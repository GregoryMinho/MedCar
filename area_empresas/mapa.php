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

    <!-- Bootstrap e Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2a4f7e;
            --accent-color: #38b2ac;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background: var(--primary-color);
            color: white;
            min-height: 100vh;
            width: 250px;
            position: fixed;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,.75);
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }

        .sidebar .nav-link.active {
            background: var(--secondary-color);
            color: white;
        }

        .sidebar .nav-link:hover {
            background: var(--secondary-color);
            color: white;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .map-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        #map {
            height: 600px;
            width: 100%;
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Menu Lateral -->
    <div class="sidebar">
        <div class="text-center mb-5">
            <img src="https://source.unsplash.com/random/100x100/?logo" 
                 class="rounded-circle mb-3" 
                 alt="Logo"
                 width="100"
                 height="100">
            <h4>Transportadora MedCar</h4>
        </div>

        <nav class="nav flex-column">
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
            <a class="nav-link" href="gestao_veiculos.php">
                <i class="fas fa-truck me-2"></i> Frota
            </a>
            <a class="nav-link" href="gestao_motoristas.php">
                <i class="fas fa-users me-2"></i> Motoristas
            </a>
            <a class="nav-link active" href="#">
                <i class="fas fa-map-marked-alt me-2"></i> Mapeamento
            </a>
        </nav>
    </div>

    <!-- Conteúdo Principal -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2><i class="fas fa-route me-2"></i>Planejamento de Rotas</h2>
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <small class="text-muted">Última atualização:</small><br>
                    <span class="text-primary"><?= date('d/m/Y H:i') ?></span>
                </div>
                <img src="https://source.unsplash.com/random/40x40/?user" 
                     class="rounded-circle" 
                     alt="Perfil">
            </div>
        </div>

        <div class="container">
            <div class="map-card">
                <div class="row g-3 mb-4">
                    <div class="col-md-5">
                        <input type="text" 
                               id="origem" 
                               class="form-control" 
                               placeholder="Endereço de origem">
                    </div>
                    <div class="col-md-5">
                        <input type="text" 
                               id="destino" 
                               class="form-control" 
                               placeholder="Endereço de destino">
                    </div>
                    <div class="col-md-2">
                        <button onclick="calcularRota()" 
                                class="btn btn-primary w-100">
                            <i class="fas fa-route me-2"></i>Calcular
                        </button>
                    </div>
                </div>
                
                <div id="map"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
        function calcularRota() {
            const origem = document.getElementById('origem').value;
            const destino = document.getElementById('destino').value;

            if (!origem || !destino) {
                alert("Preencha os dois endereços.");
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
                            styles: [{color: '#1a365d', weight: 5}]
                        }
                    }).addTo(map);
                });
            });
        }
    </script>
</body>
</html>