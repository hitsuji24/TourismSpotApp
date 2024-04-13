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
            <select name="category" required>
                <option value="">選択してください</option>
                <option value="1">カテゴリー1</option>
                <option value="2">カテゴリー2</option>
                <!-- 他のカテゴリーを追加 -->
            </select>
        </div>
        <div>
            <label for="main_latitude">聖地の緯度：</label>
            <input type="text" name="main_latitude" id="main_latitude" pattern="^-?([1-8]?[1-9]|[1-9]0)\.{1}\d{1,6}" required>
        </div>
        <div>
            <label for="main_longitude">聖地の経度：</label>
            <input type="text" name="main_longitude" id="main_longitude" pattern="^-?((1?[0-7]?|[1-9]?)[0-9]|180)\\.{1}\\d{1,6}$" required>
        </div>
        <div>
            <label for="view_latitude">視点の緯度：</label>
            <input type="text" name="view_latitude" id="view_latitude" pattern="^-?([1-8]?[1-9]|[1-9]0)\.{1}\d{1,6}">
        </div>
        <div>
            <label for="view_longitude">視点の経度：</label>
            <input type="text" name="view_longitude" id="view_longitude" pattern="^-?((1?[0-7]?|[1-9]?)[0-9]|180)\\.{1}\\d{1,6}$">
        </div>
        <div>
            <label>
                <input type="checkbox" name="view_unknown" id="view_unknown">
                視点位置不明
            </label>
        </div>
        <div>
            <label for="work_id">作品：</label>
            <select name="work_id" required>
                <option value="">選択してください</option>
                <!-- PHPを使って作品一覧を取得し、オプションを生成 -->
                <?php
                // 作品一覧を取得するコードをここに記述
                // 例: <option value="作品ID">作品名</option>
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

    <script>
        // 視点位置不明のチェックボックスの状態に応じて、視点の緯度と経度の入力欄を有効/無効化
        const viewUnknownCheckbox = document.getElementById('view_unknown');
        const viewLatitudeInput = document.getElementById('view_latitude');
        const viewLongitudeInput = document.getElementById('view_longitude');

        viewUnknownCheckbox.addEventListener('change', function() {
            if (this.checked) {
                viewLatitudeInput.disabled = true;
                viewLongitudeInput.disabled = true;
            } else {
                viewLatitudeInput.disabled = false;
                viewLongitudeInput.disabled = false;
            }
        });
    </script>
</body>
</html>
