<!-- デザインはあとで -->

<?php 
ini_set('display_errors', 1);

session_start();
require("db_connection.php");
include("funcs.php");

ss_chg();
avoidUser();

$pdo = db_conn();
$sql = "SELECT stories.story_id AS story_id, stories.title AS title, users.user_name AS user, stories.date AS `date`, stories.num_horror AS horror, stories.status AS story_status FROM stories INNER JOIN users ON stories.user_id = users.user_id 
ORDER BY story_id DESC;";
$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

// データ表示取得
$view="";
if($status==false) {
  sql_error($stmt);
}else{
}
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
        <div id="story_list" class="mx-auto pt-5">
            <h3 class="text-center mb-5">「語り」一覧</h3>
            <table class="table text-danger">
                <tr>
                    <th class="pl-4 bg-dark">story_id</th>
                    <th class="pl-2 bg-dark">Title</th>
                    <th class="pl-2 bg-dark">Author</th>
                    <th class="pl-2 bg-dark">Date of post</th>               
                    <th class="pl-2 bg-dark">Horror</th>               
                    <th class="pl-2 bg-dark">Status</th>        
                    <th class="pl-2 bg-dark">Status変更</th>        
                </tr>
                <form method="post" action="edit_user_act.php" class="form-label">
                    <?php while( $r = $stmt->fetch(PDO::FETCH_ASSOC)):?>
                    <tr>
                        <td class="pl-4"><?=$r["story_id"]?></td>
                        <td class="pl-2"><?= h($r["title"])?></td>
                        <td class="pl-2"><?= h($r["user"])?></td>
                        <td class="pl-2"><?=$r["date"]?></td>
                        <td class="pl-2"><?=$r["horror"]?></td>

                        <td class="pl-2" id="status_<?= $r["story_id"]?>"><?=$r["story_status"]?></td>
                        <td class="pl-2">
                            <input type="checkbox" name="edited" class="checkbox form-control form-control-sm" value="<?=$r["story_id"]?>">
                            <!-- 現在のstatus#を保持する用 -->
                            <input type="hidden" id="statusNum_<?= $r['story_id']?>" value="<?=$r["story_status"]?>">
                        </td>
                    </tr>
                    <?php endwhile?>
            </table>
            <div class="w-50 mx-auto">
                <p class="mb-2">※ Status</p>
                <p>
                    0: 非表示(投稿者が削除 or 管理者が非表示操作した「語り」)<br>
                    1: 表示(＊)<br>
                    ※ ブラックリストユーザーの「語り」はこの一覧では"1"として表示されますが、実際のウェブサイト上には表示されません。
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

    <!-- name & value -->
    <!-- 配列？ -->
<script>
$('input[type="checkbox"]').on('change', function(){
    let checkbox = $(this)
    let story_id = checkbox.val()
    let statusCol = $('#status_' + story_id)
    let statusNum = $('#statusNum_' + story_id).val()
    
    if(checkbox.prop('checked')){
        // checked
        // 方法1(保守性がある:複数選択肢になっても使える)
        statusCol.html('<select name="status" class="form-select form-select-sm form-control form-control-sm" id="statusRev_'+ story_id +'">'+  
                            '<option value="0">0:非表示</option>'+
                            '<option value="1">1:表示</option>'+
                        '</select>')
        $('#statusRev_' + story_id +' option[value='+ statusNum +']').prop('selected', true)

        // 方法2(可読性が高い？)
        // const options = statusNum == 0 ? 
        // '<option value="0" selected>0:非表示</option><option value="1">1:表示</option>' : 
        // '<option value="0">0:非表示</option><option value="1" selected>1:表示</option>'
        // statusCol.html('<select name="status" class="form-select form-select-sm form-control form-control-sm" id="">'+ options + '</select>')
    }else{
        // unchecked
        statusCol.text(statusNum)
        
    }
    // console.log(statusNum)

})

</script>


</body>
</html>