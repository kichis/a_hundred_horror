<?php 
//XSS対応（ echoする場所で使用！それ以外はNG ）
function h($str){
    return htmlspecialchars($str, ENT_QUOTES);
}

// ログインしたユーザならsession_idを変更する
function ss_chg(){
    if( isset($_SESSION["chk_ssid"]) && $_SESSION["chk_ssid"] == session_id() ){
        session_regenerate_id(true); //SESSION_IDを毎ページ変える
        $_SESSION["chk_ssid"] = session_id();
    }else{
    }
}
// user(1),admin(2)以外禁止
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
function sql_error($stmt){
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

// 以下、signin用バリデーション

// 空欄ではないか
function isFilled($key, $value, $valiFlg){
    if(empty($value)){
        $_SESSION["signinErrorMsg"] .= "{$key}を入力してください<br>";
    }else{
        $valiFlg += 1;
    }
    return $valiFlg;
}

// 文字数制限内か
function checkInputLength($key, $value, $valiFlg){
    $max;
    $min;
    switch($key){
        case 'お名前':
            $max = 100;
            $min = 3;
            break;
        case 'Email':
            $max = 255;
            $min = 3;
            break;
        case '確認用Email':
            $max = 255;
            $min = 3;
            break;
        case 'パスワード':
            $max = 255;
            $min = 8;
            break;
    }
    if(mb_strlen($value) > $max){
        $_SESSION["signinErrorMsg"] .= "{$key}を{$max}文字以下にしてください<br>";
    }else if(mb_strlen($value) < $min){
        $_SESSION["signinErrorMsg"] .= "{$key}を{$min}文字以下にしてください<br>";
    }else{
        $valiFlg += 1;
    }
    return $valiFlg;
}

function checkCorrectEmail($email, $confemail, $valiFlg){
    // Emailが確認用Emailの入力と合っているか
    if($email != $confemail){
        $_SESSION["signinErrorMsg"] .= "２つのEmailの入力が異なっています<br>";
    // メールアドレスは正しい形式か（半角英数・記号に限る、@必要、＠以降に文字列必要）
    }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $_SESSION["signinErrorMsg"] .= "この形式のEmailはご登録いただけません<br>";
    }else{
        $valiFlg += 1;
    }
    return $valiFlg;
}

// 同じ登録があるか
function checkSameRecord($pdo, $col, $data, $comment, $valiFlg){
    // HACK:本来はSQLに直接変数を埋め込むべきでないが、ユーザが入力できる変数でないので、コード内の可用性を考慮してこの形とする
    $sql = "SELECT * from users WHERE " . $col . " = :val";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':val', $data, PDO::PARAM_STR);
    $status = $stmt->execute();
    if($status == false) sql_error($stmt);
    $val = $stmt->fetch();
    // var_dump($val);
    if($val[$col] == $data){
        $_SESSION["signinErrorMsg"] .= $comment. "<br>";
    }else{
        $valiFlg += 1;
    }
    return $valiFlg;
}

// 規定の形式(文字種類)に適合しているか
function checkMatchPattern($pattern, $data, $which, $valiFlg){
    // 規定の形式にマッチすれば1、しなければ0(エラーはfalse)
    $isMatched = preg_match($pattern, $data);    
    if($isMatched == 1){
        $valiFlg += 1;
    }else{
        $_SESSION["signinErrorMsg"] .= $which. "に使用できない文字が含まれています<br>";
    }
    return $valiFlg;
}

?>