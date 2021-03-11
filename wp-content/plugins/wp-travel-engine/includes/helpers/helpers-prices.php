<?php
/**
 * Price and currency helpers
 *
 * @package WP_Tarvel_Engine
 */

/**
 * Used For Calculation purpose. for display purpose use wp_travel_engine_get_formated_price_with_currency.
 *
 * @param int $price Amount to be formatted.
 * @param bool $format If true should be formatted according to the WP Travel Number fomatting Setting @since WP Travel v3.0.4
 * @param int $number_of_decimals Number after decimal .00.
 */

/**
 * Currency code in db
 *
 * @since 3.0.2
 *
 * @return string Currency code stored in db.
 */

/**
 * Currency code in db.
 *
 * @return string Return currency code in db.
 */
function wte_currency_code_in_db() {
	// If the currency stored in the database is not in the
	// currency converter list, append it as well.
	$wte_settings = get_option( 'wp_travel_engine_settings' );
	if ( ! isset( $wte_settings['currency_code'] ) ) {
		$wte_settings['currency_code'] = 'USD';
	}
	$code_in_db = $wte_settings['currency_code'];

	return $code_in_db;
}

/**
 * Formats a number.
 * Use only to print number not for saving data to database.
 *
 * @since 4.3.0
 */
function wte_number_format( float $num, $decimals = '', $decimal_separator = '', $thousands_separator = '' ) {
	if ( is_string( $num ) ) {
		$num = floatval( $num );
	}
	$settings = get_option( 'wp_travel_engine_settings', array() );
	if ( $decimals === '' ) {
		$decimals = wte_array_get( $settings, 'decimal_digits', 2 );
	}
	if ( empty( $decimal_separator ) ) {
		$decimal_separator = wte_array_get( $settings, 'decimal_separator', '.' );
	}
	if ( empty( $thousands_separator ) ) {
		$thousands_separator = wte_array_get( $settings, 'thousands_separator', ',' );
	}

	return number_format( $num, $decimals, $decimal_separator, $thousands_separator );
}

/**
 * Decorate price with Currency code and symbol.
 *
 * @since 4.3.0
 *
 * @param int    $num Amount figure.
 * @param string $currency_code Currency Code.
 * @param string $format Format String.
 * @param bool   $use_currency_symbol Use Currency Symbol.
 * @return string Decorated Price with currency.
 */
function wte_get_formated_price( $num, $currency_code = '', $format = '', $use_currency_symbol = false, $use_html = false ) {
	if ( is_string( $num ) ) {
		$num = floatval( $num );
	}

	$formated_num = wte_price_value_format( +$num );

	$settings = get_option( 'wp_travel_engine_settings', array() );
	if ( empty( $currency_code ) ) {
		$currency_code =  apply_filters( 'wp_travel_engine_currency_code', wte_array_get( $settings, 'currency_code', 'USD' ), !1 );
		if ( wp_travel_engine_is_checkout_page() ) { //  @TODO: Resolve via currency convertor.
			$currency_code = apply_filters( 'wp_travel_engine_currency_code', wte_array_get( $settings, 'currency_code', 'USD' ), !0 );
		}
	}

	$currency_symbol = wp_travel_engine_get_currency_symbol( $currency_code );

	if ( empty( $format ) ) {
		$format = wte_array_get( $settings, 'amount_display_format', '' );
		if ( empty( $format ) ) {
			$format = $use_currency_symbol ? '%CURRENCY_SYMBOL% %FORMATED_AMOUNT%' : '%CURRENCY_CODE% %FORMATED_AMOUNT%';
		}
	}

	$replacer = array(
		'%CURRENCY_CODE%'   => $use_html ? '<span class="wpte-currency-code">' . $currency_code . '</span>' : $currency_code,
		'%CURRENCY_SYMBOL%' => $use_html ? '<span class="wpte-currency-code">' . $currency_symbol . '</span>' : $currency_symbol,
		'%AMOUNT%'          => $use_html ? '<span class="wpte-price">' . $num . '</span>' : $num,
		'%FORMATED_AMOUNT%' => $use_html ? '<span class="wpte-price">' . $formated_num . '</span>' : $formated_num,
	);

	return str_replace( array_keys( $replacer ), array_values( $replacer ), $format );

}

/**
 * Format Price Value converts price if necesary.
 *
 * @param float $price Price to format.
 * @since 4.3.0
 * @return void
 */
function wte_price_value_format( $price, $deprecated = array(
	'trip_id'                   => ! 1,
	'use_default_currency_code' => ! 1,
) ) {
	$price = +$price;
	// TODO : Move to filter.
	if ( class_exists( 'Wte_Trip_Currency_Converter_Init' ) && $deprecated['trip_id'] ) {
		$price = ( new Wp_Travel_Engine_Functions() )->convert_trip_price( get_post( $deprecated['trip_id'] ), $price );
	}

	$price = wte_number_format( apply_filters( 'wte_price_value', +$price ) );

	return apply_filters_deprecated( 'wp_travel_engine_get_formatted_price_separator', array( $price, $deprecated['trip_id'], $deprecated['use_default_currency_code'] ), '4.3.0', '', __( 'Not needed anymore.', 'wp-travel-engine' ) );
}

/**
 * Undocumented function
 *
 * @param [type]  $price
 * @param boolean $format
 * @param integer $number_of_decimals
 * @deprecated 4.3.0
 * @return void
 */
function wp_travel_engine_get_formated_price( $price, $format = true, $number_of_decimals = 2 ) {
	_deprecated_function( __FUNCTION__, '4.3.0', 'wte_number_format' );
	return wte_number_format( $price, $number_of_decimals, '.', '' );
}

/**
 * Undocumented function
 *
 * @param [type] $cost
 * @return void
 */
function wp_travel_engine_get_formated_price_separator( $cost, $trip_id = false, $use_default_currency_code = false ) {

	_deprecated_function( __FUNCTION__, '4.3.0', 'wte_price_value_format' );

	return wte_price_value_format(
		$cost,
		array(
			'trip_id'                   => $trip_id,
			'use_default_currency_code' => $use_default_currency_code,
		)
	);
}

/**
 * Get formatted price with currency for output.
 *
 * @deprecated 4.3.0
 */
function wp_travel_engine_get_formated_price_with_currency( $price, $trip_id = null, $use_default_currency_code = false ) {
	_deprecated_function( __FUNCTION__, '4.3.0', 'wte_get_formated_price' );

	return wte_get_formated_price( +$price, '', '%CURRENCY_SYMBOL%%FORMATED_AMOUNT% %CURRENCY_CODE%' );
}

/**
 * Get formatted price with currency for output.
 *
 * @deprecated 4.3.0
 */
function wp_travel_engine_get_formated_price_with_currency_symbol( $price, $trip_id = null, $use_default_currency_code = false ) {

	_deprecated_function( __FUNCTION__, '4.3.0', 'wte_get_formated_price' );

	return wte_get_formated_price( +$price, '', '%CURRENCY_SYMBOL%%FORMATED_AMOUNT%' );
}

/**
 * Get formatted price with currency for output with currency code.
 *
 * @deprecated 4.3.0
 */
function wp_travel_engine_get_formated_price_with_currency_code( $price, $trip_id = null, $use_default_currency_code = false ) {
	_deprecated_function( __FUNCTION__, '4.3.0', 'wte_get_formated_price' );

	return wte_get_formated_price( +$price, '', '' );
}

/**
 * Get formatted price with currency for output.
 *
 * @deprecated 4.3.0
 */
function wp_travel_engine_get_formated_price_with_currency_code_symbol( $price, $trip_id = null, $use_default_currency_code = false ) {
	_deprecated_function( __FUNCTION__, '4.3.0', 'wte_get_formated_price' );

	$format = '';

	$settings = get_option( 'wp_travel_engine_settings' );
	$option   = isset( $settings['currency_option'] ) && $settings['currency_option'] != '' ? esc_attr( $settings['currency_option'] ) : 'symbol';

	$format .= '<span class="wpte-currency-code">';
	$format .= 'code' === $option ? '%CURRENCY_CODE%' : '%CURRENCY_SYMBOL%';
	$format .= '</span><span class="wpte-price">';
	$format .= '%FORMATED_AMOUNT%';
	$format .= '</span>';
	return wte_get_formated_price( +$price, '', '', !1, !0 );
}

/**
 * Get formatted price with currency for output.
 *
 * @deprecated 4.3.0
 */
function wpte_get_formated_price_with_currency_code_symbol( $price, $trip_id = null, $use_default_currency_code = false ) {
	_deprecated_function( __FUNCTION__, '4.3.0', 'wte_get_formated_price' );

	$format = '';

	$settings = get_option( 'wp_travel_engine_settings' );
	$option   = isset( $settings['currency_option'] ) && $settings['currency_option'] != '' ? esc_attr( $settings['currency_option'] ) : 'symbol';

	$format .= '<span class="wpte-currency-code">';
	$format .= 'code' === $option ? '%CURRENCY_CODE%' : '%CURRENCY_SYMBOL%';
	$format .= '</span><span class="wpte-price">';
	$format .= '%FORMATED_AMOUNT%';
	$format .= '</span>';
	return wte_get_formated_price( +$price, '', '', !1, !0 );
}

/**
 * Get price by key.
 *
 * @param boolean $pricing_key
 * @return void
 */
function wp_travel_engine_get_price_by_pricing_key( $trip_id, $pricing_key = false ) {

	$price = 0;

	// If no trip ID supplied.
	if ( ! $trip_id ) {
		return $price;
	}

	if ( ! $pricing_key ) :

		return wp_travel_engine_get_actual_trip_price( $trip_id );

	endif;

	return $price;

}

/**
 * Is partially payable for trip id.
 *
 * @param [type] $trip_id
 * @return void
 */
function wp_travel_engine_is_trip_partially_payable( $trip_id ) {

	if ( ! $trip_id ) {
		return false;
	}

	$wte_options                  = get_option( 'wp_travel_engine_settings', true );
	$wte_trip_metas               = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );
	$trip_partial_payment_enabled = isset( $wte_trip_metas['partial_payment_enable'] ) && 'yes' === $wte_trip_metas['partial_payment_enable'] ? true : false;
	$global_partial_pay_enable    = isset( $wte_options['partial_payment_enable'] ) && 'yes' === $wte_options['partial_payment_enable'] ? true : false;

	return class_exists( 'Wte_Partial_Payment_Admin' ) && $global_partial_pay_enable && $trip_partial_payment_enabled;

}

/**
 * Get partial payment data for trip.
 *
 * @return void
 */
function wp_travel_engine_get_trip_partial_payment_data( $trip_id ) {

	$partial_payment    = array();
	$trip_price_partial = 0;
	$wte_options        = get_option( 'wp_travel_engine_settings', true );
	$wte_trip_metas     = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );

	if ( ! $trip_id ) {
		return $partial_payment;
	}

	if ( wp_travel_engine_is_trip_partially_payable( $trip_id ) ) :

		$partial_type = $wte_options['partial_payment_option'];

		if ( 'amount' === $partial_type ) :

			$trip_price_partial = isset( $wte_options['partial_payment_amount'] ) && ! empty( $wte_options['partial_payment_amount'] ) ? $wte_options['partial_payment_amount'] : 0;

			$trip_price_partial = isset( $wte_trip_metas['partial_payment_amount'] ) && ! empty( $wte_trip_metas['partial_payment_amount'] ) ? $wte_trip_metas['partial_payment_amount'] : $trip_price_partial;

			$partial_payment = array(
				'type'  => 'amount',
				'value' => $trip_price_partial,
			);

		elseif ( 'percent' === $partial_type ) :

			$trip_partial_percentage = isset( $wte_options['partial_payment_percent'] ) && ! empty( $wte_options['partial_payment_percent'] ) ? $wte_options['partial_payment_percent'] : 0;

			$trip_partial_percentage = isset( $wte_trip_metas['partial_payment_percent'] ) && ! empty( $wte_trip_metas['partial_payment_percent'] ) ? $wte_trip_metas['partial_payment_percent'] : $trip_partial_percentage;

			$partial_payment = array(
				'type'  => 'percentage',
				'value' => $trip_partial_percentage,
			);

		endif;

	endif;

	return $partial_payment;

}

/**
 * Check if cart is partially payable.
 *
 * @return void
 */
function wp_travel_engine_is_cart_partially_payable() {

	global $wte_cart;

	$cart_items = $wte_cart->getItems();

	if ( ! empty( $cart_items ) ) :

		$cart_items = array_filter(
			$cart_items,
			function( $item ) {
				return wp_travel_engine_is_trip_partially_payable( $item['trip_id'] );
			}
		);

		return ( ! empty( $cart_items ) );

	endif;

	return false;

}

/**
 * Get person format.
 *
 * @since 3.0.0
 *
 * @return string Person format
 */
function wte_get_person_format() {

	$wte_settings = wp_travel_engine_get_settings();

	$per_person = __( '/person', 'wp-travel-engine' );

	if ( $wte_settings ) :

		// Set default per person format.
		if ( ! isset( $wte_settings['person_format'] ) || empty( $wte_settings['person_format'] ) ) {
			$wte_settings['person_format'] = __( '/person', 'wp-travel-engine' );
		}
		$per_person = $wte_settings['person_format'];

	endif;

	return apply_filters( 'wte_person_format', $per_person );
}

/**
 * Get book now text.
 *
 * @since 3.0.0
 *
 * @return String book now text.
 */
function wte_get_book_now_text() {

	$wte_settings = wp_travel_engine_get_settings();

	$per_person = __( 'Book Now', 'wp-travel-engine' );

	if ( $wte_settings ) :

		if ( ! isset( $wte_settings['book_btn_txt'] ) || empty( $wte_settings['book_btn_txt'] ) ) {
			$wte_settings['book_btn_txt'] = __( 'Book Now', 'wp-travel-engine' );
		}
		$per_person = $wte_settings['book_btn_txt'];

	endif;

	return apply_filters( 'wte_book_now', $per_person );
}

/**
 * Get Total text.
 *
 * @since 3.0.0
 *
 * @return String Total text.
 */
function wte_get_total_text() {

	$total = __( 'Total:', 'wp-travel-engine' );

	return apply_filters( 'wte_total_text', $total );
}

/**
 * is multiple pricing enabled for the trip?
 *
 * @param [type] $trip_id
 * @return void
 */
function wp_travel_engine_is_trip_multiple_pricing_enabled( $trip_id ) {

	if ( ! $trip_id ) {
		return false;
	}

	$trip_settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );

	return isset( $trip_settings['multiple_pricing_enable'] ) && '1' === $trip_settings['multiple_pricing_enable'];

}

/**
 * Undocumented function
 *
 * @param [type] $pricing_key
 * @return void
 */
function wte_get_pricing_label_by_key( $trip_id, $pricing_key ) {

	if ( ! $pricing_key || ! $trip_id ) {
		return false;
	}

	$trip_settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );

	$multiple_pricing_options = isset( $trip_settings['multiple_pricing'] ) && ! empty( $trip_settings['multiple_pricing'] ) ? $trip_settings['multiple_pricing'] : array();

	if ( ! empty( $multiple_pricing_options ) && isset( $multiple_pricing_options[ $pricing_key ] ) ) :

		return isset( $multiple_pricing_options[ $pricing_key ]['label'] ) ? $multiple_pricing_options[ $pricing_key ]['label'] : $pricing_key;

	endif;

	return false;

}

function wte_multi_pricing_labels( $trip_id ) {

	$labels = array();

	$trip_settings            = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );
	$multiple_pricing_options = isset( $trip_settings['multiple_pricing'] ) && ! empty( $trip_settings['multiple_pricing'] ) ? $trip_settings['multiple_pricing'] : false;

	if ( $multiple_pricing_options ) :
		foreach ( $multiple_pricing_options as $key => $pricing_option ) :

			$pricing_label  = isset( $pricing_option['label'] ) ? $pricing_option['label'] : ucfirst( $key );
			$labels[ $key ] = $pricing_label;

		endforeach;
	endif;

	return $labels;

}

/**
 * Undocumented function
 *
 * @param [type] $pricing_key
 * @return void
 */
function wte_get_pricing_label_by_key_invoices( $trip_id, $pricing_key, $pax ) {

	if ( ! $pricing_key || ! $trip_id ) {
		return false;
	}

	$pax_label = wte_get_pricing_label_by_key( $trip_id, $pricing_key );
	if ( ! $pax_label ) {
		$pax_label = ucfirst( $pricing_key );
	}

	$trip_settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );

	$multiple_pricing_options = isset( $trip_settings['multiple_pricing'] ) && ! empty( $trip_settings['multiple_pricing'] ) ? $trip_settings['multiple_pricing'] : array();

	if ( ! empty( $multiple_pricing_options ) && isset( $multiple_pricing_options[ $pricing_key ] ) ) :

		$pax_label_str = sprintf( _nx( 'Number of %1$s', 'Number of %1$s(s)', $pax, 'number of travellers', 'wp-travel-engine' ), $pax_label );

		if ( 'child' === $pricing_key ) :

			$pax_label_str = sprintf( _nx( 'Number of %1$s', 'Number of Children', $pax, 'number of travellers', 'wp-travel-engine' ), $pax_label );

		endif;

		if ( 'group' === $pricing_key ) :

			$pax_label_str = __( 'Number of pax in Group', 'wp-travel-engine' );

		endif;

		if ( isset( $multiple_pricing_options[ $pricing_key ]['label'] ) ) :

			$pax_label_str = sprintf( _nx( 'Number of pax in %1$s', 'Number of pax in %1$s', $pax, 'number of travellers', 'wp-travel-engine' ), ucfirst( $multiple_pricing_options[ $pricing_key ]['label'] ) );

		endif;

		return $pax_label_str;

	endif;

	return false;

}

/**
 * Get currency code or symbol.
 *
 * @return void
 */
function wp_travel_engine_get_currency_code_or_symbol() {
	$wp_travel_engine_settings = get_option( 'wp_travel_engine_settings', true );
	$code                      = 'USD';

	if ( isset( $wp_travel_engine_settings['currency_code'] ) && $wp_travel_engine_settings['currency_code'] != '' ) {
		$code = $wp_travel_engine_settings['currency_code'];
	}

	$symbol = wp_travel_engine_get_currency_symbol( $code );

	$currency_option = isset( $wp_travel_engine_settings['currency_option'] ) && ! empty( $wp_travel_engine_settings['currency_option'] ) ? $wp_travel_engine_settings['currency_option'] : 'symbol';

	return 'symbol' === $currency_option ? $symbol : $code;
}
