<?php
/**
 * Trip Info
 */
global $post;

$booking     = get_post( $post->ID );
$order_trips = $booking->order_trips;

$cart_info = $booking->cart_info;

$currency = $cart_info['currency'];
?>
<div class="wpte-block wpte-col3">
	<div class="wpte-title-wrap">
		<h4 class="wpte-title"><?php echo _n( 'Trip Info', 'Trips Info', count( $order_trips ), 'wp-travel-engine' ); ?></h4>
		<div class="wpte-button-wrap wpte-edit-booking-detail">
			<a href="#" class="wpte-btn-transparent wpte-btn-sm">
				<i class="fas fa-pencil-alt"></i>
				<?php _e( 'Edit', 'wp-travel-engine' ); ?>
			</a>
		</div>
	</div>
	<div class="wpte-block-content">
		<?php
		$index = 0;
		foreach ( $order_trips as $cart_id => $order_trip ) :
			$order_trip = (object) current( $order_trips );
			?>
			<h4><?php echo sprintf( __( 'Trip #%d', 'wp-travel-engine' ), $index + 1 ); ?></h4>
			<ul class="wpte-list">
				<li>
					<b><?php _e( 'Trip Name', 'wp-travel-engine' ); ?></b>
					<span>
						<div class="wpte-field wpte-select">
							<select disabled data-attrib-name="order_trips[<?php echo esc_attr( $cart_id ); ?>][ID]" class="wpte-enhanced-select"
								id="wpte-booking-trip-id">
								<?php
								$trips_options = wp_travel_engine_get_trips_array();
								foreach ( $trips_options as $key => $trip ) {
									$selected = selected( $order_trip->ID, $key, false );
									echo '<option ' . $selected . " value='{$key}'>{$trip}</option>";
								}
								?>
							</select>
						</div>
					</span>
				</li>
				<?php
				/**
				 * wte_booking_after_trip_name hook
				 *
				 * @hooked wte_display_trip_code_booking - Trip Code Addon
				 */
				do_action( 'wte_booking_after_trip_name', $order_trip->ID );
				?>
				<li>
					<b><?php _e( 'Trip Start Date', 'wp-travel-engine' ); ?></b>
					<span>
						<div class="wpte-field wpte-text">
							<input type="text" readonly data-attrib-name="order_trips[<?php echo esc_attr( $cart_id ); ?>][datetime]" data-attrib-value="<?php echo esc_attr( $order_trip->datetime ); ?>" value="<?php echo esc_attr( wte_get_formated_date( $order_trip->datetime ) ); ?>"/>
						</div>
					</span>
				</li>
				<li>
					<b><?php _e( 'Travelers', 'wp-travel-engine' ); ?></b>
				</li>
				<?php
				foreach ( $order_trip->pax as $category => $number ) {
					?>
					<li>
						<b><?php echo esc_html( $category ); ?></b>
						<span>
							<div class="wpte-field wpte-text">
								<input type="text" readonly data-attrib-name="order_trips[<?php echo esc_attr( $cart_id ); ?>][pax][<?php echo esc_attr( $category ); ?>]" value="<?php echo esc_attr( $number ); ?>"/>
							</div>
						</span>
					</li>
					<?php
				}
				?>
				<li>
					<b><?php _e( 'Trip Cost', 'wp-travel-engine' ); ?></b>
					<span>
						<div class="wpte-field wpte-text">
							<?php
							foreach ( $order_trip->pax as $label => $number ) {
								if ( $number < 1 ) {
									continue;
								}
								$pax_cost = +$order_trip->pax_cost[ $label ] / +$number;
								$cost     = wte_get_formated_price( $pax_cost, $currency, '', ! 0 );
								$ptotal   = wte_get_formated_price( $number * $pax_cost, $currency, '', ! 0 );
								echo "{$number} X  {$label} ({$cost}) = {$ptotal}<br/>";
							}
							?>
							<input type="text" data-attrib-name="order_trips[<?php echo esc_attr( $cart_id ); ?>][cost]" data-attrib-value="<?php echo esc_attr( $order_trip->cost ); ?>" readonly value="<?php echo esc_attr( wte_get_formated_price( $order_trip->cost, $booking->cart_info['currency'], '', true ) ); ?>"/>
						</div>
					</span>
				</li>
			</ul>
			<?php
			if ( ! empty( $order_trip->trip_extras ) ) { // ifotte
				echo '<h5>' . esc_html__( 'Extra Services', 'wp-travel-engine' ) . '</h5>';
				echo '<ul class="wpte-list">';
				foreach ( $order_trip->trip_extras as $index => $tx ) { // forotteitx
					?>
					<li><b><?php echo esc_html( $tx['extra_service'] ); ?></b></li>
					<li>
						<b><i><?php esc_html_e( 'Quantity', 'wp-travel-engine' ); ?></i></b>
						<span><?php echo esc_html( $tx['qty'] ); ?></span>
					</li>
					<li>
						<b><i><?php esc_html_e( 'Price', 'wp-travel-engine' ); ?></i></b>
						<span><?php echo esc_html( wte_get_formated_price( +$tx['price'], $cart_info['currency'], '', ! 0 ) ); ?></span>
					</li>
					<li>
						<b></b>
						<span><input readonly type="text" name="" id="" value="<?php echo esc_attr( wte_get_formated_price( +$tx['qty'] * +$tx['price'], $currency, '', ! 0 ) ); ?>"/></span>
					</li>
					<?php
				} // endforotteitx
				echo '</ul>';
			} // endifotte
			$index++;
		endforeach;
		?>
	</div>
	<?php
	$discounts = $cart_info['discounts'];
	if ( ! empty( $discounts ) && is_array( $discounts ) ) {
		?>
	<div class="wpte-title-wrap">
		<h4 class="wpte-title"><?php esc_html_e( 'Coupon Discounts', 'wp-travel-engine' ); ?></h4>
	</div>
	<div class="wpte-block-content">
		<ul>
			<?php
			foreach ( $discounts as $key => $discount ) {
				$amount_str = 'percentage' === $discount['type'] ? $discount['value'] . '%' : wte_get_formated_price( +$discount['value'], $currency, '', ! 0 );
				?>
			<li>
				<b><?php _e( 'Acutal Cost:', 'wp-travel-engine' ); ?></b>
				<span>
					<?php echo wte_get_formated_price( $cart_info['subtotal'], $currency, '', ! 0 ); ?>
				</span>
			</li>
			<li>
				<b><?php _e( 'Discount Name:', 'wp-travel-engine' ); ?></b>
				<span>
					<?php echo $discount['name'] . '( ' . $amount_str . ' )'; ?>
				</span>
			</li>
			<li>
				<b><?php _e( 'Discount Amount:', 'wp-travel-engine' ); ?></b>
				<span>
					<?php echo wte_get_formated_price( 'percentage' === $discount['type'] ? +$cart_info['subtotal'] * ( +$discount['value'] / 100 ) : +$discount['value'], $currency, '', ! 0 ); ?>
				</span>
			</li>
				<?php
			}
			?>
		</ul>
	</div>
		<?php
	}
	?>
</div> <!-- .wpte-block -->
<?php
