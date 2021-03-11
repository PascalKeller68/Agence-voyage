<?php
/**
 * Place order form.
 *
 * @package    Wp_Travel_Engine
 * @subpackage Wp_Travel_Engine/includes
 * @author
 */
class WTE_Booking_Response {

	private $responses;

	public function __construct() {
		$this->responses = array(
			'pending'   => __( 'Your booking order has been placed. You booking will be confirmed after payment confirmation/settlement.', 'wp-travel-engine' ),
			'completed' => __( 'The payment transaction has been completed.', 'wp-travel-engine' ),
			'success'   => __( 'The payment transaction has been successful.', 'wp-travel-engine' ),
			'failed'    => __( 'The payment transaction has been failed.', 'wp-travel-engine' ),
		);
	}

	/**
	 * Get a data by key
	 *
	 * @param string The key data to retrieve
	 * @access public
	 */
	public function &__get( $key ) {
		$value = '';
		if ( isset( $this->responses[ $key ] ) ) {
			$value = $this->responses[ $key ];
		}

		return $value;
	}

	/**
	 * Whether or not an data exists by key
	 *
	 * @param string An data key to check for
	 * @access public
	 * @return boolean
	 * @abstracting ArrayAccess
	 */
	public function __isset( $key ) {
		return isset( $this->data[ $key ] );
	}

}
class Wp_Travel_Engine_Thank_You {

	/**
	 * Initialize the thank you form shortcode.
	 *
	 * @since 1.0.0
	 */
	function init() {
		add_shortcode( 'WP_TRAVEL_ENGINE_THANK_YOU', array( $this, 'wp_travel_engine_thank_you_shortcodes_callback' ) );
		add_filter( 'body_class', array( $this, 'add_thankyou_body_class' ) );
	}

	function add_thankyou_body_class( $classes ) {
		global $post;
		if ( is_object( $post ) ) {
			if ( has_shortcode( $post->post_content, 'WP_TRAVEL_ENGINE_THANK_YOU' ) ) {
				$classes[] = 'thank-you';
			}
		}

		return $classes;
	}

	public static function response() {
		return new WTE_Booking_Response();
	}

	public static function get_booking_details_html( $payment_id, $booking_id = null ) {
		if ( is_null( $booking_id ) ) {
			$booking_id = get_post_meta( $payment_id, 'booking_id', true );
		}

		$booking = get_post( $booking_id );
		if ( is_null( $booking ) || 'booking' !== $booking->post_type ) {
			return __( 'Invalid booking or booking has been removed.', 'wp-travel-engine' );
		}
		$date_format         = get_option( 'date_format' );
		$wte_settings        = get_option( 'wp_travel_engine_settings' );
		$extra_service_title = isset( $wte_settings['extra_service_title'] ) && ! empty( $wte_settings['extra_service_title'] ) ? $wte_settings['extra_service_title'] : __( 'Extra Services:', 'wp-travel-engine' );
		ob_start();
		$thankyou  = __( 'Thank you for booking the trip. Please check your email for confirmation.', 'wp-travel-engine' );
		$thankyou .= __( ' Below is your booking detail:', 'wp-travel-engine' );
		$thankyou .= '<br>';

		if ( ! empty( $wte_settings['confirmation_msg'] ) ) {
			$thankyou = $wte_settings['confirmation_msg'];
		}

		$currency  = $booking->cart_info['currency'];
		$cart_info = $booking->cart_info;

		// Display thany-you message.
		echo wp_kses_post( $thankyou );
		?>
		<div class="thank-you-container">
			<h3 class="trip-details"><?php echo esc_html__( 'Booking Details:', 'wp-travel-engine' ); ?></h3>
			<div class="detail-container">
				<div class="detail-item">
					<strong class="item-label"><?php esc_html_e( 'Booking ID :', 'wp-travel-engine' ); ?></strong>
					<span class="value"><?php echo esc_html( $booking->ID ); ?></span>
				</div>
				<div class="detail-item" style="text-align:center;justify-content:center;">
					<strong style="font-size:18px;font-weight:normal"><?php echo _n( 'Trip Details', 'Trips Details', count( $booking->order_trips ), 'wp-travel-engine' ); ?></strong>
				</div>
				<?php
				$order_trips = $booking->order_trips;
				foreach ( $order_trips as $key => $item ) :
					$item = (object) $item;
					$trip = get_post( $item->ID );
					?>
					<div class="detail-item">
						<a href="<?php echo esc_url( get_permalink( $trip->ID ) ); ?>"><?php echo esc_html( $trip->post_title ); ?></a> <code><?php echo esc_html( "[#{$trip->ID}]" ); ?></code>
					</div>
					<div class="detail-item">
						<strong class="item-label"><?php esc_html_e( 'Trip ID:', 'wp-travel-engine' ); ?></strong>
						<span class="value"><?php echo esc_html( $trip->ID ); ?></span>
					</div>
					<?php
					/**
					 * wte_thankyou_after_trip_name hook
					 *
					 * @hooked wte_display_trip_code_thankyou - Trip Code Addon
					 */
					do_action( 'wte_thankyou_after_trip_name', $trip->ID );
					?>
					<div class="detail-item">
						<strong class="item-label"><?php esc_html_e( 'Trip Cost:', 'wp-travel-engine' ); ?></strong>
						<span class="value">
						<?php
						foreach ( $item->pax as $label => $tcount ) {
							if ( +$tcount < 1 ) {
								continue;
							}
							$pax_cost = +$item->pax_cost[ $label ] / +$tcount;
							echo "{$tcount} X {$label} (" . wte_get_formated_price( $pax_cost, $currency, '', ! 0 ) . ') = ' . wte_get_formated_price( $item->pax_cost[ $label ], $currency, '', ! 0 );
							echo '<br/>';
						}
						?>
						</span>
					</div>

					<div class="detail-item">
						<strong class="item-label"><?php esc_html_e( 'Trip start date:', 'wp-travel-engine' ); ?></strong>
						<span
							class="value"><?php echo esc_html( date_i18n( $date_format, strtotime( $item->datetime ) ) ); ?></span>
					</div>
					<?php
					if ( isset( $item->trip_extras ) && ! empty( $item->trip_extras ) ) :
						?>
						<div class="detail-item">
							<strong class="item-label"><?php echo esc_html( $extra_service_title ); ?></strong>
							<span class="value">
								<?php foreach ( $item->trip_extras as $trip_extra ) : ?>
									<div>
										<?php
										$qty           = $trip_extra['qty'];
										$extra_service = $trip_extra['extra_service'];
										$price         = $trip_extra['price'];
										$cost          = $qty * $price;
										if ( 0 === $cost ) {
											continue;
										}
										$formattedCost = wte_get_formated_price( $cost, $currency, '', ! 0 );
										$output        = "{$qty} X {$extra_service} (" . wte_get_formated_price( $price, $currency, '', ! 0 ) . ") = {$formattedCost}";
										echo esc_html( $output );
										?>
									</div>
								<?php endforeach; ?>
							</span>
						</div>
						<?php
					endif;
				endforeach;
				$payment_method = get_post_meta( $payment_id, 'payment_gateway', true );
				?>
				<div class="detail-item" style="text-align:center;justify-content:center;">
					<strong style="font-size:18px;font-weight:normal;"><?php echo __( 'Payment Details', 'wp-travel-engine' ); ?></strong>
				</div>
				<?php
				if ( is_array( $booking->payments ) && count( $booking->payments ) > 1 ) {
					foreach ( $booking->payments as $index => $pid ) {
						$payment_obj = get_post( $pid );
						?>
						<div class="detail-item">
							<strong class="item-label"><?php echo sprintf( esc_html__( 'Partial Payment #%d', 'wp-travel-engine' ), $index + 1 ); ?></strong>
							<span class="value"><?php echo esc_html( wp_travel_engine_get_formated_price_with_currency( $payment_obj->payable['amount'], null, true ) ) . " ({$payment_obj->payment_status})"; ?></span>
						</div>
						<?php
					}
				}

				$payment = get_post( $payment_id );
				?>
				<div class="detail-item">
					<strong class="item-label"><?php esc_html_e( 'Payment amount:', 'wp-travel-engine' ); ?></strong>
					<span class="value">
					<?php echo esc_html( wte_get_formated_price( $payment->payable['amount'], $currency, '', ! 0 ) ); ?>
					<?php
					echo wp_kses(
						"<code>[{$payment->payment_status}]</code>",
						array( 'code' => array( 'style' => array() ) )
					);
					?>
					</span>
				</div>
				<div class="detail-item">
					<strong class="item-label"><?php esc_html_e( 'Due amount:', 'wp-travel-engine' ); ?></strong>
					<span class="value">
						<?php echo esc_html( wte_get_formated_price( +$cart_info['total'] - +$payment->payable['amount'], $currency, '', ! 0 ) ); ?>
					</span>
				</div>
				<div class="detail-item">
					<strong class="item-label"><?php esc_html_e( 'Remarks: ', 'wp-travel-engine' ); ?></strong>
					<div class="value"><?php echo esc_html( self::response()->{$payment->payment_status} ); ?></div>
				</div>
				<div class="detail-item">
					<strong class="item-label"><?php esc_html_e( 'Subtotal:', 'wp-travel-engine' ); ?></strong>
					<span class="value">
						<?php
							echo esc_html( wte_get_formated_price( +$booking->cart_info['subtotal'], $cart_info['currency'], '', ! 0 ) );
						?>
					</span>
				</div>
				<?php
				$discount_figure = 0;
				$cart_info       = $booking->cart_info;
				if ( ! empty( $cart_info['discounts'] ) ) :
					$discounts     = $cart_info['discounts'];
					$discount      = array_shift( $discounts );
					$discount_type = $discount['type'];

					switch ( $discount_type ) {
						case 'percentage':
							$discount_figure = +$cart_info['subtotal'] * ( +$discount['value'] / 100 );
							break;
						case 'fixed':
							$discount_figure = +$discount['value'];
							break;
						default:
							$discount_figure = 0;
							break;
					}
				endif;
				$discount_amount = wte_get_formated_price( +$discount_figure, $cart_info['currency'], '', ! 0 );
				?>
				<div class="detail-item">
					<strong class="item-label"><?php esc_html_e( 'Discount (-):', 'wp-travel-engine' ); ?></strong>
					<span class="value">
						<?php
							echo esc_html( $discount_amount );
						?>
					</span>
				</div>
				<div class="detail-item">
					<strong class="item-label"><?php esc_html_e( 'Total:', 'wp-travel-engine' ); ?></strong>
					<span class="value">
						<?php
							echo esc_html( wte_get_formated_price( +$cart_info['total'], $cart_info['currency'], '', ! 0 ) );
						?>
					</span>
				</div>
			</div>
		</div>
		<?php
		do_action( "wte_after_thankyou_booking_details_{$payment_method}", $payment_id );
		if ( count( $booking->payments ) > 1 ) :
			?>
			<div class="thank-you-container-2">
				<div class="wpte-lrf-btn-wrap">
					<a target="_blank" class="wpte-lrf-btn" href="<?php echo esc_url( get_post_type_archive_link( 'trip' ) ); ?>"><?php _e( 'Book More Trips', 'wp-travel-engine' ); ?></a>
				</div>
				<?php
				$user_account_page_id = wp_travel_engine_get_dashboard_page_id();
				if ( ! empty( $user_account_page_id ) ) {
					?>
					<div class="wpte-lrf-btn-wrap">
						<a class="wpte-lrf-btn" href="<?php echo esc_url( get_permalink( $user_account_page_id ) ); ?>"><?php _e( 'Back to User Dashboard', 'wp-travel-engine' ); ?></a>
					</div>
					<?php
				}
				?>
			</div>
			<?php
		endif;
		return ob_get_clean();
	}

	/**
	 * Place order form shortcode callback function.
	 *
	 * @since 1.0.0
	 */
	function wp_travel_engine_thank_you_shortcodes_callback() {
		if ( is_admin() ) {
			return;
		}

		$data = WTE_Booking::get_callback_token_payload( 'thankyou' );

		if ( ! $data ) {
			return __( 'Thank you for booking the trip. Please check your email for confirmation.', 'wp-travel-engine' );
		}

		if ( is_array( $data ) && isset( $data['bid'] ) ) {
			$booking_id = $data['bid'];
			$payment_id = $data['pid'];
		}

		do_action( 'wte_booking_cleanup', $payment_id, 'thankyou' );
		return self::get_booking_details_html( $payment_id, $booking_id );

	}
}
