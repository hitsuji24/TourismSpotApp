<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>作品詳細</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    //DB接続
    include("funcs.php");
    $pdo = db_conn();
    $work_id = $_GET['id'];

    $sql = "SELECT w.*, c.name AS category_name, COUNT(sw.spot_id) AS spot_count
    FROM works w
    LEFT JOIN categories c ON w.category = c.id
    LEFT JOIN spot_work sw ON w.id = sw.work_id
    WHERE w.id = ?
    GROUP BY w.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$work_id]);
    $work = $stmt->fetch(PDO::FETCH_ASSOC);
    $thumbnail_path = !empty($work['thumbnail_path']) ? $work['thumbnail_path'] : 'img/works/no-image.png';
    ?>

    <div class="workThumbnail">
        <img src="<?php echo $work['thumbnail_path']; ?>" alt="<?php echo $work['title']; ?>">
    </div>

    <div class="workInfo">
        <h3><?php echo $work['title']; ?></h3>
        <div class="Tag">
            <p><?php echo $work['category_name']; ?></p>
            <p><?php echo $work['release_date']; ?></p>
        </div>
        <div class="description">
            <p><?php echo $work['description']; ?></p>
        </div>
    </div>

    <div class="relatedSpot">
        <h2>関連スポット</h2>
        <?php
        $sql = "SELECT spots.* FROM spots 
            INNER JOIN spot_work ON spots.id = spot_work.spot_id
            WHERE spot_work.work_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$work_id]);
        $spots = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($spots as $spot) {
            echo '<div class="spot-card">';
            echo '<img src="' . $spot['ar_image_url'] . '" alt="' . $spot['name'] . '">';
            echo '<div class="info">';
            echo '<h3>' . $spot['name'] . '</h3>';
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>
    <!-- ボトムナビ -->
    <nav class="bottom-nav">
        <a href="index.php" class="nav-item">
            <i class="fas fa-home"></i>
            <span>ホーム</span>
        </a>
        <a href="works.php" class="nav-item active">
            <i class="fas fa-film"></i>
            <span>コンテンツ</span>
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