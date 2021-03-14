<!DOCTYPE html>
<html lang="ja">
<body>
    <nav class="navbar navbar-expand-lg">   
        <a href="home.php" class="m-3 mr-5"><h1>あなたと百物語</h1></a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav">
                <li class="nav-item m-3">
                    わ
                    <!-- <a href="mystories.php" class="nav-link m-4">私の「語り」</a> -->
                </li>
                <li class="nav-item  m-3">
                    る
                    <!-- <a href="form_story.php" class="nav-link m-4">新しく「語る」</a> -->
                </li>
                <li class="nav-item  m-3">
                    い
                    <!-- <a href="users.php" class="nav-link m-4">語り手たち</a> -->
                </li>
                <li class="nav-item  m-3">
                    こ
                    <!-- <a href="ayakas.php" class="nav-link m-4">妖たち</a> -->
                </li>
                <li class="nav-item  m-3">
                    だ
                </li>
                <li class="nav-item m-3">
                    れ
                </li>
                <li class="nav-item m-3">
                    だ
                </li>
            </ul>
        </div>
        
        <div id="account_area">
            <p class="mb-1 pr-2">おや、<?= $_SESSION["user_name"]?>さん</p>
            <a href="logout.php" class="text-secondary font_sawarabi font-weight-bold d-block text-right pr-4">去る<i class="fas fa-door-open fa-lg"></i></a>
        </div>
    </nav>
</body>
</html>