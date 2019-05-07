<?php
// controller fallback
// https://github.com/getshifter/shifter-wp-controller/blob/master/shifter-wp-controller.php
if ( ! has_filter( 'wp_enqueue_scripts', 'add_shifter_support_js' ) ) add_action('wp_enqueue_scripts', 'add_shifter_support_js' );
if ( ! has_filter( 'admin_enqueue_scripts', 'add_shifter_support_js' ) ) add_action('admin_enqueue_scripts', 'add_shifter_support_js' );
if ( ! function_exists( 'add_shifter_support_js' ) ) {
  function add_shifter_support_js() {
    wp_register_script( 'sweetalert2', 'https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.26.11/sweetalert2.min.js', null, null, true );
    wp_localize_script( 'sweetalert2', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    if (is_user_logged_in()) {
      wp_enqueue_script("sweetalert2");
    }
  }
}