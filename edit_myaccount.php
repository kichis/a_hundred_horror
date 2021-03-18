<?php
session_start();
require("db_connection.php");
include("funcs.php");

ss_chg();
avoid();

$user_id = $_SESSION["user_id"];

// 現行のユーザ情報取得
$pdo = db_conn();
$sql = "SELECT * FROM users WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$status = $stmt->execute();
if($status==false) sql_error($stmt);
$r = $stmt->fetch();

// 以下、情報を修正する場合の値のバリデーション

$user_id = $_SESSION["user_id"];
$uname = $_POST["uname"];
$email = $_POST["email"];
$passwNow = $_POST["passwNow"];
$passwRev = $_POST["passwRev"];
$infos = [
    'お名前' => $uname,
    'Email' => $email,
];

// バリデーションを1つ通過するごとに+1する。
$valiFlg = 0;
$_SESSION["signinErrorMsg"] = '';

// 関数実行
if(isset($uname)){

    // 1、現行パスワードがあっているか // 1  
    if(password_verify($passwNow, $r["passw"])){ 
        $valiFlg++;
    }else{
        $_SESSION["signinErrorMsg"] .= "現行パスワードが間違っています<br>";
    }

    // 2、uname,emailが入力されているか // 2
    // 3、uname,emailの入力文字数は許容内か // 2
    foreach($infos as $key => $value){
        $valiFlg = isFilled($key, $value, $valiFlg);
        $valiFlg = checkInputLength($key, $value, $valiFlg);
    }

    // 4、メールアドレスは正しい形式か（半角英数・記号に限る、@必要、＠以降に文字列必要） //1
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $_SESSION["signinErrorMsg"] .= "この形式のEmailはご登録いただけません<br>";
    }else{
        $valiFlg += 1;
    }

    // 5、 修正したuname,emailが他人の登録と被っていないか(退会済みユーザも含め) // 2
    $valiFlg = checkSameRecordExptMe($pdo, 'user_name', $uname, $user_id, 'このお名前は使用できません(すでに登録があります)', $valiFlg);
    $valiFlg = checkSameRecordExptMe($pdo, 'email', $email, $user_id, 'このEmailは使用できません(すでに登録があります)', $valiFlg);


    // 6、既定の形式に適合しているか // 2
    // ユーザ名：英数字・記号・ひらがな・カタカナ・漢字ok(日本語は常用しないものは正規表現に含めず)
    $valiFlg = checkMatchPattern('/^[ぁ-ゔァ-ヶ一-龠々・ーゞ＝0-9a-zA-Z\/\-\'".!#$%&*+=?^_`{|}~@(),:[\]]+$/u', $uname, 'お名前', $valiFlg);
    // Email:半角英数・記号(RFC822に準拠)に限る(注：正規表現中の\はエスケープ用であって、規定外の記号である)
    // HACK: checkCorrectEmail()でバリデーションできていると思われるが、FILTER_VALIDATE_EMAILの範囲が不明瞭なので保険としてここでもバリデーションする
    $valiFlg = checkMatchPattern("/^([\/\-0-9a-zA-Z.!#$%&'*+=?^_`{|}~@])+$/", $email, 'Email', $valiFlg);
   
    $isPasswChange = mb_strlen($passwRev) > 0;
    if($isPasswChange){
        // passwRevは入力文字数は許容内か // 1
        $valiFlg = checkInputLength('パスワード', $passwRev, $valiFlg);
        // 半角英数に限る(大文字・小文字区別)、記号不可 // 1
        $valiFlg = checkMatchPattern('/^[0-9a-zA-Z]+$/', $passwRev, '変更後パスワード', $valiFlg);
    }

    // 全てのバリデーションを通過できたらdbにupdate(通過できない項目があれば、#errorMsgにメッセージ表示)
    if((!$isPasswChange && $valiFlg == 10) || ($isPasswChange && $valiFlg == 12)){
        $_SESSION["uname"] = $uname;
        $_SESSION["email"] = $email;
        $_SESSION["passwRev"] = $passwRev;
        $_SESSION["passwFlg"] = $isPasswChange;
        redirect("edit_myaccount_act.php");
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- base font -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@500&display=swap" rel="stylesheet">
    <!-- login icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous"> 
    <link rel="stylesheet" href="css/style.css">
    <title>あなたと百物語 ｜ 「語り手」情報</title>
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

    <div id="mainImage_2">
        <div class="myaccount_area mx-auto">
        <!-- 編集処理 -->
            <form method="post" action="edit_myaccount.php">
                <small class="text-warning">お名前・Emailの変更：内容修正後、<u>現行パスワードを入力し</u>、「修正」ボタンを押して下さい</small>
                <!-- 3文字以上、100文字以下、英数字・記号・ひらがな・カタカナ・漢字ok -->
                <h5 class="my-4">お名前<small class="ml-3">ユーザー名として表示されます。3文字以上、100文字以下</small>
                    <input type="text" name="uname" maxlength='100' minlength='3' class="form-control mt-3" required value="<?= h($r["user_name"])?>">
                </h5>
                <!-- 3文字以上、255文字以下、半角英数・記号に限る、@必要、＠以降に文字列必要 -->
                <h5 class="mb-4">Email
                    <input type="email" name="email" maxlength='255' minlength='3' class="form-control mt-3" required value="<?= h($r["email"])?>">
                    </h5>
                <!-- 8文字以上、255文字以下、半角英数に限る(大文字・小文字区別)、記号不可 -->
                <h5 class="mb-5">現行パスワード<small class="ml-3">半角英数字のみ、8文字以上</small>
                <input type="password" name="passwNow" maxlength='255' minlength='8' class="form-control mt-3" requied>
                </h5>
                <small class="text-warning">パスワードの変更：現行パスワードと変更後パスワードを入力後、「修正」ボタンを押して下さい</small>
                <h5 class="my-3 text-muted">変更後パスワード<small class="ml-3">半角英数字のみ、8文字以上</small>
                    <input type="password" name="passwRev" maxlength='255' minlength='8' class="form-control mt-3">
                </h5>

                <p id="errorMsg" class="text-danger">
                    <!-- ユーザ入力文字列は含まないのでh()しない -->
                    <?= $_SESSION["signinErrorMsg"];?>
                </p>
                <div class="d-flex">
                    <button type="submit" class="btn btn-md bg-success text-white border-white px-5 mx-auto mt-5" name='action' value='send'>修正</button>           
                </div>
            </form>
        <!-- 削除処理 -->
            <form id="deleteForm" method="post" action="delete_myaccount_act.php">
                <input type="hidden" name="user_id" value="<?= $_SESSION["user_id"]?>">
                <div class="d-flex mt-5">
                    <button type="button" class="btn btn-md text-danger border-danger px-5 mx-auto hover_white" data-toggle="modal" data-target="#confDeleteModal">登録を削除</button>       
                </div>
            </form>

            <!-- 削除確認Modal -->
            <div class="modal fade text-dark" id="confDeleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalCenterTitle">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title font-weight-bold pl-3" id="deleteModalCenterTitle">最終確認</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>一度「語り手」としての登録を削除すると、<br>再び同じユーザ名・emailでのご登録はできません。</p>
                            <p>本当に削除してよろしいですか？</p>
                        </div>
                        <div class="modal-footer d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                            <button  id="deleteBtn" type="button" class="btn btn-danger text-dark px-5"><b>削除</b></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="story_bottom" class="mx-auto">
            <a href="home.php">← HOMEへ戻る</a>
        </div>
    </div>

    <?php include("copyright.php");?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script>
    $(function() {
    // <button>要素をクリックしたら発動
        $('#deleteBtn').click(function() {    
            // <form>要素を送信
            $('#deleteForm').submit();
        });
    });
    </script>
</body>
</html>