<!DOCTYPE html>
<html lang="ja">

<body>
    <nav class="navbar navbar-expand-lg">
        <a href="home.php" class="m-3 mr-5"><h1>あなたと百物語</h1></a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="mystories.php" class="nav-link m-4">私の「語り」</a>
                </li>
                <li class="nav-item">
                    <a href="form_story.php" class="nav-link m-4">新しく「語る」</a>
                </li>
            </ul>
        </div>

        <div id="account_area">
            <p class="mb-1 pr-2">こんばんは、<?= $_SESSION["user_name"]?>さん</p>
            <a href="logout.php" class="text-secondary font_sawarabi font-weight-bold d-block text-right pr-4">去る<i class="fas fa-door-open fa-lg"></i></a>
        </div>
    </nav>
</body>
</html>