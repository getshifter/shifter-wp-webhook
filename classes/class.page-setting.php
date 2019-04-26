<?php
namespace Shifter_Webhook;

class Page_Settings {
  public function __construct() {
    $this->options = [
      'shifter_webhook_settings' => [
        array(
          "key" => 'shifter_webhook_url',
          'sanitize_callback' => 'esc_url',
          'label' => __( 'Webhook URL', 'shifter-wp-webhook' ),
        )
      ]
    ];
    add_action( 'admin_menu', array( $this, 'register_pages' ) );
    add_action( 'admin_init', array( $this, 'register_setting_fields' ) );
  }
  public function register_setting_fields() {
    foreach ( $this->options as $group => $options ) {
      foreach ( $options as $option ) {
        var_dump( $group, $option['key'] );
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
    ?>
    <div class="wrap">
      <h1>
        <?php _e('Webhook Settings', 'shifter-wp-webhook'); ?>
      </h1>
      <div class="card">
        <h2>
          <?php _e('Outbound Webhook', 'shifter-wp-webhook'); ?>
        </h2>
        <form method="post" action="options.php">
          <?php settings_fields( $key ); ?>
          <?php do_settings_sections( $key ); ?>
          <table class="form-table">
            <?php
              foreach ( $option as $item ) {
                $value = get_option( $item[ 'key' ] );
            ?>
              <tr valign="top">
                <th scope="row">
                  <?php echo ucfirst( $item[ 'label' ] ); ?>
                </th>
                <td>
                  <input
                    type="url"
                    name="<?php echo esc_attr( $item[ 'key' ] ); ?>"
                    value="<?php echo esc_url( $value ); ?>"
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
