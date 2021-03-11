<?php
/**
 * Traveller's Information Template.
 *
 * @package WP_Travel_Engine.
 */
if ( wte_array_get( $_REQUEST, '_action', '' ) === 'thankyou' ) {
	$data = WTE_Booking::get_callback_token_payload( 'thankyou' );

	if ( ! $data ) {
		return __( 'Thank you for booking the trip. Please check your email for confirmation.', 'wp-travel-engine' );
	}

	if ( is_array( $data ) && isset( $data['bid'] ) ) {
		$booking_id = $data['bid'];
		$payment_id = $data['pid'];
	}

	$booking     = get_post( $booking_id );
	$order_trips = $booking->{'order_trips'};
	$order_trip  = is_array( $order_trips ) ? array_shift( $order_trips ) : array();
	if ( empty( $order_trip ) ) {
		return;
	}
	$total_pax = ( isset( $order_trip['pax'] ) ) ? array_sum( $order_trip['pax'] ) : 0;
}

global $wte_cart;

if ( isset( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['subscribe'] ) && $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['subscribe'] == '1' ) {
	$myvar = $_POST;
	$obj   = new Wte_Mailchimp_Main();
	$new   = $obj->wte_mailchimp_action( $myvar );
}
if ( isset( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['mailerlite'] ) && $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['mailerlite'] == '1' ) {
	$myvar = $_POST;
	$obj   = new Wte_Mailerlite_Main();
	$new   = $obj->wte_mailerlite_action( $myvar );
}
if ( isset( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['convertkit'] ) && $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['convertkit'] == '1' ) {
	$myvar = $_POST;
	$obj   = new Wte_Convertkit_Main();
	$new   = $obj->wte_convertkit_action( $myvar );
}

$options                   = get_option( 'wp_travel_engine_settings', true );
$wp_travel_engine_thankyou = isset( $options['pages']['wp_travel_engine_thank_you'] ) ? esc_attr( $options['pages']['wp_travel_engine_thank_you'] ) : '';

$wp_travel_engine_thankyou = ! empty( $wp_travel_engine_thankyou ) ? get_permalink( $wp_travel_engine_thankyou ) : home_url( '/' );

if ( isset( $_GET['booking_id'] ) && ! empty( $_GET['booking_id'] ) ) :
	$wp_travel_engine_thankyou = add_query_arg( 'booking_id', $_GET['booking_id'], $wp_travel_engine_thankyou );
		endif;
if ( isset( $_GET['redirect_type'] ) && ! empty( $_GET['redirect_type'] ) ) :
	$wp_travel_engine_thankyou = add_query_arg( 'redirect_type', $_GET['redirect_type'], $wp_travel_engine_thankyou );
		endif;
if ( isset( $_GET['wte_gateway'] ) && ! empty( $_GET['wte_gateway'] ) ) :
	$wp_travel_engine_thankyou = add_query_arg( 'wte_gateway', $_GET['wte_gateway'], $wp_travel_engine_thankyou );
		endif;
if ( isset( $_GET['status'] ) && ! empty( $_GET['status'] ) ) :
	$wp_travel_engine_thankyou = add_query_arg( 'status', $_GET['status'], $wp_travel_engine_thankyou );
		endif;
?>
		<form method="post" id="wp-travel-engine-order-form" action="">
		<?php
		if ( isset( $_GET['wte_gateway'] ) && 'paypal' === $_GET['wte_gateway'] ) {
			do_action( 'wp_travel_engine_verify_paypal_ipn' );
		}

		$hide_traveller_info = isset( $options['travelers_information'] ) ? $options['travelers_information'] : 'yes';

		if ( 'yes' === $hide_traveller_info || '1' === $hide_traveller_info ) {
			if ( isset( $_POST ) ) {
				$error_found = false;

				// Some input field checking
				if ( $error_found == false ) {
					// Use the wp redirect function
					wp_redirect( $wp_travel_engine_thankyou );
				} else {
					// Some errors were found, so let's output the header since we are staying on this page
					if ( isset( $_GET['noheader'] ) ) {
						require_once ABSPATH . 'wp-admin/admin-header.php';
					}
				}
			}
		}

			require_once WP_TRAVEL_ENGINE_ABSPATH . '/includes/lib/wte-form-framework/class-wte-form.php';

		// 	$cart_items = $wte_cart->getItems();

		// if ( is_array( $cart_items ) ) {
		// 	foreach ( $cart_items as $key => $item ) {
		// 		$pax       = array_sum( $item['pax'] );
		// 		$total_pax = absint( $total_pax + $pax );
		// 	}
		// }


			$form_fields = new WP_Travel_Engine_Form_Field();

			$traveller_fields = WTE_Default_Form_Fields::traveller_information();
			$traveller_fields = apply_filters( 'wp_travel_engine_traveller_info_fields_display', $traveller_fields );

			$emergency_contact_fields = WTE_Default_Form_Fields::emergency_contact();
			$emergency_contact_fields = apply_filters( 'wp_travel_engine_emergency_contact_fields_display', $emergency_contact_fields );

			$wp_travel_engine_settings_options = get_option( 'wp_travel_engine_settings', true );

		for ( $i = 1; $i <= $total_pax; $i++ ) {
			echo '<div class="relation-options-title">' . sprintf( __( 'Personal details for Traveler: #%1$s', 'wp-travel-engine' ), $i ) . '</div>';

			$modified_traveller_fields = array_map(
				function( $field ) use ( $i ) {
					if ( strpos( $field['name'], 'wp_travel_engine_placeorder_setting[place_order][travelers]' ) !== false ) {
							  $field['name'] = sprintf( '%s[%d]', $field['name'], $i );
					} else {
						   $field['name'] = sprintf( 'wp_travel_engine_placeorder_setting[place_order][travelers][%s][%d]', $field['name'], $i );
					}
					$field['id']            = sprintf( '%s[%d]', $field['id'], $i );
					$field['wrapper_class'] = 'wp-travel-engine-personal-details';
					return $field;
				},
				$traveller_fields
			);

			$form_fields->init( $modified_traveller_fields )->render();

			if ( ! isset( $wp_travel_engine_settings_options['emergency'] ) ) {
				echo '<div class="relation-options-title">' . sprintf( __( 'Emergency contact details for Traveler: #%1$s', 'wp-travel-engine' ), $i ) . '</div>';

				$modified_emergency_contact_fields = array_map(
					function( $field ) use ( $i ) {
						if ( strpos( $field['name'], 'wp_travel_engine_placeorder_setting[place_order][relation]' ) !== false ) {
								  $field['name'] = sprintf( '%s[%d]', $field['name'], $i );
						} else {
							   $field['name'] = sprintf( 'wp_travel_engine_placeorder_setting[place_order][relation][%s][%d]', $field['name'], $i );
						}
						$field['id']            = sprintf( '%s[%d]', $field['id'], $i );
						$field['wrapper_class'] = 'wp-travel-engine-personal-details';
						return $field;
					},
					$emergency_contact_fields
				);

				$form_fields->init( $modified_emergency_contact_fields )->render();
			}
		}
			$nonce = wp_create_nonce( 'wp_travel_engine_final_confirmation_nonce' );
		?>
			<input type="hidden" name="nonce" value="<?php echo $nonce; ?>">
			<input type="submit" name="wp-travel-engine-confirmation-submit" value="<?php _e( 'Confirm Booking', 'wp-travel-engine' ); ?>">
		</form>
<?php
