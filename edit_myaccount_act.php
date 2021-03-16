<?php 
// ユーザー自身が登録情報を更新

session_start();
require("db_connection.php");
include("funcs.php");

ss_chg();
avoid();

$user_id = $_SESSION["user_id"];

$uname = $_SESSION["uname"];
$email = $_SESSION["email"];
$passwRev = password_hash($_SESSION["passwRev"], PASSWORD_DEFAULT);
$isPasswChange = $_SESSION["passwFlg"];

$pdo = db_conn();
$sql = "UPDATE users SET user_name = :uname, email = :email WHERE user_id = :user_id;";
if($isPasswChange) $sql .= "UPDATE users SET passw = :passwRev WHERE user_id = :user_id;";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':uname', $uname, PDO::PARAM_STR);
$stmt->bindValue(':email', $email, PDO::PARAM_STR);
if($isPasswChange) $stmt->bindValue(':passwRev', $passwRev, PDO::PARAM_STR);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$status = $stmt->execute();

if($status==false){
    sql_error($stmt);
}else{
    // echo "update done!";
    $_SESSION["uname"] = "";
    $_SESSION["email"] = "";
    $_SESSION["passwRev"] = "";
    $_SESSION["passwFlg"] = "";
    redirect("edit_myaccount.php");
}

?>