<?php
// コメントを投稿する

session_start();
require("db_connection.php");
include("funcs.php");

ss_chg();
avoid();

$story_id = $_POST["story_id"];
$user_id = $_SESSION["user_id"];
$comment = $_POST["comment"];

if( isFilledLimited($comment, 10000) == false ){
  $_SESSION["comment"] = $comment;
  redirect("story.php?story_id={$story_id}");
}

$pdo = db_conn();
$sql = "INSERT INTO comments(story_id, user_id, comment, date) VALUES(:story_id, :user_id, :comm, sysdate())";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':story_id', $story_id, PDO::PARAM_INT);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':comm', $comment, PDO::PARAM_STR);
$status = $stmt->execute();

// 成功したら元のページに戻る
if($status==false) {
  sql_error($stmt);
}else{ 
  redirect("story.php?story_id={$story_id}");
}