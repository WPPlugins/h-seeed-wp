<?php
/*
Plugin Name:H Speed WP
Plugin URI:http://xn--48sa.jp/
Description:ワードプレスの高速化やセキュリティ、SEO対策、スパムコメント、パクリなどの対策等の様々な機能を実行するプラグインです。
Version:4.0.2
Author:yokudekiru
Author URI:http://xn--48sa.jp/
License:GPL2
*/

//変数の読み込み
include dirname(__FILE__).'/function/variable.php';

//初期値の設定
include dirname(__FILE__).'/function/option_default_value.php';

if ( is_admin() ) {
    //管理画面の読み込み
    include dirname(__FILE__).'/admin/admin.php';
    //プラグインに画面設定表示
    add_filter('plugin_action_links_'.plugin_basename(__FILE__), function ( $links ) {
        $add_hsw_link = '<a href="'.admin_url('options-general.php?page=h-speed-wp').'">Settings</a>';
        array_unshift( $links, $add_hsw_link);
        return $links;
    });

}
//各機能の読み込み

//設定用変数
global $goption;
$option = $goption;

//BETA機能
if ($option['84']) {
include dirname(__FILE__).'/function/beta.php';
}

//高速化
if ($option['a']) {
    include dirname(__FILE__).'/function/speeding_up.php';
}
//サーバーの容量圧迫対策
if ($option['b']) {
    include dirname(__FILE__).'/function/server_capacitance.php';
}
//スパムコメントの対策機能
if ($option['c']) {
    include dirname(__FILE__).'/function/spam_comment.php';
}
//セキュリティ
if ($option['d']) {
    include dirname(__FILE__).'/function/security.php';
}
//パクリ防止
if ($option['e']) {
    include dirname(__FILE__).'/function/steal_prevention.php';
}
//SEO
if ($option['f']) {
    include dirname(__FILE__).'/function/seo.php';
}


/*  Copyright 2016 yokudekiru (email : yokudekirugakusei@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110_1301  USA
*/