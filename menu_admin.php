<!DOCTYPE html>
<html lang="ja">

<body>
    <nav class="navbar navbar-expand-lg d-flex">
        <a href="home.php" class="navbar-brand m-3 mr-5"><h1>あなたと百物語</h1></a>

        <div id="account_area" class="order-lg-2 ml-auto mr-2">
            <p class="mb-1">いい夜ですね、<br><?= $_SESSION["user_name"]?>さん</p>
            <a href="logout.php" class="text-secondary font_sawarabi font-weight-bold d-block text-right pr-4">去る<i class="fas fa-door-open fa-lg"></i></a>
        </div>

        <button class="navbar-toggler bg-danger" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
            menu
        </button>

        <div id="navbarToggler" class="collapse navbar-collapse order-lg-1">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="mystories.php" class="nav-link my-4 mx-3">私の「語り」</a>
                </li>
                <li class="nav-item">
                    <a href="form_story.php" class="nav-link my-4 mx-3">新しく「語る」</a>
                </li>
                <li class="nav-item">
                    <a href="edit_myaccount.php" class="nav-link my-4 mx-3">私の「語り手」情報</a>
                </li>
                <li class="nav-item">
                    <a href="stories.php" class="nav-link my-4 mx-3">「語り」たち</a>
                </li>
                <li class="nav-item">
                    <a href="users.php" class="nav-link my-4 mx-3">語り手たち</a>
                </li>
                <li class="nav-item">
                    <a href="ayakas.php" class="nav-link my-4 mx-3">妖たち</a>
                </li>
            </ul>
        </div>
    </nav>
</body>
</html>