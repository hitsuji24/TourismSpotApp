<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>スポット追加</title>
    <link rel="stylesheet" href="style.css">
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
                $pdo = db_conn();
                $stmt = $pdo->query("SELECT * FROM categories");
                while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $category['id'] . '">' . $category['name'] . '</option>';
                }
                ?>
            </select>
        </div>

        <!-- 聖地の緯度経度と視点の緯度経度を入力するフォーム 面倒なのでやめる-->

        <!-- 視点位置不明のチェックボックス -->
        <div>
            <label>
                <input type="checkbox" name="view_unknown" id="view_unknown">
                視点位置不明
            </label>
        </div>

        <!-- 住所入力欄 -->
        <input type="text" id="address" name="address" placeholder="住所を入力">

        <!-- Google Map -->
        <div id="map" style="height: 400px;"></div>

        <!-- 緯度経度の隠しフィールド -->
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">

        <!-- 「バミる」モードのチェックボックス -->
        <label>
            <input type="checkbox" name="vami_mode" value="1">
            「バミる」モードで登録する
        </label>

        <!-- PHPを使って作品一覧を取得し、オプションを生成 -->
        <!-- // 作品の申請をユーザーができるようにするか悩む 重複や不適切なものがあるかもしれない
                // 作品の追加は管理者が行うべきかもしれない
                // それか、APIなどで作品一覧を取得するようにするか -->
        <div>
            <label for="work_id">作品：</label>
            <select name="work_id" id="work_id" required>
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

        <div>
            <label for="image">画像：</label>
            <input type="file" name="image" accept="image/*" required>
        </div>
        <div id="image_preview"></div>
        <button type="submit">登録</button>
    </form>

    <?php require 'config/config_googlemap.php'; ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&libraries=places"></script>
    <script>
        // Google Mapの初期化と住所入力欄の自動補完
        function initMap() {
            const map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: 35.6809591,
                    lng: 139.7673068
                },
                zoom: 12
            });

            const input = document.getElementById('address');
            const autocomplete = new google.maps.places.Autocomplete(input);

            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    console.log("No details available for input: '" + place.name + "'");
                    return;
                }

                map.setCenter(place.geometry.location);
                map.setZoom(17);

                const marker = new google.maps.Marker({
                    map: map,
                    position: place.geometry.location
                });

                document.getElementById('latitude').value = place.geometry.location.lat();
                document.getElementById('longitude').value = place.geometry.location.lng();
            });
        }
    </script>
</body>

</html>