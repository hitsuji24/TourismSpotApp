<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Vamilu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <link rel="stylesheet" type="text/css"
        href="https://coco-factory.jp/ugokuweb/wp-content/themes/ugokuweb/data/reset.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
    <link rel="stylesheet" type="text/css"
        href="https://coco-factory.jp/ugokuweb/wp-content/themes/ugokuweb/data/6-1-6/css/6-1-6.css">


</head>
<body>
<?php

include("funcs.php");
$pdo = db_conn(); 

// セッション開始
session_start();

// ログインしていない場合はログインページにリダイレクト
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION["user_id"];

// お気に入り登録したスポットを取得するDBクエリを実行
$stmt = $pdo->prepare("SELECT spots.* FROM favorite_spots INNER JOIN spots ON favorite_spots.spot_id = spots.id WHERE favorite_spots.user_id = :user_id");
$stmt->bindValue(":user_id", $userId, PDO::PARAM_INT);
$stmt->execute();
$favoriteSpots = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <div class="pageTitle">
<h2>お気に入りスポット</h2>
</div>
<ul>
    <?php foreach ($favoriteSpots as $spot) : ?>
        <li><a href="detail.php?id=<?= $spot['id'] ?>"><?= htmlspecialchars($spot['name']) ?></a></li>
    <?php endforeach; ?>
</ul>

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
            <a href="mylist.php" class="nav-item active">
                <i class="fas fa-heart"></i>
                <span>マイリスト</span>
            </a>
        </nav>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="main.js"></script>
    <script src="js/6-1-6.js"></script>

</body>
</html>