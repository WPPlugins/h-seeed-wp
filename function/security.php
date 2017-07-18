<?php
global $goption;
$option = $goption;

//不正ログイン対策
if ($option['41'] > '1') {
    add_action('login_init', function () {
        //USER_AGENTが取れない場合
        $useragent = esc_attr($_SERVER["HTTP_USER_AGENT"]);
        if (empty($useragent)) {
            status_header(404);
            exit();
        }
        //IE8以下だったら
        if(preg_match('/(?i)msie [1-8]\./',$_SERVER['HTTP_USER_AGENT'])){
            wp_die(__('<strong>ERROR</strong> : セキュリティの観点から、あなたのブラウザからのログインページへのアクセスはブロックしています。'));
        }
        //日本語以外だったら
        if (!is_JP()) {
            status_header(404);
            exit();
        }
        //ロックがかかっていたら
        $ip = $_SERVER["REMOTE_ADDR"];
        $login_kazu = get_transient( $ip );
        if ($login_kazu > 2) {
            status_header(404);
            exit();
        }
    } , 1);

    //ログインメッセージ
    add_filter('login_errors', create_function('$a', "return '<strong>ERROR</strong>: ログインできませんでした。You could not login.';"));


    //loginリダイレクト
    remove_action('template_redirect', 'wp_redirect_admin_locations', 1000);


    if ($option['41'] > '2') {

        //不正ログインの防止
        add_action('wp_login_failed', function ($username) {

            //ipの取得
            $ip = $_SERVER["REMOTE_ADDR"];

            //失敗回数
            $login_kazu = get_transient( $ip );
            if ( $login_kazu === false ) {
                $login_kazu = 0;
            }

            //js投稿時間
            if (htmlspecialchars($_POST['login_spam']) !== 'h832950') {
                $login_kazu += 4;
            } else {
                ++$login_kazu;
            }
            set_transient( $ip, $login_kazu, 60 );


            //ログイン通知メール
            if ( $login_kazu === 3) {
                $pn = get_bloginfo('name');
                $br = $_SERVER["HTTP_USER_AGENT"];
                date_default_timezone_set('Asia/Tokyo');
                $da = date( "Y年m月d日 H時i分s秒" ) ;
                $subject = "日本時間 ".$da."頃に".$pn."にログインしようとして、3回以上失敗したので、そのアクセス元に対してログインページをロックしました。見に覚えのない場合はH Speed WPのセキュリティ設定を見なおしたり、下記のIPのアクセスをブロックしたほうが良いかもしれません。\r\n\r\n--ログイン元の情報--\r\nIPアドレス : ".$ip."\r\nブラウザの情報 : ".$br."\r\nログインに使用したユーザー名 : ".$username."\r\n-------\r\nH Speed WP http://xn--48sa.jp/h-speed-wp\r\n※このメールは自動送信されています。";
                wp_mail(get_option('admin_email'), ワードプレスログイン通知, $subject);
            }
            
            //ログインロック
            if ($login_kazu > 2) {
                status_header(404);
                exit();
            }
            
        }, 1);
        
        //隠し入力欄の設置
        add_action( 'login_form', function () {
            echo '<input id="login_spam" type="hidden" name="login_spam" value="">';
            echo '<script>var spam=function(){document.getElementById("login_spam").value = "h832950";};setTimeout(spam, 100);</script>';
        } );
        
        //コメント欄にユーザー名が出るのを防止

        add_filter('comment_class',function ($classes) {
            foreach ($classes as $key => $class) {
                if (strstr($class, "comment-author-")) {
                    unset($classes[$key]);
                }
            }
            return $classes;
        } );

        if ($option['41'] === '3') {

            //ユーザー名の漏洩防止
            add_action('init',function () {
                //profileにする
                global $wp_rewrite;
                $author_slug                  = 'profile';
                $wp_rewrite->author_structure = '/';
                $wp_rewrite->author_base      = $author_slug;
                $wp_rewrite->flush_rules();
                //authorがあったらリダイレクト
                $request_uri = $_SERVER['REQUEST_URI'];
                if (strstr($request_uri, '/?author=')) {
                    wp_redirect(home_url());
                    exit;
                }
            } );

        } elseif($option['41'] === '4') {

            //ユーザー名の漏洩防止
            //完全に投稿者アーカイブの無効化
            add_action('init',function () {
                global $wp_rewrite;
                $wp_rewrite->flush_rules();
                $wp_rewrite->author_base      = '';
                $wp_rewrite->author_structure = '/';
                if (isset($_REQUEST['author']) && !empty($_REQUEST['author'])) {
                    $user_info = get_userdata(intval($_REQUEST['author']));
                    if ($user_info && array_key_exists('administrator', $user_info->caps) && in_array('administrator', $user_info->roles)) {
                        wp_redirect(home_url());
                        exit;
                    }
                }
            } );

        }
    }
}


//自動アップデート
//プラグイン自動アップデート
if (($option['42'])) {
    add_filter('auto_update_plugin', '__return_true');
}
//テーマの自動アップデート
if (($option['43'])) {
    add_filter('auto_update_theme', '__return_true');
}
//本体の自動アップデート
if (($option['44'])) {
    add_filter( 'allow_minor_auto_core_updates', '__return_true' );
    add_filter( 'allow_major_auto_core_updates', '__return_true' );
}

//サーバー関係のセキュリティ
if ($option['45'] > '1') {

    //タイムゾーンの設定
    ini_set("date.timezone", "Asia/Tokyo");

    //cookie盗用防止
    ini_set('session.cookie_httponly', 'On');
    ini_set('session.cookie_secure','On');
    ini_set('session.use_only_cookies','On');
    
    //文字コードの設定
    mb_language('Japanese');
    ini_set('default_charset', 'UTF-8');

    
    add_action( 'send_headers', function () {
        //IFRAMEを出力しない
        header('X-FRAME-OPTIONS: SAMEORIGIN');
        
        //PHPバージョンの非表示
        header_remove('X-Powered-By');

        //XSS対策
        header('X-Content-Type-Options:nosniff');
        header('X-XSS-Protection:1; mode=block');
        
        //レスポンシブヘッダーに文字コード出力
        header("Content-type: text/html; charset=utf-8");
        
    } );
    

    //phpエラー表示しない
    if (!is_admin()) {
        ini_set( 'display_errors', 0 );
    }

    if ($option['45'] > '2') {

        if (!is_admin()) {
            //PHP実行時間
            ini_set('max_execution_time', '10');
            //PHPメモリー制限
            ini_set('memory_limit', '128M');
        }

        //ピンバック悪用対策
        //ピンバックの無効化
        add_filter( 'xmlrpc_methods', function ( $methods ) {
            unset( $methods['pingback.ping'] );
            unset( $methods['pingback.extensions.getPingbacks'] );
            return $methods;
        });
        add_filter( 'wp_headers', function ( $headers ) {
            unset( $headers['X-Pingback'] );
            return $headers;
        });

        //XML-RPCの無効化
        if (($option['45'] === '4')) {
            add_filter('xmlrpc_enabled', '__return_false');
        }
    }
}

//迷惑アクセスの防止
if ($option['46'] > '1') {


    //IE6以下と迷惑ボット
    if (preg_match('/(?i)msie [1-6]\./',$_SERVER['HTTP_USER_AGENT']) || stripos($_SERVER['HTTP_USER_AGENT'], 'baiduspider') || stripos($_SERVER['HTTP_USER_AGENT'], 'MJ12bot') || stripos($_SERVER['HTTP_USER_AGENT'], 'AhrefsBot') || stripos($_SERVER['HTTP_USER_AGENT'], 'BLEXBot') || stripos($_SERVER['HTTP_USER_AGENT'], 'Yandex') || stripos($_SERVER['HTTP_USER_AGENT'], 'SemrushBot')) {
        add_action('init',function () {
            wp_die(__('<strong>ERROR</strong> : 申し訳ありませんが、あなたのブラウザでは、このサイトはご覧いただけません。'));
        } );
    }

    if ($option['46'] === '3') {

        //日本語ブラウザでない場合全て表示させない
        if (!is_JP()) {
            add_action('init', function () {
                wp_die(__('<strong>ERROR</strong> : ブラウザの設定言語が日本語以外の場合、アクセスを拒否しています。'));
            });
        }

    }
}

//ワードプレスの機能改善
if ($option['46'] === '2') {

    //バージョンを表示しない
    remove_action('wp_head', 'wp_generator');
    $wrv = array('rss2_head','commentsrss2_head','rss_head','rdf_header','atom_head','comments_atom_head','opml_head','app_head');
    foreach ( $wrv as $action) {
        remove_action($action, 'the_generator');
    }
    if ( !is_admin() && $option['2'] === '0') {
        function hsw_000($src) {
            if (strpos($src, 'ver=' . get_bloginfo('version')))
                $src = remove_query_arg('ver', $src);
            return $src;
        }
        add_filter('style_loader_src', 'hsw_000', 9999);
        add_filter('script_loader_src', 'hsw_000', 9999);
    }

    //パスワードの変更を無効に
    add_action('login_enqueue_scripts',function() {
        echo '<style>.login #nav {display: none;}</style>';
    } );
    add_filter('allow_password_reset', function () {
        return false;
    });

    //utf-8固定
    if ( is_admin()) {
        update_option('blog_charset', 'UTF-8');

        //危険アップロード不可能
        add_filter( 'upload_mimes', function( $mimes ){
            unset( $mimes['exe'] );
            unset( $mimes['htm|html'] );
            unset( $mimes['js'] );
            return $mimes;
        }, 5 );

    }
}
