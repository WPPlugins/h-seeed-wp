<?php
if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();
    delete_option( 'h_speed_wp_option' );
    delete_option( 'spamcounter' );