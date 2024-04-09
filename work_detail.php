<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>作品詳細</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    //DB接続
    include("funcs.php");
    $pdo = db_conn();

    $work_id = $_GET['id'];

    $sql = "SELECT * FROM works WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$work_id]);
    $work = $stmt->fetch(PDO::FETCH_ASSOC);
    $thumbnail_path = !empty($work['thumbnail_path']) ? $work['thumbnail_path'] : 'img/works/no-image.jpg';
    ?>



    <h1><?php echo $work['title']; ?></h1>
    <img src="<?php echo $work['thumbnail_path']; ?>" alt="<?php echo $work['title']; ?>">
    <p><?php echo $work['description']; ?></p>
    <p>カテゴリー: <?php echo $work['category']; ?></p>
    <p>リリース日: <?php echo $work['release_date']; ?></p>

    <h2>関連スポット</h2>
    <?php
    $sql = "SELECT spots.* FROM spots 
            INNER JOIN spot_work ON spots.id = spot_work.spot_id
            WHERE spot_work.work_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$work_id]);
    $spots = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($spots as $spot) {
        echo '<div class="spot">';
        echo '<h3>' . $spot['name'] . '</h3>';
        echo '<p>' . $spot['description'] . '</p>';
        echo '<p>住所: ' . $spot['address'] . '</p>';
        echo '</div>';
    }
    ?>
</body>

</html>