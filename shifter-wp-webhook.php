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
// menu


function shifter_add_admin_bar_content() {
  global $wp_admin_bar;
  $Shifter_Admin_Bar = new Admin_Bar($wp_admin_bar);
  $wp_admin_bar = $Shifter_Admin_Bar->add_menu()->get_wp_admin_bar();
}
add_action("wp_before_admin_bar_render", 'shifter_add_admin_bar_content');