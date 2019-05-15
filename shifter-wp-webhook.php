<?php
/**
 * Plugin Name:     Shifter Wp Webhook
 * Plugin URI:      https://github.com/getshifter/shifter-wp-webhook
 * Description:     Simple webhook execution (Public Beta)
 * Author:          DigitalCube
 * Author URI:      https://getshifter.io
 * Text Domain:     shifter-wp-webhook
 * Domain Path:     /languages
 * Version:         0.3.1
 *
 * @package         Shifter_Wp_Webhook
 */

// Your code starts here.

// libs
require_once 'libs/fallback.php';
require_once 'classes/class.logger.php';
require_once 'classes/class.admin-bar.php';
require_once 'classes/class.page-setting.php';
require_once 'classes/class.rest-api.php';

// menu
add_action("wp_before_admin_bar_render", function () {
  global $wp_admin_bar;
  $Shifter_Admin_Bar = new Admin_Bar($wp_admin_bar);
  $wp_admin_bar = $Shifter_Admin_Bar->add_menu()->get_wp_admin_bar();
});

// Setting pages
add_action( 'init', function () {
  // add menu
  if ( ! is_admin() ) return;
  new Shifter_Webhook\Page_Settings();
});

// API
add_action( 'rest_api_init', function () {
  new Shifter_Webhook\Rest_API();
} );


add_action('wp_enqueue_scripts', 'add_shifter_webhook_scripts' );
add_action('admin_enqueue_scripts', 'add_shifter_webhook_scripts' );

function shifter_webhook_configured ( $webhook_url ) {
  $filtered_url = filter_var( $webhook_url, FILTER_VALIDATE_URL );
  if ( $filtered_url === false ) return '';
  return 'true';
}

function add_shifter_webhook_scripts() {
  $shifter_webhook = plugins_url( 'libs/scripts.js', __FILE__ );
  if ( is_user_logged_in() ) {
    wp_enqueue_script( 'wp-api' );
    wp_enqueue_script( "shifter-webhook-js", $shifter_webhook, array( 'jquery', 'wp-api' ), '20190507');
    $webhook_url = get_option( 'shifter_webhook_url' );
    wp_localize_script( 'shifter-webhook-js', 'Shifter_Webhook', array( 'hasWebhook' => shifter_webhook_configured( $webhook_url ) ) );
  }
}
