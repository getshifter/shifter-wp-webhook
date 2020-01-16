<?php
namespace Shifter_Webhook;

class Page_Settings {
  public function __construct() {
    $this->options = [
      'shifter_webhook_settings' => [
        array(
          "key" => 'shifter_webhook_url',
          "form_type" => "url",
          'sanitize_callback' => 'esc_url',
          'label' => __( 'Webhook URL', 'shifter-wp-webhook' ),
          "default" => '',
        ),
        array(
          "key" => "shifter_webhook_content_type",
          "form_type" => "select",
          "sanitize_callback" => 'esc_attr',
          'label' => __( 'Content-Type', 'shifter-wp-webhook'),
          "default" => 'application/json',
          'options' => array(
            'application/x-www-form-urlencoded',
            'application/json'
          )
        ),
        array(
            "key" => "shifter_webhook_send_on_boot",
            "form_type" => "boolean",
            "sanitize_callback" => array( $this, 'esc_boolean' ),
            'label' => __( 'Send on boot', 'shifter-wp-webhook'),
            "default" => "0"
        ),
        array(
            "key" => "shifter_webhook_send_with_login_url",
            "form_type" => "boolean",
            "sanitize_callback" => array( $this, 'esc_boolean' ),
            'label' => __( 'Send with passwordless login URL', 'shifter-wp-webhook'),
            "default" => "0"
        )
      ]
    ];
    add_action( 'admin_menu', array( $this, 'register_pages' ) );
    add_action( 'admin_init', array( $this, 'register_setting_fields' ) );
  }
  public function esc_boolean( $value ) {
    if ( ! isset( $value ) || '1' !== $value ) {
      $value = '0';
    }
    return $value;
  }
  public function register_setting_fields() {
    foreach ( $this->options as $group => $options ) {
      foreach ( $options as $option ) {
        register_setting( $group, $option['key'], $option['sanitize_callback'] );
      }
    }
  }
  public function register_pages() {
    $Content = new Page_Content( $this->options );
    add_submenu_page(
      'shifter',
      'Outbound Webhook Settings',
      'Webhook',
      'manage_options',
      'shifter-webhook',
      array( $Content, 'shifter_webhook_settings_page' )
    );
  }
}
