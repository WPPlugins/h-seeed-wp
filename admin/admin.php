<?php
global $goption;
$option = $goption;

//------------------------------------------------------------------
//管理画面(設定メニュー)
    add_action( 'admin_menu', 'h_speed_wp_area_menu' ); //オプション表示
    add_action( 'admin_init', 'h_speed_wp_area_register' ); //オプション更新
//設定オプション表示(メニュー)
function h_speed_wp_area_menu() {
    add_options_page( 'H Speed WP 設定', 'H Speed WP 設定', 'administrator', 'h-speed-wp', 'h_speed_wp_area_options', 'dashicons-admin-tools' );
}
//設定オプション更新
function h_speed_wp_area_register() {
    register_setting( 'h_speed_wp_optiongroup', 'h_speed_wp_option' );
}
//------------------------------------------------------------------
//管理画面
function h_speed_wp_area_options() {
    
    if ( !is_user_logged_in()) {
        wp_die('<h3>エラー</h3><p>ログインしてください。</p>');
    }

    //cssの読み込み
    wp_register_style( 'hsw_admin_style', plugins_url().'/h-seeed-wp/admin/admin.css' );
    wp_enqueue_style( 'hsw_admin_style' );

    //PHPの読み込み
    include dirname(__FILE__).'/admin_action.php';
    
    //外国環境用
    if (!is_WJP()) {
        echo '<style>.onjp {display:none;}</style>';
    }

    global $goption;
    $option = $goption;

?>
<div class="wrap" style="margin: 0 auto;max-width: 1200px;">
    <div id="icon-options-general" class="icon32"></div>
    <div style="padding: 1em;margin: 1em 1%;background-color: #fff;box-shadow: 0 0 10px #999;border-top: 5px solid #FFDA73;">
        <h2 style="font-size: 30px;margin-top: 0;text-align: center;text-shadow: 3px 3px 1px #ccc;">H speed WP設定</h2>
        <div style="margin: 15px 0;">
            <p>H Speed WPはワードプレスの高速化とサーバーの負荷削減、セキュリティアップを実行するプラグインです。</p>
            <div>設定なしでも動作しますが、パフォーマンスを最大限発揮するには設定が必要です。</div>
            <div><strong><span style="background-color: #ffff99;">おすすめ設定や、各設定の詳しい設定やお知らせなどは<a style="background-color: #ffff99;" href="http://xn--48sa.jp/h-speed-wp" target="_blank">私のサイトのH Speed WPの解説ページ</a>を御覧ください。</span></strong></div>
            <?php if (!is_WJP()) { ?>
            <h3>ENGLISH</h3>
            <div>H Speed WP is made for WordPress of Japanese . So you can't use part of function to use Japanese.</div>
            <div>Items marked with ☆ is recommended check.</div>
            <div>You can use Bing Translate. Translation is may not be accurate.</div>
            <div id='MicrosoftTranslatorWidget' class='Dark' style='color:white;background-color:#555555'></div><script type='text/javascript'>setTimeout(function(){{var s=document.createElement('script');s.type='text/javascript';s.charset='UTF-8';s.src=((location && location.href && location.href.indexOf('https') == 0)?'https://ssl.microsofttranslator.com':'http://www.microsofttranslator.com')+'/ajax/v3/WidgetV3.ashx?siteData=ueOIGRSKkd965FeEGM5JtQ**&ctf=True&ui=true&settings=Manual&from=ja';var p=document.getElementsByTagName('head')[0]||document.documentElement;p.insertBefore(s,p.firstChild); }},0);</script>
             <?php } ?>
         </div>
    </div>
<div style="padding: 1em;margin: 1em 1%; background-color: #fff;box-shadow: 0 0 10px #999;">
    <h4 style="margin-top: 0;">☆設定方法☆</h4>
    <div>☆のついた項目は私のおすすめする設定項目、♯がついたものは非推奨の設定項目です。</div>
    <div>H Speed WPのそれぞれの項目を有効化することで「設定を開く」があらわれます。そこから各機能の有効化(ON) / 無効化(OFF)ができます。</div>
    <div>設定する中で分からない機能があれば、<a href="http://xn--48sa.jp/hsw-forum/other" target="_blank">H Speed WPへのその他質問などのフォーラム</a>、もしくは<a href="https://twitter.com/yokudekiru" target="_blank">私のツイッター</a>からご気軽に質問してください。忙しくないかぎり、早めに回答します。</div>
</div>
<form method="post" action="options.php">
    <?php settings_fields( 'h_speed_wp_optiongroup' ); ?>
    <div style="padding: 1em;margin: 1em 1%;background-color: #fff;box-shadow: 0 0 8px #999;">
        <h3>ページの読み込みを高速化する機能</h3>
        <p>サイトの読み込みを高速化するための設定です。不具合が起きた場合はOFFにしてください。どれぐらい高速化したか確認するには、<a href="https://gtmetrix.com/">GTmetrix</a>がおすすめです。</p>
        <input type="checkbox" name="h_speed_wp_option[a]" oo="on" value="1" <?php checked( $option['a'], 1 );?> > ☆ 高速化機能を有効化する
        <div>
            <input type="checkbox" id="Panel1" oo="on-off" name="hira-toji">
            <div>
                <h4>PHPの高速化</h4>
                <label><input type="checkbox" name="h_speed_wp_option[1]" value="1" <?php checked( $option['1'], 1 );?> > ☆ PHPの読み込みを圧縮、効率化することで、高速化します。</label>
                <h4>Javascript(JS)関係の高速化</h4>
                <p>主にJS関係の高速化ですが、CSSも多少高速化します。【強】は高速化の効果が高いですが、その分不具合が発生するかもしれません。不具合のでにくい【弱】設定をおすすめします。</p>
                <label><input type="radio" name="h_speed_wp_option[2]" value="1" <?php checked( $option['2'], 1 );?> > JSを高速化しない</label>
                <label><input type="radio" name="h_speed_wp_option[2]" value="2" <?php checked( $option['2'], 2 );?> > ☆【弱】JSの非同期化など</label>
                <label><input type="radio" name="h_speed_wp_option[2]" value="3" <?php checked( $option['2'], 3 );?> > 【強】弱に加えてJSをフッターに移動など</label>
                <h4>軽量な最新バージョンのjqueryを読み込む</h4>
                <p>ワードプレスの標準では、古いバージョンのjqueryが読み込まれていますが、軽量な新しいバージョンを読みこむようにします。新しいバージョンは一部のIEで対応していないのでIEでは古いバージョンを読み込みます。</p>
                <label><input type="radio" name="h_speed_wp_option[3]" value="1" <?php checked( $option['3'], 1 );?> > 設定しない</label>
                <label><input type="radio" name="h_speed_wp_option[3]" value="2" <?php checked( $option['3'], 2 );?> > ☆ 最新版のjqueryをヘッダーで読み込む</label>
                <label><input type="radio" name="h_speed_wp_option[3]" value="3" <?php checked( $option['3'], 3 );?> > 最新版のjqueryをフッターで読み込む</label>
                <h4>画像関係の高速化</h4>
                <p>ワードプレスの画像関係の高速化です。高速化だけでなく、サーバーの容量圧迫対策にも効果があります。画像の圧縮率などは高度な設定から変更できます。</p>
                <label><input type="radio" name="h_speed_wp_option[4]" value="1" <?php checked( $option['4'], 1 );?> > 画像の高速化をしない</label>
                <label><input type="radio" name="h_speed_wp_option[4]" value="2" <?php checked( $option['4'], 2 );?> > 【弱】ワードプレス画像保存システムの改善・アップロードした画像の自動圧縮</label>
                <label><input type="radio" name="h_speed_wp_option[4]" value="3" <?php checked( $option['4'], 3 );?> > ☆【中】弱に加えて、SEOに害を与えない画像や埋め込み要素を遅延ロード</label>
                <label><input type="radio" name="h_speed_wp_option[4]" value="4" <?php checked( $option['4'], 4 );?> > 【強】弱中に加えて、サムネイル画像のbase64化</label>

                <h4>各種プラグインの読み込み最適化</h4>
                <p>プラグインのJavascriptやcssの読み込みを制御をすることによって読み込みを高速化します。対応しているプラグインは「Contact Form 7」「wp-pagenavi」「Syntax Highlighter」「Yet Another Related Posts」「Theme My Login」「Child Pages Shortcode」「Table of Contents Plus」「Jetpack」です。</p>
                <label><input type="radio" name="h_speed_wp_option[5]" value="1" <?php checked( $option['5'], 1 );?> > プラグインの読み込み制御をしない</label>
                <label><input type="radio" name="h_speed_wp_option[5]" value="2" <?php checked( $option['5'], 2 );?> > ☆ 各種プラグインの読み込みを最適化(必要なときだけ読み込み)する</label>
                <label><input type="radio" name="h_speed_wp_option[5]" value="3" <?php checked( $option['5'], 3 );?> > 各種プラグインのJSやCSSを読み込まない</label>

                <h4>ページの先読みによる高速化</h4>
                <p>ページ全体やページのファイルなどを先読みしておくことで、ページ移動を高速化させます。</p>
                <label><input type="radio" name="h_speed_wp_option[6]" value="1" <?php checked( $option['6'], 1 );?> > 先読みを行わない</label>
                <label><input type="radio" name="h_speed_wp_option[6]" value="2" <?php checked( $option['6'], 2 );?> > ☆ HTMLの機能のDNSプリフェッチ(SNSボタンやアドセンスの高速化)とプリフェッチ(ファイルの先読み)と事前レンダリング(次に訪れそうなページの先読み)をします</label>
                <label><input type="radio" name="h_speed_wp_option[6]" value="3" <?php checked( $option['6'], 3 );?> > InstantClickを使用して先読みします。確実に先読みできますが、環境によっては正常に読め込めないかもしれません</label>

                <h4>余分な機能を無効化して高速化</h4>
                <p>ワードプレスの余分な機能を無効化することでそれ関係のファイルの読み込みを無くして高速化します</p>
                <label><input type="checkbox" name="h_speed_wp_option[7]" value="1" <?php checked( $option['7'], 1 );?> > ☆ ワードプレスの絵文字機能の無効化</label>
                <label><input type="checkbox" name="h_speed_wp_option[8]" value="1" <?php checked( $option['8'], 1 );?> > Embed機能(外部ページのブログカード)の無効化</label>
                <label><input type="checkbox" name="h_speed_wp_option[9]" value="1" <?php checked( $option['9'], 1 );?> > ☆ WP REST APIの無効化</label>
                <label><input type="checkbox" name="h_speed_wp_option[10]" value="1" <?php checked( $option['10'], 1 );?> > ☆ headに出力される余分なコードを読み込まないようにする</label>

            </div>
        </div>
    </div>
    <div style="padding: 1em;margin: 1em 1%;background-color: #fff;box-shadow: 0 0 8px #999;">
        <h3>サーバーの容量圧迫対策</h3>
        <p>サーバーのデータスペースの容量の圧迫対策です。高速化にも効果があります。</p>
        <input type="checkbox" name="h_speed_wp_option[b]" oo="on" value="1" <?php checked( $option['b'], 1 );?> > ☆ サーバーのデータスペースの容量の圧迫対策を有効化する
        <div>
            <input type="checkbox" id="Panel2" oo="on-off" name="hira-toji"><div>

            <h4>リビション・自動保存の停止</h4>
            <p>ワードプレスの投稿エディタのリビションと自動保存機能を停止させます</p>
            <label><input type="checkbox" name="h_speed_wp_option[21]" value="1" <?php checked( $option['21'], 1 );?> > 自動保存を停止</label>
            <label><input type="checkbox" name="h_speed_wp_option[22]" value="1" <?php checked( $option['22'], 1 );?> > リビションを無効化</label>

            </div>
        </div>
    </div>

    <div style="padding: 1em;margin: 1em 1%;background-color: #fff;box-shadow: 0 0 8px #999;" class="onjp" >
        <h3>スパムコメント対策</h3>
        <p style="background-color: #F5FDFF;display: inline-block;padding: 2px;">H Speed WPがいままでに防いだ、スパムコメントの数 : <b><?php echo get_option( 'spamcounter' );?></b></p>
        <p>全てを有効化すれば、99%のスパムコメントは防げます。</p>
        <input type="checkbox" name="h_speed_wp_option[c]" oo="on" value="1" <?php checked( $option['c'], 1 );?> > ☆ スパムコメント対策を有効化する
        <div>
            <input type="checkbox" id="Panel3" oo="on-off" name="hira-toji"><div>

            <h4>スパムコメント対策</h4>
            <p>何重ものブロックによって、スパムコメントを防ぎます。できるだけ、スパムでないコメントはブロックされないようになっています。</p>
            <label><input type="radio" name="h_speed_wp_option[31]" value="1" <?php checked( $option['31'], 1 );?> >  【弱】もしかしたら一部のスパムコメントを防げないかもしれません</label>
            <label><input type="radio" name="h_speed_wp_option[31]" value="2" <?php checked( $option['31'], 2 );?> > ☆【強】何重もの対策によってスパムコメントを徹底的にブロックします。</label>

            </div>
        </div>
    </div>

    <div style="padding: 1em;margin: 1em 1%;background-color: #fff;box-shadow: 0 0 8px #999;" class="onjp">
        <h3>セキュリティの強化</h3>
        <p>ワードプレスのセキュリティを強化します。ちょうどワードプレスのセキュリティ上の穴を埋めるような機能です。</p>
        <input type="checkbox" name="h_speed_wp_option[d]" oo="on" value="1" <?php checked( $option['d'], 1 );?> > ☆ セキュリティの強化機能を有効化する
        <div>
            <input type="checkbox" id="Panel4" oo="on-off" name="hira-toji"><div>

            <h4>不正ログインの対策</h4>
            <p>あらゆる手を使って、不正ログインを防止する機能です。【中】以上をおすすめします。</p>
            <label><input type="radio" name="h_speed_wp_option[41]" value="1" <?php checked( $option['41'], 1 );?> > 不正ログインの防止をしない</label>
            <label><input type="radio" name="h_speed_wp_option[41]" value="2" <?php checked( $option['41'], 2 );?> > 【弱】ログインページのシステムの改善とアクセス制限など</label>
            <label><input type="radio" name="h_speed_wp_option[41]" value="3" <?php checked( $option['41'], 3 );?> > ☆【中】弱に加えて、ログイン回数の制限やログイン失敗通知メール、投稿者アーカイブのシステム改善(URL変更など)</label>
            <label><input type="radio" name="h_speed_wp_option[41]" value="4" <?php checked( $option['41'], 4 );?> > 【強】弱中に加えて投稿者アーカイブの無効化</label>

            <h4>自動更新を有効化</h4>
            <p>プラグインやテーマの自動更新を有効にすることで、セキュリティに問題のあるバージョンがそのまま使用され続けないようにする機能です。</p>
            <label><input type="checkbox" name="h_speed_wp_option[42]" value="1" <?php checked( $option['42'], 1 );?> > ☆ プラグインの自動更新を有効化</label>
            <label><input type="checkbox" name="h_speed_wp_option[43]" value="1" <?php checked( $option['43'], 1 );?> > テーマの自動更新を有効化</label>
            <label><input type="checkbox" name="h_speed_wp_option[44]" value="1" <?php checked( $option['44'], 1 );?> > ワードプレス本体の自動更新を有効化</label>

            <h4>PHP関係ののセキュリティ対策</h4>
            <p>PHPにあるクリックジャッキングやDDoS攻撃など攻撃を防ぎ、ちょっとしたセキュリティの穴を無くす機能です。</p>
            <label><input type="radio" name="h_speed_wp_option[45]" value="1" <?php checked( $option['45'], 1 );?> > PHP関係のセキュリティ対策をしない</label>
            <label><input type="radio" name="h_speed_wp_option[45]" value="2" <?php checked( $option['45'], 2 );?> > 【弱】PHPの一部の攻撃防止設定とパーミッションの最適化</label>
            <label><input type="radio" name="h_speed_wp_option[45]" value="3" <?php checked( $option['45'], 3 );?> > ☆【中】PHPの各種攻撃設定とワードプレスのピンバック機能の無効化</label>
            <label><input type="radio" name="h_speed_wp_option[45]" value="4" <?php checked( $option['45'], 4 );?> > 【強】中に加えてピンバックのもとのxmlrpc.phpごと無効化 ※一部のブログエディタやプラグインが作動しなくなるかもしれません。</label>

            <h4>迷惑アクセス防止対策</h4>
            <p>サイトへの迷惑なアクセスを防止します。</p>
            <label><input type="radio" name="h_speed_wp_option[46]" value="1" <?php checked( $option['46'], 1 );?> > 迷惑アクセス防止対策をしない</label>
            <label><input type="radio" name="h_speed_wp_option[46]" value="2" <?php checked( $option['46'], 2 );?> > ☆【弱】迷惑なBOT(ロボット)と一部のかなり古いブラウザからのアクセスをブロック</label>
            <label><input type="radio" name="h_speed_wp_option[46]" value="3" <?php checked( $option['46'], 3 );?> > ♯【強】弱に加えて設定言語が日本語以外のブラウザからのアクセスを拒否 ※SEO的にもオススメできません</label>

            <h4>ワードプレスの機能改善</h4>
            <p>ワードプレスの一部機能をセキュリティに強くなるように改善します。</p>
            <label><input type="radio" name="h_speed_wp_option[47]" value="1" <?php checked( $option['47'], 1 );?> > 機能改善しない</label>
            <label><input type="radio" name="h_speed_wp_option[47]" value="2" <?php checked( $option['47'], 2 );?> > ☆ バージョンの非表示、パスワードの再発行を無効にするなどを有効化</label>
            </div>
        </div>
    </div>

    <div style="padding: 1em;margin: 1em 1%;background-color: #fff;box-shadow: 0 0 8px #999;">
        <h3 style="margin-top: 10px;">パクリ対策</h3>
        <p>ブログのパクリを防止する対策です。対策をしても完全に対策できるわけではありませんが抑止力にはなります。一部機能はログイン中には無効になるようにしています。</p>
        <input type="checkbox" name="h_speed_wp_option[e]" oo="on" value="1" <?php checked( $option['e'], 1 );?> > ☆ パクリ対策機能を有効化する
        <div>
            <input type="checkbox" id="Panel5" oo="on-off" name="hira-toji"><div>

            <h4>パクリ対策</h4>
            <p>ブログのパクリを防ぐ機能です。一部機能はログイン中には無効になるようになっています。</p>
            <label><input type="radio" name="h_speed_wp_option[51]" value="1" <?php checked( $option['51'], 1 );?> > ☆【弱】画像の右クリック禁止・ファンクションキー (F1,2,3…キー) の無効化・RSS設定の最後に署名を追加</label>
            <label><input type="radio" name="h_speed_wp_option[51]" value="2" <?php checked( $option['51'], 2 );?> > 【中】弱に加えてドラッグと右クリックからのコピーの無効化</label>
            <label><input type="radio" name="h_speed_wp_option[51]" value="3" <?php checked( $option['51'], 3 );?> > 【強】弱中に加えて右クリックの無効化とRSSの停止など</label>

            </div>
        </div>
    </div>

    <div style="padding: 1em;margin: 1em 1%;background-color: #fff;box-shadow: 0 0 8px #999;" class="onjp">
        <h3 style="margin-top: 10px;">SEO対策</h3>
        <div><p>「どのプラグインよりも分かりやすいのに、どのプラグインよりもSEOは強くできる」をモットーに開発したSEOを強くする設定です。</p></div>
        <input type="checkbox" name="h_speed_wp_option[f]" oo="on" value="1" <?php checked( $option['f'], 1 );?> > ☆ SEO対策機能を有効化する
        <div>
            <input type="checkbox" id="Panel6" oo="on-off" name="hira-toji">
            <div>
                <div class="bmizuiro" style="background-color: #e7feff; box-shadow: inset 0 0 5px #C7C6C6; padding: 15px; margin: 5px;display: inline-block;">
                    <div><b>基本的に☆のついているものだけ全て有効化（ON）すれば大丈夫ですが、お使いのテーマや他のプラグインと設定が重複していないかのチェックをお勧めします。</b></div>
                    <?php if ( is_plugin_active( 'all-in-one-seo-pack/all_in_one_seo_pack.php' ) || is_plugin_active( 'wordpress-seo/wp-seo.php' ) ):?>
                    <div>あなたのワードプレスにはAll in One SEO Pack、もしくはYoast SEOがダウンロードされています。機能が重複してSEOを強くするどころか、間作エンジンにペナルティを受けてしまう可能性もあるので、それらのプラグインはできるだけ無効化してください。</div>
                    <?php endif;?></div>

                <h4>noindex(検索結果に載せない)ページの設定</h4>
                <p>低品質なページをnoindexにして、検索結果に表示しないようにします。但しリンクフォローはするので、検索結果に表示するページには影響をだしません。有効化（ON）にすることでnoindexになります。</p>
                <label><input type="checkbox" name="h_speed_wp_option[61]" value="1" <?php checked( $option['61'], 1 );?> > カテゴリーページ : ひとつのカテゴリーにつき平均10以上の記事がある場合無効にしてください</label>
                <label><input type="checkbox" name="h_speed_wp_option[62]" value="1" <?php checked( $option['62'], 1 ); ?> > ☆ タグページ : 基本的に有効にします</label>
                <label><input type="checkbox" name="h_speed_wp_option[63]" value="1" <?php checked( $option['63'], 1 );?> > 分割されているページの2ページ目以降 : 「rel="next"/rel="prev"が適切に出力」を無効化していなければ、こちらも無効化のままにしてください</label>
                <label><input type="checkbox" name="h_speed_wp_option[64]" value="1" <?php checked( $option['64'], 1 );?> > ☆ 作成者ページ : 復数の投稿者がいないかぎり、重複コンテンツになるので有効化しましょう</label>
                <label><input type="checkbox" name="h_speed_wp_option[65]" value="1" <?php checked( $option['65'], 1 );?> > ☆ 日付別ページ : 有効化しましょう</label>
                <label><input type="checkbox" name="h_speed_wp_option[66]" value="1" <?php checked( $option['66'], 1 ); ?> > ☆ その他低品質ページ (404ページ、添付ファイルページ、検索ページ) :もちろん有効化しましょう</label>

                <h4>最適なmetaタグを出力する</h4>
                <p>SEOに有効なメタタグを出力するようにします。descriptionは投稿ページの抜粋やサイトのキャッチフレーズやカテゴリーの説明から、keywordはカテゴリーやタグから自動で出力します。これを設定する場合、テーマなどからmetaタグが出力されない設定になっているか確認してください。</p>
                <label><input type="radio" name="h_speed_wp_option[67]" value="1" <?php checked( $option['67'], 1 );?> > metaタグの出力をしない</label>
                <label><input type="radio" name="h_speed_wp_option[67]" value="2" <?php checked( $option['67'], 2 );?> > ☆ SEOに有効なhreflangやCanonical、rel="next"/rel="prev"、descriptionタグを出力する</label>
                <label><input type="radio" name="h_speed_wp_option[67]" value="3" <?php checked( $option['67'], 3 );?> > SEOに有効な可能性も低いkeywordやauthorタグも出力する</label>

                <h4>SEO強化のための便利機能</h4>
                <label><input type="checkbox" name="h_speed_wp_option[68]" value="1" <?php checked( $option['68'], 1 );?> > ☆ログインしていて、ページにリンク切れの画像があった場合、通知します</label>
                <label><input type="checkbox" name="h_speed_wp_option[69]" value="1" <?php checked( $option['69'], 1 );?> > ☆Alt属性(画像の説明)のない画像にAlt属性を自動追加します</label>
                <label><input type="checkbox" name="h_speed_wp_option[70]" value="1" <?php checked( $option['70'], 1 );?> > ♯ 構造化データ(hentry)を無効化します</label>
                <label><input type="checkbox" name="h_speed_wp_option[71]" value="1" <?php checked( $option['71'], 1 ); ?> > ♯ Dmozなどのディレクトリ登録サービスの紹介文を検索結果に使わないようにします</label>

                <h4>SEOに関してのプチアドバイス</h4>
                <ul style="list-style: circle;padding: 0 0 0 20px;">
                    <li>私が書いたSEOに関する記事です→<a href="http://xn--48sa.jp/50000pv-seo" target="_blank">中学生がブログを5万PVまでアクセスアップさせるために施したSEO対策</a></li>
                    <li><a href="https://www.google.com/webmasters/tools/siteoverview?hl=ja" target="_blank">Google ウェブマスター ツール</a>には登録しましたか？登録することで、SEOに役立つさまざまな情報を得ることができます。</li>
                    <li>サイトの読み込み速度もSEOに関係しています。【ページの高速化設定】もしてさらにSEOに強くしましょう。</li>
                </ul>
            </div>
        </div>
    </div>
    <div style="padding: 1em;margin: 1em 1%;background-color: #fff;box-shadow: 0 0 8px #999;">
        <h3 style="margin-top: 10px;">高度な設定</h3>
        <div><p>高度な設定です。初心者は触らなくても大丈夫です。</p></div>
            <input type="checkbox" id="Panel7" oo="on-off" name="hira-toji">
            <div>
                <h4>遅延読み込みに使うライブラリの選択</h4>
                <p>画像関係の高速化の画像の遅延読み込みに使うライブラリを選択できます。</p>
                <label><input type="radio" name="h_speed_wp_option[81]" value="1" <?php checked( $option['81'], 1 );?> >  Echo.js(改良版) </label>
                <label><input type="radio" name="h_speed_wp_option[81]" value="2" <?php checked( $option['81'], 2 );?> > ☆ Layzr.js ※古いブラウザで動きませんが、スムーズなのでお勧めです</label>
                
                <h4>画像の圧縮度</h4>
                <p>画像関係の高速化のアップロードした画像の自動圧縮の圧縮度を設定できます。</p>
                <label><input type="radio" name="h_speed_wp_option[82]" value="1" <?php checked( $option['82'], 1 );?> >【標準】ワードプレスの標準の圧縮率</label>
                <label><input type="radio" name="h_speed_wp_option[82]" value="2" <?php checked( $option['82'], 2 );?> > ☆【低圧縮】画像の劣化はかなり少なめ</label>
                <label><input type="radio" name="h_speed_wp_option[82]" value="3" <?php checked( $option['82'], 3 );?> > 【高圧縮】多少画像が劣化するかもしれません</label>
                
                <h4>開発中機能の有効化</h4>
                <p>開発者のための機能です。有効化しないでください。</p>
                <label><input type="checkbox" name="h_speed_wp_option[84]" value="1" <?php checked( $option['84'], 1 );?> > ♯ 開発者のための機能を有効化する</label>
                
                <h4>H Speed WPの設定の初期化</h4>
                <p>設定を初期化するためには、このチェックボックスを選択してから「変更を保存」ボタンを押してください。</p>
                <label><input type="checkbox" name="h_speed_wp_option[83]" value="1" <?php checked( $option['83'], 1 );?> > H Speed WPの設定を初期化します</label>
            </div>
    </div>
    <div id="subfix" style="transform: rotate(0deg);padding: 0.5em;margin: 1%;background-color: #fff;box-shadow: 0 0 8px #999;">
        <div class="submit">
            <input type="submit" name="Submit" class="button-primary" value="変更を保存" style="transition: 1s;width: 100%;height: 35px;margin: 0 auto;display: block;font-size: 1.2em;font-weight: bold;background-color: #FF9B04;text-shadow: 0 0 0;box-shadow: 0 0 10px #ccc;border: 0;border-radius: 0;">
        </div>
        <div style="font-size: 1.1em;font-weight: bold;text-align: center;">設定したら忘れずに保存しましょう</div>
    </div>
</form>
<div style="padding: 1em;margin: 1em 1% ;background-color: #fff;box-shadow: 0 0 10px #999;font-size: 14px;">
    <h3 style="margin: 10px 0;">作者について</h3>
    <p><a href="https://twitter.com/yokudekiru"><img width="100" height="100" style="border-radius: 10px;" src="data:image/png;base64,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"></a></p>
    <div>H Speed WPを開発しているのは日本人で中学生の「よくできる？学生」です。ワードプレスでサイトを作るのが趣味です。このH Speed WPの不具合、質問、提案等ありましたら、<a href="http://xn--48sa.jp/hsw-forum" target="_blank">H Speed WPのフォーラム</a>から報告してください。</div>
    <ul style="list-style-type: square;margin-left: 15px;">
        <li><a href="https://twitter.com/yokudekiru" target="_blank">「よくできる？学生」のツイッター</a>
            <div>→H Speed WPの更新情報や制作秘話などもツイートしています。ぜひ<a href="https://twitter.com/yokudekiru" target="_blank">フォロー</a>お願いします。</div></li>
        <li><a href="http://xn--48sa.jp/" target="_blank">学生による学生のためのページ</a>
            <div>→学生のためになることを紹介しています。</div></li>
        <li><a href="http://xn--48sa.jp/h-speed-wp" target="_blank">ワードプレスのさまざまな問題を解消するH Speed WPというプラグインを作りました</a>
            <div>→H Speed WPの使い方や設定方法などの紹介記事です。</div></li>
        <li><a href="http://xn--48sa.jp/hsw-forum" target="_blank">H Speed WPフォーラム</a>
            <div>→H Speed WPへの不具合報告やアドバイス、質問などができます。</div></li>
    </ul>
</div>
<div style="padding: 1em;margin: 1em 1% ;background-color: #fff;box-shadow: 0 0 10px #999;">
    <h3 style="margin: 10px 0;">注意</h3>
    <ul style="list-style: circle;padding: 0 0 0 20px;">
        <li>H Speed WPは商用・非商用を問わずどんなサイトでも無料で利用することができます。</li>
        <li> このプラグインが.htaccessやwp-config.phpなどの重要なファイルを編集することは決してありません。</li>
        <li> 管理、制作には十分に注意をしていますが、このプラグインが影響で起きた損害について、一切の責任を負うことはできません。ご了承ください。</li>
        <li> このプラグインのコードは複雑です。できるだけ、プラグインコードの編集はお控えください。</li>
    </ul>
</div>
</div>
<?php
}