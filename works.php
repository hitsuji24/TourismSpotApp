<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Vamilu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <link rel="stylesheet" type="text/css" href="https://coco-factory.jp/ugokuweb/wp-content/themes/ugokuweb/data/reset.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
    <link rel="stylesheet" type="text/css" href="https://coco-factory.jp/ugokuweb/wp-content/themes/ugokuweb/data/6-1-6/css/6-1-6.css">


</head>

<body>
    <div class="pageTitle">
    <h2>コンテンツ一覧</h2>
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
                    <option value="release_date_desc">年代が新しい順</option>
                    <option value="release_date_asc">年代が古い順</option>
                    <option value="title_asc">タイトル順</option>
                </select>
            </label>

            <label class="selectbox">
                <select class="selectbox" name="category" id="category">
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


    <!-- コンテンツ -->
    <div id="works-list"> </div>


    <!-- スポット追加ボタン -->
    <button type="button" class="add-spot-button" onclick="location.href='work_add.php'">
        <i class="fas fa-plus"></i>
    </button>

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

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/6-1-6.js"></script>
    <script src="js/works.js"></script>

</body>

</html>