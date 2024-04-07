<?php
if (session_status() === PHP_SESSION_NONE) {
    // セッションが開始されていない場合のみセッションを開始する
    session_start();
}
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
            $db_name = "hitsuji-waiwai_gs_tourismspot	";  //データベース名
            $db_id   = "hitsuji-waiwai";  //アカウント名（さくらコントロールパネルに表示されています）
            $config = require 'C:\Users\C:\Users\2xx4x\Desktop\config.php';
            $db_pw = $config['db']['password'];;  //パスワード(さくらサーバー最初にDB作成する際に設定したパスワード)
            $db_host = "mysql626.db.sakura.ne.jp"; //例）mysql**db.ne.jp...
        }
        return new PDO('mysql:dbname=' . $db_name . ';charset=utf8;host=' . $db_host, $db_id, $db_pw);
    } catch (PDOException $e) {
        exit('DB Connection Error:' . $e->getMessage());
    }
}

// データベース接続確認用
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
function getAllSpots() {
    try {
      $pdo = db_conn();
      $stmt = $pdo->query("SELECT * FROM spots");
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo 'データベースエラー: ' . $e->getMessage();
      exit();
    }
  }