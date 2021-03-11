<?php
/**
 * Booking Tab.
 *
 * @package wp-travel-engine/includes/templates/account/tab-content/
 */
$bookings = $args['bookings'];

global $wp, $wte_cart;
wp_enqueue_style( 'magnific-popup' );
wp_enqueue_script( 'magnific-popup' );
?>
<header class="wpte-lrf-header">
	<?php
	if ( ! empty( $bookings ) && isset( $_GET['action'] ) && $_GET['action'] == 'partial-payment' ) :
		?>
	<h2 class="wpte-lrf-title"><?php _e( 'Remaining Booking Payment', 'wp-travel-engine' ); ?></h2>
	<div class="wpte-lrf-description">
		<p><?php _e( 'Please pay the remaining partial payment amount from below. If you have any issue, please contact us.', 'wp-travel-engine' ); ?>
		</p>
	</div>
	<?php else : ?>
	<h2 class="wpte-lrf-title"><?php _e( 'Bookings', 'wp-travel-engine' ); ?></h2>
	<div class="wpte-lrf-description">
		<p><?php _e( 'Here is the list of bookings made successfully with your user account.', 'wp-travel-engine' ); ?>
		</p>
	</div>
	<?php endif; ?>
</header>
<div class="wpte-lrf-block-wrap">
	<div class="wpte-lrf-block">
		<?php
		if ( ! empty( $bookings ) && isset( $_GET['action'] ) && $_GET['action'] == 'partial-payment' ) :
			$booking = isset( $_GET['booking_id'] ) && ! empty( $_GET['booking_id'] ) ? sanitize_text_field( intval( $_GET['booking_id'] ) ) : '';
			wte_get_template(
				'account/remaining-payment.php',
				array(
					'booking' => $booking,
				)
			);
		elseif ( ! empty( $bookings ) && ! isset( $_GET['action'] ) ) :
			?>
			<table class="wpte-lrf-table">
				<?php
				foreach ( $bookings as $key => $booking ) :
					$booking_object = get_post( $booking );
					if ( is_null( $booking_object ) || 'booking' !== $booking_object->post_type || 'publish' !== $booking_object->post_status ) {
						continue;
					}
					$booking_metas    = get_post_meta( $booking, 'wp_travel_engine_booking_setting', true );
					$booking_meta     = booking_meta_details( $booking );
					$payment_status   = get_post_meta( $booking, 'wp_travel_engine_booking_payment_status', true );
					$show_pay_now_btn = ! 1;

					// Legacy Booking Support.
					if ( empty( $booking_object->payments ) ) {
						// @TODO: Remove Later.
						$show_pay_now_btn = ( $payment_status == 'partially-paid' || $booking_meta['remaining_payment'] > 0 ) && ! empty( $active_payment_methods );
						$total_paid       = wte_array_get( $booking_metas, 'place_order.cost', 0 );
						$due              = wte_array_get( $booking_metas, 'place_order.due', 0 );
					} else {
						if ( +$booking_object->due_amount > 0 ) {
							$show_pay_now_btn = +$booking_object->due_amount > 0;
						}
						$total_paid = $booking_object->paid_amount;
						$due        = $booking_object->due_amount;
					}

					$order_trips = $booking_object->order_trips;

					$booked_trip = is_array( $order_trips ) ? array_shift( $order_trips ) : array(); // Taking single trip incase mulitple booked.

					if ( empty( $booked_trip ) ) {
						continue;
					}

					$booked_trip = (object) $booked_trip;

					$active_payment_methods = wp_travel_engine_get_active_payment_gateways();

					if ( ! empty( $booking_metas ) ) {
						if ( ! $payment_status ) {
							$payment_status = __( 'pending', 'wp-travel-engine' );
						}

						$billing_info = $booking_object->billing_info;
						?>
						<tr>
							<th><?php echo $booked_trip->title; ?></th>
							<td>
								<span class="lrf-td-title"><?php _e( 'Departure', 'wp-travel-engine' ); ?></span>
								<span class="lrf-td-desc"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $booked_trip->datetime ) ) ); ?></span>
							</td>
							<td>
								<span class="lrf-td-title"><?php _e( 'Booking Status', 'wp-travel-engine' ); ?></span>
								<span class="lrf-td-desc"><?php _e( 'Booked', 'wp-travel-engine' ); ?></span>
							</td>
							<td>
								<span class="lrf-td-title"><?php _e( 'Payment Status', 'wp-travel-engine' ); ?></span>
								<span class="lrf-td-desc"><?php echo esc_html( $payment_status ); ?></span>
							</td>
							<td>
								<span class="lrf-td-title"><?php _e( 'Total', 'wp-travel-engine' ); ?></span>
								<span
									class="lrf-td-desc"><?php echo wp_travel_engine_get_formated_price_with_currency_code_symbol( $booking_object->cart_info['total'] ); ?></span>
							</td>
							<td>
								<span class="lrf-td-title"><?php _e( 'Paid', 'wp-travel-engine' ); ?></span>
								<span
									class="lrf-td-desc"><?php echo wp_travel_engine_get_formated_price_with_currency_code_symbol( $total_paid ); ?></span>
							</td>
							<td>
								<span class="lrf-td-title"><?php _e( 'Due', 'wp-travel-engine' ); ?></span>
								<span
									class="lrf-td-desc"><?php echo wp_travel_engine_get_formated_price_with_currency_code_symbol( $due ); ?></span>
							</td>
							<td>
								<a class="wpte-magnific-popup wpte-lrf-btn-transparent"
									href="#popup-content-<?php echo esc_attr( $booking ); ?>"><?php _e( 'View Detail', 'wp-travel-engine' ); ?></a>
							<?php if ( $show_pay_now_btn ) { ?>
								<a class="wpte-lrf-btn-transparent"
									href="<?php echo get_the_permalink() . '?action=partial-payment&booking_id=' . $booking . '"'; ?>"><?php _e( 'Pay Now', 'wp-travel-engine' ); ?></a>
								<?php } ?>
							</td>
						</tr>
						<div id="popup-content-<?php echo esc_attr( $booking ); ?>" class="white-popup mfp-hide">
							<h5><?php echo sprintf( __( 'Booking Details #%1$s', 'wp-travel-engine' ), $booking ); ?></h5>
							<h6><?php _e( 'Trip Information', 'wp-travel-engine' ); ?></h6>
							<span>
							<?php _e( 'Trip Name:', 'wp-travel-engine' ); ?>
							</span>
							<span>
							<?php echo esc_html( $booked_trip->title ); ?>
							</span>
							<br>
							<span>
							<?php _e( 'Trip Starting Date:', 'wp-travel-engine' ); ?>
							</span>
							<span>
							<?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $booking_meta['trip_start_date'] ) ) ); ?>
							</span>
							<br>
							<span>
							<?php _e( 'Travellers:', 'wp-travel-engine' ); ?>
							</span>
							<span>
							<?php echo esc_html( $booking_meta['booked_travellers'] ); ?>
							</span>
							<br>
							<span>
							<?php _e( 'Total Paid:', 'wp-travel-engine' ); ?>
							</span>
							<span>
							<?php echo wp_travel_engine_get_formated_price_with_currency_code_symbol( $total_paid ); ?>
							</span>
							<br />
							<span>
							<?php _e( 'Due:', 'wp-travel-engine' ); ?>
							</span>
							<span>
							<?php echo wp_travel_engine_get_formated_price_with_currency_code_symbol( $due ); ?>
							</span>
							<br />
							<span>
							<?php _e( 'Total Cost:', 'wp-travel-engine' ); ?>
							</span>
							<span>
							<?php echo wp_travel_engine_get_formated_price_with_currency_code_symbol( $booking_object->cart_info['total'] ); ?>
							</span>
							<h6><?php _e( 'Billing Information', 'wp-travel-engine' ); ?></h6>
							<span>
							<?php _e( 'First Name:', 'wp-travel-engine' ); ?>
							</span>
							<span>
							<?php echo esc_html( wte_array_get( $billing_info, 'fname', '' ) ); ?>
							</span>
							<br>
							<span>
							<?php _e( 'Last Name:', 'wp-travel-engine' ); ?>
							</span>
							<span>
							<?php echo esc_html( wte_array_get( $billing_info, 'lname', '' ) ); ?>
							</span>
							<br>
							<span>
							<?php _e( 'Email:', 'wp-travel-engine' ); ?>
							</span>
							<span>
							<?php echo esc_html( wte_array_get( $billing_info, 'email', '' ) ); ?>
							</span>
							<br>
							<span>
							<?php _e( 'Address:', 'wp-travel-engine' ); ?>
							</span>
							<span>
							<?php echo esc_html( wte_array_get( $billing_info, 'address', '' ) ); ?>
							</span>
							<br>
							<span>
							<?php _e( 'City:', 'wp-travel-engine' ); ?>
							</span>
							<span>
							<?php echo esc_html( wte_array_get( $billing_info, 'city', '' ) ); ?>
							</span>
							<br>
							<span>
							<?php _e( 'Country:', 'wp-travel-engine' ); ?>
							</span>
							<span>
							<?php echo esc_html( wte_array_get( $billing_info, 'country', '' ) ); ?>
							</span>
						</div>
						<?php
					}
					endforeach;
				?>
			</table>
			<?php
			else :
				_e( 'You have not made any bookings yet. Book Trips and it will be listed here', 'wp-travel-engine' );
		endif;
			?>
		</table>
		<div class="wpte-lrf-btn-wrap">
			<a target="_blank" class="wpte-lrf-btn"
				href="<?php echo esc_url( get_post_type_archive_link( 'trip' ) ); ?>"><?php _e( 'Book More Trips', 'wp-travel-engine' ); ?></a>
			<?php
			$user_account_page_id = wp_travel_engine_get_dashboard_page_id();
			if ( ! empty( $bookings ) && isset( $_GET['action'] ) && $_GET['action'] == 'partial-payment' ) :
				if ( ! empty( $user_account_page_id ) ) :
					?>
			<a class="wpte-lrf-btn"
				href="<?php echo esc_url( get_permalink( $user_account_page_id ) ); ?>"><?php _e( 'Cancel Current Payment', 'wp-travel-engine' ); ?></a>
					<?php
				endif;
			endif;
			?>
		</div>
	</div>
</div>
<?php
