<?php
/**
 * Payment Details
 *
 * @since 4.3.0
 */
global $post;

wp_enqueue_script( 'wte-highlightjs' );
wp_enqueue_style( 'wte-highlightjs' );
?>
<div class="wpte-block wpte-col3">
	<div class="wpte-title-wrap">
		<h4 class="wpte-title"><?php _e( 'Payment Details', 'wp-travel-engine' ); ?></h4>
		<div class="wpte-button-wrap wpte-edit-booking-detail">
			<a href="#" class="wpte-btn-transparent wpte-btn-sm">
				<i class="fas fa-pencil-alt"></i>
				<?php _e( 'Edit', 'wp-travel-engine' ); ?>
			</a>
		</div>
	</div>
	<div class="wpte-block-content">
		<?php
		$booking = get_post( $booking );

		$payments = $booking->payments;

		foreach ( $payments as $index => $payment_id ) {
			$payment         = get_post( $payment_id );
			$payment_status  = isset( $payment->payment_status ) ? $payment->payment_status : __( 'N/A', 'wp-travel-engine' );
			$payment_gateway = $payment->payment_gateway;
			?>
			<h4><?php echo sprintf( __( 'Payment #%d', 'wp-travel-engine' ), $index + 1 ); ?></h4>
			<ul class="wpte-list">
				<li>
					<b><?php _e( 'Payment ID', 'wp-travel-engine' ); ?></b>
					<span><a href="<?php echo get_edit_post_link( $payment_id ); ?>"><?php echo "#{$payment_id}"; ?></a></span>
				</li>
				<li>
					<b><?php _e( 'Payment Status', 'wp-travel-engine' ); ?></b>
					<span style="text-transform:uppercase;">
						<div class="wpte-field">
							<input readonly type="text" data-attrib-name="<?php echo esc_attr( "payments[{$payment->ID}][payment_status]" ); ?>" value="<?php echo esc_html( $payment_status ); ?>" />
						</div> 
					</span>
				</li>
				<li>
					<b><?php _e( 'Payment Gateway', 'wp-travel-engine' ); ?></b>
					<span>
						<div class="wpte-field">
							<input readonly type="text" data-attrib-name="<?php echo esc_attr( "payments[{$payment->ID}][payment_gateway]" ); ?>" value="<?php echo esc_html( $payment_gateway ); ?>" />
						</div>
					</span>
				</li>
				<li>
					<b><?php _e( 'Amount', 'wp-travel-engine' ); ?></b>
					<span>
						<div class="wpte-field">
							<input readonly type="text" data-attrib-name="<?php echo esc_attr( "payments[{$payment->ID}][payable][amount]" ); ?>" data-attrib-value="<?php echo esc_attr( $payment->payable['amount'] ); ?>" value="<?php echo wte_get_formated_price( $payment->payable['amount'], $payment->payable['currency'], '', !1 ); ?>" />
						</div>
					</span>
				</li>
				<?php
				if ( isset( $payment->gateway_response ) ) : // ifpgr.
					if ( is_array( $payment->gateway_response ) || is_object( $payment->gateway_response ) ) {
						$gw_response = wp_json_encode( $payment->gateway_response, JSON_PRETTY_PRINT );
					} else {
						$gw_response = $payment->gateway_response;
					}
					?>
				<li>
					<b><?php _e( 'Gateway raw response', 'wp-travel-engine' ); ?></b>
					<span><a href="#" class="wte-onclick-toggler" data-target="#gateway_response-<?php echo +$payment_id; ?>"><?php _e( 'View response', 'wp-travel-engine' ); ?></a></span>
				</li>
				<li id="gateway_response-<?php echo +$payment_id; ?>" style="display:none;">
					<pre style="width:100%">
						<code class="wte-code" data-height="500"><?php echo esc_html( $gw_response ); ?></code>
					</pre>
				</li>
				<?php endif; // endifpgr. ?>
				<?php
				$last_updated = get_post_modified_time( 'G', ! 0, $payment_id );
				if ( empty( $last_updated ) ) {
					$last_updated = '-';
				} else {
					$last_updated = date_i18n( 'M j, Y h:i:s a', $last_updated );
				}
				?>
				<li>
					<b><?php _e( 'Last Updated', 'wp-travel-engine' ); ?></b>
					<span><?php echo $last_updated; ?></span>
				</li>
			</ul>
			<?php
		}
		?>
	</div>
	<div class="wpte-block-content">
		<h4 class="wpte-title"><?php _e( 'Payment info', 'wp-travel-engine' ); ?></h4>
		<ul class="wpte-list">
			<li>
				<b><?php _e( 'Total Cost', 'wp-travel-engine' ); ?></b>
				<span>
					<div class="wpte-field">
						<input readonly type="text" min="0" step=".1" data-attrib-type="number" data-attrib-value="<?php echo esc_attr( $booking->cart_info['total'] ); ?>" data-attrib-name="<?php echo esc_attr( "cart_info[total]" ); ?>" value="<?php echo wte_get_formated_price( esc_html( $booking->cart_info['total'] ), $booking->cart_info['currency'], '', '' ); ?>" />
					</div>
				</span>
			</li>
			<li>
				<b><?php _e( 'Total Paid amount', 'wp-travel-engine' ); ?></b>
				<span>
					<div class="wpte-field">
						<input readonly type="text" min="0" step=".1" data-attrib-type="number" data-attrib-value="<?php echo esc_attr( $booking->paid_amount ); ?>" data-attrib-name="<?php echo esc_attr( 'paid_amount' ); ?>" value="<?php echo wte_get_formated_price( esc_html( $booking->paid_amount ), $booking->cart_info['currency'], '', '' ); ?>" />
					</div>
				</span>
			</li>
			<?php if ( +$booking->due_amount > 0 ) : ?>
			<li>
				<b><?php _e( 'Total Due amount', 'wp-travel-engine' ); ?></b>
				<span>
					<div class="wpte-field">
						<input readonly type="text" min="0" step=".1" data-attrib-type="number" data-attrib-value="<?php echo esc_attr( $booking->due_amount ); ?>" data-attrib-name="<?php echo esc_attr( "due_amount" ); ?>" value="<?php echo wte_get_formated_price( esc_html( $booking->due_amount ), $booking->cart_info['currency'], '', '' ); ?>" />
					</div>
				</span>
			</li>
			<?php endif; ?>
		</ul>
	</div>
</div>
