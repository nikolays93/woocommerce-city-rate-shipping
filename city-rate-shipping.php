<?php
/**
 * Plugin Name: Woocommerce Shipping Fixed Rates for each city
 * Plugin URI: https://github.com/nikolays93/woocommerce-ru-city-rate-shipping
 * Description: Custom rates for russian's cities
 * Version: 0.1
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

require_once( CUSTOM_SHIPPING_DIR . '/includes/class-wc-shipping-custom.php' );

add_action( 'woocommerce_shipping_init', 'custom_shipping_init' );

/**
 * Set area filters
 */
add_filter( 'woocommerce_states', 'get_custom_shipping_states' );
function get_custom_shipping_states( $states ){

	return include( CUSTOM_SHIPPING_DIR . '/includes/list-states.php' );
}

add_filter('woocommerce_custom_cities', 'get_custom_shipping_cities', 10, 2);
function get_custom_shipping_cities( $state = false ){
	$cities = include( CUSTOM_SHIPPING_DIR . '/includes/list-cities-ru.php' );

	if( $state !== false )
		return isset( $cities[$state] ) ? $cities[$state] : false;

	return false;
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

/**
 * Custom Checkout
 */
function custom_shipping_wc_checkout_fields( $fields ) {
	if( WC()->customer->get_shipping_city() ){
		$fields['billing']['billing_city']['value']   = WC()->customer->get_shipping_city();
		$fields['shipping']['shipping_city']['value'] = WC()->customer->get_shipping_city();
	}
	
	return $fields;
}
add_filter( 'woocommerce_checkout_fields' , 'custom_shipping_wc_checkout_fields', 50 );