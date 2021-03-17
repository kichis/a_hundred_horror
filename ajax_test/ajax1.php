<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>jQuery TIPS</title>
</head>
<body>
<form>
  <div>
    <!-- <label for="keyword">キーワード：</label> -->
    <!-- <input id="keyword" type="text" size="20" /> -->
    <input id="search" type="button" value="検索" />
  </div>
  <ul id="result" class="ajax"></ul>
</form>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>
$(function() {
  // ［検索］ボタンクリックで検索開始
  $('#search').click(function() {
    var param = { "text": "Hello" };
    // .phpファイルへのアクセス
    $.ajax({
        url: 'ajax2.php',
        type: 'GET',
        data: param,
        dataType: 'json',
        timeout: 5000,
      
    })
    // 検索成功時にはページに結果を反映
    .done(function(data) {
      // 結果リストをクリア
      // $('#result').empty();
      console.log(data) // ajax2.phpからの返値がdataとして表示
    })
    // 検索失敗時には、その旨をダイアログ表示
    .fail(function() {
        console.log("fail")
    });
  });
});
</script>
</body>
</html>