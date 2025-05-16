<?php
namespace Shifter_Webhook;

class Page_Content {
  /** @var array */
  protected $options;
  public function __construct( $options ) {
    $this->options = $options;
  }
  public function get_body_data() {
    $content_urls = new RequestContent();
    $body = $content_urls->get_body_by_current_user();
    return $body;
  }
  public function get_body( string $webhook_content_type ) {
    $body = $this->get_body_data();

    if ( 'application/json' === $webhook_content_type ) {
      return "-d '" . json_encode( $body ) . "'";
    }
    $text_content = '';
    foreach ( $body as $key => $value ) {
      $text_content .=  "-d '" . esc_attr( $key . '=' . $value ) . "' ";
    }
    return $text_content;
  }
  public function show_example( string $webhook_url, string $webhook_content_type ) {
    ?>
    <h3> Example</h3>
    <p>
        $ curl <?php echo esc_url( $webhook_url ? $webhook_url : 'https://example.com' ); ?> -XPOST
        -H "Content-Type: <?php echo esc_attr( $webhook_content_type ); ?>"
        <?php echo $this->get_body( $webhook_content_type ); ?></>
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
    } else if ( 'boolean' === $item['form_type'] ) {
      ?>
      <input
        type="checkbox"
        name="<?php echo $name ?>"
        value="1" 
        <?php checked( "1", $current_value ); ?>
      />
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
