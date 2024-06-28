<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>
<?php include '../includes/auth_check.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><i class="icon fas fa-map-marker-alt"></i> Sitios</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        #map { height: 600px; width: 100%; }
        #search-input { margin: 10px; padding: 10px; width: calc(100% - 40px); border: 1px solid black; border-radius: 5px; }
        #suggestions { position: absolute; z-index: 1000; background: white; border: 1px solid #ccc; max-height: 200px; overflow-y: auto; }
        .suggestion-item { padding: 10px; cursor: pointer; }
        .suggestion-item:hover { background-color: #f0f0f0; }
        #marker-list { margin: 10px; padding: 10px; border: 1px solid black; border-radius: 5px; }
        .marker-item { cursor: pointer; padding: 5px; border-bottom: 1px solid black; display: flex; justify-content: space-between; }
        .marker-item:hover { background-color: #e0e0e0; border-radius: 5px;color:black;}
        .delete-marker { color: red; cursor: pointer; }
        .form-container { margin: 10px; padding: 10px; border:1px solid black}
        .form-group { margin-bottom: 10px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group select { width: calc(100% - 22px); padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
    </style>
</head>
<body>

    <div class="panel">
    <h1><i class="icon fas fa-map-marker-alt"></i>&nbspSitios</h1>
        <input type="text" id="search-input" placeholder="Buscar dirección...">
        <div id="suggestions"></div>
        <div id="map"></div>

        <div id="marker-list"></div>

        <div id="form-container" class="form-container" style="display: none;">
            <form onsubmit="submitForm(event)">
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="activity">Actividad</label>
                    <input type="text" id="activity" name="activity" required>
                </div>
                <div class="form-group">
                    <label for="icon_type">Tipo de Icono</label>
                    <select id="icon_type" name="icon_type" required>
                        <option value="playa">🏖️ Playa</option>
                        <option value="restaurante">🍴 Restaurante</option>
                        <option value="sitio_de_interes">🏛️ Sitio de Interés</option>
                        <option value="montana">⛰️ Montaña</option>
                        <option value="ruta">🚶 Ruta</option>
                    </select>
                </div>
                <button type="submit">Agregar Marcador</button>
            </form>
        </div>
    </div>

    <script>
        let map;
        let markers = [];
        let selectedLocation = null;

        function initMap() {
            let searchInput = document.getElementById('search-input');
            let suggestionsContainer = document.getElementById('suggestions');
            let markerList = document.getElementById('marker-list');

            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 41.3851, lng: 2.1734 }, // Barcelona
                zoom: 13
            });

            fetchMarkers();

            const autocomplete = new google.maps.places.Autocomplete(searchInput);
            autocomplete.bindTo('bounds', map);

            autocomplete.addListener('place_changed', function () {
                suggestionsContainer.innerHTML = '';
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    console.log("Returned place contains no geometry");
                    return;
                }

                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }
            });

            map.addListener('click', function(event) {
                selectedLocation = event.latLng.toJSON();
                document.getElementById('form-container').style.display = 'block';
            });
        }

        function fetchMarkers() {
            fetch('get_markers.php')
                .then(response => response.json())
                .then(data => {
                    data.forEach(marker => {
                        const position = { lat: parseFloat(marker.latitude), lng: parseFloat(marker.longitude) };
                        addMarkerToMap(position, marker.name, marker.activity, marker.icon_type);
                        addMarkerToList(marker);
                    });
                });
        }

        function addMarkerToMap(location, name, activity, icon_type) {
            const iconMap = {
                'playa': '🏖️',
                'restaurante': '🍴',
                'sitio_de_interes': '🏛️',
                'montana': '⛰️',
                'ruta': '🚶'
            };

            const marker = new google.maps.Marker({
                position: location,
                map: map,
                title: `${name} - ${activity}`,
                label: iconMap[icon_type]
            });

            markers.push(marker);
        }

        function addMarkerToList(marker) {
            const iconMap = {
                'playa': '🏖️',
                'restaurante': '🍴',
                'sitio_de_interes': '🏛️',
                'montana': '⛰️',
                'ruta': '🚶'
            };

            const markerItem = document.createElement('div');
            markerItem.classList.add('marker-item');
            markerItem.innerHTML = `
                <div>
                    <strong>${marker.name}</strong><br>${marker.activity} ${iconMap[marker.icon_type]}
                </div>
                <div class="delete-marker" data-id="${marker.id}">X</div>
            `;
            markerItem.querySelector('.delete-marker').addEventListener('click', (event) => {
                event.stopPropagation();
                deleteMarker(marker.id, markerItem);
            });
            markerItem.addEventListener('click', () => {
                map.setCenter({ lat: parseFloat(marker.latitude), lng: parseFloat(marker.longitude) });
                map.setZoom(15);
            });
            document.getElementById('marker-list').appendChild(markerItem);
        }

        function deleteMarker(id, element) {
            fetch('delete_marker.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${encodeURIComponent(id)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    element.remove();
                    // Remove marker from map
                    const markerIndex = markers.findIndex(marker => marker.get('id') === id);
                    if (markerIndex > -1) {
                        markers[markerIndex].setMap(null);
                        markers.splice(markerIndex, 1);
                    }
                } else {
                    alert(data.message);
                }
            });
        }

        function submitForm(event) {
            event.preventDefault();
            const name = document.getElementById('name').value;
            const activity = document.getElementById('activity').value;
            const icon_type = document.getElementById('icon_type').value;

            if (name && activity && selectedLocation) {
                fetch('add_marker.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `name=${encodeURIComponent(name)}&activity=${encodeURIComponent(activity)}&icon_type=${encodeURIComponent(icon_type)}&latitude=${selectedLocation.lat}&longitude=${selectedLocation.lng}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        addMarkerToMap(selectedLocation, name, activity, icon_type);
                        addMarkerToList({ id: data.id, name, activity, icon_type, latitude: selectedLocation.lat, longitude: selectedLocation.lng });
                        document.getElementById('form-container').reset();
                        document.getElementById('form-container').style.display = 'none';
                    } else {
                        alert(data.message);
                    }
                });
            }
        }

        function loadScript(url, callback) {
            let script = document.createElement("script");
            script.type = "text/javascript";
            script.src = url;
            script.async = true;
            script.defer = true;
            script.onload = callback;
            document.head.appendChild(script);
        }

    loadScript("https://maps.googleapis.com/maps/api/js?key=AIzaSyCeNl5AEk1-zGJb-tPGD3zXXEreINyrS5Q&libraries=places&callback=initMap", function() {
        console.log("Google Maps API loaded.");
    });
</script>
</head>
<body>

<div id="form-container" class="form-container" style="display: none;">
<form onsubmit="submitForm(event)">
    <div class="form-group">
        <label for="name">Nombre</label>
        <input type="text" id="name" name="name" required>
    </div>
    <div class="form-group">
        <label for="activity">Actividad</label>
        <input type="text" id="activity" name="activity" required>
    </div>
    <div class="form-group">
        <label for="icon_type">Tipo de Icono</label>
        <select id="icon_type" name="icon_type" required>
            <option value="playa">🏖️ Playa</option>
            <option value="restaurante">🍴 Restaurante</option>
            <option value="sitio_de_interes">🏛️ Sitio de Interés</option>
            <option value="montana">⛰️ Montaña</option>
            <option value="ruta">🚶 Ruta</option>
        </select>
    </div>
    <button type="submit">Agregar Marcador</button>
</form>
</div>
</body>
</html>

<?php include '../includes/footer.php'; ?>

