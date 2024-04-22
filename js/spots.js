var latitude = 0;
var longitude = 0;
var map;
var markers = [];

function initMap() {
    // Google Maps APIが読み込まれたら、マップ関連の処理を実行
    $(document).ready(function () {
        // 現在の位置情報を取得
        navigator.geolocation.getCurrentPosition(function (position) {
            // 現在地の緯度経度を取得
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;

            // 現在地を中心にマップを表示
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: lat, lng: lng },
                zoom: 12
            });

            // スポット一覧を取得して表示
            getSpots();
        });
    });
}

// グローバルスコープでinitMap関数を定義
window.initMap = initMap;

$(function () {

    // 検索フォームの送信イベント
    $('#search-form').submit(function (event) {
        event.preventDefault();

        var keyword = $('#keyword').val();
        var sort = $('#sort').val();
        var category = $('#category').val();

        $.ajax({
            url: 'search_spots.php',
            type: 'GET',
            data: {
                keyword: keyword,
                sort: sort,
                category: category,
                latitude: latitude,
                longitude: longitude
            },
            dataType: 'json'
        })
            .done(function (data) {
                $('#spot-list').html(data.list);
                updateMap(data.spots);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('検索に失敗しました:', textStatus, errorThrown);
                alert('検索に失敗しました');
            });
    });

      // 並び順とカテゴリの変更イベント
      $('#sort, #category').change(function () {
        var keyword = $('#keyword').val();
        var sort = $('#sort').val();
        var category = $('#category').val();

        $.ajax({
            url: 'search_spots.php',
            type: 'GET',
            data: {
                keyword: keyword,
                sort: sort,
                category: category,
                latitude: latitude,
                longitude: longitude
            },
            dataType: 'json'
        })
            .done(function (data) {
                $('#spot-list').html(data.list);
                updateMap(data.spots);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('検索に失敗しました:', textStatus, errorThrown);
                alert('検索に失敗しました');
            });
    });
   
    // 初期表示時に検索を実行
    $('#search-form').submit();

    // ユーザーの現在位置を取得
    navigator.geolocation.getCurrentPosition(function (position) {
        latitude = position.coords.latitude;
        longitude = position.coords.longitude;
    });

    // 表示切替ボタンのクリックイベント
    //#list-view-btnがクリックされたら、#spot-listからhideクラスを削除し、#mapにhideクラス追加 → リストが表示され、マップが非表示になる
    //#map-view-btnがクリックされたら、逆に#spot-listにhideクラスを追加し、#mapからhideクラスを削除 → マップが表示され、リストが非表示になる
    $('#list-view-btn').click(function () {
        console.log("リスト表示ボタンがクリックされました");
        console.log("spot-listのクラス:", $('#spot-list').attr('class'));
        console.log("mapのクラス:", $('#map').attr('class'));
        $('#spot-list').removeClass('hide').addClass('show');
        $('#map').removeClass('show').addClass('hide');
        console.log("spot-listのクラス（変更後）:", $('#spot-list').attr('class'));
        console.log("mapのクラス（変更後）:", $('#map').attr('class'));
    });

    $('#map-view-btn').click(function () {
        console.log("マップ表示ボタンがクリックされました");
        console.log("spot-listのクラス:", $('#spot-list').attr('class'));
        console.log("mapのクラス:", $('#map').attr('class'));
        $('#spot-list').removeClass('show').addClass('hide');
        $('#map').removeClass('hide').addClass('show');
        console.log("spot-listのクラス（変更後）:", $('#spot-list').attr('class'));
        console.log("mapのクラス（変更後）:", $('#map').attr('class'));
    });
});

function updateMap(spots) {
    // マーカーを削除
    markers.forEach(function (marker) {
        marker.setMap(null);
    });
    markers = [];

    // スポットをマップ上に表示
    spots.forEach(function (spot) {
        var marker = new google.maps.Marker({
            map: map,
            //数値に変換 parseFloatでエラー解消できた
            position: { lat: parseFloat(spot.main_latitude), lng: parseFloat(spot.main_longitude) },
            title: spot.name
        });
        markers.push(marker);
    });
}
