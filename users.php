<?php 
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
}else{
    $result = $stmt->fetchAll();
    $php_json = json_encode($result);
}

// 以下、情報を修正する場合の値のバリデーション

$uname = $_POST["edited_uname"];
$email = $_POST["edited_email"];
$user_status = $_POST["edited_status"];
$user_id = $_POST["edited_user_id"];

$valiFlg = 0; // バリデーションに引っかかった場合は-1する
$_SESSION["signinErrorMsg"] = '';

if(isset($uname)){

    // uname,emailが入力されているか
    // uname,emailの入力文字数は許容内か
    // emailは正しい形式か（半角英数・記号に限る、@必要、＠以降に文字列必要)
    // 修正したuname,emailが他人の登録と被っていないか(退会済みユーザも含め)
    // 既定の形式に適合しているか
    foreach($uname as $key => $value){
        $valiFlg = isFilledArray($key, $value, 'ユーザ名', $user_id, $valiFlg,);
        $valiFlg = checkInputLengthArray($key, $value, 'お名前', $user_id, $valiFlg);
        $valiFlg = checkSameRecordExptMeArray($pdo, 'user_name', $value, $user_id[$key], "user_id:{$user_id[$key]} このお名前は使用できません(すでに登録があります)", $valiFlg);
        // ユーザ名：英数字・記号・ひらがな・カタカナ・漢字ok(日本語は常用しないものは正規表現に含めず)
        $valiFlg = checkMatchPatternArray('/^[ぁ-ゔァ-ヶ一-龠々・ーゞ＝0-9a-zA-Z\/\-\'".!#$%&*+=?^_`{|}~@(),:[\]]+$/u', $value, "user_id:{$user_id[$key]} お名前に使用できない文字が含まれています<br>", $valiFlg);
    }
    foreach($email as $key => $value){
        $valiFlg = isFilledArray($key, $value, 'Email', $user_id, $valiFlg,);
        $valiFlg = checkInputLengthArray($key, $value, 'Email', $user_id, $valiFlg);
        if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
            $_SESSION["signinErrorMsg"] .= "user_id:{$user_id[$key]} この形式のEmailはご登録いただけません<br>";
            $valiFlg -= 1;
        }
        $valiFlg = checkSameRecordExptMeArray($pdo, 'email', $value, $user_id[$key], "user_id:{$user_id[$key]} このEmailは使用できません(すでに登録があります)", $valiFlg);
        // Email:半角英数・記号(RFC822に準拠)に限る(注：正規表現中の\はエスケープ用であって、規定外の記号である)
        // HACK: checkCorrectEmail()でバリデーションできていると思われるが、FILTER_VALIDATE_EMAILの範囲が不明瞭なので保険としてここでもバリデーションする
        $valiFlg = checkMatchPatternArray("/^([\/\-0-9a-zA-Z.!#$%&'*+=?^_`{|}~@])+$/", $value, "user_id:{$user_id[$key]} Emailに使用できない文字が含まれています<br>", $valiFlg);
        
    }

    // 全てのバリデーションを通過できたらdbにupdate(通過できない項目があれば、#errorMsgにメッセージ表示)
    if($valiFlg == 0){
        $_SESSION["edited_uname"] = $uname;
        $_SESSION["edited_email"] = $email;
        $_SESSION["edited_status"] = $user_status;
        $_SESSION["edited_user_id"] = $user_id;
        redirect("edit_user_act.php");
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
    <!-- pagenation.js -->
    <link rel="stylesheet" href="./node_modules/paginationjs/dist/pagination.css">
    <title>あなたと百物語 | 管理画面「語り手」</title>
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
            <p id="errorMsg" class="text-danger w-50 mx-auto">
                <!-- ユーザ入力文字列は含まないのでh()しない -->
                <?= $_SESSION["signinErrorMsg"];?>
            </p>
            <form method="post" action="users.php">
                <table class="table text-danger" id="datas-all-contents"></table>
                <div class="pager d-flex justify-content-center mt-5" id="datas-all-pager"></div>
                <div class="w-25 ml-auto">
                    <p class="mb-2">※ ユーザステータス</p>
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
    <!-- 自作JSファイル -->
    <script src="./JS/funcs.js"></script>
    <!-- pagination.js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="./node_modules/paginationjs/dist/pagination.js"></script>

<script>
$(function() {

    $(document).on('change', 'input[type="checkbox"]', function(){
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

    // ページネーション
    // [1] 配列のデータを用意  注意!! datasをHTML出力する場合は、sani()を使ってサニタイズすること !!
    let datas = JSON.parse('<?= $php_json?>')

    // [2] pagination.jsの設定
    $(function() {
        $('#datas-all-pager').pagination({
            dataSource: datas,
            pageSize: 10, // 1ページあたりの表示数
            prevText: ' &nbsp; &lt; 前へ &nbsp; ',
            nextText: ' &nbsp; 次へ &gt; &nbsp;',
            // ページがめくられた時に呼ばれる
            callback: function(data, pagination) {
                const thFormat = '<tr>'+
                        '<th class="pl-4 bg-dark">user_id</th>'+
                        '<th class="pl-2 bg-dark">ユーザ名</th>'+
                        '<th class="pl-2 bg-dark">Email</th>'+
                        '<th class="pl-2 bg-dark">ユーザステータス ※</th>'+               
                        '<th class="pl-2 bg-dark">内容を変更</th>'+                     
                    '</tr>'
                $('#datas-all-contents').html(thFormat + template(data));
                $('body,html').animate({scrollTop:0}, 400, 'swing');
            }
        });
    });
    
    // [3] データ1つ1つをhtml要素に変換する
    function template(dataArray) {
        return dataArray.map(function(data) {
        return '<tr>'+
                    '<td class="pl-4">'+ data.user_id +'</td>'+
                    '<td class="pl-2" id="uname_'+ data.user_id +'">'+ data.user_name +'</td>'+
                    '<td class="pl-2" id="email_'+ data.user_id +'">'+ data.email +'</td>'+
                    '<td class="pl-2" id="status_'+ data.user_id +'">'+ data.user_status +'</td>'+
                    '<td class="pl-2">'+
                        '<input type="checkbox" name="edited_user_id[]" class="checkbox form-control form-control-sm" value="'+ data.user_id +'">'+
                    '</td>'+
                '</tr>'
        })
    }

})

</script>
</body>
</html>
