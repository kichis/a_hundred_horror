<?php
// ログアウト

session_start();

//SESSIONを初期化（空っぽにする）
$_SESSION = array();

//ブラウザ側、Cookieに保存してあるSessionIDの保存期間を過去にして破棄
if (isset($_COOKIE[session_name()])) { //session_name()は、セッションID名を返す関数
    setcookie(session_name(), '', time()-42000, '/');
}

//サーバ側での、セッションIDの破棄
session_destroy();

//処理後、home.phpへリダイレクト
include("funcs.php");
redirect("home.php");
?>
