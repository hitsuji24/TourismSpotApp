<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>スポット追加</title>
    <link rel="stylesheet" href="style.css">
    <?php require 'config/config_googlemap.php'; ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&libraries=places&callback=initMap" async defer></script>
</head>

<body>
    <h1>スポット追加</h1>
    <form action="spot_add_act_simple.php" method="post" enctype="multipart/form-data">
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
            <!-- 座標入力フィールド -->
            <div id="main_coordinates-input">
                <input type="text" id="main_coordinates" name="main_coordinates" placeholder="緯度, 経度">
            </div>
        </div>


        <div class="view_location">
            <label for="view_address">再現位置（任意）</label>
            <!-- 座標入力フィールド -->
            <div id="view_coordinates-input">
                <input type="text" id="view_coordinates" name="view_coordinates" placeholder="緯度, 経度">
            </div>
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
        // 住所と座標の排他制御
        const mainAddressInput = document.getElementById('main_address');
        const mainCoordinatesInput = document.getElementById('main_coordinates');
        const viewAddressInput = document.getElementById('view_address');
        const viewCoordinatesInput = document.getElementById('view_coordinates');

        mainAddressInput.addEventListener('input', function() {
            if (mainAddressInput.value) {
                mainCoordinatesInput.disabled = true;
            } else {
                mainCoordinatesInput.disabled = false;
            }
        });

        mainCoordinatesInput.addEventListener('input', function() {
            if (mainCoordinatesInput.value) {
                mainAddressInput.disabled = true;
            } else {
                mainAddressInput.disabled = false;
            }
        });

        viewAddressInput.addEventListener('input', function() {
            if (viewAddressInput.value) {
                viewCoordinatesInput.disabled = true;
            } else {
                viewCoordinatesInput.disabled = false;
            }
        });

        viewCoordinatesInput.addEventListener('input', function() {
            if (viewCoordinatesInput.value) {
                viewAddressInput.disabled = true;
            } else {
                viewAddressInput.disabled = false;
            }
        });

       
      
    </script>
</body>

</html>