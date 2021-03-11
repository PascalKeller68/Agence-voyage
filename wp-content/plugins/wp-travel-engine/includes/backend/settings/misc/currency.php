<?php
/**
 * Currency Converter
 */
$wp_travel_engine_settings = get_option( 'wp_travel_engine_settings', true );
$thousands_separator       = isset( $wp_travel_engine_settings['thousands_separator'] ) && $wp_travel_engine_settings['thousands_separator'] != '' ? esc_attr( $wp_travel_engine_settings['thousands_separator'] ) : ',';
$decimal_separator         = wte_array_get( $wp_travel_engine_settings, 'decimal_separator', '.' );
$decimal_digits            = wte_array_get( $wp_travel_engine_settings, 'decimal_digits', '2' );
$amount_display_format     = wte_array_get( $wp_travel_engine_settings, 'amount_display_format', '%CURRENCY_SYMBOL%%FORMATED_AMOUNT%' );
?>
<div class="wpte-form-block-wrap">
	<div class="wpte-form-block">
		<div class="wpte-form-content">
			<div class="wpte-field wpte-select wpte-floated">
				<label class="wpte-field-label" for="wp_travel_engine_settings[currency_code]"><?php _e( 'Payment Currency', 'wp-travel-engine' ); ?>
				</label>
				<select id="wp_travel_engine_settings[currency_code]" name="wp_travel_engine_settings[currency_code]" data-placeholder="<?php esc_attr_e( 'Choose a currency&hellip;', 'wp-travel-engine' ); ?>" class="wpte-enhanced-select">
					<option value=""><?php _e( 'Choose a currency&hellip;', 'wp-travel-engine' ); ?></option>
					<?php
					$obj        = new Wp_Travel_Engine_Functions();
					$currencies = $obj->wp_travel_engine_currencies();
					$code       = 'USD';
					if ( isset( $wp_travel_engine_settings['currency_code'] ) && $wp_travel_engine_settings['currency_code'] != '' ) {
						$code = $wp_travel_engine_settings['currency_code'];
					}
					$currency = $obj->wp_travel_engine_currencies_symbol( $code );
					foreach ( $currencies as $key => $name ) {
						echo '<option value="' . ( ! empty( $key ) ? esc_attr( $key ) : 'USD' ) . '" ' . selected( $code, $key, false ) . '>' . esc_html( $name . ' (' . $obj->wp_travel_engine_currencies_symbol( $key ) . ')' ) . '</option>';
					}
					?>
				</select>
				<span class="wpte-tooltip"> <?php esc_html_e( 'Choose the base currency for the trips pricing.', 'wp-travel-engine' ); ?></span>
			</div>

			<div class="wpte-field wpte-select wpte-floated">
				<label class="wpte-field-label"><?php _e( 'Display Currency Symbol or Code', 'wp-travel-engine' ); ?></label>
				<select id="wp_travel_engine_settings[currency_option]" name="wp_travel_engine_settings[currency_option]" data-placeholder="<?php esc_attr_e( 'Choose a option&hellip;', 'wp-travel-engine' ); ?>" class="wc-enhanced-select">
					<?php
					$options = array(
						'symbol' => 'Currency Symbol ( e.g. $ )',
						'code'   => 'Currency Code ( e.g. USD )',
					);
					$option  = isset( $wp_travel_engine_settings['currency_option'] ) ? esc_attr( $wp_travel_engine_settings['currency_option'] ) : 'symbol';
					foreach ( $options as $key => $val ) {
						echo '<option value="' . ( ! empty( $key ) ? esc_attr( $key ) : 'Please select' ) . '" ' . selected( $option, $key, false ) . '>' . esc_html( $val ) . '</option>';
					}
					?>
				</select>
				<span class="wpte-tooltip"><?php esc_html_e( 'Display Currency Symbol or Code in Trip Listing Templates.', 'wp-travel-engine' ); ?></span>
			</div>
			<div class="wpte-field wpte-select wpte-floated">
				<label for="wp_travel_engine_settings[amount_display_format]" data-wte-update="wte_new_430" class="wpte-field-label"><?php esc_html_e( 'Amount Display Format', 'wp-travel-engine' ); ?></label>
				<input type="text" id="wp_travel_engine_settings[amount_display_format]" name="wp_travel_engine_settings[amount_display_format]" value="<?php echo esc_attr( $amount_display_format ); ?>">
				<span class="wpte-tooltip">
				<?php
				echo wp_kses(
					sprintf( __( 'Amount Display format. Available tags: %s', 'wp-travel-engine' ), '<code>%CURRENCY_CODE%</code>, <code>%CURRENCY_SYMBOL%</code>, <code>%AMOUNT%</code>, <code>%FORMATED_AMOUNT%</code>' ),
					array( 'code' => array() )
				);
				?>
				</span>
			</div>
			<div class="wpte-field wpte-select wpte-floated">
				<label for="wp_travel_engine_settings[decimal_digits]" data-wte-update="wte_new_430" class="wpte-field-label"><?php esc_html_e( 'Decimal Digits', 'wp-travel-engine' ); ?></label>
				<input type="text" id="wp_travel_engine_settings[decimal_digits]" name="wp_travel_engine_settings[decimal_digits]" value="<?php echo esc_attr( $decimal_digits ); ?>">
				<span class="wpte-tooltip"><?php esc_html_e( 'Number of Decimal digits.', 'wp-travel-engine' ); ?></span>
			</div>
			<div class="wpte-field wpte-select wpte-floated">
				<label for="wp_travel_engine_settings[decimal_separator]" data-wte-update="wte_new_430" class="wpte-field-label"><?php esc_html_e( 'Decimal Separator', 'wp-travel-engine' ); ?></label>
				<input type="text" id="wp_travel_engine_settings[decimal_separator]" name="wp_travel_engine_settings[decimal_separator]" value="<?php echo esc_attr( $decimal_separator ); ?>">
				<span class="wpte-tooltip"><?php esc_html_e( 'Symbol to use for decimal separator in Trip Price.', 'wp-travel-engine' ); ?></span>
			</div>
			<div class="wpte-field wpte-select wpte-floated">
				<label for="wp_travel_engine_settings[thousands_separator]" class="wpte-field-label"><?php esc_html_e( 'Thousands Separator', 'wp-travel-engine' ); ?></label>
				<input type="text" id="wp_travel_engine_settings[thousands_separator]" name="wp_travel_engine_settings[thousands_separator]" value="<?php echo esc_attr( $thousands_separator ); ?>">
				<span class="wpte-tooltip"><?php esc_html_e( 'Symbol to use for thousands separator in Trip Price.', 'wp-travel-engine' ); ?></span>
			</div>
		</div>
	</div>
</div>
<?php
