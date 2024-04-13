<?php
session_start();
include("funcs.php");

// 入力値のバリデーション
$name = htmlspecialchars($_POST['name'], ENT_QUOTES);
$description = htmlspecialchars($_POST['description'], ENT_QUOTES);
$category = intval($_POST['category']);
$main_latitude = floatval($_POST['main_latitude']);
$main_longitude = floatval($_POST['main_longitude']);
$view_latitude = isset($_POST['view_unknown']) ? null : floatval($_POST['view_latitude']);
$view_longitude = isset($_POST['view_unknown']) ? null : floatval($_POST['view_longitude']);

// 画像アップロード処理
$image_path = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploaded_file = $_FILES['image'];
    $image_path = upload_image($uploaded_file);
}

// データベースに登録
$pdo = db_conn();
$stmt = $pdo->prepare("INSERT INTO spots (name, description, category, main_latitude, main_longitude, view_latitude, view_longitude, image_path) VALUES (:name, :description, :category, :main_latitude, :main_longitude, :view_latitude, :view_longitude, :image_path)");
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':description', $description, PDO::PARAM_STR);
$stmt->bindValue(':category', $category, PDO::PARAM_INT);
$stmt->bindValue(':main_latitude', $main_latitude, PDO::PARAM_STR);
$stmt->bindValue(':main_longitude', $main_longitude, PDO::PARAM_STR);
$stmt->bindValue(':view_latitude', $view_latitude, PDO::PARAM_STR);
$stmt->bindValue(':view_longitude', $view_longitude, PDO::PARAM_STR);
$stmt->bindValue(':image_path', $image_path, PDO::PARAM_STR);
$stmt->execute();

// 追加されたスポットのIDを取得
$spot_id = $pdo->lastInsertId();

// 作品IDを取得（例：フォームから送信された作品IDを使用）
$work_id = $_POST['work_id'];

// スポットと作品の紐づけをspot_workテーブルに登録
$stmt = $pdo->prepare("INSERT INTO spot_work (spot_id, work_id) VALUES (:spot_id, :work_id)");
$stmt->bindValue(':spot_id', $spot_id, PDO::PARAM_INT);
$stmt->bindValue(':work_id', $work_id, PDO::PARAM_INT);
$stmt->execute();

// スポット一覧ページにリダイレクト
header('Location: spot.php');
exit;
