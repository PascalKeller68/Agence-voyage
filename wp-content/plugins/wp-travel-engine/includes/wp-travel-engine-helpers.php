<?php
/**
 * Helper functions for WP Travel Engine.
 *
 * @package WP_Travel_Engine
 */
require sprintf( '%s/includes/helpers/helpers-prices.php', WP_TRAVEL_ENGINE_ABSPATH );
/**
 * Wrapper for _doing_it_wrong().
 *
 * @since  3.1.3
 * @param string $function Function used.
 * @param string $message Message to log.
 * @param string $version Version the message was added in.
 */
function wte_doing_it_wrong( $function, $message, $version ) {
	// @codingStandardsIgnoreStart
	$message .= ' Backtrace: ' . wp_debug_backtrace_summary();

	if ( is_ajax() ) {
		do_action( 'doing_it_wrong_run', $function, $message, $version );
		error_log( "{$function} was called incorrectly. {$message}. This message was added in version {$version}." );
	} else {
		_doing_it_wrong( $function, $message, $version );
	}
	// @codingStandardsIgnoreEnd
}
/**
 * Return array list of all trips.
 *
 * @return Array
 */
function wp_travel_engine_get_trips_array( $use_titles = false ) {
	$args = array(
		'post_type'   => 'trip',
		'numberposts' => -1,
	);

	$trips = get_posts( $args );

	$trips_array = array();
	foreach ( $trips as $trip ) {
		if ( $use_titles ) {
			$trips_array[ $trip->post_title ] = $trip->post_title;
		} else {
			$trips_array[ $trip->ID ] = $trip->post_title;
		}
	}
	return apply_filters( 'wp_travel_engine_trips_array', $trips_array, $args );
}

/**
 * Get permalink settings for WP Travel Engine.
 *
 * @since  2.2.4
 * @return array
 */
function wp_travel_engine_get_permalink_structure() {

	$permalinks = wp_parse_args(
		(array) get_option( 'wp_travel_engine_permalinks', array() ),
		array(
			'wp_travel_engine_trip_base'        => '',
			'wp_travel_engine_trip_type_base'   => '',
			'wp_travel_engine_destination_base' => '',
			'wp_travel_engine_activity_base'    => '',
		)
	);

	$permalinks['wp_travel_engine_trip_base']        = untrailingslashit( empty( $permalinks['wp_travel_engine_trip_base'] ) ? 'trip' : $permalinks['wp_travel_engine_trip_base'] );
	$permalinks['wp_travel_engine_trip_type_base']   = untrailingslashit( empty( $permalinks['wp_travel_engine_trip_type_base'] ) ? 'trip-types' : $permalinks['wp_travel_engine_trip_type_base'] );
	$permalinks['wp_travel_engine_destination_base'] = untrailingslashit( empty( $permalinks['wp_travel_engine_destination_base'] ) ? 'destinations' : $permalinks['wp_travel_engine_destination_base'] );
	$permalinks['wp_travel_engine_activity_base']    = untrailingslashit( empty( $permalinks['wp_travel_engine_activity_base'] ) ? 'activities' : $permalinks['wp_travel_engine_activity_base'] );

	return $permalinks;
}

/**
 * Get trip settings meta.
 *
 * @param int $trip_id
 * @return mixed $trip_settings | false
 * @since 2.2.4
 */
function wp_travel_engine_get_trip_metas( $trip_id ) {

	if ( ! $trip_id ) {
		return false;
	}

	$trip_settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );

	return ! empty( $trip_settings ) ? $trip_settings : false;

}

/**
 * Get trip preview price ( Before sale )
 *
 * @param int $trip_id
 * @return int $prev_price
 * @since 2.2.4
 */
function wp_travel_engine_get_prev_price( $trip_id, $no_convert = false ) {

	if ( ! $trip_id ) {
		return 0;
	}

	$trip_settings = wp_travel_engine_get_trip_metas( $trip_id );
	$prev_price    = '';

	if ( $trip_settings ) {
		$prev_price = isset( $trip_settings['trip_prev_price'] ) ? $trip_settings['trip_prev_price'] : '';
	}

	if ( $no_convert ) {
		return $prev_price;
	}

	return apply_filters( 'wp_travel_engine_trip_prev_price', $prev_price, $trip_id );

}

/**
 * Get trip sale price
 *
 * @param [type] $trip_id
 * @return void
 */
function wp_travel_engine_get_sale_price( $trip_id, $no_convert = false ) {

	if ( ! $trip_id ) {
		return 0;
	}

	$trip_settings = wp_travel_engine_get_trip_metas( $trip_id );
	$sale_price    = '';

	if ( $trip_settings ) {
		$sale_price = isset( $trip_settings['trip_price'] ) ? $trip_settings['trip_price'] : '';
	}

	if ( $no_convert ) {
		return $sale_price;
	}

	return apply_filters( 'wp_travel_engine_trip_sale_price', $sale_price, $trip_id );

}

/**
 * Check if the trip is on sale
 *
 * @param int $trip_id
 * @return bool
 */
function wp_travel_engine_is_trip_on_sale( $trip_id ) {

	if ( ! $trip_id ) {
		return false;
	}

	$trip_settings = wp_travel_engine_get_trip_metas( $trip_id );

	if ( ! $trip_settings ) {
		return false;
	}

	$trip_on_sale = isset( $trip_settings['sale'] ) ? true : false;

	return apply_filters( 'wp_travel_engine_is_trip_on_sale', $trip_on_sale, $trip_id );

}

/**
 * Get actual trip price.
 *
 * @param [type] $trip_id
 * @return void
 */
function wp_travel_engine_get_actual_trip_price( $trip_id, $no_convert = false ) {

	if ( ! $trip_id ) {
		return 0;
	}

	$on_sale = wp_travel_engine_is_trip_on_sale( $trip_id );

	$trip_actual_price = $on_sale ? wp_travel_engine_get_sale_price( $trip_id, $no_convert ) : wp_travel_engine_get_prev_price( $trip_id, $no_convert );

	return apply_filters( 'wp_travel_engine_actual_trip_price', $trip_actual_price, $trip_id );

}

/**
 * Get currenct code.
 *
 * @return void
 */
function wp_travel_engine_get_currency_code( $use_default_currency_code = false ) {

	$wp_travel_engine_settings = get_option( 'wp_travel_engine_settings', true );

	$code = 'USD';

	if ( isset( $wp_travel_engine_settings['currency_code'] ) && $wp_travel_engine_settings['currency_code'] != '' ) {
		$code = $wp_travel_engine_settings['currency_code'];
	}

	return apply_filters( 'wp_travel_engine_currency_code', $code, $use_default_currency_code );

}

/**
 * Get currency symbol
 *
 * @param string $code
 * @return void
 */
function wp_travel_engine_get_currency_symbol( $code = 'USD' ) {

	$wte      = new Wp_Travel_Engine_Functions();
	$currency = $wte->wp_travel_engine_currencies_symbol( $code );

	return $currency;

}

/**
 * Get fixed departure dates array.
 *
 * @param [type] $trip_id
 * @return void
 */
function wp_travel_engine_get_fixed_departure_dates( $trip_id, $get_month = false ) {

	$obj                         = new Wp_Travel_Engine_Functions();
	$valid_departure_dates_array = array();

	if ( ! $trip_id ) {
		return $valid_departure_dates_array;
	}

	if ( class_exists( 'WTE_Fixed_Starting_Dates_Functions' ) && method_exists( 'WTE_Fixed_Starting_Dates_Functions', 'get_formated_fsd_dates' ) ) {

		$WTE_Fixed_Starting_Dates_option_setting = get_option( 'wp_travel_engine_settings', true );

		$num = isset( $WTE_Fixed_Starting_Dates_option_setting['trip_dates']['number'] ) ? $WTE_Fixed_Starting_Dates_option_setting['trip_dates']['number'] : 3;

		$fsd_functions = new WTE_Fixed_Starting_Dates_Functions();
		$sorted_fsd    = $fsd_functions->get_formated_fsd_dates( $trip_id );

		$valid_departure_dates_array = $sorted_fsd;

	}
	return $valid_departure_dates_array;
}

/**
 * Get checkout page URL
 *
 * @return void
 */
function wp_travel_engine_get_checkout_url() {

	$wte_global_options          = get_option( 'wp_travel_engine_settings', true );
	$wp_travel_engine_placeorder = isset( $wte_global_options['pages']['wp_travel_engine_place_order'] ) ? esc_attr( $wte_global_options['pages']['wp_travel_engine_place_order'] ) : '';

	return ! empty( $wp_travel_engine_placeorder ) ? esc_url( get_permalink( $wp_travel_engine_placeorder ) ) : esc_url( home_url( '/' ) );

}

/**
 * Sorted extras
 *
 * @param [type] $trip_id
 * @param array  $extra_services
 * @return void
 */
function wp_travel_engine_sort_extra_services( $trip_id, $extra_services = array() ) {

	$sorted_extras = array();

	if ( ! $trip_id ) {

		return $sorted_extras;

	}

	$wp_travel_engine_setting = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );

	foreach ( $extra_services as $key => $value ) {
		if ( isset( $extra_services[ $key ] ) && $extra_services[ $key ] != '' && isset( $_POST['extra_service_name'][ $key ] ) && $_POST['extra_service_name'][ $key ] != '' && '0' !== $extra_services[ $key ] ) {
			$sorted_extras[ $key ] = array(
				'extra_service' => $wp_travel_engine_setting['extra_service'][ $key ],
				'qty'           => $extra_services[ $key ],
				'price'         => +$_POST['extra_service_name'][ $key ],
			);
		}
	}

	return $sorted_extras;

}
/**
 * Get trip duration [ formatted ]
 */
function wp_travel_engine_get_trip_duration( $trip_id ) {

	if ( ! $trip_id ) {
		return false;
	}

	$trip_settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );

	return sprintf( _nx( '%s Day', '%s Days', $trip_settings['trip_duration'], 'trip duration days', 'wp-travel-engine' ), number_format_i18n( $trip_settings['trip_duration'] ) ) . ' ' . sprintf( _nx( '%s Night', '%s Nights', $trip_settings['trip_duration_nights'], 'trip duration nights', 'wp-travel-engine' ), number_format_i18n( $trip_settings['trip_duration_nights'] ) );

}

add_action( 'wp_travel_engine_proceed_booking_btn', 'wp_travel_engine_default_booking_proceed' );

/**
 * Default proceed booking button.
 *
 * @return void
 */
function wp_travel_engine_default_booking_proceed() {

	$wp_travel_engine_setting_option_setting = get_option( 'wp_travel_engine_settings', true );

	global $post;

	ob_start();

	?>
		<button class="check-availability">
		<?php
		$button_txt = __( 'Check Availability', 'wp-travel-engine' );
		echo apply_filters( 'wp_travel_engine_check_availability_button_text', $button_txt );
		?>
		</button>
		<?php
		$btn_txt = __( 'Book Now', 'wp-travel-engine' );
		if ( isset( $wp_travel_engine_setting_option_setting['book_btn_txt'] ) && $wp_travel_engine_setting_option_setting['book_btn_txt'] != '' ) {
			$btn_txt = $wp_travel_engine_setting_option_setting['book_btn_txt'];
		}
		?>
		<input name="booking_btn" data-formid="booking-frm-<?php echo esc_attr( $post->ID ); ?>" type="submit" class="book-submit" value="<?php echo esc_attr( $btn_txt ); ?>">
	<?php

	$data = ob_get_clean();

	echo apply_filters( 'wp_travel_engine_booking_process_btn_html', $data );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 * yourtheme/$template_path/$template_name
 * yourtheme/$template_name
 * $default_path/$template_name
 *
 * @since 1.0.0
 *
 * @param string $template_name Template name.
 * @param string $template_path Template path. (default: '').
 * @param string $default_path Default path. (default: '').
 *
 * @return string Template path.
 */
function wte_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = apply_filters( 'wp_travel_engine_template_path', 'wp-travel-engine/' );
	}

	if ( ! $default_path ) {
		$default_path = WP_TRAVEL_ENGINE_BASE_PATH . '/includes/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);

	// Get default template.
	if ( ! $template ) {
		// Look within passed path within the theme - this is priority.
		$template = locate_template(
			array(
				trailingslashit( $template_name ),
				$template_name,
			)
		);
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}
	}

	// Return what we found.
	return apply_filters( 'wte_locate_template', $template, $template_name, $template_path );
}

/**
 * Get other templates (e.g. article attributes) passing attributes and including the file.
 *
 * @since 1.0.0
 *
 * @param string $template_name   Template name.
 * @param array  $args            Arguments. (default: array).
 * @param string $template_path   Template path. (default: '').
 * @param string $default_path    Default path. (default: '').
 */
function wte_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	$cache_key = sanitize_key( implode( '-', array( 'template', $template_name, $template_path, $default_path, WP_TRAVEL_ENGINE_VERSION ) ) );
	$template  = (string) wp_cache_get( $cache_key, 'wp-travel-engine' );

	if ( ! $template ) {
		$template = wte_locate_template( $template_name, $template_path, $default_path );
		wp_cache_set( $cache_key, $template, 'wp-travel-engine' );
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$filter_template = apply_filters( 'wte_get_template', $template, $template_name, $args, $template_path, $default_path );

	if ( $filter_template !== $template ) {
		if ( ! file_exists( $filter_template ) ) {
			/* translators: %s template */
			wte_doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'wp-travel-engine' ), '<code>' . $template . '</code>' ), '1.0.0' );
			return;
		}
		$template = $filter_template;
	}

	$action_args = array(
		'template_name' => $template_name,
		'template_path' => $template_path,
		'located'       => $template,
		'args'          => $args,
	);

	if ( ! empty( $args ) && is_array( $args ) ) {
		if ( isset( $args['action_args'] ) ) {
			wte_doing_it_wrong(
				__FUNCTION__,
				__( 'action_args should not be overwritten when calling wte_get_template.', 'wp-travel-engine' ),
				'1.0.0'
			);
			unset( $args['action_args'] );
		}
		extract( $args );
	}

	do_action( 'wte_before_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );

	include $action_args['located'];

	do_action( 'wte_after_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );
}


/**
 * Like wte_get_template, but return the HTML instaed of outputting.
 *
 * @see wte_get_template
 * @since 1.0.0
 *
 * @param string $template_name Template name.
 * @param array  $args           Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path  Default path. (default: '').
 *
 * @return string.
 */
function wte_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	ob_start();
		wte_get_template( $template_name, $args, $template_path, $default_path );
	return ob_get_clean();
}

/**
 * Get list of all available paymanet gateways.
 *
 * @return void
 */
function wp_travel_engine_get_available_payment_gateways( $gateways_list = array() ) {

	$gateways_list = array(
		'booking_only'         => array(
			'label'        => __( 'Booking Only', 'wp-travel-engine' ),
			'input_class'  => '',
			'public_label' => '',
			'icon_url'     => '',
			'info_text'    => __( 'If checked, no paymnet gateways will be used in checkout. The booking process will be completed and booking will be saved without payment.', 'wp-travel-engine' ),
		),
		'paypal_payment'       => array(
			'label'        => __( 'Paypal Standard', 'wp-travel-engine' ),
			'input_class'  => 'paypal-payment',
			'public_label' => '',
			'icon_url'     => WP_TRAVEL_ENGINE_URL . '/public/css/icons/paypal-payment.png',
			'info_text'    => __( 'Please check this to enable Paypal Standard booking system for trip booking and fill the account info below.', 'wp-travel-engine' ),
		),
		'direct_bank_transfer' => array(
			'label'        => __( 'Direct Bank Transfer', 'wp-travel-engine' ),
			'input_class'  => 'bank-transfer',
			'public_label' => __( 'Direct Bank Transfer', 'wp-travel-engine' ),
			'icon_url'     => '',
			'info_text'    => __( 'Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order will not be shipped until the funds have cleared in our account.', 'wp-travel-engine' ),
		),
		'check_payments'       => array(
			'label'        => __( 'Check Payments', 'wp-travel-engine' ),
			'input_class'  => 'wte-check-payments',
			'public_label' => __( 'Check Payments', 'wp-travel-engine' ),
			'icon_url'     => '',
			'info_text'    => __( 'Please send a check to Store Name, Store Street, Store Town, Store State / County, Store Postcode.', 'wp-travel-engine' ),
		),
	);

	return apply_filters( 'wp_travel_engine_available_payment_gateways', $gateways_list );

}

/**
 * Get sorted payment gateway list array
 *
 * @return void
 */
function wp_travel_engine_get_sorted_payment_gateways() {

	$wpte_settings      = get_option( 'wp_travel_engine_settings' );
	$available_gateways = wp_travel_engine_get_available_payment_gateways();

	$payment_gateway_sorted_settings = isset( $wpte_settings['sorted_payment_gateways'] ) && ! empty( $wpte_settings['sorted_payment_gateways'] ) ? $wpte_settings['sorted_payment_gateways'] : array_keys( $available_gateways );

	$sorted_payment_gateways = array();

	foreach ( $payment_gateway_sorted_settings as $key ) :

		if ( array_key_exists( $key, $available_gateways ) ) :

			$sorted_payment_gateways[ $key ] = $available_gateways[ $key ];

			unset( $available_gateways[ $key ] );

		endif;

	endforeach;

	return $sorted_payment_gateways + $available_gateways;

}

/**
 * return active payment gateways.
 *
 * @return void
 */
function wp_travel_engine_get_active_payment_gateways() {

	$available_sorted_gateways = wp_travel_engine_get_sorted_payment_gateways();
	$wpte_settings             = get_option( 'wp_travel_engine_settings' );

	$available_sorted_gateways = array_filter(
		$available_sorted_gateways,
		function( $gateway_key ) use ( $wpte_settings ) {
			return ! empty( $wpte_settings[ $gateway_key ] );
		},
		ARRAY_FILTER_USE_KEY
	);

	return $available_sorted_gateways;

}

/**
 * Get booking confirmation page URL
 *
 * @return url Confirmation page url.
 */
function wp_travel_engine_get_booking_confirm_url() {

	$wte_settings = get_option( 'wp_travel_engine_settings', [] );

	$wte_confirm = wte_array_get( $wte_settings, 'pages.wp_travel_engine_confirmation_page', '' );

	$hide_traveler_information = wte_array_get( $wte_settings, 'travelers_information', '' ) === 'yes';
	
	if ( $hide_traveler_information || empty( $wte_confirm ) ) {
		$wte_confirm = wte_array_get( $wte_settings, 'pages.wp_travel_engine_thank_you', '' );
	}

	if ( empty( $wte_confirm ) ) :
		$wte_confirm = esc_url( home_url( '/' ) );
	else :
		$wte_confirm = get_permalink( $wte_confirm );
	endif;

	return $wte_confirm;

}

/*
 * Delete all the transients with a prefix.
 */
function wte_purge_transients( $prefix ) {
	global $wpdb;

	$prefix = esc_sql( $prefix );

	$options = $wpdb->options;

	$t = esc_sql( "_transient_timeout_{$prefix}%" );

	$sql = $wpdb->prepare(
		"
		SELECT option_name
		FROM $options
		WHERE option_name LIKE '%s'
	  ",
		$t
	);

	$transients = $wpdb->get_col( $sql );

	// For each transient...
	foreach ( $transients as $transient ) {

		// Strip away the WordPress prefix in order to arrive at the transient key.
		$key = str_replace( '_transient_timeout_', '', $transient );

		// Now that we have the key, use WordPress core to the delete the transient.
		delete_transient( $key );

	}

	// But guess what?  Sometimes transients are not in the DB, so we have to do this too:
	wp_cache_flush();
}

/**
 * Get view mode
 *
 * @return string $view_mode
 */
function wp_travel_engine_get_archive_view_mode() {
	$default   = 'grid';
	$default   = apply_filters( 'wp_travel_engine_default_archive_view_mode', $default );
	$view_mode = $default;

	if ( isset( $_GET['view_mode'] ) && ( 'grid' === $_GET['view_mode'] || 'list' === $_GET['view_mode'] ) ) {
		$view_mode = $_GET['view_mode'];
	}

	return $view_mode;
}

/**
 * Outputs hidden form inputs for each query string variable.
 *
 * @since 3.0.6
 * @param string|array $values Name value pairs, or a URL to parse.
 * @param array        $exclude Keys to exclude.
 * @param string       $current_key Current key we are outputting.
 * @param bool         $return Whether to return.
 * @return string
 */
function wte_query_string_form_fields( $values = null, $exclude = array(), $current_key = '', $return = false ) {
	if ( is_null( $values ) ) {
		$values = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	} elseif ( is_string( $values ) ) {
		$url_parts = wp_parse_url( $values );
		$values    = array();

		if ( ! empty( $url_parts['query'] ) ) {
			// This is to preserve full-stops, pluses and spaces in the query string when ran through parse_str.
			$replace_chars = array(
				'.' => '{dot}',
				'+' => '{plus}',
			);

			$query_string = str_replace( array_keys( $replace_chars ), array_values( $replace_chars ), $url_parts['query'] );

			// Parse the string.
			parse_str( $query_string, $parsed_query_string );

			// Convert the full-stops, pluses and spaces back and add to values array.
			foreach ( $parsed_query_string as $key => $value ) {
				$new_key            = str_replace( array_values( $replace_chars ), array_keys( $replace_chars ), $key );
				$new_value          = str_replace( array_values( $replace_chars ), array_keys( $replace_chars ), $value );
				$values[ $new_key ] = $new_value;
			}
		}
	}
	$html = '';

	foreach ( $values as $key => $value ) {
		if ( in_array( $key, $exclude, true ) ) {
			continue;
		}
		if ( $current_key ) {
			$key = $current_key . '[' . $key . ']';
		}
		if ( is_array( $value ) ) {
			$html .= wte_query_string_form_fields( $value, $exclude, $key, true );
		} else {
			$html .= '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( wp_unslash( $value ) ) . '" />';
		}
	}

	if ( $return ) {
		return $html;
	}

	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Get Enquiry form field by name.
 */
function wp_travel_engine_get_enquiry_field_label_by_name( $name = false ) {
	if ( ! $name ) {
		return false;
	}
	$enquiry_form_fields = WTE_Default_Form_Fields::enquiry();
	$enquiry_form_fields = apply_filters( 'wp_travel_engine_enquiry_fields_display', $enquiry_form_fields );

	$field_label = isset( $enquiry_form_fields[ $name ] ) && isset( $enquiry_form_fields[ $name ]['field_label'] ) ? $enquiry_form_fields[ $name ]['field_label'] : $name;

	return $field_label;
}

/**
 * Get Booking form field by name.
 */
function wp_travel_engine_get_booking_field_label_by_name( $name = false ) {
	if ( ! $name ) {
		return false;
	}
	$booking_form_fields = WTE_Default_Form_Fields::booking();
	$booking_form_fields = apply_filters( 'wp_travel_engine_booking_fields_display', $booking_form_fields );

	$field_label = isset( $booking_form_fields[ $name ] ) && isset( $booking_form_fields[ $name ]['field_label'] ) ? $booking_form_fields[ $name ]['field_label'] : $name;

	return $field_label;
}

/**
 * Get ller Info form field by name.
 */
function wp_travel_engine_get_traveler_info_field_label_by_name( $name = false ) {
	if ( ! $name ) {
		return false;
	}
	$traveller_info_form_fields = WTE_Default_Form_Fields::traveller_information();
	$traveller_info_form_fields = apply_filters( 'wp_travel_engine_traveller_info_fields_display', $traveller_info_form_fields );

	$field_label = isset( $traveller_info_form_fields[ $name ] ) && isset( $traveller_info_form_fields[ $name ]['field_label'] ) ? $traveller_info_form_fields[ $name ]['field_label'] : $name;

	return $field_label;
}

/**
 * Get ller Info form field by name.
 */
function wp_travel_engine_get_relationship_field_label_by_name( $name = false ) {
	if ( ! $name ) {
		return false;
	}
	$emergency_contact_form_fields = WTE_Default_Form_Fields::emergency_contact();
	$emergency_contact_form_fields = apply_filters( 'wp_travel_engine_emergency_contact_fields_display', $emergency_contact_form_fields );

	$field_label = isset( $emergency_contact_form_fields[ $name ] ) && isset( $emergency_contact_form_fields[ $name ]['field_label'] ) ? $emergency_contact_form_fields[ $name ]['field_label'] : $name;

	return $field_label;
}

/**
 * Get Default Settings Tab
 */
function wte_get_default_settings_tab() {
	$default_tabs = array(
		'name'  => array(
			'1' => __( 'Overview', 'wp-travel-engine' ),
			'2' => __( 'Itinerary', 'wp-travel-engine' ),
			'3' => __( 'Cost', 'wp-travel-engine' ),
			'4' => __( 'Dates', 'wp-travel-engine' ),
			'5' => __( 'FAQs', 'wp-travel-engine' ),
			'6' => __( 'Map', 'wp-travel-engine' ),
		),

		'field' => array(
			'1' => 'wp_editor',
			'2' => 'itinerary',
			'3' => 'cost',
			'4' => 'dates',
			'5' => 'faqs',
			'6' => 'map',
		),
		'id'    => array(
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
		),
	);

	return $default_tabs;
}

/**
 * Get From Email Address
 */
function wte_get_from_email() {
	$admin_email = get_option( 'admin_email' );
	$sitename    = strtolower( $_SERVER['SERVER_NAME'] );

	if ( in_array( $sitename, array( 'localhost', '127.0.0.1' ) ) ) {
		return $admin_email;
	}

	if ( substr( $sitename, 0, 4 ) == 'www.' ) {
		$sitename = substr( $sitename, 4 );
	}

	if ( strpbrk( $admin_email, '@' ) == '@' . $sitename ) {
		return $admin_email;
	}

	return 'wordpress@' . $sitename;
}

/**
 * Check if site is using old booking process.
 */
function wp_travel_engine_use_old_booking_process() {
	return defined( 'WTE_USE_OLD_BOOKING_PROCESS' ) && WTE_USE_OLD_BOOKING_PROCESS;
}
/** Return All Settings of WP travel Engine. */
function wp_travel_engine_get_settings() {
	$default_settings = array();
	$settings         = get_option( 'wp_travel_engine_settings' ) ? get_option( 'wp_travel_engine_settings' ) : array();

	$settings = array_merge( $default_settings, $settings );
	return $settings;
}

/**
 * Get dashboard page ID or resort to default.
 *
 * @return void
 */
function wp_travel_engine_get_dashboard_page_id() {
	$settings = wp_travel_engine_get_settings();

	$wp_travel_engine_dashboard_id = isset( $settings['pages']['wp_travel_engine_dashboard_page'] ) ? esc_attr( $settings['pages']['wp_travel_engine_dashboard_page'] ) : wp_travel_engine_get_page_id( 'my-account' );

	return $wp_travel_engine_dashboard_id;
}

/**
 * Checks whether the content passed contains a specific short code.
 *
 * @param  string $tag Shortcode tag to check.
 * @return bool
 */
function wp_travel_engine_post_content_has_shortcode( $tag = '' ) {
	global $post;

	return is_singular() && is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, $tag );
}

/**
 * Retrieve page ids - cart, checkout. returns -1 if no page is found.
 *
 * @param string $page Page slug.
 * @return int
 */
function wp_travel_engine_get_page_id( $page ) {

	$settings = get_option( 'wp_travel_engine_settings' ); // Not used wp_travel_engine_get_settings due to infinite loop.
	$page     = str_replace( 'wp-travel-engine-', '', $page );
	$page_id  = ( isset( $settings[ $page . '_page_id' ] ) ) ? $settings[ $page . '_page_id' ] : '';

	if ( ! $page_id ) {
		$page_id = get_option( 'wp_travel_engine_' . $page . '_page_id' );
	}

	$page_id = apply_filters( 'wp_travel_engine_get_' . $page . '_page_id', $page_id );

	return $page_id ? absint( $page_id ) : -1;
}

/**
 * Retrieve page permalink.
 *
 * @param string $page page slug.
 * @return string
 */
function wp_travel_engine_get_page_permalink( $page ) {
	$page_id   = wp_travel_engine_get_page_id( $page );
	$permalink = 0 < $page_id ? get_permalink( $page_id ) : get_home_url();
	return apply_filters( 'wp_travel_engine_get_' . $page . '_page_permalink', $permalink );
}

/**
 * Retrieve page permalink by id.
 *
 * @param string $page page id.
 * @return string
 */
function wp_travel_engine_get_page_permalink_by_id( $page_id ) {
	$permalink = 0 < $page_id ? get_permalink( $page_id ) : get_home_url();
	return apply_filters( 'wp_travel_engine_get_' . $page_id . '_permalink', $permalink );
}

/**
 * Check whether page is dashboard page or not.
 *
 * @return Boolean
 */
function wp_travel_engine_is_dashboard_page() {
	if ( is_admin() ) {
		return false;
	}
	$page_id  = get_the_ID();
	$settings = wp_travel_engine_get_settings();
	if ( ( isset( $settings['dashboard_page_id'] ) && (int) $settings['dashboard_page_id'] === $page_id ) || wp_travel_engine_post_content_has_shortcode( 'wp_travel_engine_dashboard' ) ) {
		return true;
	}
	return false;
}

/**
 * Check whether page is thank you page or not.
 *
 * @return Boolean
 */
function wp_travel_engine_is_thank_you_page() {
	if ( is_admin() ) {
		return false;
	}
	$page_id  = get_the_ID();
	$settings = wp_travel_engine_get_settings();
	if ( ( isset( $settings['wp_travel_engine_thank_you'] ) && (int) $settings['wp_travel_engine_thank_you'] === $page_id ) || wp_travel_engine_post_content_has_shortcode( 'WP_TRAVEL_ENGINE_THANK_YOU' ) ) {
		return true;
	}
	return false;
}

if ( ! function_exists( 'wp_travel_engine_is_account_page' ) ) {

	/**
	 * wp_travel_engine_is_account_page - Returns true when viewing an account page.
	 *
	 * @return bool
	 */
	function wp_travel_engine_is_account_page() {
		return is_page( wp_travel_engine_get_dashboard_page_id() ) || wp_travel_engine_post_content_has_shortcode( 'wp_travel_engine_user_account' ) || apply_filters( 'wp_travel_engine_is_account_page', false );
	}
}

if ( ! function_exists( 'wp_travel_engine_is_checkout_page' ) ) {

	/**
	 * wp_travel_engine_is_checkout_page - Returns true when viewing an account page.
	 *
	 * @return bool
	 */
	function wp_travel_engine_is_checkout_page() {
		return is_page( wp_travel_engine_get_page_id( 'checkout' ) ) || wp_travel_engine_post_content_has_shortcode( 'WP_TRAVEL_ENGINE_PLACE_ORDER' ) || apply_filters( 'wp_travel_engine_is_checkout_page', false );
	}
}

/**
 * Create a page and store the ID in an option.
 *
 * @param mixed  $slug Slug for the new page
 * @param string $option Option name to store the page's ID
 * @param string $page_title (default: '') Title for the new page
 * @param string $page_content (default: '') Content for the new page
 * @param int    $post_parent (default: 0) Parent for the new page
 * @return int page ID
 */
function wp_travel_engine_create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
	global $wpdb;

	$option_value = get_option( $option );

	if ( $option_value > 0 && ( $page_object = get_post( $option_value ) ) ) {
		if ( 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ) ) ) {
			// Valid page is already in place
			if ( strlen( $page_content ) > 0 ) {
				// Search for an existing page with the specified page content (typically a shortcode)
				$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
			} else {
				// Search for an existing page with the specified page slug
				$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
			}

			$valid_page_found = apply_filters( 'wp_travel_engine_create_page_id', $valid_page_found, $slug, $page_content );

			if ( $valid_page_found ) {
				if ( $option ) {
					update_option( $option, $valid_page_found );
				}
				return $valid_page_found;
			}
		}
	}

	// Search for a matching valid trashed page
	if ( strlen( $page_content ) > 0 ) {
		// Search for an existing page with the specified page content (typically a shortcode)
		$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
	} else {
		// Search for an existing page with the specified page slug
		$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
	}

	if ( $trashed_page_found ) {
		$page_id   = $trashed_page_found;
		$page_data = array(
			'ID'          => $page_id,
			'post_status' => 'publish',
		);
		wp_update_post( $page_data );
	} else {
		$page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => $slug,
			'post_title'     => $page_title,
			'post_content'   => $page_content,
			'post_parent'    => $post_parent,
			'comment_status' => 'closed',
		);
		$page_id   = wp_insert_post( $page_data );
	}

	if ( $option ) {
		update_option( $option, $page_id );
	}

	return $page_id;
}

/**
 * Print success and error notices set by WP Travel Plugin.
 */
function wp_travel_engine_print_notices() {
	// Print Errors / Notices.
	WTE()->notices->print_notices( 'error', true );
	WTE()->notices->print_notices( 'success', true );
}

/**
 * Sort array by priority.
 *
 * @return array $array
 */
function wp_travel_engine_sort_array_by_priority( $array, $priority_key = 'priority' ) {
	$priority = array();
	if ( is_array( $array ) && count( $array ) > 0 ) {
		foreach ( $array as $key => $row ) {
			$priority[ $key ] = isset( $row[ $priority_key ] ) ? $row[ $priority_key ] : 1;
		}
		array_multisort( $priority, SORT_ASC, $array );
	}
	return $array;
}

/**
 * Retrieves unvalidated referer from '_wp_http_referer' or HTTP referer.
 *
 * Do not use for redirects, use {@see wp_get_referer()} instead.
 *
 * @since 1.3.3
 * @return string|false Referer URL on success, false on failure.
 */
function wp_travel_engine_get_raw_referer() {
	if ( function_exists( 'wp_get_raw_referer' ) ) {
		return wp_get_raw_referer();
	}

	if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
		return wp_unslash( $_REQUEST['_wp_http_referer'] );
	} elseif ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
		return wp_unslash( $_SERVER['HTTP_REFERER'] );
	}
}

function wte_get_active_single_trip_tabs() {
	global $post;

	$settings  = get_option( 'wp_travel_engine_settings', true );
	$post_meta = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );

	if ( ! isset( $settings['trip_tabs']['id'] ) || ! is_array( $settings['trip_tabs']['id'] ) ) {
		return false;
	}

	if ( empty( array_filter( $settings['trip_tabs']['id'] ) ) ) {
		return false;
	}

	if ( empty( $post_meta ) ) {
		return false;
	}

	foreach ( $settings['trip_tabs']['id'] as $key => $value ) {
		$enable = isset( $settings['trip_tabs']['enable'][ $value ] ) && ! empty( $settings['trip_tabs']['enable'][ $value ] ) ? $settings['trip_tabs']['enable'][ $value ] : 'yes';

		if ( 'no' === $enable ) {
			unset( $settings['trip_tabs']['id'][ $value ] );
			continue;
		}

		switch ( $settings['trip_tabs']['field'][ $value ] ) {
			case 'wp_editor':
				if ( ! isset( $post_meta['tab_content'][ $key . '_wpeditor' ] ) || empty( $post_meta['tab_content'][ $key . '_wpeditor' ] ) ) {
					unset( $settings['trip_tabs']['id'][ $value ] );
				}
				break;
			case 'itinerary':
				if ( empty( $post_meta['itinerary']['itinerary_title'] ) ) {
					unset( $settings['trip_tabs']['id'][ $value ] );
				}
				break;
			case 'cost':
				if ( empty( $post_meta['cost']['includes_title'] )
					&& empty( $post_meta['cost']['excludes_title'] ) ) {
					unset( $settings['trip_tabs']['id'][ $value ] );
				}
				break;
			case 'faqs':
				if ( empty( $post_meta['faq']['faq_title'] ) ) {
					unset( $settings['trip_tabs']['id'][ $value ] );
				}
				break;
			case 'review':
				if ( ! class_exists( 'Wte_Trip_Review_Init' )
				|| isset( $settings['trip_reviews']['hide'] ) ) {
					unset( $settings['trip_tabs']['id'][ $value ] );
				}
				break;
			case 'guides':
				if ( ! class_exists( 'WPTE_Guides_Profile_Init' ) ) {
					unset( $settings['trip_tabs']['id'][ $value ] );
				}
				break;
			case 'map':
				$map_image  = isset( $post_meta['map']['image_url'] ) && ! empty( $post_meta['map']['image_url'] ) ? true : false;
				$map_iframe = isset( $post_meta['map']['iframe'] ) && ! empty( $post_meta['map']['iframe'] ) ? true : false;
				if ( ! $map_image && ! $map_iframe ) {
					unset( $settings['trip_tabs']['id'][ $value ] );
				}
				break;
			case 'dates':
				$trip_id    = $post->ID;
				$active     = false;
				$fsd_active = apply_filters( 'wte_is_fsd_active_available', $active, $trip_id );
				if ( ! $fsd_active ) {
					unset( $settings['trip_tabs']['id'][ $value ] );
				}
				break;
		}
	}
	return $settings;
}

/**
 * Check if the current page is WP Travel page or not.
 *
 * @since Travel Muni release version
 * @return boolean
 */
function is_wte_archive_page() {

	if ( ( is_post_type_archive( 'trip' ) || is_tax( array( 'destination', 'activities', 'trip_types' ) ) ) && ! is_search() ) {
		return true;
	}
	return false;
}

/**
 * Check if trip is featured trip.
 *
 * @param [type] $trip_id
 * @return boolean
 */
function wte_is_trip_featured( $trip_id ) {
	if ( ! $trip_id ) {
		return false;
	}
	$featured = get_post_meta( $trip_id, 'wp_travel_engine_featured_trip', true );
	return ! empty( $featured ) && 'yes' === $featured;
}

/**
 * Get a list of featured trips id array.
 */
function wte_get_featured_trips_array() {
	$wte_global    = get_option( 'wp_travel_engine_settings', true );
	$feat_trip_num = isset( $wte_global['feat_trip_num'] ) ? $wte_global['feat_trip_num'] : 2;

	$args = array(
		'post_type'   => 'trip',
		'numberposts' => -1,
	);

	$term_id = get_queried_object_id();
	if ( $term_id ) {
		$taxonomyName      = get_term( $term_id )->taxonomy;
		$args['tax_query'] = array(
			array(
				'taxonomy' => $taxonomyName,
				'terms'    => $term_id,
			),
		);
	}

	$trips = get_posts( $args );

	$trips_array = array();
	foreach ( $trips as $trip ) {
		if ( wte_is_trip_featured( $trip->ID ) ) {
			$trips_array[] = $trip->ID;
		}
	}

	if ( ! empty( $trips_array ) ) {
		$trips_array = array_slice( $trips_array, 0, $feat_trip_num );
	}

	if ( ! empty( $trips_array ) && count( $trips_array ) == count( $trips ) ) {
		unset( $trips_array );
		$trips_array = array();
	}

	return apply_filters( 'wp_travel_engine_feat_trips_array', $trips_array, $args );
}

/**
 * Get information about available image sizes
 */
function wte_get_image_sizes( $size = '' ) {

	global $_wp_additional_image_sizes;

	$sizes                        = array();
	$get_intermediate_image_sizes = get_intermediate_image_sizes();

	// Create the full array with sizes and crop info
	foreach ( $get_intermediate_image_sizes as $_size ) {
		if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
			$sizes[ $_size ]['width']  = get_option( $_size . '_size_w' );
			$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
			$sizes[ $_size ]['crop']   = (bool) get_option( $_size . '_crop' );
		} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
			$sizes[ $_size ] = array(
				'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
				'height' => $_wp_additional_image_sizes[ $_size ]['height'],
				'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
			);
		}
	}
	// Get only 1 size if found
	if ( $size ) {
		if ( isset( $sizes[ $size ] ) ) {
			return $sizes[ $size ];
		} else {
			return false;
		}
	}
	return $sizes;
}

/**
 * Get Fallback SVG
 */
function wte_get_fallback_svg( $post_thumbnail, $dimension = false ) {
	if ( ! $post_thumbnail ) {
		return;
	}

	$image_size = array();

	if ( $dimension ) {
		$image_size['width']  = $post_thumbnail['width'];
		$image_size['height'] = $post_thumbnail['height'];
	} else {
		$image_size = wte_get_image_sizes( $post_thumbnail );
	}

	if ( $image_size ) {
		?>
		<div class="svg-holder">
				<svg class="fallback-svg" viewBox="0 0 <?php echo esc_attr( $image_size['width'] ); ?> <?php echo esc_attr( $image_size['height'] ); ?>" preserveAspectRatio="none">
					<rect width="<?php echo esc_attr( $image_size['width'] ); ?>" height="<?php echo esc_attr( $image_size['height'] ); ?>" style="fill:#f2f2f2;"></rect>
			</svg>
		</div>
		<?php
	}
}

/**
 * Get discount percent
 *
 * @param $trip_id
 */
function wte_get_discount_percent( $trip_id ) {
	if ( ! $trip_id ) {
		return false;
	}

	$trip_price = wp_travel_engine_get_prev_price( $trip_id );
	$on_sale    = wp_travel_engine_is_trip_on_sale( $trip_id );

	if ( $trip_price != '' && $on_sale ) {
		$sale_price       = wp_travel_engine_get_sale_price( $trip_id );
		$discount_percent = ( ( $trip_price - $sale_price ) * 100 ) / $trip_price;
		return round( $discount_percent );
	}
	return false;
}

/**
 * Send new account notification to users.
 */
function wp_travel_engine_user_new_account_created( $customer_id, $new_customer_data, $password_generated, $template ) {

	// Send email notification.
	$email_content = wte_get_template_html(
		$template,
		array(
			'user_login'         => $new_customer_data['user_login'],
			'user_pass'          => $new_customer_data['user_pass'],
			'blogname'           => get_bloginfo( 'name' ),
			'password_generated' => $password_generated,
		)
	);

	// To send HTML mail, the Content-type header must be set.
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	$from = get_option( 'admin_email' );
	// Create email headers.
	$headers .= 'From: ' . $from . "\r\n";
	$headers .= 'Reply-To: ' . $from . "\r\n" .
	'X-Mailer: PHP/' . phpversion();

	if ( $new_customer_data['user_login'] ) {
		$user_object     = get_user_by( 'login', $new_customer_data['user_login'] );
		$user_user_login = $new_customer_data['user_login'];
		$user_user_email = stripslashes( $user_object->user_email );
		$user_recipient  = $user_user_email;
		$user_subject    = __( 'New Account Created', 'wp-travel-engine' );

		$mail_sent = wp_mail( $user_recipient, $user_subject, $email_content, $headers );

		if ( ! $mail_sent ) {
			return false;
		}
	}
}

add_action( 'wp_travel_engine_created_customer', 'wp_travel_engine_user_new_account_created', 20, 4 );

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $var Data to sanitize.
 * @return string|array
 */
function wp_travel_engine_clean_vars( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'wp_travel_engine_clean_vars', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

/**
 * Add notices for WP Errors.
 *
 * @param WP_Error $errors Errors.
 */
function wp_travel_engine_add_wp_error_notices( $errors ) {
	if ( is_wp_error( $errors ) && $errors->get_error_messages() ) {
		foreach ( $errors->get_error_messages() as $error ) {
			WP_Travel()->notices->add( $error, 'error' );
		}
	}
}
/**
 * Get the count of notices added, either for all notices (default) or for one.
 * particular notice type specified by $notice_type.
 *
 * @param  string $notice_type Optional. The name of the notice type - either error, success or notice.
 * @return int
 */
function wp_travel_engine_get_notice_count( $notice_type = '' ) {

	$notice_count = 0;
	$all_notices  = WTE()->notices->get( $notice_type, false );

	if ( ! empty( $all_notices ) && is_array( $all_notices ) ) {

		foreach ( $all_notices as $key => $notices ) {
			$notice_count++;
		}
	}

	return $notice_count;
}
/*
 * get term lists.
 *
 * @param [type] $id
 * @param [type] $taxonomy
 * @param string $before
 * @param string $sep
 * @param string $after
 * @return void
 */
function wte_get_the_tax_term_list( $id, $taxonomy, $before = '', $sep = '', $after = '', $nofollow = false ) {

	$terms = get_the_terms( $id, $taxonomy );

	if ( is_wp_error( $terms ) ) {
		return $terms;
	}

	if ( empty( $terms ) ) {
		return false;
	}

	$nof_attr = $nofollow ? 'rel=nofollow' : 'rel=tag';
	$target   = $nofollow ? '_blank' : '_self';

	$links = array();

	foreach ( $terms as $term ) {
		$link = get_term_link( $term, $taxonomy );
		if ( is_wp_error( $link ) ) {
			return $link;
		}
		$links[] = '<a ' . esc_attr( $nof_attr ) . ' target="' . esc_attr( $target ) . '" href="' . esc_url( $link ) . '" >' . $term->name . '</a>';
	}

	/**
	 * Filters the term links for a given taxonomy.
	 *
	 * The dynamic portion of the filter name, `$taxonomy`, refers
	 * to the taxonomy slug.
	 *
	 * @since 2.5.0
	 *
	 * @param string[] $links An array of term links.
	 */
	$term_links = apply_filters( "term_links-{$taxonomy}", $links );  // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

	return $before . join( $sep, $term_links ) . $after;
}

function wte_get_trip_details( $trip_id ) {
	if ( ! $trip_id ) {
		return false;
	}

	$trip_settings = wp_travel_engine_get_trip_metas( $trip_id );
	$wte_global    = get_option( 'wp_travel_engine_settings', true );
	$code          = wp_travel_engine_get_currency_code();
	$destinations  = wte_get_the_tax_term_list( $trip_id, 'destination', '', ', ', '' );
	$destination   = '';

	if ( ! empty( $destinations ) && ! is_wp_error( $destinations ) ) {
		$destination = $destinations;
	}

	$group_discount = wte_is_group_discount_enabled( $trip_id );

	$show_excerpt = isset( $wte_global['show_excerpt'] ) && '' != $wte_global['show_excerpt'] ? true : false;
	$dates_layout = isset( $wte_global['fsd_dates_layout'] ) && '' != $wte_global['fsd_dates_layout']
	? $wte_global['fsd_dates_layout'] : 'dates_list';

	$details = array(
		'trip_settings'        => $trip_settings,
		'code'                 => $code,
		'currency'             => wp_travel_engine_get_currency_symbol( $code ),
		'trip_price'           => wp_travel_engine_get_prev_price( $trip_id ),
		'on_sale'              => wp_travel_engine_is_trip_on_sale( $trip_id ),
		'sale_price'           => wp_travel_engine_get_sale_price( $trip_id ),
		'display_price'        => wp_travel_engine_get_actual_trip_price( $trip_id ),
		'discount_percent'     => wte_get_discount_percent( $trip_id ),
		'destination'          => $destination,
		'group_discount'       => $group_discount,
		'show_excerpt'         => $show_excerpt,
		'dates_layout'         => $dates_layout,
		'trip_duration'        => isset( $trip_settings['trip_duration'] ) && ! empty( $trip_settings['trip_duration'] )
		? $trip_settings['trip_duration'] : false,
		'trip_duration_nights' => isset( $trip_settings['trip_duration_nights'] ) && ! empty( $trip_settings['trip_duration_nights'] )
		? $trip_settings['trip_duration_nights'] : false,
	);

	return $details;
}

/**
 * Check if group discount is enabled or not.
 *
 * @return boolean
 */
function wte_is_group_discount_enabled( $trip_id ) {
	if ( ! $trip_id ) {
		return false;
	}

	$trip_settings = wp_travel_engine_get_trip_metas( $trip_id );
	$wte_global    = get_option( 'wp_travel_engine_settings', true );

	if ( class_exists( 'Wp_Travel_Engine_Group_Discount' ) ) {
		$adult_gd_enable  = isset( $trip_settings['group']['discount'] ) && 1 == $trip_settings['group']['discount'] ? true : false;
		$child_gd_enable  = isset( $trip_settings['child-group']['discount'] ) && 1 == $trip_settings['child-group']['discount'] ? true : false;
		$infant_gd_enable = isset( $trip_settings['infant-group']['discount'] ) && 1 == $trip_settings['infant-group']['discount'] ? true : false;
	}

	$group_discount = class_exists( 'Wp_Travel_Engine_Group_Discount' ) && isset( $wte_global['group']['discount'] )
	&& ( $adult_gd_enable || $child_gd_enable || $infant_gd_enable ) ? true : false;

	return $group_discount;
}

/**
 * Get months array with name and code.
 *
 * @return void
 */
function wp_travel_engine_get_months_array() {
	$months = array(
		'01' => 'Jan',
		'02' => 'Feb',
		'03' => 'Mar',
		'04' => 'Apr',
		'05' => 'May',
		'06' => 'Jun',
		'07' => 'Jul',
		'08' => 'Aug',
		'09' => 'Sep',
		'10' => 'Oct',
		'11' => 'Nov',
		'12' => 'Dec',
	);

	$months = array_map(
		function( $mon ) {
			return date_i18n( 'M', strtotime( $mon ) );
		},
		$months
	);

	return apply_filters( 'wp_travel_engine_months_array', $months );
}

/**
 * check if trip has reviews
 *
 * @param [type] $type
 * @param [type] $post_id
 * @return void
 */
function wp_travel_engine_trip_has_reviews( $post_id ) {

	if ( ! $post_id ) {
		return false;
	}

	$comments = get_comments(
		array(
			'post_id' => $post_id,
			'count'   => true,
		)
	);

	return 0 < $comments;

}
/**
 * Format date string as per get_option( 'date_format' )
 *
 * @param [type] $date_string
 * @return [string] $formated_date
 */
function wte_get_formated_date( $date_string ) {
	$date_format = get_option( 'date_format' ) ? get_option( 'date_format' ) : 'Y m d';

	if ( empty( $date_string ) ) {
		return false;
	}

	$date = strtotime( $date_string );

	return date_i18n( $date_format, $date );
}

/**
 * Get Human redable Time diff / Date with default date format.
 *
 * @param [type] $timestamp
 * @return void
 */
function wte_get_human_readable_diff_post_published_date( $post_id ) {
	if ( ! $post_id ) {
		return '&ndash;';
	}

	$timestamp = get_post_time( $format = 'U', $gmt = false, $post_id, $translate = false ) ? get_post_time( $format = 'U', $gmt = false, $post_id, $translate = false ) : '';

	// Check if the order was created within the last 24 hours, and not in the future.
	if ( $timestamp > strtotime( '-1 day', time() ) && $timestamp <= time() ) {
		$show_date = sprintf(
			/* translators: %s: human-readable time difference */
			_x( '%s ago', '%s = human-readable time difference', 'wp-travel-engine' ),
			human_time_diff( $timestamp, time() )
		);
	} else {
		$show_date = get_the_date( get_option( 'date_format' ), $post_id );
	}
	return sprintf(
		'<time datetime="%1$s">%2$s</time>',
		esc_attr( get_the_date( 'c', $post_id ) ),
		esc_html( $show_date )
	);
}

/**
 * Trip cutoff array
 *
 * @param [type] $post_id
 * @return void
 */
function wpte_get_booking_cutoff( $post_id ) {

	$cutoff_array = array(
		'enable' => false,
		'cutoff' => 0,
		'unit'   => 'days',
	);

	if ( ! $post_id ) {
		return $cutoff_array;
	}

	$post_metas = get_post_meta( $post_id, 'wp_travel_engine_setting', true );

	if ( empty( $post_metas ) || ! isset( $post_metas['trip_cutoff_enable'] ) ) {
		return $cutoff_array;
	}

	$cutoff_array = array(
		'enable' => true,
		'cutoff' => isset( $post_metas['trip_cut_off_time'] ) && ! empty( $post_metas['trip_cut_off_time'] ) ? $post_metas['trip_cut_off_time'] : 0,
		'unit'   => isset( $post_metas['trip_cut_off_unit'] ) && ! empty( $post_metas['trip_cut_off_unit'] ) ? $post_metas['trip_cut_off_unit'] : 'days',
	);

	return $cutoff_array;
}

// Tabs filter to support custom tabs.
add_filter( 'wp_travel_engine_admin_trip_meta_tabs', 'wpte_add_custom_tabs_to_trip_meta' );

/**
 * Get custom tabs array.
 *
 * @return void
 */
function wpte_add_custom_tabs_to_trip_meta( $trip_meta_tabs ) {
	$default_tabs = wte_get_default_settings_tab();
	$settings     = get_option( 'wp_travel_engine_settings', true );

	$def_tabs = array(
		'2' => 'itinerary',
		'3' => 'cost',
		'4' => 'dates',
		'5' => 'faqs',
		'6' => 'map',
	);

	if ( empty( $settings ) || ! isset( $settings['trip_tabs']['id'] ) ) {
		return $trip_meta_tabs;
	}

	$priority = 50;
	foreach ( $settings['trip_tabs']['id'] as $key => $value ) {

		$field = $settings['trip_tabs']['field'][ $value ];

		if ( '1' === $value || in_array( $field, $def_tabs ) ) {
			continue;
		}

		if ( 'review' === $field ) {
			continue;
		}

		$tab_label   = isset( $settings['trip_tabs']['name'][ $value ] ) && ! empty( $settings['trip_tabs']['name'][ $value ] ) ? $settings['trip_tabs']['name'][ $value ] : __( 'Custom Tab', 'wp-travel-engine' );
		$tab_content = isset( $wp_travel_engine_setting['tab_content'][ $value . '_wpeditor' ] ) ? $wp_travel_engine_setting['tab_content'][ $value . '_wpeditor' ] : '';

		$trip_meta_tabs[ 'wp_editor_tab_' . $value ] = array(
			'tab_label'         => $tab_label,
			'tab_heading'       => $tab_label,
			'content_path'      => plugin_dir_path( WP_TRAVEL_ENGINE_FILE_PATH ) . '/admin/meta-parts/trip-tabs/custom-tabs.php',
			'callback_function' => 'wpte_tab_' . $key,
			'content_key'       => 'wp_editor_tab_' . $value,
			'tab_key'           => $value,
			'current'           => false,
			'content_loaded'    => true,
			'priority'          => $priority,
		);
		$priority++;
	}

	return $trip_meta_tabs;
}

/**
 * Get Booking Status List.
 *
 * @since 1.0.5
 */
function wp_travel_engine_get_booking_status() {
	$status = array(
		'pending'  => array(
			'color' => '#FF9800',
			'text'  => __( 'Pending', 'wp-travel-engine' ),
		),
		'booked'   => array(
			'color' => '#008600',
			'text'  => __( 'Booked', 'wp-travel-engine' ),
		),
		'refunded' => array(
			'color' => '#FE450E',
			'text'  => __( 'Refunded', 'wp-travel-engine' ),
		),
		'canceled' => array(
			'color' => '#FE450E',
			'text'  => __( 'Canceled', 'wp-travel-engine' ),
		),
		'N/A'      => array(
			'color' => '#892E2C',
			'text'  => __( 'N/A', 'wp-travel-engine' ),
		),
	);

	return apply_filters( 'wp_travel_engine_booking_status_list', $status );
}

/**
 * Check if currency is supported by the Paypal Gateway
 * Currently supports 26 currencies
 *
 * @param [type] $currency
 * @return void
 */
function wp_travel_engine_paypal_supported_currencies( $currency ) {
	if ( ! $currency ) {
		return;
	}
	$settings                  = get_option( 'wp_travel_engine_settings' );
	$currency_option           = isset( $settings['currency_option'] ) && $settings['currency_option'] != '' ? esc_attr( $settings['currency_option'] ) : 'symbol';
	$supported_paypal_currency = apply_filters(
		'wp_travel_engine_filter_paypal_supported_currencies',
		array(
			'AUD' => '&#36;', // Australian Dollar
			'BRL' => '&#82;&#36;', // Brazilian real
			'CAD' => '&#36;', // Canadian dollar
			'CNY' => '&yen;', // Chinese Renmenbi
			'CZK' => '&#75;&#269;', // Czech koruna
			'DKK' => 'DKK', // Danish krone
			'EUR' => '&euro;', // Euro
			'HKD' => '&#36;', // Hong Kong dollar
			'HUF' => '&#70;&#116;', // Hungarian forint
			'INR' => '&#8377;', // Indian rupee
			'ILS' => '&#8362;', // Israeli
			'JPY' => '&yen;', // Japanese yen
			'MYR' => '&#82;&#77;', // Malaysian ringgit
			'MXN' => '&#36;', // Mexican peso
			'TWD' => '&#78;&#84;&#36;', // New Taiwan dollar
			'NZD' => '&#36;', // New Zealand dollar
			'NOK' => '&#107;&#114;', // Norwegian krone
			'PHP' => '&#8369;', // Philippine peso
			'PLN' => '&#122;&#322;', // Polish złoty
			'GBP' => '&pound;', // Pound sterling
			'RUB' => '&#8381;', // Russian ruble
			'SGD' => '&#36;', // Singapore dollar
			'SEK' => '&#107;&#114;', // Swedish krona
			'CHF' => '&#67;&#72;&#70;', // New Zealand dollar
			'THB' => '&#3647;', // Thai baht
			'USD' => '&#36;', // United States dollar
		)
	);

	// if( isset( $currency_option ) && $currency_option == 'code'):
	// $return = array_key_exists($currency, $supported_paypal_currency)?true:false;
	// else:
	// $obj    = new Wp_Travel_Engine_Functions();
	// $return = $obj->in_multi_array($currency, $supported_paypal_currency);
	// endif;
	$return = array_key_exists( $currency, $supported_paypal_currency ) ? true : false;
	return $return;
}
