<?php
/**
 * Plugin Name: Woocommerce Shipping Fixed Rates for each city
 * Plugin URI: https://github.com/nikolays93/woocommerce-ru-city-rate-shipping
 * Description: Custom rates for russian's cities
 * Version: 1.0
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	return;

define('CUSTOM_SHIPPING_DIR', plugin_dir_path( __FILE__ ));

add_action( 'woocommerce_shipping_init', 'custom_shipping_init' );
function custom_shipping_init(){

	require_once( CUSTOM_SHIPPING_DIR . '/includes/class-wc-shipping-custom.php' );
}

/**
 * Set area filters
 */
add_filter( 'woocommerce_states', 'get_custom_shipping_states' );
function get_custom_shipping_states( $states ){

	return include( CUSTOM_SHIPPING_DIR . '/includes/list-states.php' );
}

add_filter('woocommerce_custom_cities', 'get_custom_shipping_cities', 10, 2);
function get_custom_shipping_cities( $state = false ){
	if( $state === false )
		return false;

	$cities_arr = array( '-' => __( '- Населенный пункт -', 'wsfrfec' ) );
	$cities = include( CUSTOM_SHIPPING_DIR . '/includes/list-cities-ru.php' );
	if( !isset( $cities[$state] ) )
		return false;

	$cities_arr = array_merge($cities_arr, $cities[$state]);

	$cities_arr['Другой'] = __( 'Другой населенный пункт', 'wsfrfec' );

	return $cities_arr;
}

/**
 * Set Template
 */
add_filter( 'wc_get_template', 'filter_wc_get_template', 12, 5 );
function filter_wc_get_template( $located, $template_name, $args, $template_path, $default_path ) {
	if($template_name == 'cart/shipping-calculator.php'){
		$file = CUSTOM_SHIPPING_DIR . 'template/shipping-calculator.php';
		if( is_readable($file) )
			$located = $file;
		elseif( WP_DEBUG ){
			echo "File <strong>template/shipping-calculator.php</strong> not found.";
		}
	}

	return $located;
}

/**
 * Register Method
 */
add_filter( 'woocommerce_shipping_methods', 'add_custom_shipping_method' );
function add_custom_shipping_method( $methods ) {
	$methods['custom_shipping_method'] = 'WC_Shipping_Custom';

	return $methods;
}

/**
 * Custom Checkout
 */
function custom_shipping_wc_checkout_fields( $fields ) {
    /** @var WooCommerce */
    $woocommerce = WC();

    if ( ! isset( $woocommerce->customer ) || ! $woocommerce->customer instanceof WC_Customer ) {
        return $fields;
    }

    $wc_customer    = $woocommerce->customer;
    $shipping_state = $wc_customer->get_shipping_state();
    $cities         = apply_filters( 'woocommerce_custom_cities', $shipping_state );

	// $current_cc   = WC()->customer->get_shipping_country();
	// $current_city = WC()->customer->get_shipping_city();

	// $states       = WC()->countries->get_states( $current_cc );

	// $fields['billing']['billing_city']['type'] = 'select';

	// echo "<pre>";
	// var_dump($fields);
	// echo "</pre>";
	// $city = WC()->customer->get_shipping_city();
	
	// if( $city == '-' || !$city )
	// 	return $fields;
	
	// foreach (array('billing', 'shipping') as $type) {
	// 	if( isset($fields[$type][$type . '_state']) )
	// 		$fields[$type][$type . '_state']['class'][] = 'hidden hidden-xs-up';

	// 	if( isset($fields[$type][$type . '_country']) )
	// 		$fields[$type][$type . '_country']['class'][] = 'hidden hidden-xs-up';

	// 	if( isset($fields[$type][$type . '_city']) ){
	// 		$fields[$type][$type . '_city']['value'] = $city;
	// 		$fields[$type][$type . '_city']['custom_attributes'] = array('readonly' => 'true');
	// 	}
	// }
	
	return $fields;
}
add_filter( 'woocommerce_checkout_fields' , 'custom_shipping_wc_checkout_fields', 50 );

add_filter( 'woocommerce_shipping_calculator_enable_city', '__return_true', 10 );
add_filter( 'woocommerce_shipping_calculator_enable_postcode', '__return_false', 10 );


add_filter( 'woocommerce_default_address_fields' , 'customize_checkout_city_field' );
function customize_checkout_city_field( $address_fields ) {
    /** @var WooCommerce */
    $woocommerce = WC();

    if ( ! isset( $woocommerce->customer ) || ! $woocommerce->customer instanceof WC_Customer ) {
        return $address_fields;
    }

    $wc_customer    = $woocommerce->customer;
    $shipping_state = $wc_customer->get_shipping_state();
    $cities         = apply_filters( 'woocommerce_custom_cities', $shipping_state );

    // Customizing 'billing_city' field
    $address_fields['city']['type']    = 'select';
    $address_fields['city']['label']   = __( 'Город / Населенный пункт', 'wsfrfec' );
    $address_fields['city']['options'] = $cities;

    // Returning Checkout customized fields
    return $address_fields;
}
