<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>
<?php include '../includes/auth_check.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map Marker App</title>
    <style>
        #map { height: 600px; width: 100%; }
        #search-input { margin: 10px; padding: 10px; width: calc(100% - 40px); border: 1px solid #ccc; border-radius: 5px; }
        #suggestions { position: absolute; z-index: 1000; background: white; border: 1px solid #ccc; max-height: 200px; overflow-y: auto; }
        .suggestion-item { padding: 10px; cursor: pointer; }
        .suggestion-item:hover { background-color: #f0f0f0; }
        .sidebar { position: fixed; right: 0; top: 0; width: 300px; height: 100%; overflow-y: auto; background: #f7f7f7; padding: 10px; border-left: 1px solid #ccc; }
        .marker-item { cursor: pointer; padding: 10px; border-bottom: 1px solid #ccc; }
        .marker-item:hover { background-color: #e0e0e0; }
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; bottom: 0; top: auto; border-top: 1px solid #ccc; border-left: none; }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        function initMap() {
            let map;
            let markers = [];
            let searchInput = document.getElementById('search-input');
            let suggestionsContainer = document.getElementById('suggestions');
            let sidebar = document.getElementById('sidebar');

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

                addMarkerToMap(place.geometry.location.toJSON(), 'Búsqueda', place.formatted_address, '📍');
            });

            function fetchMarkers() {
                fetch('get_markers.php')
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(marker => {
                            const position = { lat: parseFloat(marker.latitude), lng: parseFloat(marker.longitude) };
                            addMarkerToMap(position, marker.name, marker.activity, marker.icon, false);
                            addMarkerToSidebar(marker);
                        });
                    });
            }

            function addMarker(location) {
                const name = prompt("Ingrese el nombre:");
                const activity = prompt("Ingrese la actividad:");
                const icon = prompt("Ingrese el icono (emoji):");

                if (name && activity && icon) {
                    fetch('add_marker.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `name=${encodeURIComponent(name)}&activity=${encodeURIComponent(activity)}&icon=${encodeURIComponent(icon)}&latitude=${location.lat}&longitude=${location.lng}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            toastr.success(data.message);
                            addMarkerToMap(location, name, activity, icon, true);
                            addMarkerToSidebar({ name, activity, icon, latitude: location.lat, longitude: location.lng });
                        } else {
                            toastr.error(data.message);
                        }
                    });
                }
            }

            function addMarkerToMap(location, name, activity, icon, center) {
                const marker = new google.maps.marker.AdvancedMarkerElement({
                    position: location,
                    map: map,
                    title: `${name} - ${activity}`,
                    label: icon
                });

                markers.push(marker);

                if (center) {
                    map.setCenter(location);
                }
            }

            function addMarkerToSidebar(marker) {
                const markerItem = document.createElement('div');
                markerItem.classList.add('marker-item');
                markerItem.innerHTML = `<strong>${marker.name}</strong><br>${marker.activity} ${marker.icon}`;
                markerItem.addEventListener('click', () => {
                    map.setCenter({ lat: parseFloat(marker.latitude), lng: parseFloat(marker.longitude) });
                    map.setZoom(15);
                });
                sidebar.appendChild(markerItem);
            }

            map.addListener('click', function(event) {
                addMarker(event.latLng.toJSON());
            });

            fetchMarkers();
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
    <h1>Map Marker App</h1>
    <input type="text" id="search-input" placeholder="Buscar dirección...">
    <div id="suggestions"></div>
    <div id="map"></div>

    <div id="sidebar" class="sidebar"></div>

    <form id="markerForm">
        <input type="text" id="name" placeholder="Nombre" required>
        <input type="text" id="activity" placeholder="Actividad" required>
        <input type="text" id="icon" placeholder="Icono (emoji)" required>
        <button type="submit">Agregar Marcador</button>
    </form>
</body>
</html>

<?php include '../includes/footer.php'; ?>
