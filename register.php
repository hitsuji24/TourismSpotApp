<?php
//POST値を受け取る
$name = $_POST["name"];
$email = $_POST["email"];
$password = $_POST["password"];

//パスワードをハッシュ化
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

//DB接続
include("funcs.php");
$pdo = db_conn();

//データ登録SQL作成
$stmt = $pdo->prepare("INSERT INTO users(name, email, password) VALUES(:name, :email, :password)");
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':email', $email, PDO::PARAM_STR);
$stmt->bindValue(':password', $hashed_password, PDO::PARAM_STR);
$status = $stmt->execute();

//データ登録処理後
if($status==false){
  sql_error($stmt);
}else{
  redirect("login.php");
}
?>