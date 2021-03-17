<!-- デザインはあとで -->

<?php 
ini_set('display_errors', 1);

session_start();
require("db_connection.php");
include("funcs.php");

ss_chg();
avoidUser();

$pdo = db_conn();
$sql = "SELECT * FROM users ORDER BY user_id ASC;";
$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

if($status==false) {
  sql_error($stmt);
}else{}
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
    <title>あなたと百物語 | 管理画面</title>
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

    <div id="admin_area">
        <div id="user_list" class="mx-auto pt-5">
            <h3 class="text-center mb-5">「語り手」登録者一覧</h3>
            <form method="post" action="edit_user_act.php">
            <table class="table text-danger">
                <tr>
                    <th class="pl-4 bg-dark">user_id</th>
                    <th class="pl-2 bg-dark">user_name</th>
                    <th class="pl-2 bg-dark">email</th>
                    <th class="pl-2 bg-dark">user_status ※</th>               
                    <th class="pl-2 bg-dark">内容を変更</th>               
                </tr>
                <?php 
                $user_datas = array();
                while( $r = $stmt->fetch(PDO::FETCH_ASSOC)):
                      ?>
                <tr>
                    <td class="pl-4">
                        <?=$r["user_id"]?>
                    </td>
                    <td class="pl-2" id="uname_<?= $r["user_id"]?>">
                        <?=$r["user_name"]?>
                    </td>
                    <td class="pl-2" id="email_<?= $r["user_id"]?>">
                        <?=$r["email"]?>
                    </td>
                    <td class="pl-2" id="status_<?= $r["user_id"]?>">
                        <?=$r["user_status"]?>
                    </td>
                    <td class="pl-2">
                        <input type="checkbox" name="edited_user_id[]" class="checkbox form-control form-control-sm" value="<?=$r["user_id"]?>">
                    </td>
                </tr>
                <?php endwhile?>
            </table>
            <div class="w-25 ml-auto">
                <p class="mb-2">※ user_status</p>
                <p>
                    0: 退会済みユーザー<br>
                    1: 登録ユーザー<br>
                    2: 管理者<br>
                    3: ブラックリストユーザー
                </p>
            </div>
            <button type="submit" class="btn btn-md bg-dark text-white border-white d-flex py-2 px-5 mt-5 mx-auto">変更を確定</button>
            </form>
            </div>
        <div id="admin_bgimg"></div>
    </div>

    <?php include("copyright.php"); ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<script>
$(function() {

    $('input[type="checkbox"]').on('change', function(){
        let checkbox = $(this)
        let user_id = checkbox.val()
        let unameCol = $('#uname_' + user_id)
        let emailCol = $('#email_' + user_id)
        let statusCol = $('#status_' + user_id)
        
        ajax(user_id).then(function(result) {
            // result = ajaxの返値
            if(checkbox.prop('checked')){
                // checked
                unameCol.html('<input type="text" name="edited_uname[]" maxlength="100" minlength="3" class="form-control" required value="' + result["user_name"] + '">')
                emailCol.html('<input type="email" name="edited_email[]" maxlength="255" minlength="3" class="form-control" required value="'+ result["email"] +'">')
                statusCol.html('<select name="edited_status[]" class="form-select form-select-sm form-control" id="edited_status_'+ user_id + '">'+  
                        '<option value="0">0:退会済み</option>'+
                        '<option value="1">1:登録ユーザー</option>'+
                        '<option value="2">2:管理者</option>'+
                        '<option value="3">3:ブラックリストユーザー</option>'+
                    '</select>')
                $('#edited_status_' + user_id + ' option[value='+ result["user_status"] + ']').prop('selected', true)

            }else{
                // unchecked
                unameCol.text(result["user_name"])
                emailCol.text(result["email"])
                statusCol.text(result["user_status"])
    
            }
        })
    })

    function ajax(data){
        return new Promise(function(resolve, reject) {
            $.ajax({
                url: 'ajax_getUser.php',
                type: 'POST',
                data: { "user_id" : data },
                dataType: 'json',
                timeout: 5000,
            })
            // 検索成功時にはdataをresolveとして返す
            .done(function(data) {
                resolve(data)
            })
            // 検索失敗時には、その旨をダイアログ表示
            .fail(function() {
                reject("システムでエラーが発生しました"); 
                window.alert('システムでエラーが発生しました');
            });
        })
    };

})
</script>
</body>
</html>
