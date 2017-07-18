<?php
global $goption;
$option = $goption;
//初期化
if ($goption['83']) {

    delete_option('h_speed_wp_option');
    
    $i = 1;
    while( $i < 7 ) {
        $goption[chr(96+$i)] = 0;
        ++$i;
    }
    $i = 1;
    while( $i < 84 ) {
        $goption[$i] = 0;
        ++$i;
    }

    $yuukou_list1= array('1','5','7','10','42','51','62','64','65','66','68','69');
    foreach ($yuukou_list1 as $yuukou1) {
        $goption[$yuukou1] = 1;
    }
    $yuukou_list2 = array('2','3','4','6','31','45','46','47','67','81','82');
    foreach ($yuukou_list2 as $yuukou2) {
        $goption[$yuukou2] = 2;
    }
    $yuukou_list3 = array('41');
    foreach ($yuukou_list3 as $yuukou3) {
        $goption[$yuukou3] = 3;
    }
    
    add_option('h_speed_wp_option', $goption);

}

//初期設定
if ($goption == "") {
    /*個別設定*/
    $yuukou_list1= array('1','5','7','10','42','51','62','64','65','66','68','69','81');
    foreach ($yuukou_list1 as $yuukou1) {
        $goption[$yuukou1] = 1;
    }
    $yuukou_list2 = array('2','3','4','6','31','45','46','47','67','82');
    foreach ($yuukou_list2 as $yuukou2) {
        $goption[$yuukou2] = 2;
    }
    $yuukou_list3 = array('41');
    foreach ($yuukou_list3 as $yuukou3) {
        $goption[$yuukou3] = 3;
    }
}

global $goption;
$option = $goption;


//パーミッションの最適化
if ($option['45'] > '1') {
$wpp = ABSPATH.'wp-config.php';
$hta = ABSPATH.'.htaccess';
$pii = ABSPATH.'php.ini';
$pci = ABSPATH.'php.cgi';
$p5i = ABSPATH.'php5.cgi';
$inp = ABSPATH.'wp-admin/install.php';

if( file_exists($wpp) && abs(substr(sprintf('%o', fileperms($wpp)), -4)) > 404) {
    chmod($wpp, 0404);
}
if( file_exists($hta) && abs(substr(sprintf('%o', fileperms($hta)), -4)) > 606) {
    chmod($hta, 0606);
}
if( file_exists($pii) && abs(substr(sprintf('%o', fileperms($pii)), -4)) > 600) {
    chmod($hii, 0600);
}
if( file_exists($pci) && abs(substr(sprintf('%o', fileperms($pci)), -4)) > 711) {
    chmod($hci, 0711);
}
if( file_exists($p5i) && abs(substr(sprintf('%o', fileperms($p5i)), -4)) > 100) {
    chmod($p5i, 0100);
}
if( file_exists($inp) && abs(substr(sprintf('%o', fileperms($inp)), -4)) > 1) {
    chmod($inp, 0000);
}
}

//バージョンの書かれたファイルのアクセス制限
if ($option['46'] === '2') {
    $rmh = ABSPATH.'readme.html';
    $rjh = ABSPATH.'readme-ja.html';

    if( file_exists($rmh) && abs(substr(sprintf('%o', fileperms($rmh)), -4)) > 1) {
        chmod($rmh, 0000);
    }
    if( file_exists($rjh) && abs(substr(sprintf('%o', fileperms($rjh)), -4)) > 1) {
        chmod($rjh, 0000);
    }
}