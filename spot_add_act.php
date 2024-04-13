<?php
session_start();
include("funcs.php");
$pdo = db_conn();

// 入力値のバリデーション
$name = isset($_POST['name']) ? $_POST['name'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';
$category = isset($_POST['category']) ? $_POST['category'] : '';
$address = isset($_POST['address']) ? $_POST['address'] : '';
$main_latitude = isset($_POST['main_latitude']) ? $_POST['main_latitude'] : '';
$main_longitude = isset($_POST['main_longitude']) ? $_POST['main_longitude'] : '';
$view_latitude = isset($_POST['view_latitude']) ? $_POST['view_latitude'] : null;
$view_longitude = isset($_POST['view_longitude']) ? $_POST['view_longitude'] : null;

// 画像アップロード処理
$image_path = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploaded_file = $_FILES['image'];
    $image_path = upload_image($uploaded_file);
}

// データベースにデータを挿入
$stmt = $pdo->prepare("INSERT INTO spots (name, description, category, address, main_latitude, main_longitude, view_latitude, view_longitude, ar_image_url) VALUES (:name, :description, :category, :address, :main_latitude, :main_longitude, :view_latitude, :view_longitude, :ar_image_url)");
$stmt->bindValue(':name', $name);
$stmt->bindValue(':description', $description);
$stmt->bindValue(':category', $category);
$stmt->bindValue(':address', $address);
$stmt->bindValue(':main_latitude', $main_latitude);
$stmt->bindValue(':main_longitude', $main_longitude);
$stmt->bindValue(':view_latitude', $view_latitude);
$stmt->bindValue(':view_longitude', $view_longitude);
$stmt->bindValue(':ar_image_url', $image_path, PDO::PARAM_STR);
$stmt->execute();

// 追加されたスポットのIDを取得
$spot_id = $pdo->lastInsertId();

// 作品IDを取得
if (isset($_POST['work_id'])) {
    $work_id = $_POST['work_id'];
    
    // スポットと作品の紐づけをspot_workテーブルに登録
    $stmt = $pdo->prepare("INSERT INTO spot_work (spot_id, work_id) VALUES (:spot_id, :work_id)");
    $stmt->bindValue(':spot_id', $spot_id, PDO::PARAM_INT);
    $stmt->bindValue(':work_id', $work_id, PDO::PARAM_INT);
    $stmt->execute();
}

// スポット一覧ページにリダイレクト
header('Location: spot.php');
exit;
