<?
if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();

delete_option( 'shifter_webhook_url' );
delete_option( 'shifter_webhook_content_type' );
