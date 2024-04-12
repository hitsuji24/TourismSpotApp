<?php
//最初にSESSIONを開始！！ココ大事！！
session_start();

//POST値
$email = $_POST["email"];
$password = $_POST["password"];

//DB接続
include("funcs.php");
$pdo = db_conn();

//データ登録SQL作成
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email"); 
$stmt->bindValue(':email', $email, PDO::PARAM_STR);
$status = $stmt->execute();

//SQL実行時にエラーがある場合STOP
if($status==false){
    sql_error($stmt);
}

//抽出データ数を取得
$val = $stmt->fetch();

//該当レコードがあればSESSIONに値を代入
if($val != null && password_verify($password, $val["password"])){
  //Login成功時
  $_SESSION["chk_ssid"]  = session_id();
  $_SESSION["user_id"] = $val['id'];
  $_SESSION["name"]      = $val['name'];
  //Login成功時（リダイレクト）
  redirect("index.php");
}else{
  //Login失敗時(Logoutを経由：リダイレクト)
  redirect("login.php");
}

exit();
?>
