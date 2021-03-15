<!-- デザインはあとで -->

<?php
// ini_set('display_errors', 1);

session_start();
require("db_connection.php");
include("funcs.php");

ss_chg();
avoid();

$user_id = $_SESSION["user_id"];

// dbへ接続
$pdo = db_conn();

// 語り情報取得（新しい順に表示）
$sql = "SELECT * FROM users WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$status = $stmt->execute();
if($status==false) sql_error($stmt);
$r = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- スタイルはあとで -->
    <!-- base font -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@500&display=swap" rel="stylesheet">
    <!-- specific font -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@700&display=swap" rel="stylesheet"> -->

    <!-- login icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous"> 
    <link rel="stylesheet" href="css/style.css">
    <title>あなたと百物語 ｜ 語る</title>
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
            <form method="post" action="edit_myaccount_act.php">
                <!-- 3文字以上、100文字以下、英数字・記号・ひらがな・カタカナ・漢字ok -->
                <h5 class="mb-4">お名前<small class="ml-3">ユーザー名として表示されます。3文字以上、100文字以下</small>
                    <input type="text" name="uname" maxlength='100' minlength='3' class="form-control mt-3" required value="<?= h($r["user_name"])?>">
                </h5>
                <!-- 3文字以上、255文字以下、半角英数・記号に限る、@必要、＠以降に文字列必要 -->
                <h5 class="mb-5">Email
                    <input type="email" name="email" maxlength='255' minlength='3' class="form-control mt-3" required value="<?= h($r["email"])?>">
                </h5>
                <p class="text-warning">パスワード変更：現行パスワードと変更後パスワードの両方を入力して下さい</p>
                <!-- 8文字以上、255文字以下、半角英数に限る(大文字・小文字区別)、記号不可 -->
                <h5 class="mb-4">現行パスワード<small class="ml-3">半角英数字のみ、8文字以上</small>
                    <input type="password" name="passwNow" maxlength='255' minlength='8' class="form-control mt-3" value="">
                </h5>
                <h5 class="mb-4">変更後パスワード<small class="ml-3">半角英数字のみ、8文字以上</small>
                    <input type="password" name="passwRev" maxlength='255' minlength='8' class="form-control mt-3" value="<?= h($r["$passw"])?>">
                </h5>

                <p id="errorMsg" class="text-danger">
                    <!-- ユーザ入力文字列は含まないのでh()しない -->
                    <?= $_SESSION["signinErrorMsg"];?>
                </p>
                <div class="d-flex">
                    <button type="submit" class="btn btn-md bg-success border-white px-5 mx-auto mt-5 hover_white" name='action' value='send'>修正</button>           
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
    </div>

    <?php 
    $_SESSION["title"] = "";
    $_SESSION["content"] = "";
    include("copyright.php");
    ?>

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