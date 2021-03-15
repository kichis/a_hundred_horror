<?php
session_start();
require("db_connection.php");
include("funcs.php");

// ログインフォームからの値を受ける 
$email = $_POST["email"];
$passw = $_POST["passw"];

// dbへ接続
$pdo = db_conn();

//データ検索SQL作成
//* PasswordがHash化の場合→条件はlidのみ
$sql = "SELECT * FROM users WHERE email=:email AND user_status != 0";
$stmt = $pdo->prepare($sql); 
$stmt->bindValue(':email', $email, PDO::PARAM_STR);
$status = $stmt->execute();

//SQL実行時にエラーがある場合STOP
if($status==false){
  sql_error();
}

//抽出データ数を取得
$val = $stmt->fetch();   

//該当レコードがあればSESSIONに値を代入
//* PasswordがHash化の場合
if(password_verify($passw, $val["passw"])){ 
  //Login成功時
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