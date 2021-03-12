<!-- デザインはあとで -->

<?php 
ini_set('display_errors', 1);

session_start();
require("db_connection.php");
include("funcs.php");

ss_chg();

$id = $_GET["story_id"];

// dbへ接続・指定idのレコード取得
$pdo = db_conn();
$sql = "SELECT stories.story_id AS story_id, stories.title AS title, users.user_name AS user, stories.user_id AS user_id, stories.date AS date, stories.content AS content, stories.num_horror AS horror 
FROM stories INNER JOIN users ON stories.user_id = users.user_id WHERE story_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

// レコードを変数へ
if($status==false) {
  sql_error($stmt);
}else{
  $r = $stmt->fetch();
}

// コメントを取得
// $comm = showComm($id);

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

    <!-- icon用 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous"> 
   
    <link rel="stylesheet" href="css/style.css">
    <title>あなたと百物語</title>
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
        <!-- <div id="gray">a</div> -->
            <div id="story_main" class="mx-auto">
                <p>#<?=$r["story_id"]?></p>
                <h4 class="text-primary"><?= h($r["title"])?></h4>   
                <p>[ &nbsp; 語り手：<?= $r["user"];?> &nbsp; / &nbsp; <?= $r["date"];?> &nbsp; ]</p>
                <p id="story_content"><?= $r["content"];?></p>
                <p><i class="fas fa-ghost mr-2"></i><?= $r["horror"]?></p>
            
                <?php
                if($_SESSION["user_status"]==1||$_SESSION["user_status"]==2){
                    $pdo = db_conn();
                    $sql = "SELECT * FROM horrors WHERE story_id = :id AND user_id = :user_id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                    $stmt->bindValue(':user_id', $_SESSION["user_id"], PDO::PARAM_INT);
                    $status = $stmt->execute();
                    if($status==false) {
                        sql_error($stmt);
                    }else{
                        $isPushHorror = $stmt->fetch();
                    }

                    echo "<form mehod='get' action='add_horror.php'>
                            <button type='submit' name='story_id' value='".$r["story_id"]."' 
                            class='btn btn-sm bg-dark text-white'";
                        if($isPushHorror==false){
                            echo ">怖！</button>";
                        }else{
                            echo "disabled>怖！済み</button>";
                        }
                    echo    "<input type='hidden' name='user_id' value='".$_SESSION['user_id']."'>
                         </form>";
                }
                ?>
                <!-- コメントの表示 -->


                <p><br>感想：</p>
                <?php
                // echo $comm;
            
                if($_SESSION["user_status"]==1||$_SESSION["user_status"]==2){
                    echo "<br><form method='post' action='comm_act.php'>
                    <textarea name='comment' rows='3' class='form-control w-50' style='float:left'></textarea>
                    <button type='submit' class='btn btn-sm bg-dark text-white ml-3' name='id' value='".$r["id"]."'>感想</button>
                    </form>";
                }
                ?>
            </div>
        
         <!-- </div> -->
            <div id="story_2">
                <a href="home.php">▽ホームへ戻る</a>
            </div>
    </div>

    <?php include("copyright.php"); ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

</body>
</html>