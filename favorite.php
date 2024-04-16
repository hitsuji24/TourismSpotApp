<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$spot_id = $_POST["spot_id"];

include("funcs.php");
$pdo = db_conn();

$stmt = $pdo->prepare("INSERT INTO favorite_spots (user_id, spot_id) VALUES (:user_id, :spot_id)");
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':spot_id', $spot_id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status) {
    header("Location: spot_detail.php?id=" . $spot_id);
} else {
    sql_error($stmt);
}
exit;
?>
