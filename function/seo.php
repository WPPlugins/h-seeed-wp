<?php
global $goption;
$option = $goption;
add_action('wp_head',function() {
    //変数
    global $goption;
    $option = $goption;
    //投稿ページ関係
    global $post;
    global $page,$paged;
    $id = get_the_ID();
    $post_id = get_post($id);
    if(is_single()) {
        $cats = get_the_category($post->ID);//カテゴリーの取得
        $tags = get_the_tags($post->ID);//タグの取得
    }


    //URLの取得
    $hurl  = home_url();//ホーム
    $kurl = $paged;
    $murl = $page - 1;//1ページ前
    $turl = $page + 1;//1ページ後
    $turl2 = $page + 2;//2ページ目
    if(is_single() || is_page()) {
        $pages = count( explode('<!--nextpage-->', $post->post_content));//投稿のページ番号
    }

    //表示初め
    echo '<!-- この下はH Speed WP+SEOによって出力されています -->';


    if ($option['67'] > '1') {
        //hreflangタグ
        if(is_front_page() && is_home()){
            if(!is_paged()){
                echo '<link rel="alternate" hreflang="ja" href="',$hurl,'">';
            } else {
                echo '<link rel="alternate" hreflang="ja" href="',$hurl,'/page/',$kurl,'">';
            }
        } else {
            if(is_paged()){
                echo '<link rel="prev" href="'.get_pagenum_link( $paged ).'">';
            } else {
                echo '<link rel="alternate" hreflang="ja" href="',get_pagenum_link(),'">';
            }
        }

        //canonicalの出力
        if(is_front_page() && is_home()){
            if(!is_paged()){
                echo '<link rel="canonical" href="',$hurl,'">';
            } else {
                echo '<link rel="canonical" href="',$hurl,'/page/',$kurl,'">';
            }
        } else {
            if(is_paged()){
                echo '<link rel="prev" href="'.get_pagenum_link( $paged ).'">';
            } else {
                echo '<link rel="canonical" href="',get_pagenum_link(),'">';
            }
        }

        //next/prev最適化
        //トップページやカテゴリページなどのページネーションでのタグ出力
        if(!is_single() || !is_page()) {
            if ( get_previous_posts_link() ){
                echo '<link rel="prev" href="'.get_pagenum_link( $paged - 1 ).'">';
            }
            if ( get_next_posts_link() ){
                echo '<link rel="next" href="'.get_pagenum_link( $paged + 1 ).'">';
            }
        }
        if(is_single() || is_page()) {
            if ( $pages > 1 ) {
                if ( $page == $pages ) {
                    if ( $pages == 2 ) {
                        echo '<link rel="prev" href="'.$surl.'">';
                    } else {
                        echo '<link rel="prev" href="'.$surl.'/'.$murl.'">';
                    }
                } else {
                    if ( $page == 0 ){
                        echo '<link rel="next" href="'.$surl.'/'.$turl2.'">';
                    } else {
                        if ( $pages == 2 ) {
                            echo '<link rel="prev" href="'.$surl.'">';
                        } else {
                            echo '<link rel="prev" href="'.$surl.'/'.$murl.'">';
                        }
                        echo '<link rel="next" href="'.$surl.'/'.$turl.'">';
                    }
                }
            }
        }

        //descriptionの設定
        //抜擢の取得
        if(!is_paged() && is_single() || is_page()) {
            setup_postdata($post_id);
            $desc = $post_id->post_excerpt;
            //抜擢があればdescriptionに設定
            if(!empty($desc)) {
                echo '<meta name="description" content="',$desc,'">';
            }
        }

        //キャッチフレーズの取得とdescriptionに設定
        if(is_front_page() && is_home() && !is_paged()) {
            echo '<meta name="description" content="',bloginfo('description'),'">';
        }
        //カテゴリーの説明文取得
        if(is_category() && !is_paged()) {
            $cati = get_query_var('cat');
            $catd = category_description( $cati );
            if(!empty($catd)) {
                $catd = strip_tags($catd);
                $catd = str_replace(array("\r", "\n"), '', $catd);
                //出力
                echo '<meta name="description" content="',$catd,'">';
            }
        }


        if ($option['67'] === '3') {

            //authorメタ
            if(is_single() || is_page()) {
                $author = get_userdata($post->post_author);
                echo '<meta name="author" content="',$author->display_name,'">';
            }

            //metaキーワード
            if(is_single()) {
                //カテゴリー2つ取得
                $catn = $cats[0]->cat_name;
                if(!empty($cats[1])) {
                    $catn = $catn.','.$cats[1]->cat_name;
                }
                //タグ3つ取得
                if(!empty($tags)) {
                    $tagn = ','.$tags[0]->name;
                    if($tags[1]) {
                        $tagn = $tagn.','.$tags[1]->name;
                    }
                    if($tags[2]) {
                        $tagn = $tagn.','.$tags[2]->name;
                    }
                    if($tags[3] && !$cats[1]) {
                        $tagn = $tagn.','.$tags[3]->name;
                    }
                }
                //キーワード出力
                if(!empty($tags)) {
                    echo '<meta name="keywords" content="',$catn,$tagn,'">';
                }else{
                    echo '<meta name="keywords" content="',$catn,'">';
                }
            }elseif(is_front_page() && is_home()) {//ホーム
                $author = get_userdata($post->post_author);
                $argch = array(
                    'parent' => 0,
                    'orderby' => 'count'
                );
                $cath = get_categories($argch);
                foreach($cath as $category) {
                    $catf =  $category->name;
                }
                echo '<meta name="keywords" content="',$catf.','.$author->display_name,'">';//出力
            }elseif(is_category() || is_tag() ){//カテゴリー、タグページ
                $catnk =  single_cat_title('',false);
                echo '<meta name="keywords" content="',$catnk.','.$author->display_name,'">';//出力
            }
        }
    }

    //ノーインデックスページの設定
    $noin = '<meta name="robots" content="noindex,follow">';
    //カテゴリー
    if(is_category() && $option['61']) {
        echo $noin;
    }
    //タグ
    if(is_tag() && $option['62']) {
        echo $noin;
    }
    //2ページ目以降
    if(is_paged() && $option['63']) {
        echo $noin;
    }
    //作成者ページ
    if(is_author() && $option['64']) {
        echo $noin;
    }
    //日付別ページ
    if(is_date() && $option['65']) {
        echo $noin;
    }
    //添付ファイルページ・404・検索結果
    if(is_attachment() || is_search() || is_404() && $option['66']) {
        echo $noin;
    }


    //投稿画面のnofllow,noindex
    //関数取得
    if(is_single() || is_page()) {
        $pnoi = get_post_meta(get_the_ID(),'pnoi', true);
        $pnof = get_post_meta(get_the_ID(),'pnof', true);
        //nofollow,noindexmeta
        if( $pnoi == 1 && $pnof == 1 ){
            echo '<meta name="robots" content="noindex,nofollow">';
        } elseif( $pnoi == 1 && $pnof == 0 ) {
            echo '<meta name="robots" content="noindex,follow">';
        } elseif( $pnoi == 0 && $pnof == 1 ) {
            echo '<meta name="robots" content="index,nofollow">';
        }
    }


    if (($option['71'])) {
        //noydir metaタグ
        echo '<meta name="Slurp" content="noydir">';
        //noodp metaタグ
        echo '<meta name="robots" content="noodp">';
    }

    //表示終わり
    echo '<!-- この上はH Speed WP+SEOによって出力されています -->';
}, 3);

if ($option['67'] > '1') {

    //canonicalの削除
    remove_action('wp_head', 'rel_canonical');

    // Wordpressデフォルトのnext/prev出力動作を停止
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');

}

//hentry出力停止
if (($option['70'])) {
    add_filter( 'post_class', function ( $classes ) {
        $classes = array_diff( $classes, array( 'hentry' ) );//hentryを見つけて削除
        return $classes;
    });
}

//ALT属性自動追加
if ($option['69']) {
    add_filter('the_content', function ($content){
        global $post;
        $catns = get_the_category($post->ID);//カテゴリーの取得
        if(is_single()) {
            $catna = $catns[0]->cat_name;//一番初めのカテゴリー
            $content = preg_replace('/<img((?![^>]*alt=)[^>]*)>/i', '<img alt="'.$catna.'"${1}>', $content);
        } else {
            $content = preg_replace('/<img((?![^>]*alt=)[^>]*)>/i', '<img alt=""${1}>', $content);
        }
        return $content;
    });
}

//画像リンクエラー
if (($option['68'])) {
    add_action('wp_footer',function () {
        if ( is_user_logged_in() ) {
            echo '<script async defer>jQuery(document).ready(function(){jQuery("img").on("error",function(){if(!confirm("このページにはリンク切れの画像があるみたいです。画像を置き換えてください。\n※これはログインしているユーザーのみに表示されます。また、リンク切れ画像の数だけダイアログがでます。\nH Speed WP")){return false}})});</script>';
        }
    } , 9999);
}

//投稿画面のカスタマイズ
//固定ページに抜擢入力欄を追加
add_post_type_support( 'page', 'excerpt' );

//HTML5タグがXHTMLタグに変換されるのを防ぐ
remove_filter('the_content', 'convert_chars');

//nofllow,noindex投稿画面
function hsw_2(){
    global $post;
    $pnoi = get_post_meta(get_the_ID(),'pnoi', true);
    $pnof = get_post_meta(get_the_ID(),'pnof', true);
    //noindex
    echo '<label><input type="checkbox" name="pnoi" value="1"';
    if( $pnoi == 1 ){echo ' checked';}
    echo '>インデックスしない（noindex）</label>';
    echo '<p class="howto"><div>このページが検索エンジンに表示されないように、メタタグを設定します。</div><div>この記事が低品質だと思われる場合設定してください</div></p>';
    //nofollow
    echo '<label><input type="checkbox" name="pnof" value="1"';
    if( $pnof == 1 ){echo ' checked';}
    echo '>リンクをフォローしない（nofollow）</label>';
    echo '<p class="howto"><div>このページのリンク先に検索エンジンが遷移しないように、メタタグを設定します。</div><div>あまり設定はおすすめしませんが、このページのリンク先のページが信用出来ない場合（有害なサイトなど）は有効です。</div></p>';
}
//投稿画面にボックスを追加
function hsw_3(){
    add_meta_box( 'seo_setting','SEOの設定', 'hsw_2', 'post', 'side' );
    add_meta_box( 'seo_setting','SEOの設定', 'hsw_2', 'page', 'side' );
    add_meta_box( 'seo_setting','SEOの設定', 'hsw_2', 'topic', 'side' );
}
function hsw_15() {
    add_action('admin_menu', 'hsw_3');
}
add_action('init', 'hsw_15');


//nofollow,noindex設定の保存
function hsw_4(){
    //初期値
    if (!isset($_POST['pnoi'])) {
        $_POST['pnoi'] = 0;
    }
    if (!isset($_POST['pnof'])) {
        $_POST['pnof'] = 0;
    }
    $id = get_the_ID();
    //noindex
    $pnoi = $_POST['pnoi'];
    update_post_meta($id, 'pnoi', $pnoi);
    //nofollow
    $pnof = $_POST['pnof'];
    update_post_meta($id, 'pnof', $pnof);

}
add_action('save_post', 'hsw_4');

//投稿画面にcss,js
function hsw_5() {
?>
<!-- タイトル文字数  -->
<script async="async">
    function strLength(a) {
        len = 0;
        a = escape(a);
        for (i = 0; i < a.length; i++, len++) {
            if (a.charAt(i) == "%") {
                if (a.charAt(++i) == "u") {
                    i += 3;
                    len++
                }
                i++
            }
        }
        return len
    }
    jQuery(function (a) {
        function b() {
            var c = strLength(a("#title").val()).toString() / 2;
            var d = a("#title-counter").text(c)
            }
        a("#titlewrap").before('<div id="title-counter"></div>').bind("keyup", b);
        b()
    });
</script>
<script async="async">
    (function (a) {
        a("#postexcerpt .hndle span").after('<span style="padding-left:1em; color:#888; font-size:12px;">現在の文字数： <span id="excerpt-count"></span></span>');
        a("#excerpt").keyup(function () {
            a("#excerpt-count").text(a("#excerpt").val().length)
        });
        a("#postexcerpt .inside p").html("<div>抜擢はSEO上大切なdescriptionに自動で挿入される（要設定）ので、入力することをお勧めします。</div><div>description（要約）はこの記事の内容を分かりやすく、100文字程度でまとめるのがSEO上のポイントです。</div>").css("color", "#888")
    }(jQuery));
</script>
<style>
    /*タイトル文字数*/
    #title-counter {
        text-align: right;
        width: 100%;
    }

    #title-counter:before {
        content: "現在のタイトル文字数：";
        display: inline-block;
    }

    #title-counter:after {
        content: "文字　【SEO的にタイトルは32文字以内が適切です】";
        display: inline-block;
        font-size: .9em;
    }
    /*タイトル注意*/

    #titlewrap:after {
        content: "SEO上タイトルはとても大事です。いくつかキーワードを入れ、読む人を引きつけるようなタイトルにしましょう";
    }
    /*本文文字数*/
    #wp-word-count:after {
        content: "SEO的に文字数は最低でも500文字はあった方がいいでしょう";
        margin-left: 1em;
    }
    /*太字*/
    .mce-toolbar .mce-btn[aria-label="Bold"]:hover:after {
        content: "記事の中で重要なところは太字にします";
        position: absolute;
        z-index: 1;
        top: 30px;
        left: 40px;
        background-color: #fff;
        padding: 5px;
        border-radius: 5px;
        box-shadow: 0 0 5px #ccc;
    }
    /*打ち消し*/
    .mce-toolbar .mce-btn[aria-label="Strikethrough"]:hover:after {
        content: "取り消す部分などは打ち消しにします。";
        position: absolute;
        z-index: 1;
        top: 30px;
        left: 45px;
        background-color: #fff;
        padding: 5px;
        border-radius: 5px;
        box-shadow: 0 0 5px #ccc;
    }
    /*引用*/
    .mce-toolbar .mce-btn[aria-label="Blockquote"]:hover:after {
        content: "他サイトなどから引用する場合は引用元のURLも含めて引用にします";
        position: absolute;
        z-index: 1;
        top: 30px;
        left: 40px;
        background-color: #fff;
        padding: 5px;
        border-radius: 5px;
        box-shadow: 0 0 5px #ccc;
    }
    /*区切り*/
    .mce-toolbar .mce-btn[aria-label="Horizontal line"]:hover:after {
        content: "SEO的に多様するのはよくないかもしれません。";
        position: absolute;
        z-index: 1;
        top: 30px;
        left: 45px;
        background-color: #fff;
        padding: 5px;
        border-radius: 5px;
        box-shadow: 0 0 5px #ccc;
    }
    /*リンク文字列*/
    #link-options .wp-link-text-field:after {
        content: "リンクの文字列はリンク先のページの内容が分かる文字列にします";
        white-space: pre;
        display: block;
        text-align: center;
        margin: .5em 0;
    }
    /*見出し*/
    .mce-menu-align[role="application"]:after {
        content: "見出しタグを使って、文章を適切に分け、読みやするとともにSEOにも強くしましょう\a見出しタグは階段上の階層構造が最適です\a・見出し1→タイトルで設定されるので極力使わないでください\a・見出し2→実質的に最も大きい見出しです。キーワードを含むようにして、1ページに3つ以内になるようにしましょう\a・見出し3～→見出しだけで記事の内容が分かるようにして、キーワードが含まれるように意識しましょう";
        display: inline-block;
        position: absolute;
        max-width: 100vw;
        margin-top: 10px;
        background-color: #fff;
        color: #111;
        z-index: 1;
        word-wrap: break-word;
        padding: 5px;
        white-space: pre;
        box-sizing: border-box;
        box-shadow: 0 0 5px #999;
    }

    .mce-panel.mce-menu,
    .mce-menu .mce-container-body {
        overflow: visible;
    }
    /*画像alt*/
    .column-settings .setting.alt-text:after,
    label.setting[data-setting="alt"]:after {
        content: "代替テキストに画像の説明を入力することで、SEOに有利になります。その画像の内容がよく分かるように代替テキストを入力しましょう";
        white-space: normal;
        display: block;
        text-align: center;
        margin: 3em 2px .5em;
        word-wrap: break-word;
        padding: 1px;
        height: auto;
        background-color: #fff;
        box-shadow: 0 0 5px #ccc;
        box-sizing: border-box;
    }

    .column-settings .setting.alt-text:after {
        margin: 0.5em 2px .5em;
    }
    /*カテゴリー*/
    #categorydiv .inside:before {
        content: "この記事の内容にふさわしいカテゴリを選択しましょう";
        text-align: center;
        display: block;
    }
    /*タグ*/
    #tagsdiv-post_tag .inside:before {
        content: "この投稿のキーワードを2～3つほどタグとして設定しておきましょう";
        text-align: center;
        display: block;
    }
    /*アイキャッチ*/
    #postimagediv .inside:after {
        content: "アイキャッチ画像を設定すると、記事の見栄えが良くなります";
        text-align: center;
        display: block;
    }
    /*公開*/
    #publishing-action:hover:after {
        content: "これを押すと記事が公開されます。\A入力ミス等ないか確認してから公開してください";
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        background-color: #fff;
        padding: 5px;
        box-shadow: 0 0 5px #888;
        width: 100%;
        box-sizing: border-box;
        margin-top: 10px;
        white-space: pre;
        text-align: center;
    }
    /*URL設定*/
    #edit-slug-buttons:after,
    #slugdiv .inside:after {
        content: "パーマリンクはこのページのキーワードをいくつかを英語にして-(ハイフン)で繋いだものをおすすめします";
        display: block;
    }
    /*アイキャッチ画像*/
    #postimagediv #poststuff .inside > .hide-if-no-js:first-child:after {
        content: "記事を読みやすくするだけではなく、アイキャッチはさまざまなところに使われるのであったほうがいいでしょう";
        display: block;
        margin-top: .5em;
    }
    /*記事の書き方*/
    #postdivrich:before {
        content: "この記事をどんな人にに読んでもらうのか、意識しながら体験談や図や写真を用いながら分かりやすく、面白い記事をかくようにしましょう";
        font-weight: 700;
        text-align: center;
        display: block;
        margin: 0.5em 0;
    }
</style>
<?php }
add_action( 'admin_head-post.php', 'hsw_5' );
add_action( 'admin_head-post-new.php', 'hsw_5' );

function hsw_6() {
?>
<style>
    /*画像alt*/
    label.setting[data-setting="alt"]:after {
        content: "代替テキストに画像の説明を入力することで、SEOに有利になります。その画像の内容がよく分かるように代替テキストを入力しましょう";
        white-space: pre;
        display: block;
        text-align: center;
        margin: 3em 0 .5em;
        word-wrap: break-word;
        border: 1px solid #555;
        padding: 1px
    }
</style>
<?php }
add_action( 'admin_head-upload.php', 'hsw_6' );

function hsw_7() {
?>
<style>
    /*パーマリンク*/
    form[action="options-permalink.php"]:before {
        content: "SEOに強いパーマリンクにするためには、カスタム構造で【/%postname%】にすることをおすすめします";
        box-shadow: 0 0 8px #111;
        background-color: #fff;
        padding: 8px;
        margin-top: 1em;
        display: inline-block;
        font-size: 1pc
    }
</style>
<?php }
add_action( 'admin_head-options-permalink.php', 'hsw_7' );

function hsw_8() {
?>
<style>
    /*インデックス無効警告*/
    .option-site-visibility fieldset:after {
        content: "【注意】 上記の設定にチェックを入れると、検索結果のページに全く表示されなくなってしまいます";
        font-weight: 700
    }
</style>
<?php }
add_action( 'admin_head-options-reading.php', 'hsw_8' );
add_action( 'admin_head', 'hsw_8' );

function hsw_9() {
?>
<style>label[for="ping_sites"] {display: block;}</style>
<script async defer>
    jQuery(function(){
        jQuery('label[for="ping_sites"]').after('<div style="margin: 1em 0;box-shadow: 0 0 5px #999;padding: 1em;display: inline-block;user-select: none;-webkit-user-select: none;-moz-user-select: none;-khtml-user-select: none;">オススメな設定は以下のとおりです。テキストボックスの中身を全て削除してから、こちらを貼り付けてください<div style="background-color: #fff;overflow-y: scroll;max-height: 100px;padding: 10px;border: 1px solid #ccc;margin: 5px 0;user-select: text;-webkit-user-select: text;-moz-user-select: text;-khtml-user-select: text;">http://blogsearch.google.co.jp/ping/RPC2<br>http://blogsearch.google.com/ping/RPC2<br>http://ping.fc2.com<br>http://blog.with2.net/ping.php/<br>http://ranking.kuruten.jp/ping<br>http://ping.feedburner.com<br>http://ping.freeblogranking.com/xmlrpc/<br>http://ping.rss.drecom.jp/<br>http://rpc.weblogs.com/RPC2<br>http://xping.pubsub.com/ping/<br>http://rpc.pingomatic.com/<br>http://ping.blogranking.net/<br>http://ping.blo.gs/<br>http://services.newsgator.com/ngws/xmlrpcping.aspx<br>http://www.blogpeople.net/ping/</div></div>');
    });
</script>
<?php }
add_action( 'admin_head-options-writing.php', 'hsw_9' );

function hsw_10() {
    //抜擢文字数
?>
<script async="async">
    (function (a) {
        a("#postexcerpt .hndle span").after('<span style="padding-left:1em; color:#888; font-size:12px;">現在の文字数： <span id="excerpt-count"></span></span>');
        a("#excerpt").keyup(function () {
            a("#excerpt-count").text(a("#excerpt").val().length)
        });
        a("#postexcerpt .inside p").html("<div>抜擢はSEO上大切なdescriptionに自動で挿入される（要設定）ので、入力することをお勧めします。</div><div>description（要約）はこの記事の内容を分かりやすく、100文字程度でまとめるのがSEO上のポイントです。</div>").css("color", "#888")
    }(jQuery));
</script>
<?php
}
add_action( 'admin_footer-post-new.php', 'hsw_10' );
add_action( 'admin_footer-post.php', 'hsw_10' );


function hsw_11() {
?>
<style>
    /*コメント*/
    label[for="page_comments"]:before {
        content: "SEO的には無効化を推奨します→";
    }
</style>
<?php }
add_action( 'admin_head-options-discussion.php', 'hsw_11' ); 

function hsw_12() {
?>
<style>
    .form-field.term-description-wrap:after {
        content: "descriptionに設定される(要設定)ので、このカテゴリーの説明文を100文字程度で入力することをお勧めします";
    }
</style>
<?php }
add_action( 'admin_head-edit-tags.php', 'hsw_12' );