<!DOCTYPE html>
<html>
<head>
    <title>マーカーのドラッグ&ドロップで緯度・経度を取得</title>
    <?php require 'config/config_googlemap.php'; ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&callback=initMap" async defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        #map {
            height: 500px;
            width: 100%;
        }
    </style>
</head>
<body>
    <h1>マーカーのドラッグ&ドロップで緯度・経度を取得</h1>
    <div id="map"></div>
    <p>緯度: <span id="lat"></span></p>
    <p>経度: <span id="lng"></span></p>

    <script>
        let map;
        let marker;

        function initMap() {
            // ユーザーの現在位置を取得
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    // 地図の初期表示をユーザーの現在位置に設定
                    map = new google.maps.Map(document.getElementById('map'), {
                        center: pos,
                        zoom: 12
                    });
                    // マーカーを作成し、ドラッグ可能に設定
                    marker = new google.maps.Marker({
                        position: pos,
                        map: map,
                        draggable: true
                    });
                    // 初期位置の緯度・経度を表示
                    $('#lat').text(pos.lat);
                    $('#lng').text(pos.lng);
                    // マーカーのドラッグ終了時のイベントを設定
                    google.maps.event.addListener(marker, 'dragend', function(e) {
                        $('#lat').text(e.latLng.lat());
                        $('#lng').text(e.latLng.lng());
                    });
                }, function() {
                    // 位置情報の取得に失敗した場合の処理
                    alert('位置情報の取得に失敗しました。');
                });
            } else {
                // ブラウザが位置情報に対応していない場合の処理
                alert('お使いのブラウザは位置情報に対応していません。');
            }
        }
    </script>
</body>
</html>