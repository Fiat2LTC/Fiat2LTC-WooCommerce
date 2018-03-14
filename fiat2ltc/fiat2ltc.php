<?php
/**
 * @package Fiat2LTC_WooCommercePrices
 * @version 0.1
 */
/*
Plugin Name: Fiat2LTC WooCommerce Prices
Version: 0.1
Description: This is a plugin for WooCommerce/Wordpress to display live Litecoin (and Bitcoin/Ethereum) prices in your shop. It will load iframes displaying live prices from fiat2ltc.com
*/
/*

*/
define( 'WP_DEBUG', false );
defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
  define ('FL_VERSION', '0.1');

  function fl_version_id() {
    if ( WP_DEBUG )
      return time();
    return FL_VERSION;
  }
  
  $flDefaults = array(
    'display_showmenu' => '0',
    'hide_pricebtc' => '0',
    'hide_priceeth' => '0',
    'denom_ltc' => '1',
    'denom_btc' => '0',
    'denom_eth' => '0'
  );
  
  class flSettingsPage
  {
      private $options;

      public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
      }
       public function add_plugin_page() {
        add_options_page(
            'Settings Admin', 
            'Fiat2LTC Price Settings', 
            'manage_options', 
            'fl-setting-admin', 
            array( $this, 'create_admin_page' )
        );
      }
      public function create_admin_page() {
        global $flDefaults;
        $this->options = wp_parse_args(get_option('fl_option'), $flDefaults);
        ?>
        <div class="wrap">
            <h1>Fiat2LTC Live Price Settings</h1>
            <form method="post" action="options.php">
            <?php
                settings_fields( 'fl_options' );
                do_settings_sections( 'fl-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
      }
      public function page_init() {        
          register_setting(
              'fl_options', 
              'fl_option', 
              array( $this, 'sanitize' ));
          add_settings_section(
              'fl_settings_display', 
              'Display options', 
              array( $this, 'print_section_info_display' ),'fl-setting-admin');
          add_settings_field(
              'display_showmenu', 
              'Show Currency switchers below prices', 
              array( $this, 'display_showmenu_callback' ),'fl-setting-admin','fl_settings_display'); 
          add_settings_field(
              'hide_pricebtc', 
              'Show ₿TC Price', 
              array( $this, 'hide_pricebtc_callback' ),'fl-setting-admin','fl_settings_display'); 
          add_settings_field(
              'hide_priceeth', 
              'Show ΞTH Price', 
              array( $this, 'hide_priceeth_callback' ),'fl-setting-admin','fl_settings_display'); 
          add_settings_section(
              'fl_settings_denom',
              'Denomination options', 
              array( $this, 'print_section_info' ),'fl-setting-admin');
          add_settings_field(
              'denom_ltc', 
              'Show ŁTC (Ł) in łites (ł) (Ł/1,000)', 
              array( $this, 'denom_ltc_callback' ),'fl-setting-admin','fl_settings_denom'); 
          add_settings_field(
              'denom_btc', // ID
              'Show ₿TC (₿) in ƀits (ƀ) (₿/1,000,000)',
              array( $this, 'denom_btc_callback' ),'fl-setting-admin','fl_settings_denom');
          add_settings_field(
              'denom_eth', 
              'Show ΞTH (Ξ) in milliΞTH (mΞ) (Ξ/1,000)', 
              array( $this, 'denom_eth_callback' ),'fl-setting-admin','fl_settings_denom');}
      public function sanitize( $input ) {
        $new_input = array();
        (isset( $input['display_showmenu'] ) && ( "1"==$input['display_showmenu'] )) ? $new_input['display_showmenu'] = 1 : $new_input['display_showmenu'] = 0;
        (isset( $input['hide_pricebtc'] )) ? $new_input['hide_pricebtc'] = 0 : $new_input['hide_pricebtc'] = 1;
        (isset( $input['hide_priceeth'] )) ? $new_input['hide_priceeth'] = 0 : $new_input['hide_priceeth'] = 1;
        (isset( $input['denom_ltc'] ) && ( "1"==$input['denom_ltc'] )) ? $new_input['denom_ltc'] = 1 : $new_input['denom_ltc'] = 0;
        (isset( $input['denom_btc'] ) && ( "1"==$input['denom_btc'] )) ? $new_input['denom_btc'] = 1 : $new_input['denom_btc'] = 0;
        (isset( $input['denom_eth'] ) && ( "1"==$input['denom_eth'] )) ? $new_input['denom_eth'] = 1 : $new_input['denom_eth'] = 0;
        return $new_input;}
      public function print_section_info() {
          //print 'Enter your settings below:';
          print '';}
      public function print_section_info_display() {
          print 'The currency switcher is available as a widget that can be added to the sidebar, or insert this PHP code into your template: <pre>&lt;?php flCurrencyMenu(home_url($wp->request),"div","cssclassgoeshere","View prices in:","cssstylegoeshere;"); ?&gt;</pre>';}
      public function display_showmenu_callback() {
          printf(
              '<input type="checkbox" id="display_showmenu" name="fl_option[display_showmenu]" value="1" '.checked( $this->options['display_showmenu'], 1, 0 ).' />',
              isset( $this->options['display_showmenu'] ) ? esc_attr( $this->options['display_showmenu']) : '' );}
      public function hide_pricebtc_callback() {
          printf(
              '<input type="checkbox" id="hide_pricebtc" name="fl_option[hide_pricebtc]" value="1" '.checked( $this->options['hide_pricebtc'], 0, 0 ).' />',
              isset( $this->options['hide_pricebtc'] ) ? esc_attr( $this->options['hide_pricebtc']) : '' );}
      public function hide_priceeth_callback() {
          printf(
              '<input type="checkbox" id="hide_priceeth" name="fl_option[hide_priceeth]" value="1" '.checked( $this->options['hide_priceeth'], 0, 0 ).' />',
              isset( $this->options['hide_priceeth'] ) ? esc_attr( $this->options['hide_priceeth']) : '' );}
      public function denom_ltc_callback() {
          printf(
              '<input type="checkbox" id="denom_ltc" name="fl_option[denom_ltc]" value="1" '.checked( $this->options['denom_ltc'], 1, 0 ).' />',
              isset( $this->options['denom_ltc'] ) ? esc_attr( $this->options['denom_ltc']) : '' );}
      public function denom_btc_callback() {
          printf(
              '<input type="checkbox" id="denom_btc" name="fl_option[denom_btc]" value="1" '.checked( $this->options['denom_btc'], 1, 0 ).' />',
              isset( $this->options['denom_btc'] ) ? esc_attr( $this->options['denom_btc']) : '' );}
      public function denom_eth_callback() {
          printf(
              '<input type="checkbox" id="denom_eth" name="fl_option[denom_eth]" value="1" '.checked( $this->options['denom_eth'], 1, 0 ).' />',
              isset( $this->options['denom_eth'] ) ? esc_attr( $this->options['denom_eth']) : '' );}}

  if( is_admin() ) $fl_settings_page = new flSettingsPage();
  
  class fl_currency_widget extends WP_Widget {
  public function __construct() {
    parent::__construct(
      'fl_currency_widget',
      __( 'Fiat2LTC Currency Switch', 'text_domain' ),
      array(
        'customize_selective_refresh' => true,
      )
    );
  }
  public function form( $instance ) {
    $defaults = array(
      'title'    => '',
      'cssclass'    => '',
      'text'    => 'View prices in:',
      'cssstyle'    => '',
    );
    extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget Title', 'text_domain' ); ?></label>
      <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'cssclass' ) ); ?>"><?php _e( 'CSS Class', 'text_domain' ); ?></label>
      <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'cssclass' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cssclass' ) ); ?>" type="text" value="<?php echo esc_attr( $cssclass ); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'cssstyle' ) ); ?>"><?php _e( 'Element CSS Style', 'text_domain' ); ?></label>
      <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'cssstyle' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cssstyle' ) ); ?>" type="text" value="<?php echo esc_attr( $cssstyle ); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php _e( 'Display Text', 'text_domain' ); ?></label>
      <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" type="text" value="<?php echo esc_attr( $text ); ?>" />
    </p>
  <?php }
  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title']    = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
    $instance['cssclass']    = isset( $new_instance['cssclass'] ) ? wp_strip_all_tags( $new_instance['cssclass'] ) : '';
    $instance['text']    = isset( $new_instance['text'] ) ? wp_strip_all_tags( $new_instance['text'] ) : 'View prices in:';
    $instance['cssstyle']    = isset( $new_instance['cssstyle'] ) ? wp_strip_all_tags( $new_instance['cssstyle'] ) : '';
    return $instance;
  }
  public function widget( $args, $instance ) {
    extract( $args );
    global $wp;
    $title    = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
    $cssclass    = isset( $instance['cssclass'] ) ? $instance['cssclass'] : '';
    $text    = isset( $instance['text'] ) ? $instance['text'] : 'View prices in:';
    $cssstyle    = isset( $instance['cssstyle'] ) ? $instance['cssstyle'] : '';
    echo $before_widget;
    echo '<div class="widget-text wp_widget_plugin_box">';
      if ( $title ) echo $before_title . $title . $after_title;
      flCurrencyMenu(home_url($wp->request),"div",$cssclass,$text,$cssstyle);
    echo '</div>';
    echo $after_widget;
  }
  }
  function register_fl_currency_widget() {
  register_widget( 'fl_currency_widget' );
  }
  add_action( 'widgets_init', 'register_fl_currency_widget' );
  /*
  function register_load_fragments_script() {
    wp_register_script( 'load-fragments-script', plugins_url( 'fiat2ltc.js', __FILE__ ), array(), fl_version_id(), true  );
    $translation_array = array(
        'cart_hash_key' => WC()->ajax_url() . '-wc_cart_hash'
    );
    wp_localize_script( 'load-fragments-script', 'substitutions', $translation_array );

    wp_enqueue_script( 'load-fragments-script' );
  }
  add_action('wp_loaded', 'register_load_fragments_script'); */
  
  function flCurrencyMenu($url = "/", $tag = "li", $cls = "", $lbl = "View prices in:", $stl = "", $def = false) {
    $flOptions = wp_parse_args(get_option('fl_option'), $flDefaults);
    if ($def && (!$flOptions['display_showmenu'])) return '';
    ( (strpos($url, '?') !== false) || (strpos($url, '&') !== false) ) ? $prmSep = "&" : $prmSep = "?" ;
    $string = '<'.$tag.' class="'.$cls.'"  style="'.$stl.'">'.$lbl.'<br><a class="currency-switch" href="'.$url.$prmSep.'f2l_cur=LTC" title="View prices in LTC">LTC</a>';
    if (!$flOptions['hide_pricebtc']) {
      $string .= ' :: <a class="currency-switch" href="'.$url.$prmSep.'f2l_cur=BTC" title="View prices in BTC">BTC</a>';}
    if (!$flOptions['hide_priceeth']) {
      $string .= ' :: <a class="currency-switch" href="'.$url.$prmSep.'f2l_cur=ETH" title="View prices in ETH">ETH</a>';}
    $string .= '</'.$tag.'>';
    if ($def) {
      return $string;
    } else {
      echo $string;
    }
  }
  
  if ( ! function_exists( 'woocommerce_template_loop_product_link_close' ) ) {
    function woocommerce_template_loop_product_link_close() {
      global $wp;
      echo '</a>'.flCurrencyMenu(home_url($wp->request),"span","","View prices in:","display:block;text-align:center;margin-bottom:8px;",true);
    }
  }
  
  function fl_wc_format_sale_price( $regular_price, $sale_price ) {
    $price = '<del style="margin-left: .327em;">' . ( is_numeric( $regular_price ) ? fl_wc_price( $regular_price, $regular_price, array('sale' => true, 'striked_price' => true, 'enabled' => true) ) : $regular_price ) . '</del><ins style="margin-left: .327em;">' . ( is_numeric( $sale_price ) ? fl_wc_price( $sale_price, $sale_price, array('sale' => true, 'enabled' => true) ) : $sale_price ) . '</ins>';
    return $price;
  }
  
  function fl_wc_price( $wcprice, $price, $args = array() ) {
    extract( apply_filters( 'wc_price_args', wp_parse_args( $args, array(
      'ex_tax_label'       => false,
      'currency'           => '',
      'decimal_separator'  => wc_get_price_decimal_separator(),
      'thousand_separator' => wc_get_price_thousand_separator(),
      'decimals'           => wc_get_price_decimals(),
      'price_format'       => get_woocommerce_price_format(),
      'disabled'      => false,
      'enabled'      => false,
      'sale'      => false,
      'striked_price'      => false,
    ) ) ) );
    
    if (!$enabled) return $wcprice;
    global $flDefaults;
    if ((WC()->session !== NULL) && (isset($_GET['f2l_cur']) && ( ($_GET['f2l_cur'] == "LTC") || ($_GET['f2l_cur'] == "ETH") || ($_GET['f2l_cur'] == "BTC") ) )) {
      $theCurrency = $_GET['f2l_cur'];
      WC()->session->set( 'F2L_Currency', $theCurrency );
    } elseif ((WC()->session !== NULL) && WC()->session->__isset('F2L_Currency')) {
      $theCurrency = WC()->session->get( 'F2L_Currency', 'LTC' );
      if ( ($theCurrency != "LTC") && ($theCurrency != "ETH") & ($theCurrency != "BTC") ) $theCurrency = "LTC";
    } else {
      $theCurrency = "LTC";
    }
    if ($theCurrency == "LTC") {
      $subC = "";
    } else {
      $subC = $theCurrency."/";
    }
    $lRoot = "fiat2ltc.com";

    $unformatted_price = $price;
    $negative          = $price < 0;
    $price             = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * -1 : $price ) );
    $price             = apply_filters( 'formatted_woocommerce_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );
    $del = ( $striked_price ? "&DEL" : "" );
    $flOptions = wp_parse_args(get_option('fl_option'), $flDefaults);
    switch ($theCurrency) {
      case 'BTC':
        ($flOptions['denom_btc']) ? $denomMode = "&LITESONLY&LROUND=0" : $denomMode = "&LTCONLY&LROUND=6" ;
        break;
      case 'ETH':
        ($flOptions['denom_eth']) ? $denomMode = "&LITESONLY&LROUND=0" : $denomMode = "&LTCONLY&LROUND=6" ;
        break;
      default:
        ($flOptions['denom_ltc']) ? $denomMode = "&LITESONLY&LROUND=0" : $denomMode = "&LTCONLY&LROUND=6" ;
    }
    if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $decimals > 0 ) {
      $price = wc_trim_zeros( $price );
    }
    $return          = '<iframe width="100%" src="https://'.$lRoot.'/IFRAME/'.$subC.get_woocommerce_currency().'/'.$price.$denomMode.'&NOTAG'.$del.'&WC&FONT=OPENSANS" frameborder="0" style="height:1.5em;"></iframe>';

    if ( $ex_tax_label && wc_tax_enabled() ) {
      $return .= ' <small class="woocommerce-Price-taxLabel tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
    }
    
    return $return;
  }
  //add_filter( 'wc_price', 'fl_wc_price', 10, 3 );
  
  function fl_get_price_html( $price, $sentThis ) {
    if (is_admin()) return $price;
		if ( '' === $sentThis->get_price() ) {
			$price = apply_filters( 'woocommerce_empty_price_html', '', $sentThis );
		} elseif ( $sentThis->is_on_sale() ) {
			$price = fl_wc_format_sale_price( wc_get_price_to_display( $sentThis, array( 'price' => $sentThis->get_regular_price() ) ), wc_get_price_to_display( $sentThis ) );
		} else {
			$price = fl_wc_price( $price,wc_get_price_to_display( $sentThis ),array('enabled' => true) );
		}
		return $price;
	}
  add_filter( 'woocommerce_get_price_html', 'fl_get_price_html', 10, 2 );
  

}
