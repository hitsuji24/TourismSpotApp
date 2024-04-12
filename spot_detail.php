<?php
include("funcs.php");

if (isset($_GET['id'])) {
    $spot_id = $_GET['id'];

    try {
        $pdo = db_conn();
        $stmt = $pdo->prepare("SELECT * FROM spots WHERE id = :id");
        $stmt->bindValue(':id', $spot_id, PDO::PARAM_INT);
        $stmt->execute();
        $spot = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$spot) {
            echo "指定されたスポットが見つかりません。";
            exit;
        }
    } catch (PDOException $e) {
        echo "データベースエラー: " . $e->getMessage();
        exit;
    }
} else {
    echo "スポットIDが指定されていません。";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title><?= h($spot['name']) ?> - アニメ聖地スポット</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?php require 'config/config_googlemap.php'; ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&callback=initMap" async defer></script>
</head>

<body>
    <h1><?= h($spot['name']) ?></h1>
    <div id="spot-detail">
        <div id="spot-images">
            <!-- スポット画像のスライダーを表示 -->
            <img src="<?= h($spot['ar_image_url']) ?>" alt="<?= h($spot['name']) ?>">
        </div>
        <div id="spot-info">
            <p><?= h($spot['description']) ?></p>
            <p>カテゴリー: <?= h($spot['category']) ?></p>
            <p>住所: <?= h($spot['address']) ?></p>
        </div>

        <!-- お気に入り登録ボタン -->
        <form action="favorite.php" method="post">
            <input type="hidden" name="spot_id" value="<?= $spot['id'] ?>">
            <button type="submit">お気に入り登録</button>
        </form>

        <!-- AR表示ボタン -->
        <div class="arView">
            <a href="ar_view.php?id=<?= $spot['id'] ?>">AR表示</a>
        </div>

        <!-- 行ったよボタン -->
        <form action="visit.php" method="post">
            <input type="hidden" name="spot_id" value="<?= $spot['id'] ?>">
            <button type="submit">行った</button>
        </form>


        <div id="spot-map">
            <!-- スポットの位置をGoogle Mapで表示 -->
            <div id="map"></div>
        </div>
        <div id="related-spots">
            <!-- 関連スポットを表示 -->
            <?php
            try {
                $stmt = $pdo->prepare("SELECT * FROM spots WHERE category = :category AND id != :id LIMIT 4");
                $stmt->bindValue(':category', $spot['category'], PDO::PARAM_STR);
                $stmt->bindValue(':id', $spot['id'], PDO::PARAM_INT);
                $stmt->execute();
                $related_spots = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "データベースエラー: " . $e->getMessage();
                exit;
            }
            ?>
            <h2>関連スポット</h2>
            <ul>
                <?php foreach ($related_spots as $related_spot) : ?>
                    <li>
                        <a href="spot_detail.php?id=<?= $related_spot['id'] ?>">
                            <img src="<?= h($related_spot['image_url']) ?>" alt="<?= h($related_spot['name']) ?>">
                            <p><?= h($related_spot['name']) ?></p>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script>
        function initMap() {
            var map = new google.maps.Map($('#spot-map #map')[0], {
                center: {
                    lat: <?= $spot['main_latitude'] ?>,
                    lng: <?= $spot['main_longitude'] ?>
                },
                zoom: 14
            });

            var marker = new google.maps.Marker({
                map: map,
                position: {
                    lat: <?= $spot['main_latitude'] ?>,
                    lng: <?= $spot['main_longitude'] ?>
                },
                title: '<?= h($spot['name']) ?>'
            });
        }
    </script>
</body>

</html>