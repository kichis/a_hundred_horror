<?php
session_start();
require("db_connection.php");
include("funcs.php");

$email = $_POST["email"];
$passw = $_POST["passw"];

$pdo = db_conn();
$sql = "SELECT * FROM users WHERE email=:email AND user_status != 0";
$stmt = $pdo->prepare($sql); 
$stmt->bindValue(':email', $email, PDO::PARAM_STR);
$status = $stmt->execute();

if($status==false)sql_error($stmt);

$val = $stmt->fetch();   

//* PasswordがHash化の場合
if(password_verify($passw, $val["passw"])){ 
  //Login($passwが合っている)成功時
  $_SESSION["chk_ssid"]  = session_id(); //この認証が通ったときのKEYを渡しておく
  $_SESSION["user_name"] = $val['user_name'];
  $_SESSION["user_id"] = $val['user_id'];
  $_SESSION["user_status"] = $val['user_status'];
  redirect("home.php");
}else{
  //Login失敗時
  redirect("login.php");
}
exit();
?>