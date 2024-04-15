<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>スポット追加</title>
    <link rel="stylesheet" href="style.css">
    <!--  なぜかここにある方がうまくGoogle mapが表示される-->
    <?php require 'config/config_googlemap.php'; ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&callback=initMap" async></script>
</head>

<body>
    <h1>スポット追加</h1>
    <form action="spot_add_act.php" method="post" enctype="multipart/form-data">
        <div>
            <label for="name">スポット名：</label>
            <input type="text" name="name" required>
        </div>
        <div>
            <label for="description">説明文：</label>
            <textarea name="description" rows="5"></textarea>
        </div>
        <div>
            <label for="category">カテゴリー：</label>
            <select name="category" id="category" required>
                <option value="">選択してください</option>
                <?php
                include("funcs.php");
                $pdo = db_conn();
                $stmt = $pdo->query("SELECT * FROM categories");
                while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $category['id'] . '">' . $category['name'] . '</option>';
                }
                ?>
            </select>
        </div>

        <!-- 所在地入力欄 -->
        <div class="main_location">
            <label for="main_address">所在地</label>
            <!-- 登録方法の選択 -->
            <select id="main_location_type" name="main_location_type">
                <option value="">入力方法を選択してください</option>
                <option value="main_address">住所を入力</option>
                <option value="main_coordinates">座標を入力</option>
                <option value="main_marker">Google mapで入力</option>
                <!--<option value="main_current_location">現在地から入力</option> -->
            </select>

            <!-- 住所入力フィールド -->
            <div id="main_address_input" style="display:none;">
                <input type="text" id="address" name="main_address" placeholder="住所を入力">
            </div>

            <!-- 座標入力フィールド -->
            <div id="main_coordinates_input" style="display:none;">
                <input type="text" id="latitude" name="main_latitude" placeholder="緯度">
                <input type="text" id="longitude" name="main_longitude" placeholder="経度">
            </div>

            <!-- Google Map表示 -->
            <div id="main_map" style="height: 400px; display:none;"></div>

            <!-- 現在地取得ボタン -->
            <button type="button" id="main_get_current_location" style="display:none;">現在地を取得</button>
            <!-- 緯度経度の隠しフィールド -->
            <input type="hidden" id="main_latitude" name="main_latitude">
            <input type="hidden" id="main_longitude" name="main_longitude">
        </div>


        <div class="view_location">
            <label for="view_address">再現位置</label>
            <!-- 登録方法の選択 -->
            <select id="view_location_type" name="location_type">
                <option value="">入力方法を選択してください</option>
                <option value="view_address">住所を入力</option>
                <option value="view_coordinates">座標を入力</option>
                <option value="view_marker">Google mapで入力</option>
                 <!--<option value="view_current_location">現在地から入力</option> -->
            </select>

            <!-- 住所入力フィールド -->
            <div id="view_address_input" style="display:none;">
                <input type="text" id="view_address" name="view_address" placeholder="住所を入力">
            </div>

            <!-- 座標入力フィールド -->
            <div id="view_coordinates_input" style="display:none;">
                <input type="text" id="view_latitude" name="view_latitude" placeholder="緯度">
                <input type="text" id="view_longitude" name="view_longitude" placeholder="経度">
            </div>

            <!-- Google Map表示 -->
            <div id="view_map" style="height: 400px; display:none;"></div>

            <!-- 現在地取得ボタン -->
            <!-- Google mapのデフォルト位置を現在位置にすればいらないかも -->
            <!-- <button type="button" id="view_get_current_location" style="display:none;">現在地を取得</button>-->
            <!-- 緯度経度の隠しフィールド -->
            <input type="hidden" id="view_latitude" name="view_latitude">
            <input type="hidden" id="view_longitude" name="view_longitude">
        </div>


        <!-- PHPを使って作品一覧を取得し、オプションを生成 -->
        <!-- // 作品の申請をユーザーができるようにするか悩む 重複や不適切なものがあるかもしれない
                // 作品の追加は管理者が行うべきかもしれない
                // それか、APIなどで一覧を取得するようにするか -->
        <div>
            <label for="work_id">作品：</label>
            新しい作品を追加したい場合は、<a href="work_add.php">こちら</a>
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
        </div>
        <!-- <div>
            <label for="new_work">新しい作品を登録する：</label>
            <input type="text" id="new_work" name="new_work">
        </div> -->

        <div>
            <label for="image">画像：</label>
            <input type="file" name="image" accept="image/*" required>
        </div>
        <div id="image_preview"></div>
        <button type="submit">登録</button>
    </form>


    <script>
        let mainMap;
        let mainMarker;
        let viewMap;
        let viewMarker;

        let map;
let marker;

// 現在地取得が成功したときの処理
function success(pos, mapId, callback) {
    const crd = pos.coords;
    const latlng = new google.maps.LatLng(crd.latitude, crd.longitude);

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
        document.getElementById(mapId + '_latitude').value = event.latLng.lat();
        document.getElementById(mapId + '_longitude').value = event.latLng.lng();
    });

    callback();
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

function main_initMap() {
    getCurrentLocation('main_map', function() {
        // 現在地取得後の処理があれば、ここに記述
    });
}

function view_initMap() {
    getCurrentLocation('view_map', function() {
        // 現在地取得後の処理があれば、ここに記述
    });
}


        // 選択肢に応じて入力方法を切り替える処理(所在地用)
        document.getElementById('main_location_type').addEventListener('change', function() {
            switch (this.value) {
                case 'main_address':
                    document.getElementById('main_address_input').style.display = 'block';
                    document.getElementById('main_coordinates_input').style.display = 'none';
                    document.getElementById('main_map').style.display = 'none';
                    document.getElementById('main_get_current_location').style.display = 'none';
                    break;
                case 'main_coordinates':
                    document.getElementById('main_address_input').style.display = 'none';
                    document.getElementById('main_coordinates_input').style.display = 'block';
                    document.getElementById('main_map').style.display = 'none';
                    document.getElementById('main_get_current_location').style.display = 'none';
                    break;
                case 'main_marker':
                    document.getElementById('main_address_input').style.display = 'none';
                    document.getElementById('main_coordinates_input').style.display = 'none';
                    document.getElementById('main_map').style.display = 'block';
                    document.getElementById('main_get_current_location').style.display = 'none';
                    main_initMap(); // Google Mapの初期化
                    break;
                case 'main_current_location':
                    document.getElementById('main_address_input').style.display = 'none';
                    document.getElementById('main_coordinates_input').style.display = 'none';
                    document.getElementById('main_map').style.display = 'none';
                    document.getElementById('main_get_current_location').style.display = 'block';
                    break;
            }
        });

        // 選択肢に応じて入力方法を切り替える処理(再現位置用)
        document.getElementById('view_location_type').addEventListener('change', function() {
            switch (this.value) {
                case 'view_address':
                    document.getElementById('view_address_input').style.display = 'block';
                    document.getElementById('view_coordinates_input').style.display = 'none';
                    document.getElementById('view_map').style.display = 'none';
                    document.getElementById('view_get_current_location').style.display = 'none';
                    break;
                case 'view_coordinates':
                    document.getElementById('view_address_input').style.display = 'none';
                    document.getElementById('view_coordinates_input').style.display = 'block';
                    document.getElementById('view_map').style.display = 'none';
                    document.getElementById('view_get_current_location').style.display = 'none';
                    break;
                case 'view_marker':
                    document.getElementById('view_address_input').style.display = 'none';
                    document.getElementById('view_coordinates_input').style.display = 'none';
                    document.getElementById('view_map').style.display = 'block';
                    document.getElementById('view_get_current_location').style.display = 'none';
                    view_initMap(); // Google Mapの初期化
                    break;
                case 'view_current_location':
                    document.getElementById('view_address_input').style.display = 'none';
                    document.getElementById('view_coordinates_input').style.display = 'none';
                    document.getElementById('view_map').style.display = 'none';
                    document.getElementById('view_get_current_location').style.display = 'block';
                    break;
            }
        });

        
    </script>


</body>

</html>