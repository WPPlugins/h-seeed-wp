<?php
global $goption;
$option = $goption;

//キャッシュの表示

$is_logged_in = false;
$is_comment_awaiting = false;

//この時点では is_user_logged_in がまだ使用できないので直接クッキーをチェック
foreach(array_keys($_COOKIE) as $key => $value) {
    if(stripos($value , 'wordpress_logged_in') !== false ) {
        $is_logged_in = true;
        break;
    }
}
//承認待ちコメントの特定ユーザーには、承認待ちコメントを表示させるのでキャッシュは不可
foreach(array_keys($_COOKIE) as $key => $value) {
    if(preg_match("/comment_author_email/", $value)) {
        $comment_author_email = $_COOKIE[$value];
        $comment_author_email = wp_unslash($comment_author_email);
        $comment_author_email = esc_attr($comment_author_email);
        if(!empty($comment_author_email)){
            global $wpdb;
            $comment = $wpdb->get_var("SELECT comment_ID FROM $wpdb->comments WHERE comment_author_email = '$comment_author_email' and comment_approved = '0' LIMIT 1");
            if(!empty($comment)){
                $is_comment_awaiting = true;
                break;
            }
        }
    }
}

if(!$is_logged_in && !$is_comment_awaiting){
    //この時点では wp_is_mobile がまだ使用できない（wp-include/vars.php wp_is_mobile() と同等処理）
    $is_cache_mobile = false;
    if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
        $is_cache_mobile = false;
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false // many mobile devices (all iPhone, iPad, etc.)
              || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
              || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
              || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
              || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
              || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
              || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
        $is_cache_mobile = true;
    }

    if ($is_cache_mobile) {
        $cache_file = WP_CONTENT_DIR.'/plugins/h-seeed-wp/cache/mobile_'.md5($_SERVER['REQUEST_URI']).'.txt';
    } else {
        $cache_file = WP_CONTENT_DIR.'/plugins/h-seeed-wp/cache/pc_'.md5($_SERVER['REQUEST_URI']).'.txt';
    }

    if ( file_exists( $cache_file ) ) {
        if ((date( filemtime($cache_file) ) + 10800) > $_SERVER['REQUEST_TIME']) {
            
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

                //IFRAMEを出力しない
                header('X-FRAME-OPTIONS: SAMEORIGIN');

                //PHPバージョンの非表示
                header_remove('X-Powered-By');

                //XSS対策
                header('X-Content-Type-Options:nosniff');
                header('X-XSS-Protection:1; mode=block');

                //レスポンシブヘッダーに文字コード出力
                header("Content-type: text/html; charset=utf-8");


                //phpエラー表示しない
                ini_set( 'display_errors', 0 );

                if ($option['45'] > '2') {

                    //PHP実行時間
                    ini_set('max_execution_time', '10');
                    //PHPメモリー制限
                    ini_set('memory_limit', '128M');

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

            if ($is_cache_mobile) {
                $cache_file = plugins_url().'/h-seeed-wp/cache/mobile_'.md5($_SERVER['REQUEST_URI']).'.txt';
            } else {
                $cache_file = plugins_url().'/h-seeed-wp/cache/pc_'.md5($_SERVER['REQUEST_URI']).'.txt';
            }

            $hsw_cache_html = curl_init();
            curl_setopt($hsw_cache_html, CURLOPT_URL, $cache_file);
            curl_setopt( $hsw_cache_html, CURLOPT_HEADER, false );
            curl_setopt( $hsw_cache_html, CURLOPT_RETURNTRANSFER, true );
            $hsw_content = curl_exec($hsw_cache_html);
            curl_close($hsw_cache_html);
            
            //検索エンジン向けの処理
            if (stripos($_SERVER['HTTP_USER_AGENT'], 'Googlebot') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'bingbot' ) !== false ) {
                if ($option['4'] > '2') {
                    if ($option['81'] === '2') {
                        $hsw_content = str_replace('data-normal="http', 'src="http', $buffer);
                        $hsw_content = str_replace("data-normal='http", "src='http", $buffer);
                        $hsw_content = str_replace('data-srcset=', 'srcset=', $buffer);
                    } elseif ($option['81'] === '1') {
                        $hsw_content = str_replace('echo="http', 'src="http', $buffer);
                        $hsw_content = str_replace("echo='http", "src='http", $buffer);
                    }
                }
            }

            echo $hsw_content;

            exit;

        } else {
            unlink( $cache_file );
        }
    }
}


        
if(!$is_logged_in && !$is_comment_awaiting && !is_admin() && $_SERVER["REQUEST_METHOD"] != 'POST' && ( !defined('DOING_AJAX') ) && (!defined('DOING_CRON'))){

    //キャッシュデータの作成
    function callback_html( $buffer ) {
        
        //保存先の指定
        if (wp_is_mobile()) {
            $file = WP_CONTENT_DIR.'/plugins/h-seeed-wp/cache/mobile_'.md5($_SERVER['REQUEST_URI']).'.txt';
        } else {
            $file = WP_CONTENT_DIR.'/plugins/h-seeed-wp/cache/pc_'.md5($_SERVER['REQUEST_URI']).'.txt';
        }
        
        //headの圧縮
        preg_match_all( "/<head.+?<\/head>/usi", $buffer, $head_tag );
        $head = $head_tag[0][0];
        unset($head_tag);
        
        //圧縮してはいけないものを回収
        //meta
        preg_match_all( "/<meta [^>]+?>/ius", $head, $meta_array );
        $head = preg_replace( "/<meta [^>]+?>/ius", "", $head );
        //comment
        preg_match_all( "/<!--\[.+?<!\[endif\]-->/ius", $head, $comment_array );
        $head = preg_replace( "/<!--\[.+?<!\[endif\]-->/ius", "", $head );
        //link
        preg_match_all( "/<link [^>]+?>/ius", $head, $rel_array );
        $head = preg_replace( "/<link [^>]+?>/ius", "", $head );
        //style
        preg_match_all( "/<style.+?<\/style>/ius", $head, $style_array );
        $head = preg_replace( "/<style .+?<\/style>/ius", "", $head );
        //script
        preg_match_all( "/<script.+?<\/script>/ius", $head, $script_array );
        $head = preg_replace( "/<script .+?<\/script>/ius", "", $head ); 
        
        //head圧縮
        $head = str_replace("\r", '', $head);
        $head = str_replace("\n", '', $head);
        $head = str_replace( " ", '', $head );
        $head = preg_replace( "/<!--.+?-->/ius", "", $head );

        //metaを一箇所にまとめる
        foreach( array_unique( $meta_array[0] ) as $meta ) {
            $head = str_replace( "</head>", $meta . "</head>", $head );
        }

        //限定スクリプトを一箇所にまとめる
        foreach( array_unique( $comment_array[0] ) as $comment ) {
            $head = str_replace( "</head>", $comment . "</head>", $head );
        }

        // link の記述を一カ所にまとめる
        foreach( array_unique( $rel_array[0] ) as $rel ) {
            $head = str_replace( "</head>", $rel . "</head>", $head );
        }
        // style の記述を一カ所にまとめる
        foreach( array_unique( $style_array[0] ) as $style ) {
            $head = str_replace( "</head>", $style . "</head>", $head );
        }

        // script を一カ所にまとめる
        foreach( array_unique( $script_array[0] ) as $key => $script ) {
            // jquery を優先して先に表示させる
            if( stristr( $script, "jquery" ) ) {
                $head = str_replace( "</head>", $script . "</head>", $head );
                unset( $script_array[0][$key] );
            }
        }
        foreach( array_unique( $script_array[0] ) as $script ) {
            $head = str_replace( "</head>", $script . "</head>", $head );
        }
        
        //改行の追加
        //$head = str_replace( "</head>", "\n</head>", $head );
        
        //headの最適化
        $head = str_replace("\t", '', $head);
        
        
        //最終出力
        $buffer = preg_replace( "/(<head.+?<\/head>)/ius", $head, $buffer );

        $content = fopen($file, 'w');
        fwrite($content, $buffer);
        fclose($content);

        return $buffer;
    }

    add_action( 'template_redirect', function() {
        if(!is_user_logged_in() && (is_home() || is_front_page() || is_singular())) {
            
            ob_start('callback_html');
        }
    });
    add_action( 'shutdown', function() {
        if(!is_user_logged_in() && (is_home() || is_front_page() || is_singular())) {
            ob_end_flush();
        }
    });
}

//キャッシュの削除
add_action('after_switch_theme', 'hsw_cache_deletion');//テーマ変更
add_action('wp_insert_post', 'hsw_cache_deletion');//投稿
add_action('trashed_post', 'hsw_cache_deletion');//投稿ゴミ箱
add_action( 'edited_terms', 'hsw_cache_deletion');//カテゴリーの変更
add_action( 'comment_post', 'hsw_cache_deletion' );//コメント投稿
add_action( 'edit_comment', 'hsw_cache_deletion' );//コメント編集
add_action( 'activated_plugin', 'hsw_cache_deletion' );//プラグイン有効化
add_action( 'deactivated_plugin', 'hsw_cache_deletion');//プラグイン無効化
if(stripos($_SERVER['REQUEST_URI'] , 'updated=true') !== false && stripos($_SERVER['QUERY_STRING'] , '.php') !== false) {
    add_action('admin_head-theme-editor.php', 'hsw_cache_deletion');//テーマの編集
}
//add_action( 'updated_option', 'hsw_cache_deletion');//設定の更新

function hsw_cache_deletion() {
    // ディレクトリのパスを記述
    $dir =  WP_CONTENT_DIR.'/plugins/h-seeed-wp/cache/';

    // ディレクトリの存在を確認
    if( is_dir( $dir ) && $handle = opendir( $dir ) ) {
        // ループ処理
        while( ($file = readdir($handle)) !== false ) {
            // ファイルのみ取得
            if( filetype( $path = $dir . $file ) == 'file' ) {
                // 各ファイルへの処理
                unlink( $path ) ;
            }
        }
    }
}