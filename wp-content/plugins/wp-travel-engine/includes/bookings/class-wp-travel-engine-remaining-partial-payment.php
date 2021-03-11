<?php
/**
 * Process the partial payment flow in WP Travel Engine.
 *
 * @package WP_Travel_Engine
 * @since 2.2.8
 */
/**
 * Main partial payment process handler class.
 */
    class WTE_Process_Remaing_Payment
    {
		public function __construct()
		{
			add_action( 'wp_travel_engine_after_partial_payment_gateway_redirect', 'wp_travel_engine_after_partial_payment_gateway_redirect_save', 10, 2 );
		}

		/**
		 * Handle the partial payment process after the partial payment request form is submitted from user dashboard.
		 *
		 * @return void
		*/
        public function process_remaining_payment($partial_payment = false)
        {
            $post = stripslashes_deep($_POST);

            if (
		(!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action'] !== 'partial-payment'))
        || ! isset($post['wp_travel_engine_nw_bkg_submit'])
        || ! isset($post['nonce_checkout_partial_payment_remaining_field'])
        || ! wp_verify_nonce($post['nonce_checkout_partial_payment_remaining_field'], 'nonce_checkout_partial_payment_remaining_action')
        ) {
                return;
            }

		if (
        ! isset($_GET['booking_id'])
        || (
            isset($_GET['booking_id']) && empty($_GET['booking_id'])
        )) {
                return;
            } else {
                $booking_id = intval($_GET['booking_id']);
            }

        if (
        ! isset($post['wpte_checkout_paymnet_method'])
        || (
            isset($post['wpte_checkout_paymnet_method']) && empty($post['wpte_checkout_paymnet_method'])
        )) {
                return;
            } else {
                $wpte_checkout_paymnet_method = sanitize_text_field($_POST['wpte_checkout_paymnet_method']);
			}

		if ($wpte_checkout_paymnet_method == 'paypal_payment'):
			$obj = new WTE_Payment_Gateway_Paypal;
			$obj->process($booking_id, $partial_payment = false);
		else:
		do_action('wp_travel_engine_before_remaining_payment_process', $booking_id);

		$updated_array = $this->process_data_for_update($post);
		if (! empty($updated_array)) {

				/**
				 * Update booking with Remaining Details of payment
				*/

				update_post_meta($booking_id, 'wp_travel_engine_remaining_payment_detail', $updated_array);

                /**
                 * Update new payment detail for
                 * due - Remaining Payment : after partial payment
                 * cost - Total Paid : to be added
                 */

                $booking_data 	= get_post_meta($booking_id, 'wp_travel_engine_booking_setting', true);
                $partial_paid 	= isset($booking_data['place_order']['cost']) ? $booking_data['place_order']['cost'] : 0;
                $partial_due 	= isset($booking_data['place_order']['due']) && $booking_data['place_order']['due'] != ''? $booking_data['place_order']['due'] : 0;
                $total_cost 	= isset($booking_data['place_order']['due']) && $booking_data['place_order']['due'] != '' ? floatval($booking_data['place_order']['cost']) + floatval($booking_data['place_order']['due']) : $partial_paid;
				$wte_gateway 	= isset($post['wpte_checkout_paymnet_method']) && !empty($post['wpte_checkout_paymnet_method'])?sanitize_text_field($post['wpte_checkout_paymnet_method']):'';

                if (isset($booking_data['place_order']['due']) && $booking_data['place_order']['due'] > 0) {
					// Preserving older due & older cost value to $booking data
					$booking_data['place_order']['partial_cost'] 	= $partial_paid; // amount that is paid on first installment.
					$booking_data['place_order']['partial_due'] 	= $partial_due; // amount that needs top be paid on second installment.

					// Saving new paid and new due amount after second installment of partial payment
					$booking_data['place_order']['cost'] 			= $total_cost; // updating total amount paid to original cost after second installment is payed.
					$booking_data['place_order']['due'] 			= 0; // resetting due amount to 0 since second installment has been paid now.

					update_post_meta($booking_id, 'wp_travel_engine_booking_setting', $booking_data);
					update_post_meta($booking_id, 'wp_travel_engine_booking_payment_status', 'pending');
					update_post_meta($booking_id, 'wp_travel_engine_booking_payment_gateway', $wte_gateway);
                }
            }

            endif;

			do_action( 'wp_travel_engine_after_remaining_payment_process_completed', $booking_id );

			$wte_confirms  = wp_travel_engine_get_booking_confirm_url();
            $wte_confirms  = add_query_arg(array('booking_id' => $booking_id,'redirect_type' => 'partial-payment'), $wte_confirms);

            // Redirect to the traveller's information page.
            wp_redirect($wte_confirms);
            exit;
        }

		/**
		 * Pull data as array for updating into metabox in booking
		 *
		 * @param [type] $post
		 * @return void
		 */
		public function process_data_for_update($post)
		{
            unset($post['wp_travel_engine_nw_bkg_submit']);
            unset($post['nonce_checkout_partial_payment_remaining_field']);
            unset($post['_wp_http_referer']);
            unset($post['total']);
            unset($post['currency']);
            return $post;
        }
    }

	/**
	 * For redirecting Payment Gateway system to update data when Thank You page is reached
	 *
	 * @param [type] $booking_id
	 * @param [type] $wte_gateway
	 * @return void
	 */
    function wp_travel_engine_after_partial_payment_gateway_redirect_save($booking_id, $wte_gateway)
    {
        $partial_payment_detail = array(
            'wpte_checkout_paymnet_method'=>$wte_gateway
        );
        update_post_meta($booking_id, 'wp_travel_engine_remaining_payment_detail', $partial_payment_detail);

        $booking_data 	= get_post_meta($booking_id, 'wp_travel_engine_booking_setting', true);
        if (isset($booking_data['place_order']['due']) && $booking_data['place_order']['due'] > 0) {

			$partial_paid 	= isset($booking_data['place_order']['cost']) ? $booking_data['place_order']['cost'] : 0;
			$partial_due 	= isset($booking_data['place_order']['due']) && $booking_data['place_order']['due'] != ''? $booking_data['place_order']['due'] : 0;
			$total_cost 	= isset($booking_data['place_order']['due']) && $booking_data['place_order']['due'] != '' ? floatval($booking_data['place_order']['cost']) + floatval($booking_data['place_order']['due']) : $partial_paid;

			// Preserving older due & older cost value to $booking data
			$booking_data['place_order']['partial_cost'] 	= $partial_paid; // amount that is paid on first installment.
			$booking_data['place_order']['partial_due'] 	= $partial_due; // amount that needs top be paid on second installment.

			// Saving new paid and new due amount after second installment of partial payment
			$booking_data['place_order']['cost'] 			= $total_cost; // updating total amount paid to original cost after second installment is payed.
			$booking_data['place_order']['due'] 			= 0; // resetting due amount to 0 since second installment has been paid now.

			update_post_meta($booking_id, 'wp_travel_engine_booking_setting', $booking_data);
			update_post_meta($booking_id, 'wp_travel_engine_booking_payment_status', 'pending');
			update_post_meta($booking_id, 'wp_travel_engine_booking_payment_gateway', $wte_gateway);
			wp_safe_redirect($_SERVER['REQUEST_URI']);
    	}
	 }

	 /**
	  * Booking Meta related to Payment value passed as array for multi use
	  *
	  * @param [type] $booking_id
	  * @return void
	  */
	 function booking_meta_details($booking_id)
    {
        $booking_meta 						= [];
        $booking_metas 		 				= get_post_meta($booking_id, 'wp_travel_engine_booking_setting', true);
		$booking_meta['booked_travellers']	= isset($booking_metas['place_order']['traveler']) ? $booking_metas['place_order']['traveler'] : 0;

        $booking_meta['total_paid'] 		= isset($booking_metas['place_order']['cost']) ? $booking_metas['place_order']['cost'] : 0;
        $booking_meta['remaining_payment'] 	= isset($booking_metas['place_order']['due']) ? $booking_metas['place_order']['due'] : 0;
        $booking_meta['total_cost'] 		= isset($booking_metas['place_order']['due']) && $booking_metas['place_order']['due'] != '' ? floatval($booking_metas['place_order']['cost']) + floatval($booking_metas['place_order']['due']) : $booking_meta['total_paid'];
        $booking_meta['partial_due']		= isset($booking_metas['place_order']['partial_due']) ? $booking_metas['place_order']['partial_due'] : 0;
        $booking_meta['partial_cost']		= isset($booking_metas['place_order']['partial_cost']) ? $booking_metas['place_order']['partial_cost'] : 0;
        $booking_meta['trip_id'] 			= isset($booking_metas['place_order']['tid']) ? $booking_metas['place_order']['tid'] : 0;
        $booking_meta['trip_name'] 			= isset($booking_metas['place_order']['tid']) ? esc_html(get_the_title($booking_metas['place_order']['tid'])) : '';
        $booking_meta['trip_start_date'] 	= isset($booking_metas['place_order']['datetime']) ? $booking_metas['place_order']['datetime'] : '';
        $booking_meta['date_format']        = get_option('date_format');

        return $booking_meta;
	}

	/**
	 *  Is partial payment page
	 *
	 * @return void
	 */
	function wte_is_partial_payment_page(){

	 }
