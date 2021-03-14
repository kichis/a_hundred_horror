<?php
// 編集した「語り」をDBに反映

session_start();
require("db_connection.php");
include("funcs.php");

$story_id = $_POST["story_id"];
$user_id = $_SESSION["user_id"];

ss_chg();
$pdo = db_conn();
// 自分のストーリー以外編集できないように
avoidNonAuthor($pdo, $story_id, $user_id);

$title = $_POST["title"];
$content = $_POST["content"];

$sql = "UPDATE stories SET title = :title, content = :content WHERE story_id = :story_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':title', $title, PDO::PARAM_STR);
$stmt->bindValue(':content', $content, PDO::PARAM_STR);
$stmt->bindValue(':story_id', $story_id, PDO::PARAM_INT);
$status = $stmt->execute();

if($status==false){
  sql_error($stmt);
}else{
  redirect("story.php?story_id={$story_id}");
}
?>