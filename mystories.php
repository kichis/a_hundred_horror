<!-- デザインはあとで -->

<?php 
ini_set('display_errors', 1);

session_start();
require("db_connection.php");
include("funcs.php");

ss_chg();
avoid();

$user_id = $_SESSION["user_id"];

$pdo = db_conn();
$sql = "SELECT stories.story_id AS story_id, stories.title AS title, stories.content AS content, stories.date AS date, users.user_name AS user, stories.num_horror AS horror 
FROM stories INNER JOIN users ON stories.user_id = users.user_id 
WHERE users.user_id = $user_id AND status = 1 ORDER BY story_id DESC;";
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
    <!-- <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@700&display=swap"rel="stylesheet"> -->

    <!-- login icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous"> 
    
    <link rel="stylesheet" href="css/style.css">
    <title>あなたと百物語 ｜ 私の「語り」</title>
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
    <p id="test"></p>
    <div id="mainImage_2">
        <div id="mystory_main" class="mx-auto">
            <h4>私の「語り」</h4>
            <?php while( $r = $stmt->fetch(PDO::FETCH_ASSOC)):?>   
                <div>
                    <p class="d-inline-flex mr-2 mb-0">#<?=$r["story_id"]?></p>
                    <p class="d-inline-flex" class="">
                        <b>
                            <a href="story.php?story_id=<?=$r["story_id"]?>"><?= h($r["title"])?></a>
                        </b>
                    </p>
                    <p class="mb-5">
                        <i class="fas fa-ghost"></i>&nbsp;<?=$r["horror"]?>&nbsp;/&nbsp;<?=$r["date"]?>
                        <button class="btn btn-sm text-white border-success mx-3" onclick="location.href=`edit_story.php?story_id=<?= $r['story_id']?>`">編集</button>
                        <button class="btn btn-sm text-white border-danger" value="<?= $r["story_id"]?>" onclick="confirmDelete(this)">削除</button>
                    </p>
                </div>
            <?php endwhile ?>
        </div>
    </div>

    <?php include("copyright.php"); ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

<script>

function confirmDelete(btn){
    let id = btn.value
    if(window.confirm('この「語り」を本当に削除してよろしいですか？')){
        // delete手続き
        location.href = 'delete_story.php?story_id=' + id;
    }else{
        // キャンセルを押した場合は何もしない
    }
}

</script>

</body>
</html>