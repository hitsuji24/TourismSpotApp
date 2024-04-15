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

// 新しい作品が入力されていれば作品テーブルに追加
if (isset($_POST['new_work']) && !empty($_POST['new_work'])) {
    $new_work_title = $_POST['new_work'];
    $stmt = $pdo->prepare("INSERT INTO works (title) VALUES (:title)");
    $stmt->bindValue(':title', $new_work_title, PDO::PARAM_STR);
    $stmt->execute();
    $work_id = $pdo->lastInsertId(); // 追加された作品のIDを取得

    // スポットと新しい作品の紐づけをspot_workテーブルに登録
    $stmt = $pdo->prepare("INSERT INTO spot_work (spot_id, work_id) VALUES (:spot_id, :work_id)");
    $stmt->bindValue(':spot_id', $spot_id, PDO::PARAM_INT);
    $stmt->bindValue(':work_id', $work_id, PDO::PARAM_INT); // 新しい作品のIDを使用
    $stmt->execute();
} else if (isset($_POST['work_id']) && !empty($_POST['work_id'])) {
    // 既存の作品が選択された場合の処理
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
