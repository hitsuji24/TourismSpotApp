<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>スポット追加</title>
    <link rel="stylesheet" href="style.css">
    <?php require 'config/config_googlemap.php'; ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&callback=initMap" async defer></script>
</head>

<body>
    <h1>スポット追加</h1>
    <form action="spot_add_act.php" method="post" enctype="multipart/form-data">
        <div> <label for="name">スポット名：</label> <input type="text" name="name" required> </div>
        <div> <label for="description">説明文：</label> <textarea name="description" rows="5"></textarea> </div>
        <div> <label for="category">カテゴリー：</label> <select name="category" id="category" required>
                <option value="">選択してください</option>
                <?php include("funcs.php");
                $pdo = db_conn();
                $stmt = $pdo->query("SELECT * FROM categories");
                while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $category['id'] . '">' . $category['name'] . '</option>';
                } ?>
            </select> </div>

        <!-- 所在地入力欄 -->
        <div class="main_location">
            <label for="main_address">所在地</label>
            <!-- 登録方法の選択 -->
            <select id="main_location-type" name="main_location_type">
                <option value="">入力方法を選択してください</option>
                <option value="address">住所を入力</option>
                <option value="coordinates">座標を入力</option>
                <option value="marker">Google mapで入力</option>
            </select>

            <!-- 住所入力フィールド -->
            <div id="main_address-input" style="display:none;">
                <input type="text" id="main_address" name="main_address" placeholder="住所を入力">
            </div>

            <!-- 座標入力フィールド -->
            <div id="main_coordinates-input" style="display:none;">
                <input type="text" id="main_latitude" name="main_latitude" placeholder="緯度">
                <input type="text" id="main_longitude" name="main_longitude" placeholder="経度">
            </div>

            <!-- Google Map表示 -->
            <div id="main_map" style="height: 400px; display:none;"></div>

            <!-- 緯度経度の隠しフィールド -->
            <input type="hidden" id="main_latitude" name="main_latitude">
            <input type="hidden" id="main_longitude" name="main_longitude">
        </div>


        <div class="view_location">
            <label for="view_address">再現位置</label>
            <!-- 登録方法の選択 -->
            <select id="view_location-type" name="location_type">
                <option value="">入力方法を選択してください</option>
                <option value="address">住所を入力</option>
                <option value="coordinates">座標を入力</option>
                <option value="marker">Google mapで入力</option>
            </select>

            <!-- 住所入力フィールド -->
            <div id="view_address-input" style="display:none;">
                <input type="text" id="view_address" name="view_address" placeholder="住所を入力">
            </div>

            <!-- 座標入力フィールド -->
            <div id="view_coordinates-input" style="display:none;">
                <input type="text" id="view_latitude" name="view_latitude" placeholder="緯度">
                <input type="text" id="view_longitude" name="view_longitude" placeholder="経度">
            </div>

            <!-- Google Map表示 -->
            <div id="view_map" style="height: 400px; display:none;"></div>

            <!-- 緯度経度の隠しフィールド -->
            <input type="hidden" id="view_latitude" name="view_latitude">
            <input type="hidden" id="view_longitude" name="view_longitude">
        </div>


        <div>
            <label for="work_id">作品：</label>
            <select name="work_id" id="work_id">
                <option value="">選択してください</option>
                <?php
                $pdo = db_conn();
                $stmt = $pdo->query("SELECT * FROM works");
                while ($work = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $work['id'] . '">' . $work['title'] . '</option>';
                }
                ?>
            </select>
            <div class="">新しい作品を追加したい場合は、<a href="work_add.php">こちら</a></div>

        </div>

        <div>
            <label for="image">画像：</label>
            <input type="file" name="image" accept="image/*" required>
        </div>
        <div id="image-preview"></div>
        <button type="submit">登録</button>
    </form>


    <script>
        let mainMap;
        let mainMarker;
        let viewMap;
        let viewMarker;

        // 現在地取得が成功したときの処理
        function success(pos, mapId, callback) {
            if (pos.coords) {
                const crd = pos.coords;
                const latlng = new google.maps.LatLng(crd.latitude, crd.longitude);
                initMap(mapId, latlng, callback);

                const map = new google.maps.Map(document.getElementById(mapId), {
                    center: latlng,
                    zoom: 12
                });

                const marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    draggable: true
                });

                // マーカーのドラッグ終了イベントのリスナーを追加
                marker.addListener('dragend', function(event) {
                    const latitudeElement = document.getElementById(mapId + '_latitude');
                    const longitudeElement = document.getElementById(mapId + '_longitude');
                    if (latitudeElement && longitudeElement) {
                        latitudeElement.value = event.latLng.lat();
                        longitudeElement.value = event.latLng.lng();
                    }

                    // コンソールログを追加
                    console.log(mapId + ' marker position:');
                    console.log('Latitude: ' + event.latLng.lat());
                    console.log('Longitude: ' + event.latLng.lng());

                });

                callback();
            } else {
                console.error('位置情報が取得できませんでした。');
            }
        }

        // 現在地取得が失敗したときの処理
        function error(err) {
            console.warn('ERROR(' + err.code + '): ' + err.message);
            // デフォルトの位置を使用
            initMap('main_map', 35.6809591, 139.7673068, function() {});
            initMap('view_map', 35.6809591, 139.7673068, function() {});
        }

        // 現在地取得
        function getCurrentLocation(map, callback) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                success(pos, map, callback);
            }, error);
        }


        // Google Mapの初期化
        function initMap(mapId, center, callback) {
            const map = new google.maps.Map(document.getElementById(mapId), {
                center: center,
                zoom: 12
            });

            const marker = new google.maps.Marker({
                position: center,
                map: map,
                draggable: true
            });


            // マーカーのドラッグ終了イベントのリスナーを追加
            marker.addListener('dragend', function(event) {
                document.getElementById(mapId + '-latitude').value = event.latLng.lat();
                document.getElementById(mapId + '-longitude').value = event.latLng.lng();
            });

            callback(map, marker);
        }

        function main_initMap() {
            getCurrentLocation('main_map', function(pos) {
                const latlng = new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude);
                initMap('main_map', latlng, function(map, marker) {
                    mainMap = map;
                    mainMarker = marker;
                });
            });
        }

        function view_initMap() {
            getCurrentLocation('view_map', function(pos) {
                const latlng = new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude);
                initMap('view_map', latlng, function(map, marker) {
                    viewMap = map;
                    viewMarker = marker;
                });
            });
        }
        // 選択肢に応じて入力方法を切り替える処理(所在地用)
        document.getElementById('main_location-type').addEventListener('change', function() {
            switch (this.value) {
                case 'address':
                    document.getElementById('main_address-input').style.display = 'block';
                    document.getElementById('main_coordinates-input').style.display = 'none';
                    document.getElementById('main_map').style.display = 'none';
                    break;
                case 'coordinates':
                    document.getElementById('main_address-input').style.display = 'none';
                    document.getElementById('main_coordinates-input').style.display = 'block';
                    document.getElementById('main_map').style.display = 'none';
                    break;
                case 'marker':
                    document.getElementById('main_address-input').style.display = 'none';
                    document.getElementById('main_coordinates-input').style.display = 'none';
                    document.getElementById('main_map').style.display = 'block';
                    main_initMap(); // Google Mapの初期化
                    break;
            }
        });

        // 選択肢に応じて入力方法を切り替える処理(再現位置用)
        document.getElementById('view_location-type').addEventListener('change', function() {
            switch (this.value) {
                case 'address':
                    document.getElementById('view_address-input').style.display = 'block';
                    document.getElementById('view_coordinates-input').style.display = 'none';
                    document.getElementById('view_map').style.display = 'none';
                    break;
                case 'coordinates':
                    document.getElementById('view_address-input').style.display = 'none';
                    document.getElementById('view_coordinates-input').style.display = 'block';
                    document.getElementById('view_map').style.display = 'none';
                    break;
                case 'marker':
                    document.getElementById('view_address-input').style.display = 'none';
                    document.getElementById('view_coordinates-input').style.display = 'none';
                    document.getElementById('view_map').style.display = 'block';
                    view_initMap(); // Google Mapの初期化
                    break;
            }
        });
    </script>
</body>

</html>