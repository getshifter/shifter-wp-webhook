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
        'permission_callback' => array( $this, 'permission_check' ),
        "callback" => array( $this, 'invoke_webhook' )
      )
    );
  }
  private function _create_request_body( string $home_url, string $content_type ) {
    $content = array(
      "CONTAINER_URL" => $home_url,
    );
    if ( $content_type === 'application/json') {
      return json_encode( $content );
    }
    return $content;
  }
  private function _create_request( string $home_url, string $content_type ) {
    return array(
        'headers' => array(
            'Content-Type' => $content_type
        ),
      'body' => $this->_create_request_body( $home_url, $content_type ),
    );
  }
  public function invoke_webhook() {
    $webhook_url = get_option( 'shifter_webhook_url' );
    $home_url = get_option( 'home' );
    $webhook_content_type = get_option( 'shifter_webhook_content_type' );
    $webhook_content_type = $webhook_content_type ? $webhook_content_type : 'application/x-www-form-urlencoded';
    $request = $this->_create_request( $home_url, $webhook_content_type );
    Log::info('Invoke webhook request: ' . json_encode( $request ));
    $result = wp_remote_post( $webhook_url, $request );
    if ( is_wp_error( $result ) ) {
      Log::error('Invoke webhook result: ' . json_encode( $result ));
    } else {
      Log::info('Invoke webhook result: ' . json_encode( $result ));
    }
    $response = new \WP_REST_Response( array(
      "result" => is_wp_error( $result ) ? $result : $result,
      "url" => $webhook_url
    ) );
    $response->set_status( is_wp_error( $result ) ? 500 : 201 );
    return $response;
  }
  public function permission_check() {
    return current_user_can('publish_posts');
  }
}
