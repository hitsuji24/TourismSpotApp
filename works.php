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
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
    <link rel="stylesheet" type="text/css"
        href="https://coco-factory.jp/ugokuweb/wp-content/themes/ugokuweb/data/6-1-6/css/6-1-6.css">


</head>
<body>

<h1>作品一覧</h1>
    
    <!-- 検索フォーム -->
    <form id="search-form">
        <input type="text" name="keyword" id="keyword" placeholder="キーワードを入力">
        <select name="sort" id="sort">
            <option value="release_date">リリース日順</option>
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


    <!-- コンテンツ -->
    <div id="works-list"></div>
    
    <script src="js/works.js"></script>

    <!-- ボトムナビ -->
    <nav class="bottom-nav">
        <a href="index.html" class="nav-item">
            <i class="fas fa-home"></i>
            <span>ホーム</span>
        </a>
        <a href="works.php" class="nav-item active">
            <i class="fas fa-film"></i>
            <span>作品</span>
        </a>
        <a href="spot.php" class="nav-item">
            <i class="fas fa-map-marker-alt"></i>
            <span>スポット</span>
        </a>
        <a href="mylist.html" class="nav-item">
            <i class="fas fa-heart"></i>
            <span>マイリスト</span>
        </a>
        <a href="announce.html" class="nav-item">
            <i class="fas fa-bell"></i>
            <span>お知らせ</span>
        </a>
    </nav>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/6-1-6.js"></script>

</body>
</html>