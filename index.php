<?php
session_start();

// ログイン状態のチェック
if (isset($_SESSION["chk_ssid"]) && $_SESSION["chk_ssid"] == session_id()) {
    $isLoggedIn = true;
    $userName = $_SESSION["name"];
} else {
    $isLoggedIn = false;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Vamilu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <link rel="stylesheet" type="text/css" href="https://coco-factory.jp/ugokuweb/wp-content/themes/ugokuweb/data/reset.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
    <link rel="stylesheet" type="text/css" href="https://coco-factory.jp/ugokuweb/wp-content/themes/ugokuweb/data/6-1-6/css/6-1-6.css">
</head>

<body>
    <header>
        <h2>Vamilu</h2>

        <div id="user-info">
            <?php if ($isLoggedIn) : ?>
                <p>ようこそ、<?= htmlspecialchars($userName) ?>さん</p>
                <p>訪れたスポット数:
                    <?php
                    // 訪れたスポット数を取得するDBクエリを実行
                    include("funcs.php");
                    $pdo = db_conn();
                    if (isset($_SESSION["user_id"])) { // 追加
                        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM user_spots WHERE user_id = :user_id");
                        $stmt->bindValue(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
                        $stmt->execute();
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        echo $result["count"];
                    } else {
                        echo "0"; // デフォルトで0を表示
                    }
                    ?>
                </p>
                <p><a href="logout.php">ログアウト</a></p>
            
            <?php else : ?>
                <a id="button-login" href="login.php">ログイン</a>
                <a id="button-signup" href="signup.php">新規登録</a>
            <?php endif; ?>
        </div>
    </header>




    <body>

        <!-- 特集を横カルーセルで表示 -->
        <div class="features">
            <H3>特集</H3>
            <ul class="slider">
                <li><img src="img/feature-tate/1.jpg" alt=""></li>
                <li><img src="img/feature-tate/2.jpg" alt=""></li>
                <li><img src="img/feature-tate/3.jpg" alt=""></li>
                <li><img src="img/feature-tate/4.jpg" alt=""></li>
            </ul>
        </div>

        <nav class="bottom-nav">
            <a href="index.php" class="nav-item active">
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

        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
        <script src="js/main.js"></script>
    </body>

</body>

</html>