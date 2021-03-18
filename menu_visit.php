<!DOCTYPE html>
<html lang="ja">
<body>
    <nav class="navbar navbar-expand-lg">
        <a href="home.php" class="m-3"><h1>あなたと百物語</h1></a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav"></ul>
        </div>

        <div id="login" class="mr-4">
            <a href="login.php" style="color:gray">
                <b id="login_text">入室する</b>
                <img src="img/icon_candle.png" id="login_icon" alt="candle" width="40">
            </a>
        </div>       
    </nav>

<script>
    var login = document.querySelector('#login');
    var loginText = document.querySelector('#login_text');
    var loginIcon = document.querySelector('#login_icon');
    
    function colorChange(color, image){
        loginText.style.color = color
        loginIcon.src = image
    }
    
    login.addEventListener("mouseover", function (event) {
        colorChange("red", "img/icon_candle_red.png")
    }, false);
    login.addEventListener("mouseout", function (event) {
        colorChange("gray", "img/icon_candle.png")
    }, false);
</script>

</body>
</html>