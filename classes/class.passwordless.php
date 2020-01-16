<?php
namespace Shifter_Webhook;

class PasswordlessUtils {
  public function __construct() {
    $onelogin_file_path = WPMU_PLUGIN_DIR . '/shifter-artifact-helper/include/class-shifter-onelogin.php';
    if ( ! file_exists( $onelogin_file_path ) ) {
      Log::error( '[Shifter Webhook] Can not read the file:' . $onelogin_file_path );
      $this->one_login = null;
    } else {
      require_once( $onelogin_file_path );
      $this->one_login = \ShifterOneLogin::get_instance();
    }
  }
  private function get_magic_link( string $username , string $email = null ) {
    if ( ! $this->one_login ) return null;
    return $this->one_login->magic_link( $username, $email );
  }
  public function get_url_by_current_user() {
    $user = wp_get_current_user();
    return $this->get_url_by_user( $user );
  }
  public function get_url_by_username( string $username ) {
    $user = get_user_by( 'login' , $username);
    return $this->get_url_by_user( $user );
  }
  public function get_url_by_email( string $email ) {
    $user = get_user_by( 'email' , $email );
    return $this->get_url_by_user( $user );
  }
  public function get_url_by_user( \WP_User $user ) {
    return $this->get_magic_link( $user->user_login, $user->user_email );
  }
}