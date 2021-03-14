<!DOCTYPE html>
<html lang="ja">
<body>
    <nav class="navbar navbar-expand-lg">   
        <a href="home.php" class="m-3 mr-5"><h1>あなたと百物語</h1></a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <button class="btn btn-md ml-5 m-4" onclick="alert('う')">わ</button>
                </li>
                <li class="nav-item">
                    <button class="btn btn-md m-4" onclick="alert('し')">る</button>
                </li>
                <li class="nav-item">
                    <button class="btn btn-md m-4" onclick="alert('ろ')">い</button>
                </li>
                <li class="nav-item">
                    <button class="btn btn-md m-4" onclick="alert('に')">こ</button>
                </li>
                <li class="nav-item">
                    <button class="btn btn-md m-4" onclick="alert('い')">だ</button>
                </li>
                <li class="nav-item">
                    <button class="btn btn-md m-4" onclick="alert('る')">れ</button>
                </li>
                <li class="nav-item">
                    <button class="btn btn-md m-4" onclick="alert('よ')">だ</button>
                </li>
            </ul>
        </div>
        
        <div id="account_area">
            <p class="mb-1 pr-2">おや、<?= $_SESSION["user_name"]?>さん</p>
            <button class="btn text-secondary font_sawarabi d-flex mr-2 ml-auto" onclick="scaringUser()">去る<i class="fas fa-door-open fa-lg"></i></button>
        </div>
    </nav>

<script>
    function scaringUser(){
        alert("きみだ")
        let target = document.querySelector("body")
        target.innerHTML = ""
        var ele = document.documentElement;
        ele.requestFullscreen();
        // HACK: setTimeoutを重ねるのはあまり良い実装ではなさそうだが、他に手段を思い付かず。更に回数が多い場合は繰り返し処理にした方がよい？
        window.setTimeout(function(){
            target.classList.add('scaring')

            window.setTimeout(function(){
                target.classList.remove('scaring')
                var music = new Audio('不気味な笑い声.mp3');
                music.play();

                window.setTimeout(function(){
                document.exitFullscreen()  
                location.href = 'logout.php' 
                }, 2000);

            }, 1200);

        }, 2000);
    }  
</script>

</body>
</html>