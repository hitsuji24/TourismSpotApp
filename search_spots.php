<?php
// エラー表示 
ini_set('display_errors', 1);
error_reporting(E_ALL);

//DB接続
include("funcs.php");
$pdo = db_conn();

// 検索条件の取得
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// 検索クエリの作成
$sql = "SELECT s.*, c.name AS category_name 
        FROM spots s
        LEFT JOIN categories c ON s.category = c.id";
$conditions = [];

if (!empty($keyword)) {
    $conditions[] = "(s.name LIKE '%$keyword%' OR s.description LIKE '%$keyword%' OR s.main_address LIKE '%$keyword%')";
}

if (!empty($category)) {
    $conditions[] = "s.category = '$category'";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

if ($sort === 'distance') {
    $sql .= " ORDER BY (6371 * acos(cos(radians(?)) * cos(radians(s.main_latitude)) * cos(radians(s.main_longitude) - radians(?)) + sin(radians(?)) * sin(radians(s.main_latitude))))";
} else {
    $sql .= " ORDER BY s.created_at DESC";
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
    'spots' => [],
    'empty_message' => '該当する作品がありません。',
];

if (count($spots) > 0) {
    foreach ($spots as $spot) {
        $response['list'] .= '<a href="spot_detail.php?id=' . $spot['id'] . '">';

        $response['list'] .= '<div class="spot-card">';
        $response['list'] .= '<img src="' . $spot['ar_image_url'] . '" alt="' . $spot['name'] . '">';
        $response['list'] .= '<div class="info">';
        $response['list'] .= '<h2>' . $spot['name'] . '</h2>';
        $response['list'] .= '<p>カテゴリー: ' . $spot['category_name'] . '</p>';
    
        // 住所を県まで表示する
        $address_parts = explode(',', $spot['main_address']);
        $response['list'] .= '<p>住所: ' . trim($address_parts[0]) . '</p>';
    
        $response['list'] .= '</div>'; // info
        $response['list'] .= '</div>'; // spot-card
        $response['list'] .= '</a>';

        $response['spots'][] = [
            'name' => $spot['name'],
            'main_latitude' => $spot['main_latitude'],
            'main_longitude' => $spot['main_longitude']
        ];
    }
} else {
    $response['list'] = '<div id="empty-message">該当するスポットがありません。</div>';
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>