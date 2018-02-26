<?php

/*
Plugin Name: Fiat2LTC WooCommerce Prices
Version: 0.1
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 
/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
  
  
  function fl_wc_format_sale_price( $return, $regular_price, $sale_price ) {
    //return "1__".$regular_price.$sale_price;
    $price = '<del>' . ( is_numeric( $regular_price ) ? wc_price( $regular_price, array('striked_price' => true,) ) : $regular_price ) . '</del> <ins>' . ( is_numeric( $sale_price ) ? wc_price( $sale_price ) : $sale_price ) . '</ins>';
    return $price;
  }
  add_filter( 'woocommerce_format_sale_price', 'fl_wc_format_sale_price', 10, 3 );
  function fl_wc_price( $return, $price, $args = array() ) {
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
    $return          = '<iframe width="100%" height="24" src="https://'.$lRoot.'/IFRAME/'.get_woocommerce_currency().'/'.$price.'&LITESONLY&NOTAG'.$del.'&WC&LROUND=0&FONT=OPENSANS" frameborder="0"></iframe>';

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
