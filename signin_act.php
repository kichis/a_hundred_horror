<?php 
// アカウントをdbに登録する

session_start();
require("db_connection.php");
include("funcs.php");

$uname = $_SESSION["uname"];
$email = $_SESSION["email"];
$passw = password_hash($_SESSION["passw"], PASSWORD_DEFAULT);

$pdo = db_conn();
$sql = "INSERT INTO users(user_name, email, passw) VALUES(:user_name, :email, :passw)";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_name', $uname, PDO::PARAM_STR);
$stmt->bindValue(':email', $email, PDO::PARAM_STR);
$stmt->bindValue(':passw', $passw, PDO::PARAM_STR);
$status = $stmt->execute(); //実行

if($status==false){
  sql_error($stmt);
}else{
  $_SESSION = array();
  redirect("login.php");
}
?>