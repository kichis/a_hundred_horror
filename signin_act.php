<?php 
// アカウントをdbに登録する

session_start();
require("db_connection.php");
include("funcs.php");

$uname = $_SESSION["uname"];
$email = $_SESSION["email"];
$passw = password_hash($_SESSION["passw"], PASSWORD_DEFAULT);

// dbへ接続
$pdo = db_conn();

// データ登録SQL作成
$sql = "INSERT INTO users(user_name, email, passw) VALUES(:user_name, :email, :passw)";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_name', $uname, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':email', $email, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':passw', $passw, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute(); //実行

//データ登録処理後
if($status==false){
  sql_error();
}else{
  $_SESSION = array();
  redirect("login.php");
}
?>