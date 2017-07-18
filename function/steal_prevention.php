<?php
global $goption;
$option = $goption;

//パクリ防止
if (!is_admin()) {//管理画面以外
    add_action('wp_footer', function() {
        
        global $goption;
        $option = $goption;
        
        if ( !is_user_logged_in()) { //ログイン中以外

            //画像の盗用防止
            if ($option['51'] !== '3') {
                echo '<script async defer>jQuery(function(){jQuery("img").on("contextmenu",function(){return!1}),jQuery("img").on("onmousedown",function(){return!1})});</script><style>img { touch-callout: none !important; -webkit-touch-callout: none; user-select: none !important; -webkit-user-select: none !important; -moz-user-select: none !important; -khtml-user-select: none !important; -webkit-user-drag: none !important; -khtml-user-drag: none !important; touch-action: manipulation; -ms-touch-action : manipulation; }</style>';
            }

            //ファンクションキーを無効に
            echo '<script>function keydown() {if (event.keyCode >= 112 && event.keyCode <= 123) {event.keyCode = 0;return false; }}window.document.onkeydown = keydown;</script>';

            if ($option['51'] > '1') {
                //選択の無効化
                echo '<style>body { touch-callout: none !important; -webkit-touch-callout: none !important; user-select: none !important; -webkit-user-select: none !important; -moz-user-select: none !important; -khtml-user-select: none !important; -webkit-user-drag: none !important; -khtml-user-drag: none !important; ::selection { background: transparent !important; } ::-moz-selection { background: transparent !important; } }</style>';

                if ($option['51'] === '2') {

                    //コピーの禁止
                    echo '<script>document.oncopy = function () {;return false;};</script>';

                } elseif ($option['51'] === '3') {

                    //右クリックの禁止・スクリプト無効時に表示しない
                    echo '<script>document.oncontextmenu = function () {;return false;};</script><noscript><style>html {display: none !important;}body {display: none !important;}</style></noscript>';

                }
            }
        }
    } , 999999);
}

//rssに署名追加
function hsw_048($content) {
    global $post;
    $content = $content . '<p><a href="' . get_permalink($post->ID) . '">' . get_the_title($post->ID) . '</a> - <a href="' . get_bloginfo('url') . '">&copy;&thinsp;' . get_bloginfo('name') . '</a></p>';
    return $content;
}
add_filter('the_excerpt_rss', 'hsw_048');
add_filter('the_content_feed', 'hsw_048');

// フィードの生成を停止
if ($option['51'] === '3') {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('do_feed_rdf', 'do_feed_rdf', 10, 1);
    remove_action('do_feed_rss', 'do_feed_rss', 10, 1);
    remove_action('do_feed_rss2', 'do_feed_rss2', 10, 1);
    remove_action('do_feed_atom', 'do_feed_atom', 10, 1);
    automatic_feed_links(false);
}