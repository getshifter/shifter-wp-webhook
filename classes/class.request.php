<?php
namespace Shifter_Webhook;

class RequestContent {
  public function __construct() {
    $this->passwordless = new PasswordlessUtils();
  }
  public function get_base_body() {
    $container_url = esc_url( get_option( 'home' ) );
    $body = array(
      "CONTAINER_URL" => $container_url,
    );
    return $body;
  }
  public function push_login_magic_link( array $body, string $url = null ) {
    if ( ! $url ) return $body;
    return array_merge( $body, array(
      "LOGIN_MAGIC_LINK" => $url,
    ) );
  }
  /**
   * Get request body parameters by current user
   */
  public function get_body_by_current_user() {
    $base = $this->get_base_body();
    $should_add_passwordless = get_option( 'shifter_webhook_send_with_login_url' );
    if ( ! $should_add_passwordles ) return $base;
    $passwordless_url = $this->passwordless->get_url_by_current_user();
    return $this->push_login_magic_link( $base, $passwordless_url );
  }
  public function get_email( string $email = null ) {
    if ( $email ) return $email;
    return getenv( 'SHIFTER_USER_EMAIL' );
  }
  /**
   * Get request body parameters by specific email user
   * By default, the email get from env "SHIFTER_USER_EMAIL".
   */
  public function get_body_by_email( string $email = null ) {
    $base = $this->get_base_body();
    $should_add_passwordless = get_option( 'shifter_webhook_send_with_login_url' );
    if ( ! $should_add_passwordles ) return $base;
    $email = $this->get_email( $email );
    if ( ! $email ) return $base;
    $passwordless_url = $this->passwordless->get_url_by_email( $email );
    $this->push_login_magic_link( $base );
  }
  public function get_body_data() {
    $container_url = esc_url( get_option( 'home' ) );

    $body = array(
      "CONTAINER_URL" => $container_url,
    );

    $utils = new PasswordlessUtils();
    $passwordless_url = $utils->get_url_by_current_user();
    if ( ! $passwordless_url ) return $body;
    return array_merge( $body, array(
      "LOGIN_MAGIC_LINK" => $passwordless_url,
    ) );
  }

}