<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option( 'shifter_webhook_url' );
delete_option( 'shifter_webhook_content_type' );
delete_option( 'shifter_webhook_send_on_boot' );
delete_option( 'shifter_webhook_send_with_login_url' );
