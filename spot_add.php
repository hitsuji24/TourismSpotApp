<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>VAMILU_スポット登録</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php require 'config/config_googlemap.php'; ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&callback=initMap" async defer></script>
</head>

<body>
    <div class="pageTitle">
        <h2>スポット追加</h2>
    </div>

    <!-- 必須マークつけたかったらこれ→<span class="required">*</span> -->
    <form id="spotForm" action="spot_add_act.php" method="post" enctype="multipart/form-data">
        <div> <label for="name">スポット名：</label><input type="text" name="name" required> </div>
        <div> <label for="description">説明文：</label><textarea name="description" rows="5" required></textarea> </div>
        <!-- <div> <label for="category">カテゴリー：<span class="required">*</span></label><select name="category" id="category" required>
                <option value="">選択してください</option>
                <?php include("funcs.php");
                $pdo = db_conn();
                $stmt = $pdo->query("SELECT * FROM categories");
                while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $category['id'] . '">' . $category['name'] . '</option>';
                } ?>
            </select> </div> -->

        <div class="mainLocation">

            <div>
                <label for="main_address">スポットの所在地</label>

                <div>
                    <label for="main_latitude">緯度:</label>
                    <input type="text" id="main_latitude" name="main_latitude">
                </div>
                <div>
                    <label for="main_longitude">経度:</label>
                    <input type="text" id="main_longitude" name="main_longitude">
                </div>
                <div id="main_map"></div>
                <!-- タイプボタンにするとバリデーションチェックが働かない -->
                <div class="searchOptionButtons">
                    <div class="searchByAddress">
                        <button type="button" id="show-search-address-main">住所で検索</button>
                        <div class="search-address main hidden">
                            <input type="text" id="main_address" name="main_address">
                            <button type="button" onclick="searchAddress('main')"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <div class="resetToCurrentLocation">
                        <button type="button" onclick="resetToCurrentLocation('main')"><i class="fas fa-redo"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="viewLocation">
            <!-- 視点位置不明の場合のチェックボックス必要 NULLで登録させる -->
            <div>
                <label for="view_address">バミリ位置</label>

                <div>
                    <label for="view_latitude">緯度:</label>
                    <input type="text" id="view_latitude" name="view_latitude">
                </div>
                <div>
                    <label for="view_longitude">経度:</label>
                    <input type="text" id="view_longitude" name="view_longitude">
                </div>
                <div id="view_map"></div>
                <div class="searchOptionButtons">
                    <div class="searchByAddress">
                        <button type="button" id="show-search-address-view">住所で検索</button>
                        <div class="search-address view hidden">
                            <input type="text" id="view_address" name="view_address">
                            <button type="button" onclick="searchAddress('view')"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <div class="resetToCurrentLocation">
                        <button type="button" onclick="resetToCurrentLocation('view')"><i class="fas fa-redo"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <label for="work_id">作品：</label>
            <select name="work_id" id="work_id">
                <option value="">選択してください</option>
                <?php
                $pdo = db_conn();
                $stmt = $pdo->query("SELECT * FROM works");
                while ($work = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $work['id'] . '">' . $work['title'] . '</option>';
                }
                ?>
            </select>
            <div class="">新しい作品を追加したい場合は、<a href="work_add.php">こちら</a></div>

        </div>

        <div>
            <label for="image">画像：</label>
            <input type="file" name="image" accept="image/*" required>
        </div>
        <div id="image-preview"></div>
        <div class="action-button">
            <button type="submit">登録</button>
        </div>
    </form>

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
        <a href="spot.php" class="nav-item">
            <i class="fas fa-map-marker-alt"></i>
            <span>スポット</span>
        </a>
        <a href="mylist.php" class="nav-item">
            <i class="fas fa-heart"></i>
            <span>マイリスト</span>
        </a>
    </nav>

    <script>
        let mainMap;
        let mainMarker;
        let mainCurrentLocationMarker;

        let viewMap;
        let viewMarker;
        let viewCurrentLocationMarker;

        function initMap() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    // メインの地図を初期化
                    mainMap = new google.maps.Map(document.getElementById('main_map'), {
                        center: pos,
                        zoom: 18
                    });
                    mainMarker = new google.maps.Marker({
                        position: pos,
                        map: mainMap,
                        draggable: true
                    });
                    document.getElementById('main_latitude').value = pos.lat;
                    document.getElementById('main_longitude').value = pos.lng;
                    mainMarker.addListener('dragend', function() {
                        const pos = mainMarker.getPosition();
                        document.getElementById('main_latitude').value = pos.lat();
                        document.getElementById('main_longitude').value = pos.lng();
                    });

                    // 視点用の地図を初期化
                    viewMap = new google.maps.Map(document.getElementById('view_map'), {
                        center: pos,
                        zoom: 18
                    });
                    viewMarker = new google.maps.Marker({
                        position: pos,
                        map: viewMap,
                        draggable: true
                    });
                    document.getElementById('view_latitude').value = pos.lat;
                    document.getElementById('view_longitude').value = pos.lng;
                    viewMarker.addListener('dragend', function() {
                        const pos = viewMarker.getPosition();
                        document.getElementById('view_latitude').value = pos.lat();
                        document.getElementById('view_longitude').value = pos.lng();
                    });
                }, function() {
                    alert('位置情報の取得に失敗しました。');
                });
            } else {
                alert('お使いのブラウザは位置情報に対応していません。');
            }
        }

        document.getElementById('show-search-address-main').addEventListener('click', function() {
            document.querySelector('.search-address.main').classList.toggle('hidden');
        });

        document.getElementById('show-search-address-view').addEventListener('click', function() {
            document.querySelector('.search-address.view').classList.toggle('hidden');
        });

        // function searchAddress(type) {
        //     const address = document.getElementById(type + '_address').value;
        //     const geocoder = new google.maps.Geocoder();
        //     geocoder.geocode({
        //         'address': address
        //     }, function(results, status) {
        //         if (status === 'OK') {
        //             const location = results[0].geometry.location;
        //             if (type === 'main') {
        //                 mainMap.setCenter(location);
        //                 mainMarker.setPosition(location);
        //                 document.getElementById('main_latitude').value = location.lat();
        //                 document.getElementById('main_longitude').value = location.lng();
        //             } else if (type === 'view') {
        //                 viewMap.setCenter(location);
        //                 viewMarker.setPosition(location);
        //                 document.getElementById('view_latitude').value = location.lat();
        //                 document.getElementById('view_longitude').value = location.lng();
        //             }
        //         } else {
        //             alert('住所から位置を特定できませんでした。');
        //         }
        //     });
        // }

        function searchAddress(type) {
            const addressInput = document.getElementById(type + '_address');
            const address = addressInput.value;
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                'address': address
            }, function(results, status) {
                if (status === 'OK') {
                    const location = results[0].geometry.location;
                    if (type === 'main') {
                        mainMap.setCenter(location);
                        mainMarker.setPosition(location);
                        document.getElementById('main_latitude').value = location.lat();
                        document.getElementById('main_longitude').value = location.lng();
                    } else if (type === 'view') {
                        viewMap.setCenter(location);
                        viewMarker.setPosition(location);
                        document.getElementById('view_latitude').value = location.lat();
                        document.getElementById('view_longitude').value = location.lng();
                    }
                } else {
                    alert('住所から位置を特定できませんでした。');
                }
            });
        }

        function resetToCurrentLocation(type) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    if (type === 'main') {
                        mainMap.setCenter(pos);
                        mainMarker.setPosition(pos);
                        document.getElementById('main_latitude').value = pos.lat;
                        document.getElementById('main_longitude').value = pos.lng;
                    } else if (type === 'view') {
                        viewMap.setCenter(pos);
                        viewMarker.setPosition(pos);
                        document.getElementById('view_latitude').value = pos.lat;
                        document.getElementById('view_longitude').value = pos.lng;
                    }
                }, function() {
                    alert('位置情報の取得に失敗しました。');
                });
            } else {
                alert('お使いのブラウザは位置情報に対応していません。');
            }
        }

        // document.addEventListener('DOMContentLoaded', function() {
        //     const form = document.getElementById('spotForm');

        //     form.addEventListener('submit', function(event) {
        //         event.preventDefault(); // フォームのデフォルトの送信動作を防ぐ

        //         const requiredFields = form.querySelectorAll('[required]');
        //         let isValid = true;

        //         requiredFields.forEach(function(field) {
        //             if (!field.value) {
        //                 isValid = false;
        //                 alert(field.previousElementSibling.textContent + 'は必須です。');
        //             }
        //         });

        //         if (isValid) {
        //             form.submit(); // すべてのバリデーションが通過した場合にフォームを送信
        //         }
        //     });
        // });
    </script>
</body>

</html>