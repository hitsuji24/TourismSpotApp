<!DOCTYPE html>
<html>

<head>
    <title>スポット登録</title>
    <?php require 'config/config_googlemap.php'; ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&callback=initMap" async defer></script>
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>

<body>
    <h1>スポット登録</h1>
    <div id="map"></div>
    <div>
        <label for="address">住所:</label>
        <input type="text" id="address" name="address">
        <button onclick="searchAddress()">住所検索</button>
        <button onclick="resetToCurrentLocation()">現在地にリセット</button>
    </div>
    <div>
        <label for="latitude">緯度:</label>
        <input type="text" id="latitude" name="latitude" readonly>
    </div>
    <div>
        <label for="longitude">経度:</label>
        <input type="text" id="longitude" name="longitude" readonly>
    </div>
    <script>
        let map;
        let marker;
        let currentLocationMarker;

        function initMap() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            map = new google.maps.Map(document.getElementById('map'), {
                center: pos,
                zoom: 18
            });

            // メインのマーカーを設定
            marker = new google.maps.Marker({
                position: pos,
                map: map,
                draggable: true
            });
            document.getElementById('latitude').value = pos.lat;
            document.getElementById('longitude').value = pos.lng;
            marker.addListener('dragend', function() {
                const pos = marker.getPosition();
                document.getElementById('latitude').value = pos.lat();
                document.getElementById('longitude').value = pos.lng();
            });

            // 現在地マーカーを設定
            currentLocationMarker = new google.maps.Marker({
                position: pos,
                map: map,
                title: "現在地",
                icon: {
                    url: "https://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                }
            });
        }, function() {
            alert('位置情報の取得に失敗しました。');
        });
    } else {
        alert('お使いのブラウザは位置情報に対応していません。');
    }
}


        function searchAddress() {
            const address = document.getElementById('address').value;
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                'address': address
            }, function(results, status) {
                if (status === 'OK') {
                    const location = results[0].geometry.location;
                    map.setCenter(location);
                    marker.setPosition(location);
                    document.getElementById('latitude').value = location.lat();
                    document.getElementById('longitude').value = location.lng();
                    console.log("検索された住所: " + address);

                } else {
                    alert('住所から位置を特定できませんでした。');
                }
            });
        }

        function resetToCurrentLocation() {
            if (currentLocationMarker) {
                map.setCenter(currentLocationMarker.getPosition());
                marker.setPosition(currentLocationMarker.getPosition());
                document.getElementById('latitude').value = currentLocationMarker.getPosition().lat();
                document.getElementById('longitude').value = currentLocationMarker.getPosition().lng();
                console.log("現在地にリセットされました");

            }
        }
    </script>
</body>

</html>