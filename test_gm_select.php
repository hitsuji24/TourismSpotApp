<!DOCTYPE html>
<html>
<head>
    <title>スポット登録</title>
    <?php require 'config/config_googlemap.php'; ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>"></script>
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>
<body>
    <h1>スポット登録</h1>
    <div>
        <label>登録方法を選択:</label>
        <select id="registration-method">
            <option value="">選択してください</option>
            <option value="address">住所</option>
            <option value="coordinates">座標</option>
            <option value="map">Google Map</option>
        </select>
    </div>

    <div id="address-form" style="display: none;">
        <label for="address-input">住所:</label>
        <input type="text" id="address-input" placeholder="住所を入力">
        <button onclick="registerByAddress()">実行</button>
    </div>

    <div id="coordinates-form" style="display: none;">
        <label for="latitude-input">緯度:</label>
        <input type="text" id="latitude-input" placeholder="緯度を入力">
        <label for="longitude-input">経度:</label>
        <input type="text" id="longitude-input" placeholder="経度を入力">
        <button onclick="registerByCoordinates()">実行</button>
    </div>

    <div id="map-form" style="display: none;">
        <div id="map"></div>
        <button onclick="registerByMap()">実行</button>
    </div>

    <div id="result" style="display: none;">
        <h2>登録結果</h2>
        <p>住所: <span id="result-address"></span></p>
        <p>緯度: <span id="result-latitude"></span></p>
        <p>経度: <span id="result-longitude"></span></p>
    </div>

    <script>
        let map;
        let marker;

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 35.6895, lng: 139.6917 },
                zoom: 15
            });

            marker = new google.maps.Marker({
                position: map.getCenter(),
                map: map,
                draggable: true
            });

            google.maps.event.addListener(marker, 'dragend', function() {
                updateMarkerPosition(marker.getPosition());
            });
        }

        function updateMarkerPosition(latLng) {
            document.getElementById('latitude-input').value = latLng.lat();
            document.getElementById('longitude-input').value = latLng.lng();
        }

        function registerByAddress() {
            const address = document.getElementById('address-input').value;
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ 'address': address }, function(results, status) {
                if (status === 'OK') {
                    const location = results[0].geometry.location;
                    displayResult(results[0].formatted_address, location.lat(), location.lng());
                } else {
                    alert('住所から座標を取得できませんでした。');
                }
            });
        }

        function registerByCoordinates() {
            const latitude = document.getElementById('latitude-input').value;
            const longitude = document.getElementById('longitude-input').value;
            const latlng = new google.maps.LatLng(latitude, longitude);
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ 'latLng': latlng }, function(results, status) {
                if (status === 'OK') {
                    displayResult(results[0].formatted_address, latitude, longitude);
                } else {
                    alert('座標から住所を取得できませんでした。');
                }
            });
        }

        function registerByMap() {
            const position = marker.getPosition();
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ 'latLng': position }, function(results, status) {
                if (status === 'OK') {
                    displayResult(results[0].formatted_address, position.lat(), position.lng());
                } else {
                    alert('座標から住所を取得できませんでした。');
                }
            });
        }

        function displayResult(address, latitude, longitude) {
            document.getElementById('result-address').textContent = address;
            document.getElementById('result-latitude').textContent = latitude;
            document.getElementById('result-longitude').textContent = longitude;
            document.getElementById('result').style.display = 'block';
        }

        const registrationMethod = document.getElementById('registration-method');
        registrationMethod.addEventListener('change', function() {
            const addressForm = document.getElementById('address-form');
            const coordinatesForm = document.getElementById('coordinates-form');
            const mapForm = document.getElementById('map-form');
            const result = document.getElementById('result');

            addressForm.style.display = 'none';
            coordinatesForm.style.display = 'none';
            mapForm.style.display = 'none';
            result.style.display = 'none';

            if (this.value === 'address') {
                addressForm.style.display = 'block';
            } else if (this.value === 'coordinates') {
                coordinatesForm.style.display = 'block';
            } else if (this.value === 'map') {
                mapForm.style.display = 'block';
                initMap();
            }
        });
    </script>
</body>
</html>
35.5955743	longitude：経度	139.5349474
