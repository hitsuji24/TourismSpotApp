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
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category_id = $_POST['category'];
    $release_date = $_POST['release_date'];

    // サムネイル画像のアップロード処理
    $thumbnail_path = '';
    if (!empty($_FILES['thumbnail']['name'])) {
        $filename = date('YmdHis') . '_' . $_FILES['thumbnail']['name'];
        $uploaded_path = 'uploads/' . $filename;
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], $uploaded_path);
        $thumbnail_path = $uploaded_path;
    }

    // worksテーブルにデータを登録
    $pdo = db_conn();
    $stmt = $pdo->prepare("INSERT INTO works (
        title, description, thumbnail_path, category, release_date
    ) VALUES (
        :title, :description, :thumbnail_path, :category_id, :release_date
    )");

    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(':description', $description, PDO::PARAM_STR);
    $stmt->bindValue(':thumbnail_path', $thumbnail_path, PDO::PARAM_STR);
    $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->bindValue(':release_date', $release_date, PDO::PARAM_STR);
    $stmt->execute();

    // 登録完了メッセージを表示してリダイレクト
    $_SESSION['message'] = '作品を登録しました。';
    header('Location: works.php');
    exit;
}
?>
