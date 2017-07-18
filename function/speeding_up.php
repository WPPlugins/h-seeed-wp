<?php
global $goption;
$option = $goption;

if ($option['1']) {
    if (!is_admin()) {
        //gzip圧縮
        add_action('init',function(){
            if(strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== true) {
                ob_start('ob_gzhandler');
            }else{
                ob_start();
            }
        });
    }
    
        //flush()関数を利用した高速化
        function hsw_005() {
            flush();
        }
        add_action('admin_head', 'hsw_005', 9999);
        add_action('wp_head', 'hsw_005', 9999);
}

//jsやcssの読み込み最適化
if ( !is_admin() && $option['2'] > '1') {
    //verを取り除く
    function hsw_051($src) {
            return remove_query_arg('ver', $src);
    }
    add_filter('style_loader_src', 'hsw_051');
    add_filter('script_loader_src', 'hsw_051');

    //CSSの不要属性を取り除く
    add_filter( 'style_loader_tag', function ( $tag ) {
        return preg_replace( array( "| type='.+?'\s*|","| id='.+?'\s*|", '| />|' ), array( ' ',' ', '>' ), $tag );
    });

    //JS非同期、不要属性を取り除く
    add_filter('script_loader_tag', function($tag) {
        $tag = str_replace("type='text/javascript'", 'async defer', $tag);
        $tag = str_replace('http://', '//', $tag);
        $tag = str_replace('https://', '//', $tag);
        return $tag;
    });

    //opensansフォントを読み込まない
    add_action('wp_enqueue_scripts', function () {
        wp_deregister_style('open-sans');
        wp_register_style('open-sans', false);
    });

    if ($option['2'] === '3') {
        //jsをフッターに移動
        remove_action('wp_head', 'wp_print_scripts');
        add_action('wp_footer', 'wp_print_scripts', 5);
        remove_action('wp_head', 'wp_print_head_scripts', 9);
        add_action('wp_footer', 'wp_print_head_scripts', 5);
        remove_action('wp_head', 'wp_enqueue_scripts', 1);
        add_action('wp_footer', 'wp_enqueue_scripts', 5);
    }
}

//最新版のjqueryを読み込む
if (!is_admin()) {
    if ($option['3'] > '1') {
        add_action('wp_head', function () {
            wp_deregister_script('jquery');
        } , 0);
        
        //ヘッダー
        if ($option['3'] === '2') {
        add_action('wp_head', function () {
            global $is_IE;
            if ($is_IE) {
                echo '<script src="', plugins_url() ,'/h-seeed-wp/js/jquery-1.12.3.min.js"></script>';
            } else {
                echo '<script src="', plugins_url() ,'/h-seeed-wp/js/jquery-2.2.2.min.js"></script>';
            }
        } , 15);
        }
        
        //フッター
        if ($option['3'] === '3') {
            add_action('wp_footer', function () {
                global $is_IE;
                if ($is_IE) {
                    echo '<script src="', plugins_url() ,'/h-seeed-wp/js/jquery-1.12.3.min.js"></script>';
                } else {
                    echo '<script src="', plugins_url() ,'/h-seeed-wp/js/jquery-2.2.2.min.js"></script>';
                }
            } , 0);
        }
    }
}

//画像関係
if ($option['4'] > '1') {
    //画像圧縮
    if (is_admin() || $option['82'] !== '1') {
        
        //ワードプレスの標準圧縮の無効化
        add_filter( 'wp_editor_set_quality', function( $quality ) { return 100; } );
        
        //画像圧縮
        add_action('wp_handle_upload', function ($file) {

            global $goption;
            $option = $goption;

            $imf = $file['file'];
            $image_size = strlen(file_get_contents($imf));

            //圧縮度の設定
            $quality = 82;
            if ($option['82'] === '2') {
                $quality = 70;
            } elseif ($option['82'] === '3') {
                $quality = 60;
            }

            //jpeg画像
            if($file['type'] == 'image/jpeg') {

                $image = wp_get_image_editor( $imf );

                if ( !is_wp_error( $image ) ) {
                    $image->set_quality( $quality-5 );
                    $image->save( $imf );
                }


            } elseif ($file['type'] == 'image/png' && $image_size > 10000 && stripos( $file['url'] , 'not' ) === false) {

                //png画像
                $hozon = str_replace('.png', '.jpg', $imf);

                //pngの変換
                $him = imagecreatefrompng($imf);
                imagesavealpha($him, true);
                imagejpeg($him, $hozon, 100);


                if( $image_size <= strlen(file_get_contents($hozon) )) {

                    unlink($hozon);

                } else {

                    imagedestroy($him);

                    $image = wp_get_image_editor( $hozon );
                    if ( !is_wp_error( $image ) ) {
                        $image->set_quality( $quality+5 );
                        $image->save( $hozon );
                        $file['file'] = $hozon;
                        $file['url'] = str_replace('.png', '.jpg', $file['url']);
                        $file['type'] = 'image/jpeg';
                    }
                }

            }

            return $file;
        } , 1);

        //画像のリサイズ
        add_action('wp_handle_upload', function ($file) {
            if ($file['type'] == 'image/jpeg' || $file['type'] == 'image/png') {
                $w     = intval(get_option('large_size_w'));
                $h     = intval(get_option('large_size_h'));
                $image = wp_get_image_editor($file['file']);
                if (!is_wp_error($image)) {
                    $size = getimagesize($file['file']);
                    if ($size[0] > $w || $size[1] > $h) {
                        $image->resize($w, $h, false);
                        $final_image = $image->save($file['file']);
                    }
                }
            }
            return $file;
        });
    }

    if ($option['4'] !== '2') {
        //画像の遅延読み込み

        if (is_admin() || stripos($_SERVER['HTTP_USER_AGENT'], 'Googlebot') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'bingbot' ) !== false ) {} else {
            if ($option['81'] === '2') {
                add_action('wp_footer', function () {
                    
                    echo '<script src="',plugins_url(),'/h-seeed-wp/js/layzr.js"></script>';
                } , 9999999);

                function hsw_050_1($content) {
                    $content = str_replace('src="http', 'data-normal="http', $content);
                    $content = str_replace("src='http", "data-normal='http", $content);
                    $content = str_replace('srcset=', 'data-srcset=', $content);
                    return $content;
                }
                add_filter('the_content', 'hsw_050_1', 999999);
                add_filter('post_thumbnail_html', 'hsw_050_1', 99999);
                add_filter('get_avatar', 'hsw_050_1', 999999);
                add_filter('widget_text', 'hsw_050_1', 999999);
                add_filter('comment_text ', 'hsw_050_1', 999999);
                
            } elseif ($option['81'] === '1') {
                
                // Webサイト全体の画像をResponsive images機能の対象から外す
                add_filter( 'wp_calculate_image_srcset', '__return_false' );
                
                add_action('wp_footer', function () {
                    echo '<script async defer src="',plugins_url(),'/h-seeed-wp/js/echo.js"></script>';
                }, 9999999);

                function hsw_049_1($content) {
                    $content = str_replace('src="http', 'echo="http', $content);
                    $content = str_replace("src='http", "echo='http", $content);
                    return $content;
                }
                add_filter('the_content', 'hsw_049_1', 999999);
                add_filter('post_thumbnail_html', 'hsw_049_1', 99999);
                add_filter('get_avatar', 'hsw_049_1', 999999);
                add_filter('widget_text', 'hsw_049_1', 999999);
                add_filter('comment_text ', 'hsw_049_1', 999999);

            }
        }
        if ($option['4'] === '4') {
            // base64エンコード
            function hsw_014($img) {
                $type = substr(strrchr($img, '.'), 1);
                $img = curl_init($img);
                curl_setopt($img, CURLOPT_HEADER, false);
                curl_setopt($img, CURLOPT_RETURNTRANSFER, true);
                $imgs = curl_exec($img);
                curl_close($img);
                
                $imga = base64_encode($imgs);
                return 'data:image/' . $type . ';base64,' . $imga;
            }

            //imgタグのsrcを書き換える
            add_filter('post_thumbnail_html', function ($html, $post_id, $post_thumbnail_id) {
                preg_match('/(?<=src=[\'"])([^\'"]*)/', $html, $match);
                $img = hsw_014($match[0]);
                return str_replace($match[0], $img, $html);
                return $html;
            }, 10, 4);

        }
    }
}
//プラグインの読み込み最適化
if ($option['5'] === '3') {

    //読み込みの停止

    //Yet Another Related Postsの読み込み最適化
    add_action('wp_enqueue_scripts','hsw_010'); //ヘッダー
    add_action('wp_footer','hsw_010'); //フッター
    function hsw_010(){
        wp_dequeue_style('yarppRelatedCss');
        wp_dequeue_style('yarpp-thumbnails-yarpp-thumbnail');
    }

    add_action('wp_enqueue_scripts', function() {
        //Table of Contents Plus
        wp_deregister_script('toc-front');
        wp_deregister_style('toc-screen');
        //Syntax Highlighterの読み込み最適化
        wp_dequeue_style('crayon-theme-classic');
        wp_dequeue_style('crayon');
        wp_dequeue_style('crayon-font-monaco');
        //wp-pagenaviの読み込み最適化
        wp_deregister_style('wp-pagenavi');
    }, 100);

    add_action('wp_enqueue_scripts', function () {
        //Contact Form 7の読み込み最適化
        wp_deregister_script('contact_form_7');
        wp_deregister_style('contact_form_7');
        //Child Pages Shortcodeの最適化
        wp_deregister_style('child-pages-shortcode-css');
        //Theme My Loginの最適化
        wp_deregister_style('theme-my-login');
    });

    //jetpack
    add_action('wp_enqueue_scripts', function() {
        wp_deregister_style('the-neverending-homepage'); // Infinite Scroll
        wp_deregister_style('infinity-twentyten'); // Infinite Scroll - Twentyten Theme
        wp_deregister_style('infinity-twentyeleven'); // Infinite Scroll - Twentyeleven Theme
        wp_deregister_style('infinity-twentytwelve'); // Infinite Scroll - Twentytwelve Theme
        wp_deregister_style('publicize'); // Publicize
        wp_deregister_style('sharedaddy'); // Sharedaddy
        wp_deregister_style('sharing'); // Sharedaddy Sharing
        wp_deregister_style('stats_reports_css'); // Stats
        wp_deregister_style('AtD_style'); // After the Deadline
        wp_deregister_style('jetpack-carousel'); // Carousel
        wp_deregister_style('grunion.css'); // Grunion contact form
        wp_deregister_style('noticons'); // Notes
        wp_deregister_style('post-by-email'); // Post by Email
        wp_deregister_style('jetpack-widgets'); // Widgets
    });
    add_filter('jetpack_implode_frontend_css', '__return_false');//CSS全般

} elseif ($option['5'] === '2') {
    //読み込みの最適化

    if (!is_single()) {

        //Yet Another Related Postsの読み込み最適化
        add_action('wp_enqueue_scripts','hsw_010'); //ヘッダー
        add_action('wp_footer','hsw_010'); //フッター
        function hsw_010(){
            wp_dequeue_style('yarppRelatedCss');
            wp_dequeue_style('yarpp-thumbnails-yarpp-thumbnail');
        }

        //Table of Contents Plus
        add_action('wp_enqueue_scripts', function() {
            wp_deregister_script('toc-front');
            wp_deregister_style('toc-screen');
        }, 100);

    } elseif (!is_page()) {

        //Contact Form 7の読み込み最適化
        add_action('wp_enqueue_scripts', function () {
            wp_deregister_script('contact_form_7');
            wp_deregister_style('contact_form_7');
        });

        add_action('wp_enqueue_scripts', function () {
            //Child Pages Shortcodeの最適化
            wp_deregister_style('child-pages-shortcode-css');
            //Theme My Loginの最適化
            wp_deregister_style('theme-my-login');
        });

        if (!is_single()) {
            //Syntax Highlighterの読み込み最適化
            add_action('wp_enqueue_scripts',function () {
                wp_dequeue_style('crayon-theme-classic');
                wp_dequeue_style('crayon');
                wp_dequeue_style('crayon-font-monaco');
            } , 100);
        }

    } elseif (is_single() || is_page()) {

        //wp-pagenaviの読み込み最適化
        add_action('wp_enqueue_scripts', function () {
            wp_deregister_style('wp-pagenavi');
        }, 100);

    }
}

//事前に読み込んでおく
if ($option['6'] > '1' && !is_admin()) {

    //dns prefetch をヘッダーに書き出す
    add_action('wp_head', function () {
        $output = '<meta http-equiv="x-dns-prefetch-control" content="on">';
        $output .= '<link rel="dns-prefetch" href="//maps.google.com/maps/api/js?sensor=false">';
        $output .= '<link rel="dns-prefetch" href="//www.google.com/jsapi">';
        $output .= '<link rel="dns-prefetch" href="//connect.facebook.net">';
        $output .= '<link rel="dns-prefetch" href="//s-static.ak.facebook.com">';
        $output .= '<link rel="dns-prefetch" href="//static.ak.fbcdn.net">';
        $output .= '<link rel="dns-prefetch" href="//static.ak.facebook.com">';
        $output .= '<link rel="dns-prefetch" href="//www.facebook.com">';
        $output .= '<link rel="dns-prefetch" href="//cdn.api.twitter.com">';
        $output .= '<link rel="dns-prefetch" href="//p.twitter.com">';
        $output .= '<link rel="dns-prefetch" href="//platform.twitter.com">';
        $output .= '<link rel="dns-prefetch" href="//twitter.com">';
        $output .= '<link rel="dns-prefetch" href="//apis.google.com">';
        $output .= '<link rel="dns-prefetch" href="//oauth.googleusercontent.com">';
        $output .= '<link rel="dns-prefetch" href="//ssl.gstatic.com">';
        $output .= '<link rel="dns-prefetch" href="//api.b.st-hatena.com">';
        $output .= '<link rel="dns-prefetch" href="//b.hatena.ne.jp">';
        $output .= '<link rel="dns-prefetch" href="//b.st-hatena.com">';
        $output .= '<link rel="dns-prefetch" href="//cdn-ak.b.st-hatena.com">';
        $output .= '<link rel="dns-prefetch" href="//cdn.api.b.hatena.ne.jp">';
        $output .= '<link rel="dns-prefetch" href="//d7x5nblzs94me.cloudfront.net">';
        $output .= '<link rel="dns-prefetch" href="//widgets.getpocket.com">';
        $output .= '<link rel="dns-prefetch" href="//assets.pinterest.com">';
        $output .= '<link rel="dns-prefetch" href="//stats.wordpress.com">';
        $output .= '<link rel="dns-prefetch" href="//i0.wp.com">';
        $output .= '<link rel="dns-prefetch" href="//i1.wp.com">';
        $output .= '<link rel="dns-prefetch" href="//i2.wp.com">';
        $output .= '<link rel="dns-prefetch" href="//s0.wp.com">';
        $output .= '<link rel="dns-prefetch" href="//0.gravatar.com">';
        $output .= '<link rel="dns-prefetch" href="//1.gravatar.com">';
        $output .= '<link rel="dns-prefetch" href="//2.gravatar.com">';
        $output .= '<link rel="dns-prefetch" href="//googleads.g.doubleclick.net">';
        $output .= '<link rel="dns-prefetch" href="//pagead2.googlesyndication.com">';
        $output .= '<link rel="dns-prefetch" href="//www.google-analytics.com">';
        $output .= '<link rel="dns-prefetch" href="//cdn.jsdelivr.net">';
        $output .= '<link rel="dns-prefetch" href="//file.cdn.tokyo">';
        echo $output;
    }, 1);

    if ($option['6'] === '2') {
        //事前レンダリング
        add_action('wp_head',function () {
            if(!is_single() || !is_page()) {
                global $paged;
                if ( get_next_posts_link() ){
                    echo '<link rel="prerender" href="',get_pagenum_link( $paged + 1 ),'">';
                } else {
                    echo '<link rel="prerender" href="',home_url(),'">';
                }
            } else {
                echo '<link rel="prerender" href="',home_url(),'">';
            }
        } , 2);

        //プリフェッチ
        add_action('wp_head', function () {
            global $goption;
            $option = $goption;
            $purl   = plugins_url();
            if ($option['4'] > '2') {
                if ($option['11'] === '1') {
                    echo '<link rel="prefetch" href="', $purl, '/h-seeed-wp/js/echo.min.js" as="javascript">';
                }
                if ($option['11'] === '2') {
                     echo '<link rel="prefetch" href="', $purl, '/h-seeed-wp/js/layzr.min.js" as="javascript">';
                }
            }
            if ($option['6'] === '3') {
                echo '<link rel="prefetch" href="', $purl, '/h-seeed-wp/js/instantclick.min.js" as="javascript">';
            }
        });
    }
    if ($option['6'] === '3') {
        //ホバーで先に読み込み
        add_action('wp_footer', function () {
            echo '<script src="',plugins_url(),'/h-seeed-wp/js/instantclick.min.js" data-no-instant></script>';
            echo '<script data-no-instant="data-no-instant">InstantClick.init(50);</script>';
        } , 99);
    }
}


if ($option['7']) {
    
    //絵文字機能の無効化
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    
    //ワードプレスの自動スペル無効化
    remove_filter('the_title', 'capital_P_dangit');
    remove_filter('the_content', 'capital_P_dangit');
    remove_filter('comment_text', 'capital_P_dangit');
}

if (version_compare($GLOBALS['wp_version'], '4.4', '>=')) { //WP4.4以上

//4.4からのEmbed削除
if ($option['8']) {
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    remove_action( 'template_redirect', 'rest_output_link_header');
}

//4.4からのWP REST API停止
if ($option['9']) {
    remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
    remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
    remove_action('template_redirect', 'rest_output_link_header', 11, 0);
    add_filter('rest_enabled', '__return_false');
    add_filter('rest_jsonp_enabled', '__return_false');
}

}

//ヘッダから余分なものを削除
if ($option['10']) {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
    remove_action('wp_head', 'parent_post_rel_link', 10, 0);
    remove_action('wp_head', 'start_post_rel_link', 10, 0);
}

