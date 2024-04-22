<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>作品追加</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h4>作品追加</h4>
    <form action="work_add_act.php" method="post" enctype="multipart/form-data">
        <div>
            <label for="title">作品名：</label>
            <input type="text" name="title" required>
            <!-- すでに同じ作品名が登録されている場合はアラートが出るようにしたい -->
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
        <div>
            <label for="release_date">公開日：</label>
            <input type="date" name="release_date" required>
        </div>
        <div>
            <label for="thumbnail">サムネイル画像：</label>
            <input type="file" name="thumbnail" accept="image/*">
        </div>
        <button type="submit">登録</button>
    </form>

    <nav class="bottom-nav">
        <a href="index.php" class="nav-item">
            <i class="fas fa-home"></i>
            <span>ホーム</span>
        </a>
        <a href="works.php" class="nav-item">
            <i class="fas fa-film"></i>
            <span>作品</span>
        </a>
        <a href="spot.php" class="nav-item">
            <i class="fas fa-map-marker-alt"></i>
            <span>スポット</span>
        </a>
        <a href="mylist.php" class="nav-item">
            <i class="fas fa-heart"></i>
            <span>マイリスト</span>
        </a>

    </nav>
</body>
</html>
