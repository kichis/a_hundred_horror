<?php
// 怖評価を+1する

session_start();
require("db_connection.php");
include("funcs.php");
 
ss_chg();
avoid();

$story_id = $_GET["story_id"];
$user_id = $_GET["user_id"];

// // db接続,sql文
$pdo = db_conn();
$sql = "UPDATE stories SET num_horror = num_horror +1 WHERE story_id = :id;
INSERT INTO horrors(story_id, user_id) VALUES(:id, :user_id) ";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $story_id, PDO::PARAM_INT);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$status = $stmt->execute();

// 成功したら元のページに戻る
if($status==false) {
  sql_error($stmt);
}else{ 
  redirect("story.php?story_id=". $story_id ."");
}

?>