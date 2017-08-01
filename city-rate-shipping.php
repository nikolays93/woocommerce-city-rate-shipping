<?php
/**
 * Plugin Name: Woocommerce Shipping Fixed Rates for each city
 * Plugin URI: https://github.com/nikolays93/woocommerce-ru-city-rate-shipping
 * Description: Custom rates for russian's cities
 * Version: 1.1
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

add_filter('woocommerce_countries', 'clear_countries_for_russia');
function clear_countries_for_russia($countries){
	//file_put_contents( __DIR__ . '/debug.log', print_r($countries, 1) );
	// for rus only
	return array('RU' => $countries['RU']);
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

	$cities_arr = array('-' => '- Населенный пункт -');
	$cities = include( CUSTOM_SHIPPING_DIR . '/includes/list-cities-ru.php' );
	if( !isset( $cities[$state] ) )
		return false;

	$cities_arr = array_merge($cities_arr, $cities[$state]);

	$cities_arr['Другой'] = 'Другой населенный пункт';

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
			echo "Файл <strong>template/shipping-calculator.php</strong> не найден";
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

add_filter( 'woocommerce_shipping_calculator_enable_city', '__return_true', 10 );
add_filter( 'woocommerce_shipping_calculator_enable_postcode', '__return_false', 10 );

/**
 * Custom Checkout
 */
add_filter( 'woocommerce_default_address_fields' , 'customize_checkout_city_field', 50, 1 );
function customize_checkout_city_field( $address_fields ) {

    $current_r    = WC()->customer->get_shipping_state();
    $cities       = apply_filters( 'woocommerce_custom_cities', $current_r );

    // Customizing 'billing_city' field
    $address_fields['city']['type'] = 'select';
    //$address_fields['city']['class'] = array('form-row-last', 'my-custom-class'); // your class here
    $address_fields['city']['label'] = 'Город / Населенный пункт';
    $address_fields['city']['options'] = $cities;


    // Returning Checkout customized fields
    return $address_fields;
}