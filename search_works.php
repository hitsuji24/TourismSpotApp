<?php
//DB接続
include("funcs.php");
$pdo = db_conn();

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT * FROM works";
$conditions = [];

if (!empty($keyword)) {
    $conditions[] = "(title LIKE '%$keyword%' OR description LIKE '%$keyword%')";
}

if (!empty($category)) {
    $conditions[] = "category = '$category'";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$stmt = $pdo->prepare($sql);
$stmt->execute();
$works = $stmt->fetchAll(PDO::FETCH_ASSOC);

$response = [
    'list' => ''
];

foreach ($works as $work) {
    $thumbnail_path = !empty($work['thumbnail_path']) ? $work['thumbnail_path'] : 'img/works/no-image.png';

    $response['list'] .= '<div class="work">';
    $response['list'] .= '<a href="work_detail.php?id=' . $work['id'] . '">';
    $response['list'] .= '<img src="' . $thumbnail_path . '" alt="' . $work['title'] . '">';
    $response['list'] .= '<h2>' . $work['title'] . '</h2>';
    $response['list'] .= '<p>' . $work['description'] . '</p>';
    $response['list'] .= '</a>';
    $response['list'] .= '</div>';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>