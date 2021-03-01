# a_hundred_horror
2021.02 2020.06に製作した『あなたと百物語(kichis/Gs_php_final)』のリファクタリングver.

 ## todo
 - branchを作成、セルフでプルリクする
 
 ## achieved
 - DB接続に関わる関数のみgitignoreした。


## learned
PHP  
- closure = 無名関数を表すためのクラス
PHP&JS  
- login_act.phpでログインできずにlogin.phpに戻ってきたとき、JSの”document.referrer”で取得した”現ページに遷移する前のページ”はlogin.phpだった。  
  login_act.phpと予想していたが、考えてみれば理屈が通る話だった。  
  ”document.referrer”はJS(ブラウザ)で感知できる範囲の履歴と思われる。
  login_act.phpはブラウザに渡すべきHTMLがなく、サーバサイドで処理が完結する、よってブラウザ側に渡されるHTMLの履歴は現login.phpの前は、(login_act.phpの前の)login.php。
  図解すると、  
  1. /PHP/login.php : HTMLをブラウザに渡す -> /ブラウザ/ HTML(ユーザはログイン情報を入力)
  1. /PHP/login_act.php : login.phpからのログイン情報を処理(ブラウザ側には何も渡さない)
  1. /PHP/login.php : HTMLをブラウザに渡す -> /ブラウザ/ HTML(ここで遷移履歴をみると、”document.referrer”==login.php)

 ## ?
 - リモートリポジトリにpush&mergeした後のローカル作業用ブランチはpullすべきか、rebaseすべきか？->
 rebaseだと、再pushできない、という記事を見かけたができる。（言っている意味が違う？）
 rebaseだと、コミットログがきれいになる。
 「マージした」という事実は失われる
マージに比べるとコンフリクトの解消が面倒
 分岐元のブランチとして最新のブランチ状態を反映させる、という意味ではやりたいことに沿っていると思われる。  
 する必要はないか？->複数人開発の場合を想定して、pullかrebaseする習慣をつけた方がいい。
 =>とりあえず、pullしたlocal masterをrebaseする方向で運用してみる
 （実務だと、merge直前のrebaseしか推奨されないのかもしれない・・わからん）

