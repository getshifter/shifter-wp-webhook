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
      'Webhook (Beta)',
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
  public function get_body( string $webhook_content_type ) {
    $container_url = esc_url( get_option( 'home' ) );
    if ( 'application/json' === $webhook_content_type ) {
      return json_encode( array(
        "CONTAINER_URL" => $container_url,
      ) );
    }
    return esc_attr( 'CONTAINER_URL=' . $container_url );
  }
  public function show_example( string $webhook_url, string $webhook_content_type ) {
    ?>
    <h3> Example</h3>
    <p>
        $ curl <?php echo esc_url( $webhook_url ? $webhook_url : 'https://example.com' ); ?> -XPOST
        -H "Content-Type: <?php echo esc_attr( $webhook_content_type ); ?>"
        -d '<?php echo $this->get_body( $webhook_content_type ); ?>'</>
    </p>
    <?
  }
  public function get_option( string $key, string $form_type, string $default ) {
    $value = get_option( $key );
    $value = $value ? $value : $default;
    if ( 'url' === $form_type ) {
      return esc_url( $value );
    } else {
      return esc_attr( $value );
    }
  }
  public function get_form_input( $item, $current_value ) {
    $name = esc_attr( $item[ 'key' ] );
    if ( 'select' === $item[ 'form_type'] ) {
      ?>
      <select name="<?php echo $name; ?>" style="min-width: 300px">
        <?php
          foreach( $item[ 'options' ] as $option ) {
            $option_value = esc_attr( $option );
            ?>
            <option
              value="<?php echo $option_value; ?>"
              <?php
              if ( $current_value === $option_value) {
                echo 'selected';
              }
              ?>
            >
              <?php echo $option_value; ?>
            </option>
            <?php
          }
        ?>
      </select>
      <?php
    } else {
      ?>
      <input
        type="<?php echo esc_attr( $item[ 'form_type'] ); ?>"
        name="<?php echo $name ?>"
        value="<?php echo $current_value; ?>"
        style="min-width: 300px"
      />
      <?php
    }
  }
  public function get_form( string $webhook_url, string $webhook_content_type ) {
    $key = 'shifter_webhook_settings';
    $option = $this->options[ $key ];
    ?>
    <form method="post" action="options.php">
      <?php settings_fields( $key ); ?>
      <?php do_settings_sections( $key ); ?>
      <table class="form-table">
        <?php
          foreach ( $option as $item ) {
            $value = $this->get_option( $item[ 'key' ], $item[ 'form_type'], $item[ 'default' ] );
        ?>
          <tr valign="top">
            <th scope="row">
              <?php echo ucfirst( $item[ 'label' ] ); ?>
            </th>
            <td>
              <?php $this->get_form_input( $item, $value ); ?>
            </td>
          </tr>
        <?php } ?>
      </table>
      <?php submit_button(); ?>
    </form>
    <?php
  }
  public function shifter_webhook_settings_page() {
    $webhook_url = get_option( 'shifter_webhook_url' );
    $webhook_content_type = get_option( 'shifter_webhook_content_type' );
    $webhook_content_type = $webhook_content_type ? $webhook_content_type : 'application/json';
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
          <?php $this->show_example( $webhook_url, $webhook_content_type ); ?>
          <?php $this->get_form( $webhook_url, $webhook_content_type ); ?>
      </div>
    </div>
    <?
  }
}
