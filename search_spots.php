<?php
 //DB接続
 include("funcs.php");
 $pdo = db_conn();
 
// 検索条件の取得
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$category = isset($_GET['category']) ? $_GET['category'] : '';


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
    // $sql .= " ORDER BY (POW(69.1 * (main_latitude - ?), 2) + POW(69.1 * (? - main_longitude) * COS(main_latitude / 57.3), 2))";
    $sql .= " ORDER BY (6371 * acos(cos(radians(?)) * cos(radians(main_latitude)) * cos(radians(main_longitude) - radians(?)) + sin(radians(?)) * sin(radians(main_latitude))))";
} else {
    $sql .= " ORDER BY created_at DESC";
}

$stmt = $pdo->prepare($sql);

if ($sort === 'distance') {
    $userLatitude = $_GET['latitude'];
    $userLongitude = $_GET['longitude'];
    $stmt->bindValue(1, $userLatitude);
    $stmt->bindValue(2, $userLongitude);
    $stmt->bindValue(3, $userLatitude);
}

$stmt->execute();
$spots = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 検索結果をJSON形式で返す
$response = [
    'list' => '',
    'spots' => []
];

foreach ($spots as $spot) {
    $response['list'] .= '<div class="spot">';
    $response['list'] .= '<h2><a href="spot_detail.php?id=' . $spot['id'] . '">' . $spot['name'] . '</a></h2>';
    $response['list'] .= '<p>' . $spot['description'] . '</p>';
    $response['list'] .= '<p>カテゴリー: ' . $spot['category'] . '</p>';
    $response['list'] .= '<p>住所: ' . $spot['address'] . '</p>';
    $response['list'] .= '</div>';
    
    $response['spots'][] = [
        'name' => $spot['name'],
        'main_latitude' => $spot['main_latitude'],
        'main_longitude' => $spot['main_longitude']
    ];
}

header('Content-Type: application/json');
//日本語文字列をエスケープせずにエンコード JSON_UNESCAPED_UNICODE 
//日本語の文字列がそのままJSONエンコードされると有効なJSON形式ではなくなるため
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>