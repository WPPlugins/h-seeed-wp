<?php
if ( is_admin() ) {
global $goption;
$option = $goption;
//オートセーブ禁止
if ($option['21']) {
add_action('wp_print_scripts', function () {
    wp_deregister_script('autosave');
});
}
//リビション無効化
if ($option['22']) {
    remove_action('pre_post_update', 'wp_save_post_revision');
}
}