<?php 
// Ajax以外からのアクセスを遮断(※ajaxならヘッダー情報に"HTTP_X_REQUESTED_WITH"がついてくる)
$request = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : '';
if($request !== 'xmlhttprequest') exit;
 
$id = filter_input(INPUT_POST, 'user_id');
// $text = $_GET['text'] でもok;

require("db_connection.php");
$pdo = db_conn();
$sql = "SELECT * FROM users WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $id, PDO::PARAM_INT);
$status = $stmt->execute();
if($status==false) sql_error($stmt);
$r = $stmt->fetch();

header('Content-type: application/json; charset=utf-8');
// 以下のjsonがリクエスト元に返される
echo json_encode([
    'user_id'=>$r["user_id"],
    'user_name'=>$r["user_name"],
    'email'=>$r["email"],
    'user_status'=>$r["user_status"]
]);
?>