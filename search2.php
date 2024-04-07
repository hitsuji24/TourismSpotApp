<?php
require_once 'funcs.php';

// 検索条件の取得
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// データベース接続
$pdo = db_conn();

// 検索クエリの作成
$sql = "SELECT * FROM spots";
$conditions = [];

if (!empty($keyword)) {
    $conditions[] = "(name LIKE '%$keyword%' OR description LIKE '%$keyword%' OR address LIKE '%$keyword%')";
}

if (!empty($category)) {
    $conditions[] = "category = '$category'";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

if ($sort === 'distance') {
    $sql .= " ORDER BY (POW(69.1 * (main_latitude - ?), 2) + POW(69.1 * (? - main_longitude) * COS(main_latitude / 57.3), 2))";
} else {
    $sql .= " ORDER BY created_at DESC";
}

$stmt = $pdo->prepare($sql);

if ($sort === 'distance') {
    $userLatitude = $_GET['latitude'];
    $userLongitude = $_GET['longitude'];
    $stmt->bindValue(1, $userLatitude);
    $stmt->bindValue(2, $userLongitude);
}

$stmt->execute();
$spots = $stmt->fetchAll(PDO::FETCH_ASSOC);

// スポットの表示
foreach ($spots as $spot) {
    echo '<div class="spot">';
    echo '<h2>' . $spot['name'] . '</h2>';
    echo '<p>' . $spot['description'] . '</p>';
    echo '<p>カテゴリー: ' . $spot['category'] . '</p>';
    echo '<p>住所: ' . $spot['address'] . '</p>';
    echo '</div>';
}
?>
