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
    <title><?= h($spot['name']) ?> </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?php require 'config/config_googlemap.php'; ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&callback=initMap" async defer></script>
</head>

<body>
    <div id="spotDetail">
        <div class="spotImages">
            <!-- スポット画像のスライダーを表示 -->
            <img src="<?= h($spot['ar_image_url']) ?>" alt="<?= h($spot['name']) ?>">
        </div>

        <div class="relatedWorkInfo">
            <?php
            try {
                $stmt = $pdo->prepare("SELECT w.title, w.thumbnail_path, c.name AS category_name, w.id
                FROM spots s
                INNER JOIN spot_work sw ON s.id = sw.spot_id  
                INNER JOIN works w ON sw.work_id = w.id
                LEFT JOIN categories c ON w.category = c.id
                WHERE s.id = :id");
                $stmt->bindValue(':id', $spot['id'], PDO::PARAM_INT);
                $stmt->execute();
                $related_works = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "データベースエラー: " . $e->getMessage();
                exit;
            }
            ?>

            <?php if (count($related_works) > 0) : ?>

                <?php foreach ($related_works as $work) : ?>
                    <a href="work_detail.php?id=<?= $work['id'] ?>">
                        <div class="relWorkBanner">
                            <div class="relWorkThumbnail">
                                <img src="<?= h($work['thumbnail_path']) ?>" alt="<?= h($work['title']) ?>">
                            </div>
                            <div class="relWorkInfo">
                                <h4><?= h($work['title']) ?></h4>
                                <div class="relWorkTag">
                                    <p><?= h($work['category_name']) ?></p>
                                </div>
                            </div>
                            <div class="relWorkButton">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </div>
                    </a>

                <?php endforeach; ?>

            <?php else : ?>
                <p>関連作品が見つかりませんでした。</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="spotInfo">
        <h2><?= h($spot['name']) ?></h2>
        <p><?= h($spot['description']) ?></p>
        <p>所在地： <?= h($spot['main_address']) ?></p>
    </div>


    <div class="spotButtons">
        <!-- ARボタン -->
        <!-- 視点の座標が登録されているかどうかに応じて、AR表示のボタンとバミるモードのボタンを切り替える 一旦削除 ARのみ -->
        <div id="arViewButton">
            <button class="action-button" onclick="location.href=" ar_view.php?id=<?= $spot['id'] ?>""><i class="far fa-eye"></i> AR表示</button>
        </div>

        <!-- お気に入り登録ボタン -->
        <div class="iconButton">
            <form action="favorite.php" method="post">
                <input type="hidden" name="spot_id" value="<?= $spot['id'] ?>">
                <button type="submit"><i class="far fa-heart"></i></button>
            </form>
        </div>

        <!-- 行ったよボタン -->
        <div class="iconButton">
            <form action="visit.php" method="post">
                <input type="hidden" name="spot_id" value="<?= $spot['id'] ?>">
                <button type="submit"><i class="far fa-flag"></i></button>
            </form>
        </div>
    </div>

    <div id="spot-map">
        <!-- スポットの位置をGoogle Mapで表示 -->
        <div id="spot-map-view"></div>
    </div>
    <div class="relatedSpot">
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
                    <div class="spot-card">
                        <a href="spot_detail.php?id=<?= $related_spot['id'] ?>">
                            <img src="<?= h($related_spot['ar_image_url']) ?>" alt="<?= h($related_spot['name']) ?>">
                            <div class="info">
                                <h3><?= h($related_spot['name']) ?></h3>
                            </div>
                        </a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    </div>

    <nav class="bottom-nav">
        <a href="index.php" class="nav-item">
            <i class="fas fa-home"></i>
            <span>ホーム</span>
        </a>
        <a href="works.php" class="nav-item">
            <i class="fas fa-film"></i>
            <span>コンテンツ</span>
        </a>
        <a href="spot.php" class="nav-item active">
            <i class="fas fa-map-marker-alt"></i>
            <span>スポット</span>
        </a>
        <a href="mylist.php" class="nav-item">
            <i class="fas fa-heart"></i>
            <span>マイリスト</span>
        </a>

    </nav>

    <script>
        function initMap() {
            var map = new google.maps.Map($('#spot-map #spot-map-view')[0], {
                center: {
                    lat: parseFloat('<?= $spot['main_latitude'] ?>'),
                    lng: parseFloat('<?= $spot['main_longitude'] ?>')
                },
                zoom: 14
            });

            var marker = new google.maps.Marker({
                map: map,
                position: {
                    lat: parseFloat('<?= $spot['main_latitude'] ?>'),
                    lng: parseFloat('<?= $spot['main_longitude'] ?>')
                },
                title: '<?= h($spot['name']) ?>'
            });
        }
    </script>
</body>

</html>