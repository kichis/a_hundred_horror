<!-- デザインはあとで -->

<?php 
session_start();
require("db_connection.php");
include("funcs.php");

// フォームに入力した値のバリデーション

$uname = $_POST["uname"];
$email = $_POST["email"];
$confemail = $_POST["confirmEmail"];
$passw = $_POST["passw"];

// フォームへの入力があるかチェックする
function isFilled($data){
    if(empty($data)){
        // カラの時の処理
        echo "empty data";
    }else{
        return $data;
    }
}
isFilled($uname);
isFilled($email);
isFilled($confemail);
isFilled($passw);

// 文字数制限内かチェックする
function checkInputLength($data, $limit){
    if(mb_strlen($data) > $limit){
        // 文字数オーバーのときの処理
        echo "over size";
    }else{
        return $data;
    }
}
checkInputLength($uname, 100);
checkInputLength($email, 255);
checkInputLength($confemail, 255);
checkInputLength($passw, 255);

// メールアドレスは正しい形式化？半角英数・記号に限る、@必要、＠以降に文字列必要
$em = "l'l.!#$%&*+-/=?^_{|}~l@tk.com";
// ドットなしドメイン名 (dotless domain name)-> false
// @の前に何もない->f
// 全角の入力->f
// 空白の入力->f
checkCorrectEmail($em);
function checkCorrectEmail($data){
    if(!filter_var($data, FILTER_VALIDATE_EMAIL)){
        echo '正しいEメールアドレスを指定してください。';
    }else{
        echo "ok";
    }
}
    // Emailが確認用Emailの入力と合っているか
// if($email != $confemail){
//   $_SESSION["signinErrorMsg"] .= "２つのEmailの入力が異なっています<br>";
//   redirect("signin.php");
// }

// // uname重複check
// // 同じユーザ名の登録があるか
// $sql = "SELECT user_name from users WHERE email=:email";
// $stmt = $pdo->prepare($sql);
// $stmt->bindValue(':email', $email, PDO::PARAM_STR);
// $val = $stmt->fetch();
// if($val["email"] == $email){
//   $_SESSION["signinErrorCode"] += "このEmailアドレスは使用できません(すでに登録があります)<br>";
// }
// // email重複check
// // 同じEmailの登録があるか
// $sql = "SELECT email from users WHERE email=:email";
// $stmt = $pdo->prepare($sql);
// $stmt->bindValue(':email', $email, PDO::PARAM_STR);
// $val = $stmt->fetch();
// if($val["email"] == $email){
//   $_SESSION["signinErrorCode"] += "このEmailアドレスは使用できません(すでに登録があります)<br>";
// }

// 適切な形に
// ユーザ名：英数字・記号・ひらがな・カタカナ・漢字ok
// アドレス：半角英数・記号に限る
// pass：半角英数に限る(大文字・小文字区別)、記号不可 
// ユーザー入力データから不要な文字（余分なスペース、タブ、改行）を取り除く（PHPのtrim()関数を使用）
// ユーザ入力データからバックスラッシュ（\）を削除する（PHPのstripslashes()関数を使用）



// 適切化はHTMLタグもいじる
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
                    <!-- 100文字以下、英数字・記号・ひらがな・カタカナ・漢字ok -->
                    <p>お名前<small class="ml-3">ユーザー名として表示されます。100文字以下</small><input type="text" name="uname" maxlength='100' class="form-control"></p>
                    <!-- 255文字以下、半角英数・記号に限る、@必要、＠以降に文字列必要 -->
                    <p>Email<input type="email" name="email" maxlength='255' class="form-control"></p>
                    <p>Email(確認用)<input type="email" name="confirmEmail" maxlength='255' class="form-control"></p>
                    <!-- 255文字以下、半角英数に限る(大文字・小文字区別)、記号不可 -->
                    <p>パスワード<small class="ml-3">半角英数字のみ(※大文字と小文字は区別されます)</small><input type="password" name="passw" maxlength='255' class="form-control"></p>
                    <p id="errorMsg" class="text-danger">
                        <?php 
                        echo $_SESSION["signinErrorMsg"];
                        // switch($_SESSION["signinErrorCode"]){
                        //     case "1":
                        //         echo "２つのEmailの入力が異なっています";
                        //         continue;
                        //     default:
                        //         // echo "なぞのエラー！";
                        // }
                        // $_SESSION["signinErrorMsg"] = "";
                        ?>
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