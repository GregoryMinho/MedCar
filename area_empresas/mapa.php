<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa do Motorista</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=SUA_API_KEY&libraries=places"></script>
    <style>
        #map {
            width: 100%;
            height: 500px;
        }
    </style>
</head>
<body>
    <h2>Mapa com Trajetória do Motorista</h2>
    <div id="map"></div>

    <script>
        function initMap() {
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 14,
                center: { lat: -23.55052, lng: -46.633308 }, // São Paulo (exemplo)
            });

            const directionsService = new google.maps.DirectionsService();
            const directionsRenderer = new google.maps.DirectionsRenderer();
            directionsRenderer.setMap(map);

            // Coordenadas de origem e destino (pode puxar do banco de dados no PHP)
            const origem = { lat: -23.55052, lng: -46.633308 };
            const destino = { lat: -23.56311, lng: -46.656571 };

            directionsService.route(
                {
                    origin: origem,
                    destination: destino,
                    travelMode: google.maps.TravelMode.DRIVING,
                },
                (result, status) => {
                    if (status === google.maps.DirectionsStatus.OK) {
                        directionsRenderer.setDirections(result);
                    } else {
                        console.error("Erro ao buscar rota:", status);
                    }
                }
            );
        }

        window.onload = initMap;
    </script>
</body>
</html>
