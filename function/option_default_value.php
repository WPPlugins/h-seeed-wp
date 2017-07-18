<?php
global $goption;

/*未入力時の処理*/
$hsi = 1;
while( $hsi < 7 ) {
    if (!isset($goption[chr(96+$hsi)])) {
        $goption[chr(96+$hsi)] = 0;
    }
    ++$hsi;
}
$hsi = 1;
while( $hsi < 84 ) {
    if (!isset($goption[$hsi])) {
        $goption[$hsi] = 0;
    }
    ++$hsi;
}

//日本語以外
if(!is_WJP()) {
    $mukou_list = array('c','d','f');
    foreach ($mukou_list as $mukou) {
        $goption[$mukou] = 0;
    }
}