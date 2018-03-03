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
  
  if ( ! function_exists( 'storefront_header_cart' ) ) {
    function storefront_header_cart() {
      global $wp;
      
      if ( storefront_is_woocommerce_activated() ) {
        if ( is_cart() ) {
          $class = 'current-menu-item';
        } else {
          $class = '';
        }
      ?>
      <ul id="site-header-cart" class="site-header-cart menu">
        <li class="">
          View prices in:<br>
          <a class="currency-switch" href="<?php echo home_url( $wp->request ); ?>?f2l_cur=LTC" title="View prices in LTC">LTC</a> :: 
          <a class="currency-switch" href="<?php echo home_url( $wp->request ); ?>?f2l_cur=BTC" title="View prices in BTC">BTC</a> :: 
          <a class="currency-switch" href="<?php echo home_url( $wp->request ); ?>?f2l_cur=ETH" title="View prices in ETH">ETH</a>
        </li>
        <li class="<?php echo esc_attr( $class ); ?>">
          <?php storefront_cart_link(); ?>
        </li>
        <li>
          <?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
        </li>
      </ul>
      <?php
      }
    }
  }
  
  function fl_wc_format_sale_price( $return, $regular_price, $sale_price ) {
    //return "1__".$regular_price.$sale_price;
    $price = '<del style="margin-left: .327em;">' . ( is_numeric( $regular_price ) ? wc_price( $regular_price, array('striked_price' => true,) ) : $regular_price ) . '</del><ins style="margin-left: .327em;">' . ( is_numeric( $sale_price ) ? wc_price( $sale_price ) : $sale_price ) . '</ins>';
    return $price;
  }
  add_filter( 'woocommerce_format_sale_price', 'fl_wc_format_sale_price', 10, 3 );
  function fl_wc_price( $return, $price, $args = array() ) {
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
    $lRoot = "awayfromkeyboard.co.uk/LTC";
    //return "[".$price."|".floatval( $negative ? $price * -1 : $price )."|".$args."|".$return."]";
    extract( apply_filters( 'wc_price_args', wp_parse_args( $args, array(
      'ex_tax_label'       => false,
      'currency'           => '',
      'decimal_separator'  => wc_get_price_decimal_separator(),
      'thousand_separator' => wc_get_price_thousand_separator(),
      'decimals'           => wc_get_price_decimals(),
      'price_format'       => get_woocommerce_price_format(),
      'striked_price'      => false,
    ) ) ) );

    $unformatted_price = $price;
    $negative          = $price < 0;
    $price             = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * -1 : $price ) );
    $price             = apply_filters( 'formatted_woocommerce_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );
    $del = ( $striked_price ? "&DEL" : "" );

    if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $decimals > 0 ) {
      $price = wc_trim_zeros( $price );
    }

    $formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, '<span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency_symbol( $currency ) . '</span>', $price );
    $return          = '<iframe width="100%" src="https://'.$lRoot.'/IFRAME/'.$subC.get_woocommerce_currency().'/'.$price.'&LITESONLY&NOTAG'.$del.'&WC&LROUND=0&FONT=OPENSANS" frameborder="0" style="height:1.5em;"></iframe>';

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
