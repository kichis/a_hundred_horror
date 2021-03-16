<?php 
// admin権限で、ストーリーの表示、非表示を切り替える
ini_set('display_errors', 1);

session_start();
require("db_connection.php");
include("funcs.php");

ss_chg();
avoidUser();

$story_id = $_POST["edited_story_id"]; //配列
$statusRev = $_POST["statusRev"]; //配列

$story_id = join(",", $story_id);
$statusRev = join(",", $statusRev);

$pdo = db_conn();
// HACK:よろしくない方式だが、変数にユーザー入力値が含まれないのでこれで実装
$sql = "UPDATE stories SET status = ELT(FIELD(story_id, ".$story_id."), ".$statusRev.") WHERE story_id IN (".$story_id.")";
$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

if($status==false){
    sql_error($stmt);
}else{
    redirect("stories.php");
}

// MEMO: 下記のSQL文だと、$story_idと$statusRevの一つ目の要素しか実行されないが、エラーにもならない(PDO::PARAM_部分は省略)
// $sql = "UPDATE stories SET status = ELT(FIELD(story_id, :story_ids), :statusRevs) WHERE story_id IN (:story_idst)";
?>