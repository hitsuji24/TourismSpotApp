<?php
session_start();
require_once 'funcs.php';

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// POST送信されたデータを変数に格納
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $category_id = $_POST['category'];
    $address = $_POST['address'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $view_latitude = $_POST['view_latitude'];
    $view_longitude = $_POST['view_longitude'];
    $work_id = $_POST['work_id'];
    $new_work = $_POST['new_work'];
    $creator = $_SESSION['user_id'];

    // 画像のアップロード処理
    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $filename = date('YmdHis') . '_' . $_FILES['image']['name'];
        $uploaded_path = 'uploads/' . $filename;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploaded_path);
        $image = $uploaded_path;
    }

    // 新しい作品の登録
    // やっぱり作品は別で登録させる
// if (!empty($new_work)) {
//     error_log("新しい作品の登録処理開始");
//     error_log("new_work: " . $new_work);
    
//     $pdo = db_conn();
//     $stmt = $pdo->prepare("INSERT INTO works (title) VALUES (?)");
//     $stmt->execute(array($new_work));
//     $work_id = $pdo->lastInsertId();
    
//     error_log("新しい作品の登録処理完了");
//     error_log("生成されたwork_id: " . $work_id); 
// }

    // spotsテーブルにデータを登録
    error_log("spotsテーブルへの登録処理開始");
error_log("name: " . $name);
error_log("description: " . $description);
error_log("image: " . $image);
error_log("category_id: " . $category_id);
error_log("latitude: " . $latitude);
error_log("longitude: " . $longitude);
error_log("address: " . $address);
error_log("creator: " . $creator);
error_log("view_latitude: " . $view_latitude);
error_log("view_longitude: " . $view_longitude);

    $pdo = db_conn();
    $stmt = $pdo->prepare("INSERT INTO spots (
        name, description, ar_image_url, category, main_latitude, 
        main_longitude, address, creator, view_latitude, view_longitude
    ) VALUES (
        :name, :description, :image, :category_id, :latitude,
        :longitude, :address, :creator, :view_latitude, :view_longitude 
    )");

    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':description', $description, PDO::PARAM_STR);
    $stmt->bindValue(':image', $image, PDO::PARAM_STR);
    $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->bindValue(':latitude', $latitude, PDO::PARAM_STR);
    $stmt->bindValue(':longitude', $longitude, PDO::PARAM_STR);
    $stmt->bindValue(':address', $address, PDO::PARAM_STR);
    $stmt->bindValue(':creator', $creator, PDO::PARAM_STR);
    $stmt->bindValue(':view_latitude', $view_latitude, PDO::PARAM_STR);
    $stmt->bindValue(':view_longitude', $view_longitude, PDO::PARAM_STR);
    $stmt->execute();
    $spot_id = $pdo->lastInsertId();

    error_log("spotsテーブルへの登録処理完了");
error_log("生成されたspot_id: " . $spot_id);

    // spot_workテーブルに紐付けデータを登録
    error_log("spot_workテーブルへの登録処理開始");
error_log("spot_id: " . $spot_id);
error_log("work_id: " . $work_id);
    $stmt = $pdo->prepare("INSERT INTO spot_work (spot_id, work_id) VALUES (?, ?)");
    $stmt->execute(array($spot_id, $work_id));

    // 登録完了メッセージを表示してリダイレクト  
    $_SESSION['message'] = 'スポットを登録しました。';
    header('Location: spot.php');
    exit;
}
