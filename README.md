# a_hundred_horror
2021.02 2020.06に製作した『あなたと百物語(kichis/Gs_php_final)』のリファクタリングver.

## 使用技術
- HTML5
- CSS3
- Bootstrap 4.5.0
- jQuery 3.6.0
- pagination.js 2.1.4 [参考サイト](https://qiita.com/hiroism/items/eee7a9eb0bd9539c30b2)
- PHP 7.4.2

## 工夫した点
- copyrightsのあたりをクリックすると良いことがあります。

 ## todo
 - branchを作成、セルフでプルリクする
 
 ## achieved
 - DB接続に関わる関数のみgitignoreした。
 - 削除処理に入る前にユーザに再確認するプロセスを実装できた。(対象の重要度によってalertとモーダル２通り)


## 仕様
- バリデーション方針 :  
1. 軽微な入力ミス防止にHTMLタグの属性を使用
1. HTMLタグ属性では検証できない項目に加え、検証した(はずの)項目も二重にPHPで行う。
(2.のバリデーションはJSでも可能(かつ都合がいい)と思われるが、"HTMLやJSでのバリデーションは突破できるのでサーバ側で行うべき"という意見を見たことと、PHPの習熟を目的として今回はこの方針とする。[参照](https://www.webdesignleaves.com/pr/php/php_basic_06.php))  
1. XSS対策はHTML出力時に行う。

---
---
以下、個人的なメモ

---
---

## 学んだこと
- UTF-8は、1文字あたり1~4byte。  
  (strlen()で軽く検証した感触では、全角=3,半角=1。マイナーな漢字(JIS X 0213の第3・4水準漢字)は4byteになるらしい。)  

- アカウント作成・ログイン字の各要素の条件や退会手続き以後にそれらをどう扱うか、という制度設計が甘かった。
（ユーザ名・emailは同じものを（すでに）登録できない、という仕様にしたが、emailは同じ人が再度登録する、という可能性を考慮して許容すべきだった）
- どの権限者なら、どのページでどのような動きをする（できる）というツメが甘かった。
- ex.ユーザーが「語り」を削除にした場合の0と、管理者が非表示にした0は区別した方が良かったかもしれない。(管理者が操作を誤ると、ユーザが削除したつもりの「語り」が掲載されてしまう。)
- user_nameとemailが他の登録者と重複しないためのバリデーションにおいて、"入力値と同じデータがあるかという検索に対して、DBからの返ってきたデータと照合して照合があれば重複ありとみなす"としていたが、これだと、入力値がブランクだった場合も"結果なし(falseでなくNULL)"という返値と照合されてしまうので注意。(今回は空欄のバリデーションもあるし、大きな問題にはならないが注意しておかないと何かのバグにつながりそう)

__PHP__  
- closure : 無名関数を表すためのクラス
- JSと変数のスコープの扱いが違う。  
  JS:関数外で宣言した変数は引数として渡さなくても関数ないで使用可能。  
  PHP:関数外で宣言した変数であっても、ある関数で使用する場合は引数として渡す必要がある。
- 関数の結果を変数に代入する式にしているばあい、何もreturnされないと、"NULLの代入"という扱いになってしまうようなので注意！
- 文字数などを取得するメソッド  
  下3つの違いははっきりわからず。
||返値|全角(漢字・ひらがな・カタカナ・空白・記号(ex.#))|半角|""|
|---|---|---|---|---|
|strlen()|バイト数(not文字数)|3|1|0|
|iconv_strlen()|バイト列 string 中に現れる文字の数|1|1|0|
|mb_strlen()|文字列の長さ|1|1|0|
|grapheme_strlen()|書記素単位の文字列の長さ|1|1|0|  
- FILTER_VALIDATE_EMAILの型に合致しないもの  
  - RFC822に適合しないもの  
  - ドットなしドメイン名 (dotless domain name)  
  - @の前に何もない  
  - 全角の入力  
  - 空白の入力  
- 正規表現が3種類ある  
  - Perl 互換の正規表現 (pcre) : 標準装備。UTF-8のみ。 
  - mbstring の正規表現 (mbregex) : 要追加インストール。UTF-8以外の形式も可能。
  - POSIX 拡張正規表現 (regex)->__PHP 5.3.0 で 非推奨__ とのこと。(確かに定義済み文字クラスが使用できなかった。)  
  []内 : 文字クラス ([]内でも、\~-はメタ文字なのでエスケープすること！)
  "メタ文字"  : 正規表現内において、特定のパターンを表す文字  
  エスケープ = \をつける  
- bindValue()でSQL文に変数を埋め込む場合でも、テーブル名とフィールド名には埋め込めない。  
  (bindValue()なしで、単純にPHPの変数を埋め込む形なら可能だが脆弱性の問題あり)
- bindValue()でSQL文に変数を埋め込む場合、PDO::PARAM_で指定した通りのデータ型を与えないとエラーになる。(PHP変数であれば、DBではint型のところ、SQL文に文字列で埋め込んでも処理される)  
  =>違った。story_idが元々stringで渡ってきているにも関わらず、他の箇所では”PDO::PARAM_INT”で正常に処理されているので不思議に思ったら、「たとえ変数の中身がstringであっても、受け取る側のDBカラムの型がintegerである場合、”PDO::PARAM_INT"で渡せば処理される。」ということがわかった。
- DBに渡すSQL文は、;で区切れば複数渡せるし、実行もされる。  
- 但し、(両方SELECTの場合など)複数種類の値が返ってくるものは、1つ目のSELECTの値しか取得できない。(調べた範囲では解決法見つからず)  
  => やはりデフォルトだと"1文目の返値しかPHPに渡されない(取得できない?)"という仕様になっている。(= 1文目:INSERT & 2文目:SELECT でも返値が取得できない)
     解決法: $stmt->nextRowset(); で"次の結果に移る"と言う指示をだしてから、 $stmt->fetch(); する。
- `echo`や`print`は変数の値を一度文字列化した上で出力する

__xssについて__  
PHP
- script入りの文字列 + そのままvar_dump -> scriptが実行される(危険！)
- json_encode(script入りの文字列) + var_dump -> scriptはHTMLに反映されるがなぜか実行はされない(たぶん危険)
with JS  
- json_encode(script入りの文字列) -> JSON.parse() -> HTMLに挿入 -> scriptはHTMLに反映されるがなぜか実行はされない(たぶん危険)
- json_encode(script入りの文字列) -> JSON.parse() -> サニタイズ(危険な文字をエスケープ) -> HTMLに挿入 -> scriptではなく文字列としてHTMLに反映される(安全)

__PHP&JS__  
- PHP -> JSへの値渡しは&lt;script&gt;内にPHPの記述をするだけ、簡単。 
  配列の場合は、PHP配列をjson_encode()->JS変数にJSON.parse()。（この場合でもxss可能なのでサニタイズの必要あり）
  JSではxss対応のためのメソッドがないらしいので、replace+正規表現などでエスケープする。    
  [これ](http://senoway.hatenablog.com/entry/2013/05/31/235051)などが役に立ちそう。
- login_act.phpでログインできずにlogin.phpに戻ってきたとき、JSの”document.referrer(=現ページに遷移する前のページ)”を取得した結果は"login.php"だった。  
  login_act.phpと予想していたが、考えてみれば理屈が通る話だった。  
  ”document.referrer”はJS(ブラウザ)で感知できる範囲の履歴と思われる。  
  login_act.phpはブラウザに渡すべきHTMLがなく、サーバサイドで処理が完結する、よってブラウザ側に渡されるHTMLの履歴は現login.phpの前は、(login_act.phpの前の)login.php。  
  1. /PHP/login.php : HTMLをブラウザに渡す -> /ブラウザ/ HTML受け取る (-> /ユーザ/ ログイン情報を入力 -> 2.へ)
  1. /PHP/login_act.php : login.phpからのログイン情報を処理(ブラウザ側には何も渡さない)
  1. /PHP/login.php : HTMLをブラウザに渡す -> /ブラウザ/ HTML受け取る(ここで遷移履歴をみると、”document.referrer”==login.php)

__JS__  
- change():元々のHTML要素に対して有効
- on('change', '子要素'):javascriptで変更された後のHTMLにも有効
- 属性を取得するのはattr()、プロパティを取得するのはprop()  
  (HTML属性はattr()、inputの値関係はprop()で取得するのがよい、という意見あり)


__mySQL__  
- UPDATE文は処理のたびに通信が発生する(memo:共有ロックでなくて専有ロックだから？)
- bulk update = 複数のレコードを一度処理(UPDATE)できる
- INSERT -- ON DEPLICATE KEY UPDATE
- ELT(1, "Tokto", "Osaka") // "Tokto" 第一引数=n番目にある要素を返す
- FIELD("大阪", "東京", "大阪") // 2 第一引数を検索してn番目にあるかを返す
- CASE WHIE - THEN --
// "UPDATE stories SET story_id = case story_id
// WHEN 2 THEN 'tokyo'
// WHEN 3 THEN 'kyoto'
// END
// , `modified` = NOW()
// WHERE story_id IN (2,3)";
- varcharの制限数はbyteではなく文字数（全角でも半角でも1文字） 
- text型の文字数(*1bit=1文字ではない文字はこの文字数以下になる)  
|型|上限bit数|上限文字数(*)|
|---|---|---|
|text|16|65,535|
|mediumtext|24|16,777,215|
|longtext|32|4,294,967,295|  

__HTML__
- 属性のpattern=""でお手軽バリデーションができる 
- formタグはtableタグの内側に記述すると動的レンダリングしたフォームの値がPOSTできないので、tableタグの外側に記述すること！！！ 

__CSS__
- hover:カーソルが重なっている、focus:選択されている(tabを使って選択した場合も含む)  

__OTHER__
- RFC(Request For Comments) : インターネットに関する技術の標準を定める団体であるIETF(インターネット技術標準化委員会)が正式に発行する文書
- RFC822 : 電子メールを交換する際に使用されるテキストのフォーマットである、“インターネット・ メッセージ・フォーマット”をパーズ/生成する手続きを定義したもの。(最新版はもっと数字が進んでいるが最初に定めたのがRFC822なので、現在も"RFC822形式"という呼称になっている)。  
- RFC821/822 => RFC2821/2822 => RFC5321/5322 
- RFC違反メールアドレス : RFCで定められている仕様・要件に準拠しないメールアドレス  
  2009年3月頃までのdocomo、auのキャリアメールは違反メールアドレスでも作成できた。現在はできない。  
例：    
1. アットマーク（@）の直前やメールアドレスの先頭にピリオド (.) がある  
1. アットマーク（@）より前で、ピリオド (.) が連続している  
1. 半角英数字と一部の記号(. ! # $ % & ' * + – / = ? ^ _ `{ | } ~)以外の文字列を含んでいる  
注意:  
@の前の部分全体を「”」で囲んでいる場合は許可される場合もある。（例：”ab..cd”@example.co.jp）  
3.はinputタグをtype="email"にするとはじかれる。
- PHPのフィルタ型であるFILTER_VALIDATE_EMAILは、RFC822形式に則ったバリデーションを行っているが、「RFC822はメールを”受け取る”側の情報取り扱い方式であるので、メールを送信・伝達するSMTPの方式に則ったバリデーションにすべき」という意見もあるようだ。

## 疑問
__git__
- 「リモートリポジトリにpush&mergeした後のローカル作業用ブランチはどういう扱いにする？  
  (ローカル作業用ブランチを再利用して次の作業にも使用する場合)」  
  1. local feature -> remote feature(push)
  1. remote feature -> remote master(merge)
  1. remote master -> local master(pull)
  1. remote master -> local feature(pull)? or local master -> local feature(merge?rebase?)
- merge : 
- rebase :  [参考](https://www.shigemk2.com/entry/20121123/1353640913)
    - コミットログがきれいになる
    - 「マージした」という事実は失われる
    - マージに比べるとコンフリクトの解消が面倒



## 所感・感想
- emailバリデーションは意外に奥が深い。  
  PHPでもメソッドがあるし、色々な人が記事を書いているが、emailを登録してもらう目的が”登録者に届く(有効な)メールアドレスを入手すること”なので、  
  入力されたemailアドレスにメールを送信＆そこから次のステップに進む、という方式がやはり確実そうだ。
- 基本情報の勉強で、passwordの保存に関し、色々な方法があることを学んだ。面白い。  
    1. "passw+ソルト"でハッシュ化 => ハッシュ化したもの+ソルト 保存  
    2. 1.を何回か繰り返す => ハッシュ化したもの+ソルト+繰り返しの回数 保存  
    3. サーバ側:ユーザIDとハッシュ化したpasswだけ保存  
       照合：ブラウザ側で入力値をハッシュ化したもの と ユーザIDで検索したハッシュ で行う  
       (=passwをネットワークに流さない)  
- DB設計時に複数主キーの知識やSQLのGROUPBY+HAVINGの知識があれば、よりよい形のDBにできたかもしれない。
- 今回、ストーリーの削除機能・編集機能のあるphpファイルへのアクセスは"該当ストーリーの筆者か"というバリデーションを行い、第三者のアクセスをはじいた。(URLにstory_idを追加する形で遷移しているので、URLを変更することで第三者が削除・編集できてしまうため)  
  ただ、この方式だとDBへの接続が発生するので、formを削除ボタン・編集ボタンそれぞれに作成し、POSTでデータを渡す方がよいのだろうか。  
  (story_idをハッシュ化して推測できない形にするという案も考えたが、それだとphpファイル側もstory_idがわからないか・・)
  追記：さらに今回の実装の問題点として、筆者であれば、実際のプロセス(mystories.phpからの遷移)でなくてもURL操作で編集・削除ができてしまう、という問題点がある。(セキュリティ上大きな問題ではなさそうだが、ユーザが意図しない操作をしてしまう可能性がある)
