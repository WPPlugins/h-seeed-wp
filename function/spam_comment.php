<?php
global $goption;
$option = $goption;

//スパムちゃんぷるーによるスパムコメント対策(サービス終了)
/*add_action('check_comment_flood', function ($ip, $email, $date) {
    $spam_IP  = '127.0.0.2';
    $host     = "dnsbl.spam-champuru.livedoor.com";
    $pattern  = '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/';
    $check_IP = trim(preg_match($pattern, $ip) ? $ip : $_SERVER['REMOTE_ADDR']);
    $spam     = false;
    if (preg_match($pattern, $check_IP)) {
        $host = implode('.',array_reverse(split('\.',$check_IP))) . '.' . $host;
        if (function_exists('dns_get_record')) {
            $check_recs = dns_get_record($host, DNS_A);
            if (isset($check_recs[0]['ip'])) $spam = ($check_recs[0]['ip'] === $spam_IP);
            unset($check_recs);
        } elseif (function_exists('gethostbyname')) {
            $checked = (gethostbyname($host) === $spam_IP);
        } elseif (class_exists('Net_DNS_Resolver')) {
            $resolver = new Net_DNS_Resolver();
            $response = $resolver->query($host, 'A');
            if ($response) {
                foreach ($response->answer as $rr) {
                    if ($rr->type === 'A') {
                        $spam = ($rr->address === $spam_IP);
                        break;
                    }
                }
            }
            unset($response);
            unset($resolver);
        } elseif (function_exists('checkdnsrr')) {
            $spam = (checkdnsrr($host, "A") === true);
        }
    }
    if ($spam) {
        $spamcounter = get_option('spamcounter');
        $spamcounter = intval($spamcounter) + 1;
        update_option('spamcounter', $spamcounter);
        wp_die('エラー: スパムちゃんぷるーDNSBL(http://spam-champuru.livedoor.com/dnsbl/)に登録されているホストからはコメントすることができません。');
    }
}, 10, 3);*/

add_action('pre_comment_on_post',function() {
    global $goption;
    $option = $goption;
    $comment = htmlspecialchars($_POST['comment']);
    $is_spam = false;
    //日本語が含まれているか
    if (isset($comment) && !preg_match("/[ぁ-んァ-ヶ]+/u", $comment)) {
        $is_spam = true;
    }
    //USER_AGENTが取れないコメント拒否
    $useragent = esc_attr($_SERVER["HTTP_USER_AGENT"]);
    if (empty($useragent)) {
        $is_spam = true;
    }
    //日本語以外のブラウザからのコメント拒否
    if (!is_JP()) {
        $is_spam = true;
    }
    //IE8以下だったら
    if(preg_match('/(?i)msie [1-8]\./',$_SERVER['HTTP_USER_AGENT'])){
        $is_spam = true;
    }
    //コメント文字数
    if (mb_strlen($comment) < 10) {
        $is_spam = true;
    }
    //コメントURL数
    if (substr_count($comment, 'http') >= 3) {
        $is_spam = true;
    }
    if ($option['31'] === '2') {
        //スパムワード
        $spam_count = 0;
        $spamword_list = array('期間限定', '無料ダウンロード', '無料インストール', '無料登録', '未承認広告', '死ね', 'SEO自動', '会員募集', '証拠金', '多重債務', 'サラ金', '破産', '消費者金融', '情報商材', '精力', '矯正', 'わきが', 'パチンコ', 'パチスロ', '換金', '合法', '風水', '霊感', '占い', 'アダルト', 'カジノ', 'ポーカー', 'セフレ', 'セックス', 'sex', 'SEX', 'フェラ', '射精', '愛液', '性器', '中出し', 'エロ', '無修正', '乱交', 'エッチ', '風俗', 'fc2', '自動相互', '稼ぐ', '儲け', '商材', '即金', '殺す', '出合い系', '健康器具'); // スパム単
        foreach ($spamword_list as $spamword) {
            if (mb_strpos($comment, $spamword) !== false) {
                ++$spam_count;
            }
        }
        if($spam_count > 1) {
            $is_spam = true;
        }
        //JSと投稿時間
        if (htmlspecialchars($_POST['spam_check']) !== 'h235970' && !is_user_logged_in() ) {
            $is_spam = true;
        }
    }
    //スパム判定
    if($is_spam) {
        $spamcounter = get_option('spamcounter');
        $spamcounter = intval($spamcounter) + 1;
        update_option('spamcounter', $spamcounter);
        wp_die('<h3>エラー</h3><p>あなたのコメントはH Speed WPのスパム感知システムによって、スパムと見なされました。コメントが以下の条件に当てはまっていないかお確かめください。</p><div><ul><li>コメントに日本語が含まれていない<li>コメントが短い<li>コメントにスパムと思われるようなワードが含まれている<li>ブラウザの設定言語が日本語以外になっている<li>ブラウザが極端に古い<li>Javascriptが無効になっている</ul></div>');
    }

} , 1);


//連続コメント秒数
add_filter('comment_flood_filter', function ($block, $time_lastcomment, $time_newcomment) {
    if (($time_newcomment - $time_lastcomment) < 60) {
        $spamcounter = get_option('spamcounter');
        $spamcounter = intval($spamcounter) + 1;
        update_option('spamcounter', $spamcounter);
        return true;
    } else {
        return false;
    }
}, 3, 9);

if ( $option['31'] === '2' ) {
        //スパム発見用コメント入力欄
        add_action('comment_form_after_fields', function() {
            echo '<input type="hidden" id="spam_check" name="spam_check" value="">';
            echo '<script async defer>var spam=function(){document.getElementById("spam_check").value = "h235970";};setTimeout(spam, 10000);</script>';
        } );

        add_action('comment_form', function() {
            echo '<div id="spam_hsw" style="font-size: 0.9em;text-align: right;">スパムコメント対策中 - <a href="http://xn--48sa.jp/h-speed-wp" target="_blank" rel="nofollow">H Speed WP</a></div>';
        } );
}
