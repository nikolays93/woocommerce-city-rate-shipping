<?php
if ( ! defined( 'ABSPATH' ) ) 
	exit; // Exit if accessed directly

if ( 'no' === get_option( 'woocommerce_enable_shipping_calc' ) || ! WC()->cart->needs_shipping() )
	return;

$input_class = apply_filters( 'shipping_calculator_input_class', 'form-control' );

$current_cc   = WC()->customer->get_shipping_country();
$current_r    = WC()->customer->get_shipping_state();
$current_city = WC()->customer->get_shipping_city();

$states       = WC()->countries->get_states( $current_cc );
$cities       = apply_filters( 'woocommerce_custom_cities', $current_r );
?>

<?php do_action( 'woocommerce_before_shipping_calculator' ); ?>

<form class="woocommerce-shipping-calculator" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

	<section class="shipping-calculator-form-custom">
		<p class="form-row form-row-wide" id="calc_shipping_country_field">
			<select name="calc_shipping_country" id="calc_shipping_country" class="<?php echo $input_class ?>" rel="calc_shipping_state">
				<option value=""><?php _e( 'Select a country&hellip;', 'woocommerce' ); ?></option>
				<?php
					foreach ( WC()->countries->get_shipping_countries() as $key => $value )
						echo '<option value="' . esc_attr( $key ) . '"' . selected( WC()->customer->get_shipping_country(), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
				?>
			</select>
		</p>

		<p class="form-row form-row-wide" id="calc_shipping_state_field">
			<?php
				// Hidden Input
				if ( is_array( $states ) && empty( $states ) ) {

					?><input type="hidden" name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php esc_attr_e( 'State / County', 'woocommerce' ); ?>" /><?php

				// Dropdown Input
				} elseif ( is_array( $states ) ) {

					?><span>
						<select name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php esc_attr_e( 'State / County', 'woocommerce' ); ?>" class="<?php echo $input_class ?>">
							<option value=""><?php esc_html_e( 'Select a state&hellip;', 'woocommerce' ); ?></option>
							<?php
								foreach ( $states as $ckey => $cvalue )
									echo '<option value="' . esc_attr( $ckey ) . '" ' . selected( $current_r, $ckey, false ) . '>' . esc_html( $cvalue ) . '</option>';
							?>
						</select>
					</span><?php

				// Standard Input
				} else {

					?><input type="text" class="input-text" value="<?php echo esc_attr( $current_r ); ?>" placeholder="<?php esc_attr_e( 'State / County', 'woocommerce' ); ?>" name="calc_shipping_state" id="calc_shipping_state" /><?php

				}
			?>
		</p>

		<?php
		if ( apply_filters( 'woocommerce_shipping_calculator_enable_city', true ) )

			if( $cities ){
				?>
				<p class="form-row form-row-wide" id="calc_shipping_city_field">
					<select id="calc_shipping_city" name="calc_shipping_city" class="input-text <?php echo $input_class; ?>" placeholder="<?php esc_attr_e( 'City', 'woocommerce' ); ?>">

						<?php
						echo "<option value=''>" . __("- Населенный пункт -") . "</option>";
						foreach ($cities as $city) {
							$active = ($current_city == $city) ? ' selected' : '';

							echo "<option value='{$city}'{$active}>{$city}</option>";
						}
						echo "<option value=''>" . __("Другой населенный пункт") . "</option>";
						?>
					</select>

				</p>
				<?php
			}
			?>

		<p><button type="submit" name="calc_shipping" value="1" class="button btn btn-info"><?php _e( 'Update totals', 'woocommerce' ); ?></button></p>

		<?php wp_nonce_field( 'woocommerce-cart' ); ?>
	</section>
	
</form>

<?php do_action( 'woocommerce_after_shipping_calculator' ); ?>
