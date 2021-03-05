<!-- デザインはあとで -->

<?php 
// ini_set('display_errors', 1);

session_start();
require("db_connection.php");
include("funcs.php");


// 以下、フォームに入力した値のバリデーション

$uname = $_POST["uname"];
$email = $_POST["email"];
$confemail = $_POST["confirmEmail"];
$passw = $_POST["passw"];
$infos = [
    'お名前' => $uname,
    'Email' => $email,
    '確認用Email' => $confemail,
    'パスワード' => $passw
];

// バリデーションを1つ通過するごとに+1する。全バリデーション通過 = 14
$valiFlg = 0;
$_SESSION["signinErrorMsg"] = '';

// 関数実行
if(isset($uname)){
    foreach($infos as $key => $value){
        $valiFlg = isFilled($key, $value, $valiFlg); // 4
        // echo $key . "のブランクis" .$valiFlg;
        $valiFlg = checkInputLength($key, $value, $valiFlg); // 4
    }
    $valiFlg = checkCorrectEmail($email, $confemail, $valiFlg); // 1
    
    // dbへ接続
    $pdo = db_conn();
    $valiFlg = checkSameRecord($pdo, 'user_name', $uname, 'このお名前は使用できません(すでに登録があります)', $valiFlg); // 1
    $valiFlg = checkSameRecord($pdo, 'email', $email, 'このEmailは使用できません(すでに登録があります)', $valiFlg); // 1
    
    // ユーザ名：英数字・記号・ひらがな・カタカナ・漢字ok(日本語は常用しないものは正規表現に含めず)
    $valiFlg = checkMatchPattern('/^[ぁ-ゔァ-ヶ一-龠々・ーゞ＝0-9a-zA-Z\/\-\'".!#$%&*+=?^_`{|}~@(),:[\]]+$/u', $uname, 'お名前', $valiFlg); // 1
    // Email:半角英数・記号(RFC822に準拠)に限る(注：正規表現中の\はエスケープ用であって、規定外の記号である)
    // HACK: checkCorrectEmail()でバリデーションできていると思われるが、FILTER_VALIDATE_EMAILの範囲が不明瞭なので保険としてここでもバリデーションする
    $valiFlg = checkMatchPattern("/^([\/\-0-9a-zA-Z.!#$%&'*+=?^_`{|}~@])+$/", $email, 'Email', $valiFlg); // 1
    // password:半角英数に限る(大文字・小文字区別)、記号不可
    $valiFlg = checkMatchPattern('/^[0-9a-zA-Z]+$/', $passw, 'パスワード', $valiFlg); // 1
}

if($valiFlg == 14){
    // dbへ登録する処理へ
    echo "入力完璧！";
    $_SESSION["uname"] = $uname;
    $_SESSION["email"] = $email;
    $_SESSION["passw"] = $passw;
    redirect("signin_act.php");
}

?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- デザインはあとで -->
    <!-- base font -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@500&display=swap" rel="stylesheet">
    <!-- specific font -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@700&display=swap" rel="stylesheet"> -->

    <!-- Bootstrap only -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous"> 
    <link rel="stylesheet" href="css/style.css">
    <title>あなたと百物語 ｜語り手登録</title>

</head>

<body class="body">
    <?php 
    $menu = function (){
        switch($_SESSION["user_status"]){
            case 1:
                return "menu_user.php";
            case 2:
                return "menu_admin.php";
            case 3:
                return "menu_ban.php";
            default:
                return "menu_visit.php";
        }
    };
    include($menu());
    ?>

    <div id="mainImage">
        <div id="signin_1" class="text-dark mx-auto">
            <form method="post" action="signin.php">
                <div class="jumbotron pb-4">
                    <h3 class="mb-4">「語り手」アカウント作成</h3>
                    <!-- 3文字以上、100文字以下、英数字・記号・ひらがな・カタカナ・漢字ok -->
                    <p>お名前<small class="ml-3">ユーザー名として表示されます。3文字以上、100文字以下</small><input type="text" name="uname" maxlength='100' minlength='3' placeholder='おばけちゃん' class="form-control" required value="<?=$uname?>"></p>
                    <!-- 3文字以上、255文字以下、半角英数・記号に限る、@必要、＠以降に文字列必要 -->
                    <p>Email<input type="email" name="email" maxlength='255' minlength='3' placeholder='obake@ghost.com' class="form-control" required value="<?=$email?>"></p>
                    <p>Email(確認用)<input type="email" name="confirmEmail" maxlength='255' minlength='3' placeholder='obake@ghost.com' class="form-control" required value="<?=$confemail?>"></p>
                    <!-- 8文字以上、255文字以下、半角英数に限る(大文字・小文字区別)、記号不可 -->
                    <p>パスワード<small class="ml-3">半角英数字のみ、8文字以上</small><input type="password" name="passw" maxlength='255' minlength='8' class="form-control" required value="<?=$passw?>"></p>
                    <p id="errorMsg" class="text-danger">
                        <?= $_SESSION["signinErrorMsg"];?>
                    </p>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-lg btn-secondary mt-3" name='action' value='send'>登録</button>           
                    </div>
                </div>
            </form>
        </div>      
    </div>

    <?php include("copyright.php"); ?>

<!-- JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
</body>
</html>