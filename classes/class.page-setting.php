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
              "form_type" => "text",
              "sanitize_callback" => 'esc_attr',
              'label' => __( 'Content-Type', 'shifter-wp-webhook'),
              "default" => 'application/x-www-form-urlencoded',
          )
      ]
    ];
    add_action( 'admin_menu', array( $this, 'register_pages' ) );
    add_action( 'admin_init', array( $this, 'register_setting_fields' ) );
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

class Page_Content {
  public function __construct( $options ) {
    $this->options = $options;
  }
  public function shifter_webhook_settings_page() {
    $key = 'shifter_webhook_settings';
    $option = $this->options[ $key ];
    $webhook_url = get_option( 'shifter_webhook_url' );
    $webhook_content_type = get_option( 'shifter_webhook_content_type' );
    $webhook_content_type = $webhook_content_type ? $webhook_content_type : 'application/x-www-form-urlencoded';
    $body = array(
            "CONTAINER_URL" => esc_url( get_option( 'home' ) ),
    );
    ?>
    <div class="wrap">
      <h1>
        <?php _e('Webhook Settings', 'shifter-wp-webhook'); ?>
      </h1>
      <div class="card" style="width: 100%; max-width: 100%;">
        <h2>
          <?php _e('Outbound Webhook', 'shifter-wp-webhook'); ?>
        </h2>
          <p>
              The plugin can send a POST request with the container URL.<br />
              You can run some build script to use the request body parameter.
          </p>
          <h3> Example</h3>
          <p>
              $ curl <?php echo esc_url( $webhook_url ? $webhook_url : 'https://example.com' ); ?> -XPOST
              -H "Content-Type: <?php echo esc_attr( $webhook_content_type ); ?>"
              -d '<?php echo json_encode( $body ); ?>'</>
          </p>
        <form method="post" action="options.php">
          <?php settings_fields( $key ); ?>
          <?php do_settings_sections( $key ); ?>
          <table class="form-table">
            <?php
              foreach ( $option as $item ) {
                $value = get_option( $item[ 'key' ] );
                $value = $value ? $value : $item[ 'default' ];
                if ( $item[ 'form_type'] === 'url' ) {
                    $value = esc_url( $value );
                } else {
                    $value = esc_attr( $value );
                }
            ?>
              <tr valign="top">
                <th scope="row">
                  <?php echo ucfirst( $item[ 'label' ] ); ?>
                </th>
                <td>
                  <input
                    type="<?php echo esc_attr( $item[ 'form_type'] ); ?>"
                    name="<?php echo esc_attr( $item[ 'key' ] ); ?>"
                    value="<?php echo $value; ?>"
                    style="min-width: 300px"
                  />
                </td>
              </tr>
            <?php } ?>
          </table>
          <?php submit_button(); ?>
        </form>
      </div>
    </div>
    <?
  }
}
