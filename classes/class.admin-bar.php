<?php

class Admin_Bar {
  public function __construct($bar) {
    $this->wp_admin_bar = $bar;
  }
  public function add_menu() {
    $local_class = getenv("SHIFTER_LOCAL") ? "disable_shifter_operation" : "";
    $shifter_support_generate = array(
      "id"    => "send-webhook",
      "title" => __( 'Send webhok', 'shifter-wp-webhook' ),
      "parent" => "shifter",
      "href" => "#",
      "meta" => array("class" => $local_class)
    );
    $this->wp_admin_bar->add_menu($shifter_support_generate);
    return $this;
  }
  public function get_wp_admin_bar() {
    return $this->wp_admin_bar;
  }
}