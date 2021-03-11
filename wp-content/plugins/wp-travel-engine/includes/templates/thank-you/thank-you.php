<?php
/**
 * Thank you page template.
 */
global $wte_cart;
$validated_request = true;

$wte_options               = get_option( 'wp_travel_engine_settings', true );
$wp_travel_engine_thankyou = isset( $wte_options['pages']['wp_travel_engine_thank_you'] ) ? esc_attr( $wte_options['pages']['wp_travel_engine_thank_you'] ) : '';

if ( ! empty( $wp_travel_engine_thankyou ) && isset( $wte_options['travelers_information'] ) && 'no' === $wte_options['travelers_information'] ) :

	if ( ! isset( $_POST['wp-travel-engine-confirmation-submit'] ) ) {
		$validated_request = false;
	}

	if ( ! isset( $_POST['wp_travel_engine_placeorder_setting'] ) || empty( $_POST['wp_travel_engine_placeorder_setting'] ) ) {
		$validated_request = false;
	}

	if ( ! isset( $_POST['nonce'] ) || empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp_travel_engine_final_confirmation_nonce' ) ) {
		$validated_request = false;
	}
endif;

if ( empty( $wte_cart->getItems() ) ) {
	$validated_request = false;
}

if ( ( isset( $_GET['booking_id'] ) && ! empty( $_GET['booking_id'] ) ) && ( isset( $_GET['redirect_type'] ) && ! empty( $_GET['redirect_type'] ) && $_GET['redirect_type'] == 'partial-payment' ) ) :
	wte_get_template(
		'thank-you/thankyou-remaining-payment.php',
		array(
			'booking' => $_GET['booking_id'],
		)
	);
elseif ( ( isset( $_GET['booking_id'] ) && ! empty( $_GET['booking_id'] ) && $validated_request ) ) :
	$booking_id = $wte_cart->get_attribute( 'booking_id' );
	if ( ! empty( $booking_id ) ) {
		$_GET['booking_id'] = $booking_id;
	}
	$booking_id        = $_GET['booking_id'];
	$booking_post_type = get_post_type( $_GET['booking_id'] );

	if ( 'booking' === $booking_post_type ) :
		if ( isset( $_POST['wp_travel_engine_placeorder_setting'] ) ) :
			// Update hook for addons
			do_action( 'wp_travel_engine_before_traveller_information_save' );

			// Update travellers information to booking id.
			update_post_meta( $booking_id, 'wp_travel_engine_placeorder_setting', stripslashes_deep( $_POST['wp_travel_engine_placeorder_setting'] ) );

			// Update hook for addons
			do_action( 'wp_travel_engine_after_traveller_information_save' );
		endif;

		// Show thank you HTML.
		wte_get_template( 'template-booking-thank-you.php' );

	else :

		$thank_page_msg = __( 'Invalid booking id supplied.', 'wp-travel-engine' );

		echo wp_kses_post( $thank_page_msg );

	endif;

else :

	$thank_page_msg = __( 'Sorry, you may not have confirmed your booking. Please fill up the form and confirm your booking. Thank you.', 'wp-travel-engine' );

	$thank_page_error = apply_filters( 'wp_travel_engine_thankyou_page_error_msg', $thank_page_msg );

	echo wp_kses_post( $thank_page_error );

endif;
