<?php
// admin権限で、ユーザー情報を一括して更新

session_start();
require("db_connection.php");
include("funcs.php");

ss_chg();
avoidUser();

// $user_status = $_POST["user_status"];
$user_id = $_POST["edited_user_id"];
$uname = $_POST["edited_uname"];

var_dump(count($user_id));
var_dump($uname);

$pdo = db_conn();
$sql = "";
for( $i = 0 ; $i < count($user_id); $i++ ){
  $sql .= "UPDATE users SET user_name = :uname WHERE user_id = :user_id;";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':uname', $uname[$i], PDO::PARAM_STR);
  $stmt->bindValue(':user_id', $user_id[$i], PDO::PARAM_INT);
  $status = $stmt->execute();
}

if($status==false){
  sql_error($stmt);
}else{
  redirect("users.php");
}


?>