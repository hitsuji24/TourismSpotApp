<!DOCTYPE html>
<html>
<head>
    <title>Google Map with Current Location, Address Search, and Draggable Marker</title>
    <?php require 'config/config_googlemap.php'; ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&libraries=places" async defer></script>
    <script>
        let map;
        let marker;

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: 0, lng: 0},
                zoom: 12
            });

            // 現在地を取得して地図の中心に設定
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    map.setCenter(pos);
                    addMarker(pos);
                });
            }

            // 住所検索の入力欄を設定
            const input = document.getElementById('search-input');
            const searchBox = new google.maps.places.SearchBox(input);

            // 検索結果が選択されたときの処理
            searchBox.addListener('places_changed', function() {
                const places = searchBox.getPlaces();
                if (places.length === 0) return;
                const place = places[0];
                map.setCenter(place.geometry.location);
                addMarker(place.geometry.location);
            });
        }

        // マーカーを追加する関数
        function addMarker(position) {
            if (marker) marker.setMap(null);
            marker = new google.maps.Marker({
                position: position,
                map: map,
                draggable: true
            });
            marker.addListener('dragend', function() {
                const pos = marker.getPosition();
                document.getElementById('latitude').value = pos.lat();
                document.getElementById('longitude').value = pos.lng();
            });
        }
    </script>
</head>
<body onload="initMap()">
    <div id="map" style="height: 400px;"></div>
    <input id="search-input" type="text" placeholder="住所を入力">
    <div>
        <label for="latitude">緯度:</label>
        <input id="latitude" type="text" readonly>
    </div>
    <div>
        <label for="longitude">経度:</label>
        <input id="longitude" type="text" readonly>
    </div>
</body>
</html>
