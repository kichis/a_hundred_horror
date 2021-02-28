<?php 
//XSS対応（ echoする場所で使用！それ以外はNG ）
function h($str){
    return htmlspecialchars($str, ENT_QUOTES);
}

//SessionCheck
function ss_chg(){
 if(isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"]=session_id()){
    session_regenerate_id(true); //SESSION_IDを毎ページ変える
    $_SESSION["chk_ssid"] = session_id();
 }else{
    
 }
}
// user,admin以外禁止
function avoid(){
    if($_SESSION["user_status"]==1 || $_SESSION["user_status"]==2){

    }else{
        redirect("home.php");
    }
}

// admin以外禁止
function avoidUser(){
    if($_SESSION["user_status"]==2){

    }else{
        redirect("home.php");
    }
}



//SQLエラー
function sql_error(){
    //execute（SQL実行時にエラーがある場合）
    $error = $stmt->errorInfo();
    exit("データベース内でエラーが発生しました:".$error[2]);
}

//リダイレクト
function redirect($file_name){
    header("Location: ".$file_name);
    exit();
}

// story.phpにコメントを表示
function showComm($id){
    $pdo = db_conn();
    $sql = "SELECT comments.comment AS comment, users.user_name AS user FROM comments INNER JOIN users ON comments.comm_user = users.user_id WHERE story_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $status = $stmt->execute();

// レコードを変数へ
$view="";
if($status==false) {
  sql_error();
}else{
    while($a = $stmt->fetch(PDO::FETCH_ASSOC)){
        $view .= $a["user"]."&#160;&#160;&#160;「";
        $view .= $a["comment"]."」<br>";
    }
    return $view;
}
}

?>