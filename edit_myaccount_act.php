<?php 
// ユーザー自身が登録情報を更新

session_start();
require("db_connection.php");
include("funcs.php");

ss_chg();
avoidUser();


$uname = $_POST["uname"];
$email = $_POST["email"];


// バリデーション
// passwがある場合

$pdo = db_conn();
$sql = "UPDATE users SET user_name = :uname, email = :email WHERE user_id = :user_id;";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':uname', $uname, PDO::PARAM_STR);
$stmt->bindValue(':email', $email, PDO::PARAM_STR);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$status = $stmt->execute();

if($status==false){
  sql_error($stmt);
}else{
//   redirect("users.php");
}


?>