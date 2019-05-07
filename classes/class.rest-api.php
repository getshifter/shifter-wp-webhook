<?php
namespace Shifter_Webhook;

class Rest_API {
  protected $route = 'shifter';
  protected $version = 'v1';
  public function __construct() {
    $this->_register();
  }
  private function _register() {
    register_rest_route(
      "{$this->route}/{$this->version}",
      'webhook',
      array(
        "methods" => "POST",
        // 'permission_callback' => array( $this, 'permission_check' ),
        "callback" => array( $this, 'invoke_webhook' )
      )
    );
  }
  public function createRequest( string $home_url ) {
    return array(
      'body' => array(
        "CONTAINER_URL" => $home_url,
      ),
    );
  }
  public function invoke_webhook() {
    $webhook_url = get_option( 'shifter_webhook_url' );
    $home_url = get_option( 'home' );
    $request = $this->createRequest( $home_url );
    Log::info('Invoke webhook request: ' . json_encode( $request ));
    $result = wp_remote_post( $webhook_url, $request );
    if ( is_wp_error( $result ) ) {
      Log::error('Invoke webhook result: ' . json_encode( $result ));
    } else {
      Log::info('Invoke webhook result: ' . json_encode( $result ));
    }
    $response = new \WP_REST_Response( array(
      "result" => is_wp_error( $result ) ? $result->get_error_message() : $result,
      "url" => $webhook_url
    ) );
    $response->set_status( is_wp_error( $result ) ? 500 : 201 );
    return $response;
  }
  public function permission_check() {
    return current_user_can('publish_posts');
  }
}