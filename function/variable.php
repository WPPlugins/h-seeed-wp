<?php
//設定用の変数
    $goption = get_option('h_speed_wp_option');
    global $goption;
    $option = $goption;

//日本語ブラウザか判定
function is_JP() {
    $l = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    if (($l) && ($l == 'ja')) {
        return true;
    } else {
        return false;
    }
}

//ワードプレスの言語取得
function is_WJP() {
$wlanguage = get_bloginfo('language');
    if(stripos( $wlanguage , 'ja' ) !== false){
        return true;
    } else {
        return false;
    }
}

//H Speed WP 読み込み順番を早める
add_action( 'activated_plugin', function() {
    $this_plugin        = 'h-seeed-wp/H-Seeed-WP.php';
    $active_plugins     = get_option( 'active_plugins' );
    $new_active_plugins = array();
    foreach ( $active_plugins as $plugins ) {
        if( $plugins != $this_plugin ){
            $new_active_plugins[] = $plugins;
        }else{
            $this_activeplugin = $this_plugin;
        }
    }
    if( $this_activeplugin ){
        array_unshift( $new_active_plugins, $this_activeplugin );
    }
    if( ! empty( $new_active_plugins ) ){
        update_option( 'active_plugins' ,  $new_active_plugins );
    }
} );