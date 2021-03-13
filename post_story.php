<?php
ini_set('display_errors', 1);

// 語りを投稿する

session_start();
require("db_connection.php");
include("funcs.php");

ss_chg();
avoid();

$user_id = $_SESSION["user_id"];
$title = $_POST["title"];
$content = $_POST["content"];

$isOkTitle = isFilledLimited($title, 100);
$isOkContent = isFilledLimited($content, 100000);

if($isOkTitle && $isOkContent){}
else{
  $_SESSION["title"] = $title;
  $_SESSION["content"] = $content;
  redirect("form_story.php");
  exit();
}

$pdo = db_conn();
$sql = "INSERT INTO stories(user_id, title, content, date) VALUES(:user_id, :title, :content, sysdate());SELECT last_insert_id() FROM stories";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':title', $title, PDO::PARAM_STR);
$stmt->bindValue(':content', $content, PDO::PARAM_STR);
$status = $stmt->execute();

//データ登録処理後
if($status==false){
  sql_error($stmt);
}else{
  $stmt->nextRowset();
  $last_id = $stmt->fetch();
  redirect("story.php?story_id={$last_id[0]}");
}
?>