<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>スポット追加</title>
    <link rel="stylesheet" href="style.css">
    <!--  なぜかここにある方がうまくGoogle mapが表示される-->
    <?php require 'config/config_googlemap.php'; ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&callback=initMap" async defer></script>
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
                <option value="address">住所を入力</option>
                <option value="coordinates">座標を入力</option>
                <option value="marker">Google mapで入力</option>
                <option value="current_location">現在地から入力</option>
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
                <option value="address">住所を入力</option>
                <option value="coordinates">座標を入力</option>
                <option value="marker">Google mapで入力</option>
                <option value="current_location">現在地から入力</option>
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
        // 作品追加ボタンのクリックイベント
        // 別ページで追加できるようにした
        // document.getElementById('add_work').addEventListener('click', function() {
        //     let newWorkTitle = document.getElementById('new_work').value;
        //     if (newWorkTitle) {
        //         let option = document.createElement('option');
        //         option.value = newWorkTitle;
        //         option.textContent = newWorkTitle;
        //         document.getElementById('work_id').appendChild(option);
        //         document.getElementById('new_work').value = '';
        //     }
        // });

        let map;
        let marker;
        let viewMarker;

        // Google Mapの初期化と住所入力欄の自動補完
        window.initMap = function() {
            const map = new google.maps.Map(document.getElementById('main_map'), {
                center: {
                    lat: 35.6809591,
                    lng: 139.7673068
                },
                zoom: 12
            });

            const viewMap = new google.maps.Map(document.getElementById('view_map'), {
                center: {
                    lat: 35.6809591,
                    lng: 139.7673068
                },
                zoom: 12
            });
            const input = document.getElementById('address');
            const autocomplete = new google.maps.places.Autocomplete(input);

            const viewInput = document.getElementById('view_address');
            const viewAutocomplete = new google.maps.places.Autocomplete(viewInput);



            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    console.log("No details available for input: '" + place.name + "'");
                    return;
                }

                map.setCenter(place.geometry.location);
                map.setZoom(17);

                if (marker) {
                    marker.setMap(null);
                }
                marker = new google.maps.Marker({
                    map: map,
                    position: place.geometry.location,
                    draggable: true
                });

                document.getElementById('main_latitude').value = place.geometry.location.lat();
                document.getElementById('main_longitude').value = place.geometry.location.lng();

                // マーカーのドラッグ終了時に住所を更新
                google.maps.event.addListener(marker, 'dragend', function(evt) {
                    document.getElementById('main_latitude').value = evt.latLng.lat();
                    document.getElementById('main_longitude').value = evt.latLng.lng();

                    // 座標から住所を取得
                    const geocoder = new google.maps.Geocoder();
                    geocoder.geocode({
                        'latLng': evt.latLng
                    }, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results) {
                                document.getElementById('address').value = results.formatted_address;
                            }
                        }
                    });
                });
            });

            viewAutocomplete.addListener('place_changed', function() {
                const place = viewAutocomplete.getPlace();
                if (!place.geometry) {
                    console.log("No details available for input: '" + place.name + "'");
                    return;
                }

                viewMap.setCenter(place.geometry.location);
                viewMap.setZoom(17);

                if (viewMarker) {
                    viewMarker.setMap(null);
                }
                viewMarker = new google.maps.Marker({
                    map: viewMap,
                    position: place.geometry.location,
                    draggable: true
                });

                document.getElementById('view_latitude').value = place.geometry.location.lat();
                document.getElementById('view_longitude').value = place.geometry.location.lng();

                // マーカーのドラッグ終了時に住所を更新
                google.maps.event.addListener(viewMarker, 'dragend', function(evt) {
                    document.getElementById('view_latitude').value = evt.latLng.lat();
                    document.getElementById('view_longitude').value = evt.latLng.lng();

                    // 座標から住所を取得
                    const geocoder = new google.maps.Geocoder();
                    geocoder.geocode({
                        'latLng': evt.latLng
                    }, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results) {
                                document.getElementById('view_address').value = results.formatted_address;
                            }
                        }
                    });
                });
            });
        }

        function geocodeAddress(address, callback) {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                'address': address
            }, function(results, status) {
                if (status === 'OK') {
                    callback(results[0].geometry.location);
                } else {
                    alert('Geocode was not successful for the following reason: ' + status);
                }
            });
        }

        // 住所入力欄の値が変更されたときに座標を取得
        document.getElementById('address').addEventListener('change', function() {
            const address = this.value;
            geocodeAddress(address, function(location) {
                document.getElementById('latitude').value = location.lat();
                document.getElementById('longitude').value = location.lng();
            });
        });

        // マーカーのドラッグ終了時に住所を更新
        google.maps.event.addListener(marker, 'dragend', function(evt) {
            document.getElementById('main_latitude').value = evt.latLng.lat();
            document.getElementById('main_longitude').value = evt.latLng.lng();

            // 座標から住所を取得
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                'latLng': evt.latLng
            }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results) {
                        document.getElementById('address').value = results.formatted_address;
                    }
                }
            });
        });
    </script>


</body>

</html>