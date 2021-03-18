<?php
// ini_set('display_errors', 1);

session_start();
require("db_connection.php");
include("funcs.php");

$pdo = db_conn();
$sql = "SELECT stories.story_id AS story_id, stories.title AS title, users.user_name AS user, stories.user_id AS user_id, stories.date AS `date`, stories.num_horror AS horror 
FROM stories INNER JOIN users ON stories.user_id = users.user_id 
WHERE status = 1 AND user_status != 3 ORDER BY story_id DESC";
$stmt = $pdo->prepare($sql);
$status = $stmt->execute();
if($status==false) sql_error($stmt);
$r = $stmt->fetchAll();
$php_json = json_encode($r);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- base font -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@500&display=swap" rel="stylesheet">
    <!-- highlight font -->
    <link href="https://fonts.googleapis.com/css2?family=Sawarabi+Gothic&display=swap" rel="stylesheet">
    <!-- login icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous"> 
    <link rel="stylesheet" href="css/style.css">
    <!-- pagenation.js -->
    <link rel="stylesheet" href="./node_modules/paginationjs/dist/pagination.css"><!-- 後で -->
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
        <div id="inst">
            <h5>《百物語（ひゃくものがたり）》</h5>
            <p>夜、数人が集まって順番に怪談を語り合う遊び。<br>
            ろうそくを100本立てておいて、1話終わるごとに1本ずつ消していき、<br>
            100番目が終わって真っ暗になったとき、化け物が現れるとされる。</p>
        </div>
    </div>

    <div id="storyArea" class="mt-5 mx-auto">
        <div id="under"></div>
        <div id="over">
            <ul>
                <div id="datas-all-contents"></div>
            </ul>
            <div class="pager d-flex justify-content-center mt-4" id="datas-all-pager"></div>
        </div>
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
    // ページネーション
    // [1] 配列のデータを用意  注意!! datasをHTML出力する場合は、sani()を使ってサニタイズすること !!
    let datas = JSON.parse('<?= $php_json?>')

    // [2] pagination.jsの設定
    let counter = 0
    $(function() {
        $('#datas-all-pager').pagination({
            dataSource: datas,
            pageSize: 10, // 1ページあたりの表示数
            prevText: ' &nbsp; &lt; 前へ &nbsp; ',
            nextText: ' &nbsp; 次へ &gt; &nbsp;',
            // ページがめくられた時に呼ばれる
            callback: function(data, pagination) {
                // dataの中に次に表示すべきデータが入っているので、html要素に変換
                $('#datas-all-contents').html(template(data));
                // HACK:初回ロード時にscrollTopが呼ばれないようにするためのif文(良い実装ではないが、clickイベントがページネーションボタンで感知できなかったため仕方なし)
                if(counter > 0){
                    var position = $('#storyArea').offset().top;
                    $('body,html').animate({scrollTop:position}, 400, 'swing');
                    counter++
                }else{
                    // 初回 counter = 0
                    counter++
                }
            }
        });
    });
    // [3] データ1つ1つをhtml要素に変換する
    function template(dataArray) {
      return dataArray.map(function(data) {
        return '<div>'+
                    '<p class="d-inline-flex mr-2 mb-0">#' + data.story_id + '</p>'+
                    '<p class="d-inline-flex" class="">'+
                        '<b>'+
                            '<a href="story.php?story_id=' + data.story_id + '">' + sani(data.title) + '</a>'+
                        '</b>'+
                    '</p>'+
                    '<p class="mb-5">'+
                        '語り手：' + sani(data.user) + '&nbsp;/&nbsp;'+
                        '<i class="fas fa-ghost"></i>&nbsp;' + data.horror + '&nbsp;/&nbsp;'+ data.date +
                    '</p>'+
                '</div>'
      })
    }
    </script>

</body>
</html>