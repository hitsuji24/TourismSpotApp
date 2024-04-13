<?php
// if (session_status() === PHP_SESSION_NONE) {
//     // セッションが開始されていない場合のみセッションを開始する
//     session_start();
// }
//XSS対応（ echoする場所で使用！それ以外はNG ）
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}


//DB接続関数：db_conn()
function db_conn()
{
    try {
        //localhostの場合
        $db_name = "gs_tourismspot";    //データベース名
        $db_id   = "root";      //アカウント名
        $db_pw   = "";          //パスワード：XAMPPはパスワード無しに修正してください。
        $db_host = "localhost"; //DBホスト

        //localhost以外＊＊自分で書き直してください！！＊＊
        if ($_SERVER["HTTP_HOST"] != 'localhost') {
            $db_name = "hitsuji-waiwai_gs_tourismspot";  //データベース名
            $db_id   = "hitsuji-waiwai";  //アカウント名（さくらコントロールパネルに表示されています）
            require 'config/config_db.php';
            $db_pw = db_pw;  //パスワード(さくらサーバー最初にDB作成する際に設定したパスワード)
            $db_host = "mysql57.hitsuji-waiwai.sakura.ne.jp"; //例）mysql**db.ne.jp...
        }
        return new PDO('mysql:dbname=' . $db_name . ';charset=utf8;host=' . $db_host, $db_id, $db_pw);
    } catch (PDOException $e) {
        exit('DB Connection Error:' . $e->getMessage());
    }
}


//spotの検索でJSONのエラーが起きていたのはこれが原因ぽい
// try {
//     $pdo = db_conn();
//     if ($pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS)) {
//         echo "データベースに正常に接続できました。";
//     } else {
//         echo "データベース接続に失敗しました。";
//     }
// } catch (PDOException $e) {
//     echo "データベース接続エラー: " . $e->getMessage();
// }

//SQLエラー関数：sql_error($stmt)
function sql_error($stmt)
{
    $error = $stmt->errorInfo();
    exit("SQLError:" . $error[2]);
}


//リダイレクト関数: redirect($file_name)
function redirect($file_name)
{
    header("Location: " . $file_name);
    exit();
}

//SessionCheck(スケルトン)
function sschk()
{
    if ($_SESSION["chk_ssid"] != session_id()) {
        exit('LOGIN ERROR');
    } else {
        session_regenerate_id(true);
        $_SESSION["chk_ssid"] = session_id();
    }
}

// データベースから全スポットを取得する
function getAllSpots($userLat, $userLon)
{
    try {
        $pdo = db_conn();
        $stmt = $pdo->prepare("SELECT *, ( 6371 * acos(cos(radians(:userLat)) * cos(radians(main_latitude)) * cos(radians(main_longitude) - radians(:userLon)) + sin(radians(:userLat)) * sin(radians(main_latitude))) ) AS distance 
        FROM spots
        ORDER BY ( 6371 * acos(cos(radians(:userLat)) * cos(radians(main_latitude)) * cos(radians(main_longitude) - radians(:userLon)) + sin(radians(:userLat)) * sin(radians(main_latitude))) )
        ");
        $stmt->bindValue(':userLat', $userLat);
        $stmt->bindValue(':userLon', $userLon);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'データベースエラー: ' . $e->getMessage();
        exit();
    }
}

/**
 * 画像アップロード処理
 *
 * @param array $uploaded_file $_FILES['image']の値
 * @return string|null アップロードされた画像のパス（失敗した場合はnull）
 */
function upload_image($uploaded_file) {
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $extension = pathinfo($uploaded_file['name'], PATHINFO_EXTENSION);
    if (!in_array(strtolower($extension), $allowed_extensions)) {
        return null;
    }

    $max_size = 1024 * 1024 * 5; // 5MB
    if ($uploaded_file['size'] > $max_size) {
        return null;
    }

    $upload_dir = 'uploads/';
    $filename = uniqid() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    if (!move_uploaded_file($uploaded_file['tmp_name'], $filepath)) {
        return null;
    }

    return $filepath;
}