<!DOCTYPE html>
<html lang="ja">

<body>
    <nav class="navbar navbar-expand-lg">
        <a href="home.php" class="m-3 mr-5"><h1>あなたと百物語</h1></a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="mystory.php" class="nav-link m-4">私の「語り」</a>
                </li>
                <li class="nav-item">
                    <a href="user_post.php" class="nav-link m-4">新しく「語る」</a>
                </li>
            </ul>
        </div>

        <div id="name">
            <p>こんばんは、<?php echo $_SESSION["user_name"];?>さん</p>
            <a href="logout.php" class="text-secondary">去る<i class="fas fa-door-open fa-lg"></i></a>
        </div>
        
    </nav>



</body>
</html>