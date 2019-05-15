<?php

class Admin_Bar {
  public function __construct($bar) {
    $this->wp_admin_bar = $bar;
  }
  public function add_menu() {
    $shifter_support_generate = array(
      "id"    => "send-webhook",
      "title" => __( 'Send webhook(Beta)', 'shifter-wp-webhook' ),
      "parent" => "shifter",
      "href" => "#"
    );
    $this->wp_admin_bar->add_menu($shifter_support_generate);
    return $this;
  }
  public function get_wp_admin_bar() {
    return $this->wp_admin_bar;
  }
}
