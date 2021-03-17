<?php
// admin権限で、ユーザー情報を一括して更新

session_start();
require("db_connection.php");
include("funcs.php");

ss_chg();
avoidUser();

$uname = $_SESSION["edited_uname"];
$email = $_SESSION["edited_email"];
$user_status = $_SESSION["edited_status"];
$user_id = $_SESSION["edited_user_id"];

var_dump(count($user_id));
var_dump($uname);

$pdo = db_conn();
$sql = "";
for( $i = 0 ; $i < count($user_id); $i++ ){
  $sql .= "UPDATE users SET user_name = :uname, email = :email, user_status = :user_status WHERE user_id = :user_id;";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':uname', $uname[$i], PDO::PARAM_STR);
  $stmt->bindValue(':email', $email[$i], PDO::PARAM_STR);
  $stmt->bindValue(':user_status', $user_status[$i], PDO::PARAM_STR);
  $stmt->bindValue(':user_id', $user_id[$i], PDO::PARAM_INT);
  $status = $stmt->execute();
}

$_SESSION["edited_uname"] = "";
$_SESSION["edited_email"] = "";
$_SESSION["edited_status"] = "";
$_SESSION["edited_user_id"] = "";

if($status==false){
  sql_error($stmt);
}else{
  redirect("users.php");
}


?>