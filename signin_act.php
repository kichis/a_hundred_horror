<?php 
// アカウントをdbに登録する

session_start();
require("db_connection.php");
include("funcs.php");

$uname = $_POST["uname"];
$email = $_POST["email"];
$confemail = $_POST["confirmEmail"];
$passw = password_hash($_POST["passw"], PASSWORD_DEFAULT);

$_SESSION["signinErrorMsg"] = "";



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
  redirect("login.php");
}

// signinErrorCode
// 1: Emailが確認用Emailと異なる = "２つのEmailの入力が異なっています"
// 2: 同じEmailがすでに登録がある = "このEmailアドレスは使用できません(すでに登録があります)"
// 3: 同じuser_nameがすでに登録がある = "このお名前は使用できません(すでに登録があります)"
// 4: 
?>