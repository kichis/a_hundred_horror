# a_hundred_horror  
2021.02 『あなたと百物語』    
: 怪談を読んだり、投稿したりできるウェブサイト。  
  ユーザ登録すれば、投稿された怪談に対して「怖！」ボタンを押したり、コメントを書くことも可能。ちょっとした仕掛けもあります。  
- 2020.06に製作した『あなたと百物語(kichis/Gs_php_final)』のリファクタリングver.
- 2020ver.には無かった、ユーザ登録時のバリデーションやユーザ情報の編集機能..etcを実装(詳細は下記"実装したこと")。
- 実際のサイトは[こちら](http://kichis.sakura.ne.jp/a_hundred_horror/index.php")より。(ログインする場合は下記"体験用アカウント"をご利用ください)

## 目次
1. [使用技術](#使用技術)
1. [DB設計(ER図)](#DB設計)
1. [体験用アカウント](#体験用アカウント)
1. [おすすめポイント](#おすすめポイント)
1. [実装したこと( = 2020ver.からの進化 )](#実装したこと)
1. [実装できなかったこと(しなかったこと)](#実装できなかったこと)
1. [バリデーション方針](#バリデーション方針)
---
個人的メモ
1. [学んだこと](#学んだこと)
2. [疑問](#疑問)
3. [個人的に達成できたこと](#個人的に達成できたこと)
4. [反省](#反省)
5. [総括と感想](#総括と感想)
7. [今後の課題](#今後の課題)
8. [アプリをさらにブラッシュアップするなら](#アプリをさらにブラッシュアップするなら)

## 使用技術
- HTML5
- CSS3
- Bootstrap 4.5.0
- jQuery 3.6.0
- pagination.js 2.1.4 -> [参考サイト](https://qiita.com/hiroism/items/eee7a9eb0bd9539c30b2)
- PHP 7.4.2
- mySQL 5.7  

## DB設計
###### ER図
注：
- 主キーは１行目、青字
- 依存関係や親子は区別せず
- \[ ]内はデフォルト値
<img width="786" alt="DBのER図" src="https://user-images.githubusercontent.com/64632997/111866278-4a0f6a00-89a7-11eb-881a-9962adc711f4.png">  
tags, tag_kindsテーブルは作成したものの、機能実装せず。  

## 体験用アカウント
- __通常ユーザ__  
email: test_user@ghost.com  
パスワード: testuser  
- __ブラックリストユーザ__ ←おすすめです！   
email: blacklisted@ghost.com  
パスワード: blacklisted  

## おすすめポイント
:ghost: 感じた恐怖を投稿者に伝えられる「怖！」ボタン  
:ghost: __隠しページに遷移するリンク__(画面下方、copyrightsのあたり)  
:ghost: 悪質なブラックリストユーザを驚かせる __ドッキリイベント__   (※Safariでは動作しません。Google Chrome/Brave Browserでは動作確認済み。)
:ghost: ホラーな雰囲気を醸し出すデザイン  

## 実装したこと
- 基本機能(ユーザ登録、ログイン、ログアウト、投稿、投稿削除、ユーザの権限毎の表示切り替え)
###### 2020ver.からの進化
- __ユーザの入力情報(ex. アカウント情報、投稿する話)が規定の形式に沿っているかのバリデーション__
- バリデーションではじかれた場合の、 __エラーメッセージ__ の表示
- ユーザ自身による登録情報の編集機能
- 投稿やアカウントの削除が選択された時、 __削除処理をする前にユーザに再確認するプロセス__  
  (削除対象の重要度によってダイアログとモーダルの２通り)
- 「怖」ボタンを同一投稿に対して同一ユーザが1回しか押せない仕様
- Home画面や管理者画面でのページネーション & スクロールアップ
- 管理者画面にてユーザや投稿の編集をする際、該当レコードにチェックを入れることで __目的のレコードだけを編集モード(inputフォーム)に切り替え & DBへアップデート__ できる  
  (○)2020ver.は編集有無に関わらずリストに全てのレコードをアップデートしていたが、チェック方式にすることで"ムダなUPDATEをしない"、"意図しない編集を防ぐ"ことが実現できた  
  @edit_stories.php : PHPでHTMLを作成する際にinputタグ(type=hidden)に情報を埋め込む形で実装  
  @users.php : チェックがついたレコードの情報をajaxでDBから取得->inputフォームに組み込み->表示 する形で実装  

## 実装できなかったこと
###### しなかったこと
- 削除・編集の処理完了後、「〇〇を削除しました」というコメントを表示した方がユーザにとってわかりやすかった。  
   : 一部に"所定のプロセス後にコメントを出す"機能を実装したが、URLをベタ打ちで指定するなど、あまり良い形ではないと思ったため全体には実装しなかった。  
     次の機会により良い実装方法を探らねば。   

## バリデーション方針
1. 軽微な入力ミス防止にHTMLタグの属性を使用
1. HTMLタグ属性では検証できない項目に加え、検証した(はずの)項目も二重にPHPで検証。  
(このバリデーションはJSでも可能(かつ都合がいい)と思われるが、"HTMLやJSでのバリデーションは突破できるのでサーバ側で行うべき"という[意見](https://www.webdesignleaves.com/pr/php/php_basic_06.php)を見たことと、PHPの習熟を目的としているので今回はこの方針とする。)   
1. XSS対策はHTML出力時に行う。

---
---
以下、個人的なメモ

---
--- 

## 学んだこと

__PHP__
- closure : 無名関数を表すためのクラス
- JSと比べて、変数のスコープが異なる。  
  JS : 関数外で宣言した変数は引数として渡さなくても関数内で使用可能。  
  PHP : 関数外で宣言した変数であっても、ある関数で使用する場合は引数として渡す必要がある。  
- __関数の結果を変数に代入する場合(ex. hoge = function(){} )、returnされる値が無いと、"NULLの代入"という扱いになってしまうので注意！__  
  (= "変数が更新されない"というわけでは無く、NULLで更新される)  
- 正規表現  
  3種類
  - Perl 互換の正規表現 (pcre) : 標準装備。UTF-8のみ。 
  - mbstring の正規表現 (mbregex) : 要追加インストール。UTF-8以外の形式も可能。
  - POSIX 拡張正規表現 (regex) : __PHP 5.3.0 で 非推奨__ とのこと。(確かに定義済み文字クラスが使用できなかった。)  
  []内 : 文字クラス。正規表現の記号でなく、"文字"として扱える。 (※[]内でも、\ ~ - はメタ文字なのでエスケープすること！)  
  メタ文字 : 正規表現内において、特定のパターンを表す文字  
  エスケープ : \ をつける
- echo() や print() は変数の値を一度文字列化した後で出力する (= 配列に対してecho()などを使うと、"Array"と表示される。)
- __文字数などを取得するメソッド__ (strlen()以外の3つの違いははっきりわからず。)  

|メソッド名          |返値                        |全角(漢字・ひらがな・カタカナ・空白・記号(ex.#))|半角|""|
|---              |---                        |:--:                                 |:--:|:--:|
|strlen()         |バイト数(not文字数)            |3                                   |1   |0  |
|iconv_strlen()   |バイト列 string中に現れる文字の数|1                                    |1  |0  |
|mb_strlen()      |文字列の長さ                  |1                                    |1  |0  |
|grapheme_strlen()|書記素単位の文字列の長さ        |1                                    |1  |0  | 
- __inputフォームに入力された「改行」__ -> 改行文字("\n"や"\r")として保存されている。  
                                  単純にecho()するだけだと、改行文字はHTMLの改行タグ`<br>`に変換されない。  
                                  => __出力するとき、`nl2br()`を使用すること__。
- FILTER_VALIDATE_EMAILに合致しない形式  
  - RFC822に適合しないもの  
  - ドットなしドメイン名 (dotless domain name)  
  - @の前に何もない  
  - 全角の入力  
  - 空白の入力  

bindValue()でSQL文に変数を埋め込む場合
- __テーブル名とフィールド名には埋め込めない__。  
  (bindValue()を使わず、単純にPHPの変数を埋め込む形なら可能。ただし脆弱性の問題あり)
- "PDO::PARAM_"で指定するデータ型はDBのカラムで設定しているデータ型に合わせる。  
  (渡す変数のデータ型とは違ってもok ex. 変数"5" -> PDO::PARAM_INT -> int型カラム = OK)  

DBに渡すSQL
- ; で区切れば複数文を渡せ、全て実行される。  
- 但し、 __デフォルトだと"1文目の返値しかPHPに渡されない(取得できない?)"__ という仕様。  
  (ex. 1文目:INSERT & 2文目:SELECT -> SELECTの返値が取得できない )
- __2文目以降の返値も取得する方法__ :  
    `$stmt->nextRowset();` "次の結果に移る"と言う指示  
    `$stmt->fetch();` 次の結果を取得  
- アカウント登録時、user_nameとemailが他の登録と重複しないためのバリデーションにおいて、  
  "入力値と同じデータをDBで検索 -> DBから返ってきたデータと照合して合致 -> 重複ありとみなす" という判断プロセスとした。  
  しかし、このような判断プロセスだと、 __入力値がブランクだった場合も、"結果なし(falseでなくNULL)"というDBからの返値と照合されてしまう__ ので注意！  
  (今回はブランク入力をはじくバリデーションもあるし、大きな問題にはならないが、注意しておかないと何かのバグにつながりそう)

__PHP & JS__  
値渡し  
- PHP -> JS : &lt;script&gt;内にPHPの記述をするだけ。簡単。  
              (配列の場合)PHP配列をjson_encode() -> JS変数にJSON.parse()  
              ※この場合でもXSS可能なのでサニタイズの必要あり。  
               JSではXSS対応のためのメソッドがないので、replace + 正規表現などでエスケープする。   
               本アプリでは、[こちら](http://senoway.hatenablog.com/entry/2013/05/31/235051)を参考にしてサニタイズ関数を作成した。  
- JS -> PHP : ajaxなどを使用する。  

ブラウザの履歴
- login.php -> login_act.php(ログイン失敗) -> login.php に戻ってきたとき、  
  JS"document.referrer(=現ページに遷移する前のページ)" = login.php   
  何故こうなるのか? : "document.referrer"はJS(ブラウザ)で感知できる範囲の履歴と思われる。  
                  login_act.phpはブラウザに渡すべきHTMLがなく、サーバサイドで処理が完結する。  
                  よって、ブラウザ側に渡されるHTMLの履歴は現login.phpの前は、(login_act.phpの前の)login.php。  
  1. {PHP}login.php : HTMLをブラウザに渡す -> <u>{ブラウザ} HTML受け取る</u> -> {ユーザ} ログイン情報を入力 -> ii.へ
  1. {PHP}login_act.php : login.phpからのログイン情報を処理(ブラウザ側には何も渡さない)
  1. {PHP}login.php : HTMLをブラウザに渡す -> <u>{ブラウザ} HTML受け取る</u>  
                      (ここで遷移履歴をみると、”document.referrer” = login.php)

__JS__  
イベントハンドラ
- change() : 始めからレンダリングされているHTML要素に対して有効
- on('change', '子要素', function()) : javascriptで変更された後のHTMLにも有効  

HTML要素の値取得
- __属性を取得するのはattr()、プロパティを取得するのはprop()__ 
  (HTML属性はattr()、inputの値関係はprop()で取得するのがよい、という意見あり)

__mySQL__
- __varcharの制限単位はbyteではなく文字数__ （ex. 1 = 全角でも半角でも1文字） 　
- text型の文字数上限(※1bit=1文字ではない文字はこの文字数以下になる)  

| 型          | 上限bit数 | 上限文字数(※)   |
| :--:        | :--:     | :--:          |
| text       | 16        | 65,535        |
| mediumtext | 24        | 16,777,215    |
| longtext   | 32        | 4,294,967,295 |
- __UPDATE文は(1回で複数文送信しても)UPDATE1回の処理のたびに通信が発生する__  
　(予想:共有ロックでなくて専有ロックだから？)
- bulk update : 複数のレコードを一度にUPDATEすること
- `ELT(1, "Tokto", "Osaka") // "Tokto" `   第一引数 = n番目にある要素を返す  
- `FIELD("大阪", "東京", "大阪")  // 2 `   第一引数を検索してn番目にあるかを返す

__HTML__
- 属性のpattern=""でお手軽バリデーションができる 
- __formタグはtableタグの内側に記述すると動的レンダリングしたフォームの値がPOSTできないので、tableタグの外側に記述すること！！！__  

__CSS__
- hover : カーソルが重なっている
- focus : 選択されている(tabを使って選択した場合も含む)  
 
__XSSについてまとめ__  
PHP  
- script入りの文字列 + var_dump() -> scriptが実行される (__危険！__)
- json_encode(script入りの文字列) + var_dump -> scriptとしてHTMLに反映されるがなぜか実行はされない (たぶん危険?)  

JS    
- json_encode(script入りの文字列) -> JSON.parse() -> HTMLに挿入  
  -> scriptとしてHTMLに反映されるがなぜか実行はされない (たぶん危険?)
- json_encode(script入りの文字列) -> JSON.parse() -> サニタイズ(危険な文字をエスケープ) -> HTMLに挿入  
  -> scriptではなく文字列としてHTMLに反映される (__安全__)  

__OTHER__
- __UTF-8は、1文字あたり1~4byte__  
  (strlen()で軽く検証した感触では、全角=3,半角=1。マイナーな漢字(JIS X 0213の第3・4水準漢字)は4byteになるらしい。)  
- RFC(Request For Comments) : インターネットに関する技術の標準を定める団体であるIETF(インターネット技術標準化委員会)が正式に発行する文書。
- RFC822 : 電子メールを交換する際に使用されるテキストのフォーマットである、“インターネット・ メッセージ・フォーマット”をパーズ/生成する手続きを定義したもの。  
　(最新版はもっと数字が進んでいるが、最初に定めたのがRFC822なので、現在も"RFC822形式"という呼称になっている)。  
　( RFC821/822 => RFC2821/2822 => RFC5321/5322 )
- RFC違反メールアドレス : RFCで定められている仕様・要件に準拠しないメールアドレス。  
  2009年3月頃までのdocomo、auのキャリアメールは違反メールアドレスでも作成できた。現在はできない。  
  
  例：    
  1. アットマーク（@）の直前やメールアドレスの先頭にピリオド (.) がある  
  1. アットマーク（@）より前で、ピリオド (.) が連続している  
  1. 半角英数字と一部の記号(. ! # $ % & ' * + – / = ? ^ _ \`{ | } ~)以外の文字列を含んでいる  
  注意：   
  - @の前の部分を「”」で囲んでいる場合は許可される場合もある。（例：”ab..cd”@example.co.jp）  
  - iii.はinputタグをtype="email"にするとはじかれる。
  - PHPのフィルタ型であるFILTER_VALIDATE_EMAILは、RFC822形式に則ったバリデーションを行っている。  
    (ただ、「RFC822はメールを”受け取る”側の情報取り扱い方式であるので、メールを送信・伝達するSMTPの方式に則ったバリデーションにすべき」という意見もあるようだ。)

## 疑問
- 送信ボタンを押した後のバリデーションは、JSで行った方がその後の流れが楽。  
  なるべくJSでやりたいが、JSでやってもいいパターンはあるのだろうか。  
  (clickイベント -> バリデーション -> submitする/ストップ : 全てJSでできる。)  

__git__  
「リモートリポジトリにpush&mergeした後のローカル作業用ブランチはどういう扱いにすべき？ 」　(ローカル作業用ブランチを再利用して次の作業にも使用する場合)　　
1. local feature -> remote feature　(push)
1. remote feature -> remote master　(merge)
1. remote master -> local master　(pull)
1. remote master -> local feature(pull)? or local master -> local feature(merge?rebase?)
- merge : 
- rebase :  [参考](https://www.shigemk2.com/entry/20121123/1353640913)
    - コミットログがきれいになる
    - 「マージした」という事実は失われる
    - マージに比べるとコンフリクトの解消が面倒

## 個人的に達成できたこと
- DB接続に関わる関数のみ別ファイルに分け、gitignoreした : これまで自前でgitignoreファイルを作成したことがなかったため。  
- 作業用ブランチ作成、セルフでプルリク&マージ  
　 : これまでmasterブランチから直接プッシュしていた。  
     より実践的な形に近づけるため、作業用ブランチを作成、  
     (ローカル作業)->プッシュ->(リモート作業)->プルリク・マージ->(リモートmaster)->(ローカルmaster) というフローでアップデートするようにした。  
     作業用ブランチで作業中に別種の不具合を見つけた際、別のブランチを作成して対応&pushするなど、gitの使い方を少しずつ広げられたのは良かった。
- 実装機能単位でのpush。
- ずっと、実装してみなければと思っていたajaxとPromiseの実装が叶った。  
  特にPromiseはこれを使うことで処理フローのきれいなコードにできたので良かった。 

## 反省
- 今回、「作りつつ学ぶ」ことも目的のひとつだったので多くのことが学べたのは良かった。  
  一方、製作途中で新しい実装方法を知りそれを使用し始めることで、"__同様の機能にも関わらず実装方法が違う__ "プログラムができてしまった。  
  本来は、同様の機能であれば同じ実装方法にしたほうが読みやすいコードになるはず<。長期的に動くアプリのコードを書く場合はなおさら注意。
- __汎用性が無い関数__  
   : 事前の見通しが甘かったのか、似たような関数を複数作成してしまった。(特に、バリデーション部分)  
  　　=> このような場合にオブジェクト指向を活用すべきなのかもしれない。(それが実感できたという意味では良かった。)  
         ・ 今回の場合、バリデーションやDBとの接続などで使えそう。  
         ・ 今回のバリデーション : 関数で基本の流れを作り、引数で各項目に合致するように調整した。  
                             (✖️)同じ判定をするたびごとに、同じ引数を渡すのは面倒  
         ・ オブジェクト指向を使うなら : バリデーションの原型クラス -> user_nameやemail用の子クラスへ継承しそれぞれカスタム、など  
                                (入力値を渡したら、true,falseやエラー文が返ってくる、というくらいシンプルに使えるメソッドを作成した方が便利)                    
- DB設計 △  
  - SELECTを重視したDB設計となっており、UPDATEしにくいテーブルになっていた。  
    （1つのデータのUPDATEに2箇所変える必要ある）
  - 設計時に複数主キーの知識やSQLのGROUPBY+HAVINGなどの知識があれば、もっとよい形のDBにできたかもしれない。
- 情報の渡し方  
   : 投稿の削除機能・編集機能のあるphpファイルへのアクセスは"該当ストーリーの筆者か"というバリデーションを行い、第三者のアクセスをはじいた。  
  (URLにstory_idを追加する形で遷移しているので、URLを変更することで第三者が削除・編集できてしまうため)  
  (✖️)この方式だとDBへの接続が発生する  
  (✖️)筆者であれば、実際のプロセス(mystories.phpからの遷移)でなくてもURL操作で編集・削除ができてしまう  
     (セキュリティ上大きな問題ではなさそうだが、ユーザが意図しない操作をしてしまう可能性がある)  
   => 今後は __formタグが複数になったとしても__ 、削除ボタン・編集ボタンそれぞれに作成し、 __POST__ でデータを渡す方がよさそう・・
- __アカウント作成時の各要素の"条件"や"退会手続き以降にそれらをどう扱うか"__ 、という制度設計が必要  
   ： ユーザ名・emailは(退会済みのアカウントも含めてすでに登録があるものと)同じものを登録できない、という仕様にしたが、  
     emailに関しては同じ人が再度登録する、という可能性を考慮すべきだった
- __"どの権限があれば、どのページでどのような動きをする（できる）"__ 、また、 __"ユーザの行動に伴って起きうること"__ のしっかりした検討が必要。  
   ： ユーザーが投稿を削除した場合のstatus=0と、管理者が非表示にした場合のstatus=0は区別した方が良かったかもしれない  
   (✖️)管理者から見て、非表示にしたのが管理者なのかユーザなのかわからない  
   (✖️)管理者が操作を誤ると、ユーザが削除したつもりの投稿が掲載されてしまう  

## 総括・感想
- emailバリデーションは意外に奥が深い。  
  PHPでもメソッドがあるし、色々な人が方法を記事を書いているが、  
  emailを登録してもらう目的が"登録者に届く(有効な)メールアドレスを入手すること"なのであれば、  
  入力されたemailアドレスにメールを送信 ＆ そこから次のステップに進む、という方式がやはり確実そうだ。
- 基本情報技術者試験の勉強で、passwordの保存に関し、色々な方法があることを学んだ。面白い。  
  1. "passw + ソルト"でハッシュ化 -> ハッシュ化したもの + ソルト => 保存  
  1. i.を何回か繰り返す -> ハッシュ化したもの + ソルト + 繰り返しの回数 => 保存  
  1. サーバ側 : ユーザID + ハッシュ化したpassw => 保存  
     ブラウザ側で入力値をハッシュ化したもの > //照合// < ユーザIDで検索したハッシュ で行う ( = passwをネットワークに流さない )  

- "更新するボタンはチェックボックスの横にあった方がいい"との意見をもらった。

## 今後の課題
- オブジェクト指向を活用したコーディング
- 処理完了後のコメント表示
###### やってみたいこと
- ソルトを使ったpassw保存や、ブラウザ側でハッシュ化するpassw保存方法

## アプリをさらにブラッシュアップするなら
- 検索機能(user/tag)
- 処理完了後のコメント表示
- NGワードを含む投稿の規制
- home.php 新しい記事に"new!"をつける
- home.php 各投稿のコメント数も表示
- コメントをコメントの投稿者が削除できるようにする
- edit_users.php バリデーションに引っ掛かった場合、入力した情報が再度表示される
- ボタンの押下回数でイベントが起きる（サイトも文字フォントが変わるとか、悲鳴、怖い画像）
