<?php 
// ini_set('display_errors', 1);

session_start();
require("db_connection.php");
include("funcs.php");

ss_chg();
avoid();

$user_id = $_POST["user_id"];

$pdo = db_conn();

$sql = "UPDATE users SET user_status = 0 WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$status = $stmt->execute();
if($status==false){
    sql_error($stmt);
}else{
    redirect("logout.php");
}
?>