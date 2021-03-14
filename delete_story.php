<?php
ini_set('display_errors', 1);

// 「語り」を削除する

session_start();
require("db_connection.php");
include("funcs.php");

$story_id = $_GET["story_id"];
$user_id = $_SESSION["user_id"];

ss_chg();
avoid();

// 自分のストーリー以外削除できないように
$pdo = db_conn();
$stmt = $pdo->prepare("SELECT user_id FROM stories WHERE story_id = :story_id");
$stmt->bindValue(':story_id', $story_id, PDO::PARAM_INT);
$status = $stmt->execute(); 
if($status==false)sql_error($stmt);
$author = $stmt->fetch();

if($author["user_id"] != $user_id){
  redirect("home.php");
}

// 当該ストーリー削除 = statusを0にする
$stmt = $pdo->prepare("UPDATE stories SET status = 0 WHERE story_id = :story_id");
$stmt->bindValue(':story_id', $story_id, PDO::PARAM_INT);
$status = $stmt->execute();

if($status==false){
  sql_error($stmt);
}else{
  redirect("mystories.php");
}

?>