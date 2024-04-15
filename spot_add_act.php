<?php session_start();
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
    $main_address = $_POST['main_address'];
    $main_latitude = $_POST['main_latitude'];
    $main_longitude = $_POST['main_longitude'];
    $view_address = $_POST['view_address'];
    $view_latitude = $_POST['view_latitude'];
    $view_longitude = $_POST['view_longitude'];
    $work_id = $_POST['work_id'];
    $creator = $_SESSION['user_id']; // 画像のアップロード処理 
    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $filename = date('YmdHis') . '_' . $_FILES['image']['name'];
        $uploaded_path = 'uploads/' . $filename;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploaded_path);
        $image = $uploaded_path;
    }

    // バリデーション 
    $errors = [];
    if (empty($name)) {
        $errors['name'] = 'スポット名を入力してください。';
    }
    if (empty($category_id)) {
        $errors['category'] = 'カテゴリーを選択してください。';
    }
    if (empty($work_id)) {
        $errors['work'] = '作品を選択してください。';
    }
    if (empty($image)) {
        $errors['image'] = '画像を選択してください。';
    }
    if (count($errors) === 0) {

        // spotsテーブルにデータを登録 
        $pdo = db_conn();
        $stmt = $pdo->prepare("INSERT INTO spots (
            name, description, ar_image_url, category, main_latitude, 
            main_longitude, main_address, creator, view_latitude, view_longitude, view_address
        ) VALUES (
            :name, :description, :image, :category_id, :main_latitude,
            :main_longitude, :main_address, :creator, :view_latitude, :view_longitude, :view_address  
        )");
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':image', $image, PDO::PARAM_STR);
        $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindValue(':main_latitude', $main_latitude, PDO::PARAM_STR);
        $stmt->bindValue(':main_longitude', $main_longitude, PDO::PARAM_STR);
        $stmt->bindValue(':main_address', $main_address, PDO::PARAM_STR);
        $stmt->bindValue(':creator', $creator, PDO::PARAM_STR);
        $stmt->bindValue(':view_latitude', $view_latitude, PDO::PARAM_STR);
        $stmt->bindValue(':view_longitude', $view_longitude, PDO::PARAM_STR);
        $stmt->bindValue(':view_address', $view_address, PDO::PARAM_STR);
        $stmt->execute();
        $spot_id = $pdo->lastInsertId();
        // spot_workテーブルに紐付けデータを登録 
        $stmt = $pdo->prepare("INSERT INTO spot_work (spot_id, work_id) VALUES (?, ?)");
        $stmt->execute(array($spot_id, $work_id));
        // 登録完了メッセージを表示してリダイレクト
        $_SESSION['message'] = 'スポットを登録しました。';
        header('Location: spot.php');
        exit;
    }
}
?>
