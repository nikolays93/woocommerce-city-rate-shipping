<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cost_desc = __( 'Enter a cost (excl. tax) or sum, e.g. <code>10.00 * [qty]</code>.', 'woocommerce' ) . '<br/><br/>' . __( 'Use <code>[qty]</code> for the number of items, <br/><code>[cost]</code> for the total cost of items, and <code>[fee percent="10" min_fee="20" max_fee=""]</code> for percentage based fees.', 'woocommerce' );

$states = include( CUSTOM_SHIPPING_DIR . '/includes/list-states.php' );
$cities = include( CUSTOM_SHIPPING_DIR . '/includes/list-cities-ru.php' );

$list_areas = array();
foreach ($states['RU'] as $state => $state_label) {
	$list_areas[$state] = $state_label;
	if( isset($cities[$state]) ){
		foreach ($cities[$state] as $city_label) {
			$list_areas[$city_label] = '-- '.$city_label;
		}
	}
}

/**
 * Settings for flat rate shipping.
 */
$settings = array(
	'title' => array(
		'title' 		=> __( 'Method title', 'woocommerce' ),
		'type' 			=> 'text',
		'description' 	=> __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
		'default'		=> 'Доставка по городу',
		'desc_tip'		=> true,
	),
	'zone' => array(
		'title' 		=> __( 'State', 'woocommerce' ) . " / " .__( 'City', 'woocommerce' ),
		'type' 			=> 'select',
		'class'         => 'wc-enhanced-select',
		'default' 		=> 'izh',
		'options'		=> $list_areas,
	),
	'cost' => array(
		'title' 		=> __( 'Cost', 'woocommerce' ),
		'type' 			=> 'text',
		'placeholder'	=> '',
		'description'	=> $cost_desc,
		'default'		=> '0',
		'desc_tip'		=> true,
	),
);

/*
$shipping_classes = WC()->shipping->get_shipping_classes();
if ( ! empty( $shipping_classes ) ) {
	$settings['class_costs'] = array(
		'title'			 => __( 'Shipping class costs', 'woocommerce' ),
		'type'			 => 'title',
		'default'        => '',
		'description'    => sprintf( __( 'These costs can optionally be added based on the <a href="%s">product shipping class</a>.', 'woocommerce' ), admin_url( 'admin.php?page=wc-settings&tab=shipping&section=classes' ) ),
	);
	foreach ( $shipping_classes as $shipping_class ) {
		if ( ! isset( $shipping_class->term_id ) ) {
			continue;
		}
		$settings[ 'class_cost_' . $shipping_class->term_id ] = array(
			// * translators: %s: shipping class name * //
			'title'       => sprintf( __( '"%s" shipping class cost', 'woocommerce' ), esc_html( $shipping_class->name ) ),
			'type'        => 'text',
			'placeholder' => __( 'N/A', 'woocommerce' ),
			'description' => $cost_desc,
			'default'     => $this->get_option( 'class_cost_' . $shipping_class->slug ), // Before 2.5.0, we used slug here which caused issues with long setting names
			'desc_tip'    => true,
		);
	}
	$settings['no_class_cost'] = array(
		'title'       => __( 'No shipping class cost', 'woocommerce' ),
		'type'        => 'text',
		'placeholder' => __( 'N/A', 'woocommerce' ),
		'description' => $cost_desc,
		'default'     => '',
		'desc_tip'    => true,
	);
	$settings['type'] = array(
		'title' 		=> __( 'Calculation type', 'woocommerce' ),
		'type' 			=> 'select',
		'class'         => 'wc-enhanced-select',
		'default' 		=> 'class',
		'options' 		=> array(
			'class' 	=> __( 'Per class: Charge shipping for each shipping class individually', 'woocommerce' ),
			'order' 	=> __( 'Per order: Charge shipping for the most expensive shipping class', 'woocommerce' ),
		),
	);
}
*/
return $settings;
