<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>作品追加</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>作品追加</h1>
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
</body>
</html>
