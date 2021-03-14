<!-- デザインはあとで -->

<?php 
ini_set('display_errors', 1);

session_start();
require("db_connection.php");
include("funcs.php");

$story_id = $_GET["story_id"];
$user_id = $_SESSION["user_id"];

ss_chg();
$pdo = db_conn();
// 自分のストーリー以外編集できないように
avoidNonAuthor($pdo, $story_id, $user_id);

$sql = "SELECT * FROM stories WHERE story_id = :story_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':story_id', $story_id, PDO::PARAM_INT);
$status = $stmt->execute();

if($status==false) {
    sql_error($stmt);
}else{
    $r = $stmt->fetch();
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
    <title>あなたと百物語 ｜ 「語り」を編集</title>
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
        <div class="storyform_area mx-auto">
            <form method="post" action="edit_story_act.php">
                <h5 class="ml-2">#<?=$r["story_id"]?></h5>
                <!-- 必須、文字数 100字まで -->
                <h4 class="text-primary mb-4 ml-2">題名</h4>
                    <input type="text" name="title" maxlength="100" class="form-control mb-4" value="<?= h($r["title"])?>" required>
                <!-- 必須、文字数 10万字まで -->
                <h5 class="text-primary mb-4 ml-3">語り</h5>
                    <textarea name="content" cols="30" rows="20" maxlength="100000" class="form-control mb-5" required><?= h($r["content"])?></textarea>
                <div class="d-flex">
                    <button type="submit" class="btn btn-md bg-success px-5 mx-auto hover_white">編集 完了</button>
                </div>
                <input type="hidden" name="story_id" value="<?=$r["story_id"]?>">
                <!-- <input type="hidden" name="hoge" value="1"> -->
            </form>
        </div>  
    </div>

    <?php include("copyright.php"); ?>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
</body>
</html>