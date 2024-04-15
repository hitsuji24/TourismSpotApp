<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>Vamilu</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <?php require 'config/config_googlemap.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&callback=initMap" async defer></script>
</head>

<body>
    <h1>アニメ聖地スポット一覧</h1>

    <!-- 検索フォーム -->
    <form id="search-form">
        <input type="text" name="keyword" id="keyword" placeholder="キーワードを入力">
        <select name="sort" id="sort">
            <option value="created_at">登録日順</option>
            <option value="distance">距離順</option>
        </select>
        <select name="category" id="category">
            <option value="">すべて</option>
            <option value="アニメ">アニメ</option>
            <option value="漫画">漫画</option>
            <option value="映画">映画</option>
            <option value="アート">アート</option>
            <option value="歴史">歴史</option>
            <option value="その他">その他</option>
        </select>
        <button type="submit">検索</button>
    </form>

    <!-- 表示切り替えボタン -->
    <div id="view-switch">
        <button id="list-view-btn">リスト表示</button>
        <button id="map-view-btn">マップ表示</button>
    </div>

    <!-- コンテンツ -->
    <div id="content">
        <div id="spot-list" class="show"></div>
        <div id="map" class="hide"></div>
    </div>

    <!-- スポット追加ボタン -->
    <button class="add-spot-button" onclick="location.href='spot_add_simple.php'">
        <i class="fas fa-plus"></i>
        <span>スポットを追加</span>
    </button>

    <!-- ボトムナビ -->
    <nav class="bottom-nav">
        <a href="index.php" class="nav-item <?php if (basename($_SERVER['PHP_SELF']) === 'index.php') echo 'active'; ?>">
            <i class="fas fa-home"></i>
            <span>ホーム</span>
        </a>
        <a href="works.php" class="nav-item <?php if (basename($_SERVER['PHP_SELF']) === 'works.php') echo 'active'; ?>">
            <i class="fas fa-film"></i>
            <span>作品</span>
        </a>
        <a href="spot.php" class="nav-item <?php if (basename($_SERVER['PHP_SELF']) === 'spot.php') echo 'active'; ?>">
            <i class="fas fa-map-marker-alt"></i>
            <span>スポット</span>
        </a>
        <a href="mylist.php" class="nav-item <?php if (basename($_SERVER['PHP_SELF']) === 'mylist.php') echo 'active'; ?>">
            <i class="fas fa-heart"></i>
            <span>マイリスト</span>
        </a>
    </nav>

    <script src="js/spots.js"></script>

</body>

</html>