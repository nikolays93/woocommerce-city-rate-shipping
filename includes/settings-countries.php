<?php
// Включить ввод города
add_filter( 'woocommerce_shipping_calculator_enable_city', '__return_true' );
// Выключить ввод почтового индекса
add_filter( 'woocommerce_shipping_calculator_enable_postcode', '__return_false' );


add_filter('woocommerce_countries', 'custom_shipping_countries');
function custom_shipping_countries(){
	return array(
		'RU' => __( 'Russia', 'woocommerce' ),
		);
}


add_filter( 'woocommerce_states', 'custom_shipping_states' );
function custom_shipping_states( $states ){
	return include( CUSTOM_SHIPPING_DIR . '/includes/list-countries.php' );
}

function get_custom_shipping_cities( $state = "" ){
	$cities = include( CUSTOM_SHIPPING_DIR . '/includes/list-cities-ru.php' );

	if( $state )
		return isset( $cities[$state] ) ? $cities[$state] : false;

	return false;
}

add_action( 'form_input_cities', 'woocommerce_shipping_calculator_input_city');
function woocommerce_shipping_calculator_input_city() {
	$cities = get_custom_shipping_cities( WC()->customer->get_shipping_state() );
	$active_city = WC()->customer->get_shipping_city();

	if( $cities ){
		?>
		<p class="form-row form-row-wide" id="calc_shipping_city_field">
			<?php
			# value="<?php echo esc_attr( WC()->customer->get_shipping_city() ); ? >" ?>
			<select id="calc_shipping_city" name="calc_shipping_city" class="input-text" placeholder="<?php esc_attr_e( 'City', 'woocommerce' ); ?>">

				<?php
				echo "<option value=''>" . __("- Населенный пункт -") . "</option>";
				foreach ($cities as $city) {
					$active = ($active_city == $city) ? ' selected' : '';

					echo "<option value='{$city}'{$active}>{$city}</option>";
				}
				echo "<option value=''>" . __("Другой населенный пункт") . "</option>";
				?>
			</select>

		</p>
		<?php
	}
}