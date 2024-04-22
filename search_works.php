<?php
//DB接続
include("funcs.php");
$pdo = db_conn();

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT w.*, c.name AS category_name, COUNT(sw.spot_id) AS spot_count 
        FROM works w
        LEFT JOIN categories c ON w.category = c.id
        LEFT JOIN spot_work sw ON w.id = sw.work_id";
$conditions = [];

if (!empty($keyword)) {
    $conditions[] = "(w.title LIKE '%$keyword%' OR w.description LIKE '%$keyword%')";
}
  
if (!empty($category)) {
    $conditions[] = "w.category = '$category'";
}
  
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " GROUP BY w.id";
  
switch ($sort) {
    case 'release_date_desc':
        $sql .= " ORDER BY w.release_date DESC";
        break;
    case 'release_date_asc':
        $sql .= " ORDER BY w.release_date ASC"; 
        break;
    case 'title_asc':
        $sql .= " ORDER BY w.title ASC";
        break;
    default:
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute();
$works = $stmt->fetchAll(PDO::FETCH_ASSOC);

$response = [
    'list' => '',
    'empty_message' => '該当する作品がありません。',
];

if (count($works) > 0) {
    foreach ($works as $work) {
        $thumbnail_path = !empty($work['thumbnail_path']) ? $work['thumbnail_path'] : 'img/works/no-image.png';
        $response['list'] .= '<a href="work_detail.php?id=' . $work['id'] . '">';

        $response['list'] .= '<div class="result_container">';

        $response['list'] .= '<div class="work-img">';
        $response['list'] .= '<img src="' . $thumbnail_path . '" alt="' . $work['title'] . '">';
        $response['list'] .= '</div>';

        $response['list'] .= '<div class="work-info">';
        $response['list'] .= '<h2>' . $work['title'] . '</h2>';
        $response['list'] .= '<p id="releaseDate">' . $work['release_date'] . '</p>';
        $response['list'] .= '<p id="category">カテゴリー: ' . $work['category_name'] . '</p>';
        $response['list'] .= '<p id="spotNum">登録スポット数: ' . $work['spot_count'] . '</p>';
        $response['list'] .= '</div>';

        $response['list'] .= '</div>';
        $response['list'] .= '</a>';

    }
} else {
    $response['list'] = '<div id="empty-message">該当する作品がありません。</div>';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);