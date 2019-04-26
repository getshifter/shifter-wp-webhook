<?php
/**
 * Plugin Name:     Shifter Wp Webhook
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     shifter-wp-webhook
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Shifter_Wp_Webhook
 */

// Your code starts here.

// libs
require_once 'libs/fallback.php';
require_once 'classes/class.admin-bar.php';
require_once 'classes/class.page-setting.php';

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
