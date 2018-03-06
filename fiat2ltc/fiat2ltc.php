<?php

/*
Plugin Name: Fiat2LTC WooCommerce Prices
Version: 0.1
*/
/*

*/
define( 'WP_DEBUG', true );
defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
  define ('VERSION', '0.1');

  function version_id() {
    if ( WP_DEBUG )
      return time();
    return VERSION;
  }
  
  $flDefaults = array(
    'display_showmenu' => '1',
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
        // Set class property
        //$this->options = get_option( 'fl_option' );
        $this->options = wp_parse_args(get_option('fl_option'), $flDefaults);
        ?>
        <div class="wrap">
            <h1>Fiat2LTC Live Price Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
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
              'fl_options', // Option group
              'fl_option', // Option name
              array( $this, 'sanitize' ) // Sanitize
          );
          add_settings_section(
              'fl_settings_display', // ID
              'Display options', // Title
              array( $this, 'print_section_info_display' ), // Callback
              'fl-setting-admin' // Page
          );
          add_settings_field(
              'display_showmenu', 
              'Show Currency switchers below prices', 
              array( $this, 'display_showmenu_callback' ), 
              'fl-setting-admin', 
              'fl_settings_display'
          ); 
          add_settings_section(
              'fl_settings_denom', // ID
              'Denomination options', // Title
              array( $this, 'print_section_info' ), // Callback
              'fl-setting-admin' // Page
          );
          add_settings_field(
              'denom_ltc', 
              'Show ŁTC (Ł) in łites (ł) (Ł/1,000)', 
              array( $this, 'denom_ltc_callback' ), 
              'fl-setting-admin', 
              'fl_settings_denom'
          ); 
          add_settings_field(
              'denom_btc', // ID
              'Show ₿TC (₿) in ƀits (ƀ) (₿/1,000,000)', // Title 
              array( $this, 'denom_btc_callback' ), // Callback
              'fl-setting-admin', // Page
              'fl_settings_denom' // Section           
          );
          add_settings_field(
              'denom_eth', 
              'Show ΞTH (Ξ) in milliΞTH (mΞ) (Ξ/1,000)', 
              array( $this, 'denom_eth_callback' ), 
              'fl-setting-admin', 
              'fl_settings_denom'
          );      
      }
      public function sanitize( $input ) {
        $new_input = array();
        (isset( $input['display_showmenu'] ) && ( "1"==$input['display_showmenu'] )) ? $new_input['display_showmenu'] = 1 : $new_input['display_showmenu'] = 0;
        (isset( $input['denom_ltc'] ) && ( "1"==$input['denom_ltc'] )) ? $new_input['denom_ltc'] = 1 : $new_input['denom_ltc'] = 0;
        (isset( $input['denom_btc'] ) && ( "1"==$input['denom_btc'] )) ? $new_input['denom_btc'] = 1 : $new_input['denom_btc'] = 0;
        (isset( $input['denom_eth'] ) && ( "1"==$input['denom_eth'] )) ? $new_input['denom_eth'] = 1 : $new_input['denom_eth'] = 0;
        return $new_input;
      }
      public function print_section_info() {
          //print 'Enter your settings below:';
          print '';
      }
      public function print_section_info_display() {
          print 'To add the currency switchers to your template, insert this code: <pre>&lt;?php flCurrencyMenu(home_url($wp->request),"span","","View prices in:","display:block;text-align:center;"); ?&gt;</pre>';
      }
      public function display_showmenu_callback() {
          printf(
              '<input type="checkbox" id="display_showmenu" name="fl_option[display_showmenu]" value="1" '.checked( $this->options['display_showmenu'], 1, 0 ).' />',
              isset( $this->options['display_showmenu'] ) ? esc_attr( $this->options['display_showmenu']) : '' );
      }
      public function denom_ltc_callback() {
          printf(
              '<input type="checkbox" id="denom_ltc" name="fl_option[denom_ltc]" value="1" '.checked( $this->options['denom_ltc'], 1, 0 ).' />',
              isset( $this->options['denom_ltc'] ) ? esc_attr( $this->options['denom_ltc']) : '' );
      }
      public function denom_btc_callback() {
          printf(
              '<input type="checkbox" id="denom_btc" name="fl_option[denom_btc]" value="1" '.checked( $this->options['denom_btc'], 1, 0 ).' />',
              isset( $this->options['denom_btc'] ) ? esc_attr( $this->options['denom_btc']) : '' );
      }
      public function denom_eth_callback() {
          printf(
              '<input type="checkbox" id="denom_eth" name="fl_option[denom_eth]" value="1" '.checked( $this->options['denom_eth'], 1, 0 ).' />',
              isset( $this->options['denom_eth'] ) ? esc_attr( $this->options['denom_eth']) : '' );
      }
  }

  if( is_admin() ) $fl_settings_page = new flSettingsPage();
  
  function register_load_fragments_script() {
    // Register the script

    wp_register_script( 'load-fragments-script', plugins_url( 'fiat2ltc.js', __FILE__ ), array(), version_id(), true  );

    // Substitustions in script
    $translation_array = array(
        'cart_hash_key' => WC()->ajax_url() . '-wc_cart_hash'
    );
    wp_localize_script( 'load-fragments-script', 'substitutions', $translation_array );

    wp_enqueue_script( 'load-fragments-script' );
  }
  add_action('wp_loaded', 'register_load_fragments_script'); 
  
  function flCurrencyMenu($url = "/", $tag = "li", $cls = "", $lbl = "View prices in:", $stl = "", $def = false) {
    $flOptions = wp_parse_args(get_option('fl_option'), $flDefaults);
    if ($def && (!$flOptions['display_showmenu'])) return '';
    ( (strpos($url, '?') !== false) || (strpos($url, '&') !== false) ) ? $prmSep = "&" : $prmSep = "?" ;
    $string = '<'.$tag.' class="'.$cls.'"  style="'.$stl.'">'.$lbl.'<br><a class="currency-switch" href="'.$url.$prmSep.'f2l_cur=LTC" title="View prices in LTC">LTC</a> :: <a class="currency-switch" href="'.$url.$prmSep.'f2l_cur=BTC" title="View prices in BTC">BTC</a> :: <a class="currency-switch" href="'.$url.$prmSep.'f2l_cur=ETH" title="View prices in ETH">ETH</a></'.$tag.'>';
    if ($def) {
      return $string;
    } else {
      echo $string;
    }
  }
  
  if ( ! function_exists( 'woocommerce_template_loop_product_link_close' ) ) {
    /**
     * Insert the opening anchor tag for products in the loop.
     */
    function woocommerce_template_loop_product_link_close() {
      global $wp;
      echo '</a>'.flCurrencyMenu(home_url($wp->request),"span","","View prices in:","display:block;text-align:center;margin-bottom:8px;",true);
    }
  }
  
  function fl_wc_format_sale_price( $return, $regular_price, $sale_price ) {
    //return "1__".$regular_price.$sale_price;
    $price = '<del style="margin-left: .327em;">' . ( is_numeric( $regular_price ) ? wc_price( $regular_price, array('sale' => true, 'striked_price' => true,) ) : $regular_price ) . '</del><ins style="margin-left: .327em;">' . ( is_numeric( $sale_price ) ? wc_price( $sale_price, array('sale' => true,) ) : $sale_price ) . '</ins>';
    return $price;
  }
  add_filter( 'woocommerce_format_sale_price', 'fl_wc_format_sale_price', 10, 3 );
  function fl_wc_price( $return, $price, $args = array() ) {
    global $flDefaults;
    if (isset($_GET['f2l_cur']) && ( ($_GET['f2l_cur'] == "LTC") || ($_GET['f2l_cur'] == "ETH") || ($_GET['f2l_cur'] == "BTC") ) ) {
      $theCurrency = $_GET['f2l_cur'];
      WC()->session->set( 'F2L_Currency', $theCurrency );
    } elseif (WC()->session->__isset('F2L_Currency')) {
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
    //$lRoot = "awayfromkeyboard.co.uk/LTC";
    //return "[".$price."|".floatval( $negative ? $price * -1 : $price )."|".$args."|".$return."]";
    extract( apply_filters( 'wc_price_args', wp_parse_args( $args, array(
      'ex_tax_label'       => false,
      'currency'           => '',
      'decimal_separator'  => wc_get_price_decimal_separator(),
      'thousand_separator' => wc_get_price_thousand_separator(),
      'decimals'           => wc_get_price_decimals(),
      'price_format'       => get_woocommerce_price_format(),
      'sale'      => false,
      'striked_price'      => false,
    ) ) ) );

    $unformatted_price = $price;
    $negative          = $price < 0;
    $price             = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * -1 : $price ) );
    $price             = apply_filters( 'formatted_woocommerce_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );
    $del = ( $striked_price ? "&DEL" : "" );
    //$currMenu = ( $sale ? "" : flCurrencyMenu( home_url( $wp->request ), "span", "", "View prices in:",'display: block;text-align: center;margin-bottom: 8px;' ) );
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
        //$denomMode = "&LITESONLY&LROUND=0";
    }

    if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $decimals > 0 ) {
      $price = wc_trim_zeros( $price );
    }

    $formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, '<span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency_symbol( $currency ) . '</span>', $price );
    $return          = '<iframe width="100%" src="https://'.$lRoot.'/IFRAME/'.$subC.get_woocommerce_currency().'/'.$price.$denomMode.'&NOTAG'.$del.'&WC&FONT=OPENSANS" frameborder="0" style="height:1.5em;"></iframe>';

    if ( $ex_tax_label && wc_tax_enabled() ) {
      $return .= ' <small class="woocommerce-Price-taxLabel tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
    }

    /**
     * Filters the string of price markup.
     *
     * @param string $return 			Price HTML markup.
     * @param string $price	            Formatted price.
     * @param array  $args     			Pass on the args.
     * @param float  $unformatted_price	Price as float to allow plugins custom formatting. Since 3.2.0.
     */
    return apply_filters( 'fl_wc_price', $return, $price, $args, $unformatted_price );
    //return "help";
  }
  add_filter( 'wc_price', 'fl_wc_price', 10, 3 );

}
