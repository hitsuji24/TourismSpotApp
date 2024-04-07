<?php

session_start();

try {
    //1.  DB接続します
    include("funcs.php");
    $pdo = db_conn();

    // 検索条件の取得
    // POST配列のキーの存在をチェック→存在しない場合はデフォルト値を設定
    $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $sort = isset($_POST['sort']) ? $_POST['sort'] : '';
    // $keyword = $_POST['keyword'];
    // $category = $_POST['category'];
    // $sort = $_POST['sort'];

    // SQLクエリの構築
    $sql = "SELECT * FROM spots";
    $conditions = [];

    if (!empty($keyword)) {
        $conditions[] = "(name LIKE :keyword OR description LIKE :keyword OR address LIKE :keyword)";
    }

    if (!empty($category)) {
        $conditions[] = "category = :category";
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY " . $sort;

    // プリペアドステートメントの準備
    $stmt = $pdo->prepare($sql);

    // プレースホルダへの値のバインド
    if ($keyword) {
        $stmt->bindValue(':keyword', '%' . $keyword . '%');
    }

    if ($category) {
        $stmt->bindValue(':category', $category);
    }

    // クエリの実行
    $stmt->execute();

    // 検索結果の取得
    $spots = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 現在地からの距離順の場合の処理
    if ($sort === 'distance') {
        // ユーザーの現在地の緯度経度を取得 isset関数で値があるかどうかを確認
        $userLat = isset($_POST['userLat']) ? $_POST['userLat'] : '';
        $userLon = isset($_POST['userLon']) ? $_POST['userLon'] : '';

        // 距離計算関数
        function calculateDistance($lat1, $lon1, $lat2, $lon2)
        {
            $earthRadius = 6371; // 地球の半径（km）
            $dLat = deg2rad($lat2 - $lat1);
            $dLon = deg2rad($lon2 - $lon1);
            $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $distance = $earthRadius * $c;
            return $distance;
        }

        // 距離計算カラムをSELECT句に追加
        $sql = "SELECT *, ( 6371 * acos(cos(radians(:userLat)) * cos(radians(main_latitude)) * cos(radians(main_longitude) - radians(:userLon)) + sin(radians(:userLat)) * sin(radians(main_latitude))) ) AS distance FROM spots";

        // 距離の昇順でソート
        $sql .= " ORDER BY distance";

        // プリペアドステートメントの準備
        $stmt = $pdo->prepare($sql);

        // プレースホルダへの値のバインド
        $stmt->bindValue(':userLat', $userLat);
        $stmt->bindValue(':userLon', $userLon);

        // クエリの実行
        $stmt->execute();

        // 検索結果の取得
        $spots = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 検索結果のHTML生成
    $result = '';
    foreach ($spots as $spot) {
        $result .= '<div class="spot">';
        $result .= '<h2>' . $spot['name'] . '</h2>';
        $result .= '<p>カテゴリ: ' . $spot['category'] . '</p>';
        $result .= '<p>住所: ' . $spot['address'] . '</p>';
        $result .= '<p>現在地からの距離: 約' . round($spot['distance'], 2) . ' km</p>';
        $result .= '<p>登録日: ' . $spot['created_at'] . '</p>';
        $result .= '</div>';
    }

    // 検索結果の返却
    echo $result;
} catch (PDOException $e) {
    // エラーハンドリング
    $errorMessage = 'データベースエラーが発生しました。' . $e->getMessage();
    error_log($errorMessage);
    echo '<div id="error-message">' . $errorMessage . '</div>';
}
