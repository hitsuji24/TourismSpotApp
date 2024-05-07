<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Vamilu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <?php require 'config/config_googlemap.php'; ?>
    <link rel="stylesheet" type="text/css" href="https://coco-factory.jp/ugokuweb/wp-content/themes/ugokuweb/data/reset.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&callback=initMap" async defer></script>
</head>

<body>
<div class="pageTitle">
    <h2>スポット一覧</h2>
    </div>

    <!-- 検索フォーム -->
    <form id="search-form">

        <div class="search_container">
            <input type="text" name="keyword" id="keyword" size="25" placeholder="キーワード検索">
            <input type="submit" value="&#xf002;">
        </div>

        <div class="sort-select">
            <label class="selectbox">
                <select name="sort" id="sort">
                    <option value="created_at">登録日順</option>
                    <option value="distance">距離順</option>
                </select>
            </label>

            <label class="selectbox">
                <select name="category" id="category">
                    <option value="">すべて</option>
                    <option value="1">アニメ</option>
                    <option value="2">漫画</option>
                    <option value="3">映画</option>
                    <option value="4">アート</option>
                    <option value="5">歴史</option>
                    <option value="6">その他</option>
                </select>
            </label>
        </div>
    </form>

    <!-- 表示切り替えボタン -->
    <div id="view-switch">
    <button id="list-view-btn" class="view-btn active">リスト表示</button>
    <button id="map-view-btn" class="view-btn">マップ表示</button>
</div>

    <!-- コンテンツ -->
    <div id="content">
        <div id="spot-list" class="show"></div>
        <div id="map" class="hide"></div>
    </div>

    <!-- スポット追加ボタン -->
    <button class="add-spot-button" onclick="location.href='spot_add.php'">
        <i class="fas fa-plus"></i>
    </button>

    <!-- ボトムナビ -->
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

    <script src="js/spots.js"></script>

</body>

</html>