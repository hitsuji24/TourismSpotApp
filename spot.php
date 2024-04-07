<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>THE spot</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <link rel="stylesheet" type="text/css" href="https://coco-factory.jp/ugokuweb/wp-content/themes/ugokuweb/data/reset.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
    <link rel="stylesheet" type="text/css" href="https://coco-factory.jp/ugokuweb/wp-content/themes/ugokuweb/data/6-1-6/css/6-1-6.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body>
    <form id="search-form">
        <input type="text" id="keyword" placeholder="キーワード">
        <select id="category">
            <option value="">カテゴリを選択</option>
            <option value="アニメ">アニメ</option>
            <option value="漫画">漫画</option>
            <option value="映画">映画</option>
            <option value="アート">アート</option>
            <option value="歴史">歴史</option>
            <option value="その他">その他</option>
        </select>
        <select id="sort">
            <option value="distance">現在地からの距離順</option>
            <option value="created_at">登録日順</option>
        </select>
        <button type="submit">検索</button>
    </form>



    <script>
        //  navigator.geolocation.getCurrentPosition()をPromiseでラップし、非同期処理を扱いやすくしています。
        // 位置情報の取得に失敗した場合のエラーハンドリングを行っています。エラーが発生した場合は、userLatとuserLonにnullを設定しています。
        // 位置情報の取得が完了してから、Ajax通信を実行するようにしています。
        $(document).ready(function() {
            $('#search-form').submit(function(event) {
                event.preventDefault();

                var keyword = $('#keyword').val();
                var category = $('#category').val();
                var sort = $('#sort').val();

                // 位置情報の取得
                var userLocation = new Promise(function(resolve, reject) {
                    navigator.geolocation.getCurrentPosition(resolve, reject, {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    });
                });

                userLocation
                    .then(function(position) {
                        var userLat = position.coords.latitude;
                        var userLon = position.coords.longitude;
                        return {
                            userLat: userLat,
                            userLon: userLon
                        };
                    })
                    .catch(function(error) {
                        console.warn('位置情報の取得に失敗しました。', error.message);
                        $('#error-message').text('位置情報の取得に失敗しました。' + error.message);
                        return {
                            userLat: null,
                            userLon: null
                        };
                    })
                    .then(function(location) {
                        $.ajax({
                            url: 'search.php',
                            type: 'POST',
                            data: {
                                keyword: keyword,
                                category: category,
                                sort: sort,
                                userLat: location.userLat,
                                userLon: location.userLon
                            },
                            success: function(response) {
                                $('#spot-list').html(response);
                            },
                            error: function(xhr, status, error) {
                                console.error('検索リクエストに失敗しました。', error);
                                $('#error-message').text('検索リクエストに失敗しました。' + error);
                            }
                        });
                    });
            });
        });
    </script>

    <div id="spot-list">
        <?php

        include("funcs.php");
        // データベースから全スポットを取得
        $userLat = $_POST['userLat'] ?? null;
        $userLon = $_POST['userLon'] ?? null;
        $spots = getAllSpots($userLat, $userLon);

        foreach ($spots as $spot) {
            echo '<div class="spot">';
            echo '<h2>' . htmlspecialchars($spot['name']) . '</h2>';
            echo '<p>カテゴリ: ' . htmlspecialchars($spot['category']) . '</p>';
            echo '<p>住所: ' . htmlspecialchars($spot['address']) . '</p>';
            echo '<p>登録日: ' . htmlspecialchars($spot['created_at']) . '</p>';
            echo '</div>';
        }
        ?>
    </div>
    <nav class="bottom-nav">
        <a href="index.html" class="nav-item">
            <i class="fas fa-home"></i>
            <span>ホーム</span>
        </a>
        <a href="works.html" class="nav-item">
            <i class="fas fa-film"></i>
            <span>作品</span>
        </a>
        <a href="spot.html" class="nav-item active">
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

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="main.js"></script>
</body>

</html>