<?php  
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$wp_travel_engine_tabs = get_option( 'wp_travel_engine_settings' );
$wp_travel_engine_first_time_activation_flag = get_option('wp_travel_engine_first_time_activation_flag',false);
            
$obj = new Wp_Travel_Engine_Functions();
$self_obj = new WP_TRAVEL_ENGINE_ONBOARDING_PROCESS();
$currencies = $obj->wp_travel_engine_currencies();
$code = 'USD';
$addons_data = get_transient( 'wp_travel_engine_onboard_addons_list' );
if ( ! $addons_data ) {
    $addons_data = wp_safe_remote_get( WP_TRAVEL_ENGINE_STORE_URL . '/edd-api/v2/products/?category=payment-gateways&number=-1' );
    if( is_wp_error( $addons_data ) ){
        return;
    }
    $addons_data = wp_remote_retrieve_body( $addons_data );
   	set_transient( 'wp_travel_engine_onboard_addons_list', $addons_data, 128 * HOUR_IN_SECONDS );
}
if ( ! empty( $addons_data ) ) {
    $addons_data = json_decode( $addons_data );
    $addons_data = $addons_data->products;
}
?>
<div class="wpte-main-wrap wpte-onboard-process">
	<header class="wpte-header">
		<div class="wpte-left-block">
			<img src="<?php echo plugin_dir_url(WP_TRAVEL_ENGINE_FILE_PATH) . 'includes/onboard-process/images/wpte-onboard-logo.png';?>" alt="WP Travel Engine">
		</div>
	</header>
	<div class="obp-main-content">
		<div class="obp-process-outer">
			<div class="obp-process-inner">
				<div class="obp-process obp-getting-started current">
					<span class="obp-process-title"><?php _e('Getting Started', 'wp-travel-engine');?></span>
				</div>
				<div class="obp-process obp-currency">
					<span class="obp-process-count"><?php _e('1', 'wp-travel-engine');?></span>
					<span class="obp-process-title"><?php _e('Currency', 'wp-travel-engine');?></span>
				</div>
				<div class="obp-process obp-email">
					<span class="obp-process-count"><?php _e('2', 'wp-travel-engine');?></span>
					<span class="obp-process-title"><?php _e('Email', 'wp-travel-engine');?></span>
				</div>
				<div class="obp-process obp-pages">
					<span class="obp-process-count"><?php _e('3', 'wp-travel-engine');?></span>
					<span class="obp-process-title"><?php _e('Pages', 'wp-travel-engine');?></span>
				</div>
				<div class="obp-process obp-payment">
					<span class="obp-process-count"><?php _e('4', 'wp-travel-engine');?></span>
					<span class="obp-process-title"><?php _e('Payment', 'wp-travel-engine');?></span>
				</div>
				<div class="obp-process obp-ready">
					<span class="obp-process-count"><?php _e('5', 'wp-travel-engine');?></span>
					<span class="obp-process-title"><?php _e('Ready', 'wp-travel-engine');?></span>
				</div>
			</div><!-- obp-process-inner -->
			<span class="obp-progress-bar"></span>
		</div><!-- obp-process-outer -->
		<div class="obp-process-content-outer">
			<div class="obp-process-content-inner">
				<div class="obp-process-content obp-getting-started-content current" id="obp-homepage">
					<h2 class="obp-process-title"><?php _e('Welcome To WP Travel Engine', 'wp-travel-engine');?></h2>
					<button class="obp-btn-fill obp-next-step"><?php _e('Let\'s Get Started', 'wp-travel-engine');?></button>
					<a href="<?php echo admin_url();?>" class="obp-btn-link"><?php _e('Return to dashboard', 'wp-travel-engine');?></a>
				</div>
				<div class="obp-process-content obp-currency-content" id="obp-currency-setting">
					<div class="wpte-block"> 
						<div class="wpte-title-wrap">
							<h2 class="wpte-title">
								<?php _e('Currency Setting', 'wp-travel-engine');?>
							</h2>
							<div class="wpte-desc">
								<?php _e('You can configure  currency options from the following setting fields.', 'wp-travel-engine');?>
							</div>
						</div>
						<div class="wpte-block-content">
							<div class="wpte-field wpte-select wpte-floated">
								<label class="wpte-field-label">
									<?php _e('Trip\'s Base Currency', 'wp-travel-engine');?>
								</label>
								<select id="wp_travel_engine_settings[currency_code]" name="wp_travel_engine_settings[currency_code]" data-placeholder="<?php esc_attr_e( 'Choose a currency&hellip;', 'wp-travel-engine'); ?>" class="onboard-select2-select onboard-select2-select-currency">
									<option value=""><?php _e( 'Choose a currency&hellip;', 'wp-travel-engine'); ?></option>
									<?php
									
						            if( isset( $wp_travel_engine_tabs['currency_code'] ) && $wp_travel_engine_tabs['currency_code']!= '' )
						            {
						                $code = $wp_travel_engine_tabs['currency_code'];
						            } 
									$currency = $obj->wp_travel_engine_currencies_symbol( $code );
									foreach ( $currencies as $key => $name ) {
										echo '<option value="' .( !empty($key)?esc_attr( $key ):"USD")  . '" ' . selected( $code, $key, false ) . '>' . esc_html( $name . ' (' . $obj->wp_travel_engine_currencies_symbol( $key ) . ')' ) . '</option>';
									}
									?>
								</select>
								<span class="wpte-tooltip">
									<?php _e('Choose the base currency for the trips pricing.', 'wp-travel-engine'); ?>
								</span>
							</div>
							<div class="wpte-field wpte-select wpte-floated">
								<label class="wpte-field-label"><?php _e('Currency Symbol or Code', 'wp-travel-engine'); ?> </label>
								<select id="wp_travel_engine_settings[currency_option]" name="wp_travel_engine_settings[currency_option]" data-placeholder="<?php esc_attr_e( 'Choose a option&hellip;', 'wp-travel-engine'); ?>" class="onboard-select2-select">
									<?php
									$options = array(
						            	'symbol' => 'Currency Symbol ( e.g. $ )',
						            	'code'=> 'Currency Code ( e.g. USD )'
									);
									$option = isset( $wp_travel_engine_tabs['currency_option'] ) ? esc_attr( $wp_travel_engine_tabs['currency_option'] ) : 'symbol';
									foreach ( $options as $key => $val ) {
										echo '<option value="' .( !empty($key) ? esc_attr( $key ) : "Please select")  . '" ' . selected( $option, $key, false ) . '>' . esc_html( $val ) . '</option>';
									}
									?>
								</select>
								<span class="wpte-tooltip"><?php _e('Display Currency Symbol or Code in Trip Listing Templates.', 'wp-travel-engine'); ?></span>
							</div>

							<div class="wpte-field wpte-text wpte-floated">
								<?php 
								$thousands_separator = isset( $wp_travel_engine_tabs['thousands_separator'] ) && $wp_travel_engine_tabs['thousands_separator']!='' ? esc_attr( $wp_travel_engine_tabs['thousands_separator'] ) : ',';
								?>
								<label class="wpte-field-label" for="wp_travel_engine_settings[thousands_separator]">
									<?php _e('Thousands Separator','wp-travel-engine');?>
								</label>
								<input type="text" id="wp_travel_engine_settings[thousands_separator]" name="wp_travel_engine_settings[thousands_separator]" value="<?php echo apply_filters('wp_travel_engine_default_separator', $thousands_separator);?>">
								<span class="wpte-tooltip"><?php _e('Symbol to use for thousands separator in Trip Price.', 'wp-travel-engine');?></span>
							</div>
						</div><!-- wpte-block-content -->
					</div>
				</div><!-- obp-currency-content -->

				<div class="obp-process-content obp-email-content" id="obp-email-setting">
					<div class="wpte-block">
						<div class="wpte-title-wrap">
							<h2 class="wpte-title"><?php _e('Email Setting', 'wp-travel-engine');?></h2>
							<div class="wpte-desc"><?php _e('You can configure sales notification email and enquiry notification emails from the following setting fields.', 'wp-travel-engine');?></div>
						</div>
						<div class="wpte-block-content">
							<div class="wpte-field wpte-checkbox advance-checkbox">
								<label class="wpte-field-label" for="disable-admin-notification"><?php esc_html_e( 'Disable Admin Notification', 'wp-travel-engine' ); ?></label>
								<div class="wpte-checkbox-wrap">
									<?php 
									$disable_admin_notification = isset( $wp_travel_engine_tabs['email']['disable_notif'] ) ? esc_attr( $wp_travel_engine_tabs['email']['disable_notif'] ): '0'; ?>
									<input type="checkbox"  name="wp_travel_engine_settings[email][disable_notif]" value="1" <?php
									echo checked( $disable_admin_notification, '1' ); ?> id="disable-admin-notification">
									<label for="disable-admin-notification"></label>
								</div>
								<span class="wpte-tooltip"><?php esc_html_e( 'Turn this on if you do not want to receive sales notification emails.', 'wp-travel-engine' ); ?></span>
							</div>
							<div class="wpte-field wpte-textarea wpte-floated">
								<label class="wpte-field-label" for="wp_travel_engine_settings[email][emails]">
									<?php _e( 'Sales Notification Emails','wp-travel-engine' ); ?>
								</label>
								<?php $admin_email = get_option( 'admin_email' ); ;?>
								<textarea class="large-text" cols="50" rows="5" name="wp_travel_engine_settings[email][emails]" id="wp_travel_engine_settings[email][emails]"><?php echo isset( $wp_travel_engine_tabs['email']['emails'] ) && $wp_travel_engine_tabs['email']['emails']!='' ? esc_attr($wp_travel_engine_tabs['email']['emails']): esc_attr( $admin_email ); ?></textarea>
								<span class="wpte-tooltip">
									<?php _e('Enter the email address(es) that should receive a notification anytime a sale is made, separated by comma (,) and no spaces.', 'wp-travel-engine');?>
								</span>
							</div>
							<div class="wpte-field wpte-checkbox advance-checkbox">
								<label class="wpte-field-label" for="enable-customer-enquiry-email"><?php _e('Enable Enquiry Email', 'wp-travel-engine');?></label>
								<div class="wpte-checkbox-wrap">
									<?php 
									$enable_customer_notification = isset( $wp_travel_engine_tabs['email']['cust_notif'] ) ? esc_attr( $wp_travel_engine_tabs['email']['cust_notif'] ): '0'; ?>
									<input type="checkbox"  name="wp_travel_engine_settings[email][cust_notif]" value="1" <?php
									echo (!isset($wp_travel_engine_tabs['email']['cust_notif']) || isset($wp_travel_engine_tabs['email']['cust_notif']) && $enable_customer_notification == '1')?'checked="checked"':''; ?> id="enable-customer-enquiry-email">
									<label for="enable-customer-enquiry-email"></label>
								</div>
								<span class="wpte-tooltip"><?php _e('Turn this on if you want to send enquiry notification emails to customer as well.', 'wp-travel-engine');?></span>
							</div>
							<div class="wpte-field wpte-text wpte-floated">
								<label class="wpte-field-label" for="wp_travel_engine_settings[email][from]">
									<?php _e( 'From Email','wp-travel-engine' ); ?>
								</label>
								<input type="text" name="wp_travel_engine_settings[email][from]" id="wp_travel_engine_settings[email][from]" 
								value="<?php if( isset($wp_travel_engine_tabs['email']['from'] ) && $wp_travel_engine_tabs['email']['from']!='' ){ echo esc_attr($wp_travel_engine_tabs['email']['from']); } else{ echo get_option("admin_email"); } ?>" >
								<span class="wpte-tooltip">
									<?php _e('Enter the mail address from which the purchase receipts will be sent. This will act as as the from and reply-to address.', 'wp-travel-engine');?>
								</span>
							</div>
						</div>
					</div>
				</div>

				<div class="obp-process-content obp-pages-content" id="obp-page-setting">
					<div class="wpte-block">
						<div class="wpte-title-wrap">
							<h2 class="wpte-title"><?php _e('Page Setting', 'wp-travel-engine');?></h2>
							<div class="wpte-desc"><?php _e('You can configure default pages from the following setting fields.', 'wp-travel-engine');?></div>
						</div>
						<div class="wpte-block-content">
						<?php	
						$pages   = array(
						    'wte-checkout-page' => array(
						        'label' => __( 'Checkout Page', 'wp-travel-engine' ),
						        'name'  => 'wp_travel_engine_settings[pages][wp_travel_engine_place_order]',
						        'selected' => isset($wp_travel_engine_tabs['pages']['wp_travel_engine_place_order']) ? esc_attr($wp_travel_engine_tabs['pages']['wp_travel_engine_place_order']) : '',
						        'tooltip' => __( 'This is the checkout page where buyers will complete their order. The [WP_TRAVEL_ENGINE_PLACE_ORDER] shortcode must be on this page.', 'wp-travel-engine' ),
						    ),
						    'wte-terms-page' => array(
						        'label' => __( 'Terms and Conditions', 'wp-travel-engine' ),
						        'name'  => 'wp_travel_engine_settings[pages][wp_travel_engine_terms_and_conditions]',
						        'selected' => isset($wp_travel_engine_tabs['pages']['wp_travel_engine_terms_and_conditions']) ? esc_attr($wp_travel_engine_tabs['pages']['wp_travel_engine_terms_and_conditions']) : '',
						        'tooltip' => __( 'This is the terms and conditions page where trip bookers will see the terms and conditions for booking.', 'wp-travel-engine' ),
						    ),
						    'wte-thankyou-page' => array(
						        'label' => __( 'Thank You Page', 'wp-travel-engine' ),
						        'name'  => 'wp_travel_engine_settings[pages][wp_travel_engine_thank_you]',
						        'selected' => isset($wp_travel_engine_tabs['pages']['wp_travel_engine_thank_you']) ? esc_attr($wp_travel_engine_tabs['pages']['wp_travel_engine_thank_you']) : '',
						        'tooltip' => __( 'This is the thank you page where trip bookers will get the payment confirmation message. The [WP_TRAVEL_ENGINE_THANK_YOU] shortcode must be on this page.', 'wp-travel-engine' ),
						    ),
						    'wte-confirmation-page' => array(
						        'label' => __( 'Confirmation Page', 'wp-travel-engine' ),
						        'name'  => 'wp_travel_engine_settings[pages][wp_travel_engine_confirmation_page]',
						        'selected' => isset($wp_travel_engine_tabs['pages']['wp_travel_engine_confirmation_page']) ? esc_attr($wp_travel_engine_tabs['pages']['wp_travel_engine_confirmation_page']) : '',
						        'tooltip' => __( 'This is the confirmation page where trip bookers will fill the full form of the travelers. The [WP_TRAVEL_ENGINE_BOOK_CONFIRMATION] shortcode must be on this page.', 'wp-travel-engine' ),
						    ),
						    'wte-dashboard-page' => array(
						        'label' => __( 'User Dashboard Page', 'wp-travel-engine' ),
						        'name'  => 'wp_travel_engine_settings[pages][wp_travel_engine_dashboard_page]',
						        'selected' => isset($wp_travel_engine_tabs['pages']['wp_travel_engine_dashboard_page']) ? esc_attr($wp_travel_engine_tabs['pages']['wp_travel_engine_dashboard_page']) : wp_travel_engine_get_page_id( 'my-account' ),
						        'tooltip' => __( 'This is the dasbhboard page that lets your users to login and interact to bookings from frontend. The [wp_travel_engine_dashboard] shortcode must be on this page.', 'wp-travel-engine' ),
						    )
						);
						$pages_options = apply_filters( 'wpte_global_page_options', $pages );
						if ( ! empty( $pages_options ) ) :
						    foreach( $pages_options as $key => $page ) :
						        ?>
						        <div class="wpte-field wpte-select wpte-floated">
						            <label for="<?php echo esc_attr( $key ); ?>" class="wpte-field-label"><?php echo esc_html( $page['label'] ); ?></label>
						            <?php 
						                wp_dropdown_pages(
											array(
						                        'id'                => $key,
						                        'class'				=> 'onboard-select2-select',
						                        'name'              => $page['name'],
						                        'echo'              => 1,
						                        'show_option_none'  => __( '&mdash; Select &mdash;', 'wp-travel-engine' ),
						                        'option_none_value' => '0',
						                        'selected'          => $page['selected'],
											)
										);
						            if ( isset( $page['tooltip'] ) && ! empty( $page['tooltip'] ) ) : ?>
						                <span class="wpte-tooltip"><?php echo esc_html( $page['tooltip'] ); ?></span>
						            <?php 
						            endif; ?>
						        </div>
						        <?php
						    endforeach;
						endif; ?>
						</div>
					</div>
				</div>

				<div class="obp-process-content obp-payment-content" id="obp-payment-gateway-setting">
					<div class="wpte-block">
						<div class="wpte-title-wrap">
							<h2 class="wpte-title"><?php _e('Payment Gateway Setting', 'wp-travel-engine');?></h2>
							<div class="wpte-desc"><?php _e('You can configure payment gateways from following setting fields.', 'wp-travel-engine');?></div>
						</div>
						<div class="wpte-block-content">
							<div class="wpte-field wpte-onoff-block">
						        <?php 
								$enable_paypal_payment = isset( $wp_travel_engine_tabs['default_gateway']) ? esc_attr( $wp_travel_engine_tabs['default_gateway'] ): '0';?>
						        <a href="Javascript:void(0);" for="wp_travel_engine_setting_paypal_payment" class="wte-onboard-paypal-payment wpte-onoff-toggle <?php echo $enable_paypal_payment == 'paypal_payment' ? 'active' : ''; ?>">
						            <label for="wp_travel_engine_setting_paypal_payment" class="wpte-field-label"><img src="<?php echo plugin_dir_url(WP_TRAVEL_ENGINE_FILE_PATH) . 'includes/onboard-process/images/paypal.png';?>" alt="paypal"><span class="wpte-onoff-btn"></span></label>
						        </a>
						        <input type="checkbox" id="wp_travel_engine_setting_paypal_payment" class="paypal-payment" name="wp_travel_engine_settings[default_gateway]" value="paypal_payment" <?php echo checked( $enable_paypal_payment, 'paypal_payment' ); ?>>
								<span class="wpte-tooltip"><?php _e('Please check this to enable Paypal Standard booking system for trip booking and fill the account info below.', 'wp-travel-engine');?></span>
						        <div class="wpte-onoff-popup" style="display:<?php echo $enable_paypal_payment == 'paypal_payment' ? 'block' : 'none'; ?>;">
						            <div class="wpte-field wpte-floated">
						                <label for="wp_travel_engine_settings[paypal_id]" class="wpte-field-label">
										<?php _e('PayPal Email/ID', 'wp-travel-engine');?>
										</label>
						                <div class="wpte-floated">
						            		<input type="text" class="wp_travel_engine_settings_paypal_id" id="wp_travel_engine_settings[paypal_id]" name="wp_travel_engine_settings[paypal_id]" value="<?php echo isset($wp_travel_engine_tabs['paypal_id']) ? esc_attr($wp_travel_engine_tabs['paypal_id']) : ''; ?>">
						                </div>
						                <span class="wpte-tooltip"><?php _e('Enter a valid Merchant account ID (strongly recommend) or PayPal account email address. All payments will go to this account.', 'wp-travel-engine');?></span>
						            </div>
						        </div>
						    </div>
						</div>
					</div>
					<div class="wpte-block">
						<div class="wpte-title-wrap">
							<h2 class="wpte-title"><?php _e('Payment Gateway Recommendations', 'wp-travel-engine');?></h2>
						</div>
						<div class="wpte-block-content">
							<div class="wpte-field wpte-block-link wpte-floated" id="wpte-onboard-recommendations">
			        	  	</div>
						</div>
						<div class="wpte-info-block">
							<p><?php _e('Can\'t find your desired payment gateway on the list above?', 'wp-travel-engine');?> </p>
							<a href="//wptravelengine.com/downloads/category/add-ons/payment-gateways" target="_blank" class="wpte-btn wpte-btn-transparent"><?php _e('Get Premium Gateways', 'wp-travel-engine');?></a>
						</div>
					</div>
				</div>
				<?php 
				$twitter_link = 'https://wptravelengine.com';
				$twitter_message = urlencode( sprintf( esc_html__( 'I just finished setting up @wptravelengine #WordPress Plugin. Looks Great! %s', 'wp-travel-engine' ), $twitter_link ) );
				$facebook_title = esc_html__('WP Travel Engine', 'wp-travel-engine');
				$facebook_link = urlencode( 'https://wptravelengine.com' );
				$facebook_message = urlencode( esc_html__( 'I just finished setting up WP Travel Engine WordPress Plugin. Looks Great!', 'wp-travel-engine') );
				?>
				<div class="obp-process-content obp-ready-content" id="obp-ready-setting">
					<h2 class="obp-process-title"><?php _e('Your Site Is Ready!', 'wp-travel-engine');?></h2>
					<a href="https://twitter.com/intent/tweet?<?php echo $twitter_link;?>&text=<?php echo $twitter_message;?>&hashtags=wptravelengine" target="_blank" class="obp-btn-twitter"><i class="fab fa-twitter"></i> <?php _e('Tweet', 'wp-travel-engine');?></a>
					<a onClick="window.open('https://www.facebook.com/sharer/sharer.php?s=100&c=<?php echo $facebook_title;?>&quote=<?php echo $facebook_message;?>&u=<?php echo $facebook_link;?>&hashtag=wptravelengine', 'sharewindow', 'resizable,width=600,height=300'); return false;" href="javascript: void(0)" class="obp-btn-facebook"><i class="fab fa-facebook-f"></i> <?php _e('Share', 'wp-travel-engine');?></a>
					<div class="wpte-btn-wrap">
						<a href="//wptravelengine.com/wp-travel-engine-documentation" target="_blank" class="wpte-btn wpte-btn-transparent wpte-kb"><?php _e('WP Travel Engine Knowledge Base', 'wp-travel-engine');?></a>
						<a href="//wordpress.org/support/plugin/wp-travel-engine" target="_blank" class="wpte-btn wpte-btn-transparent wpte-error-fix"><?php _e('Common WordPress Errors &amp; Fixes', 'wp-travel-engine');?></a>
						<a href="//wptravelengine.com/support-ticket" target="_blank" class="wpte-btn wpte-btn-transparent wpte-support"><?php _e('Get 24x7 Support', 'wp-travel-engine');?></a>
					</div> 
					<div class="wpte-block">
						<div class="obp-create-trip">
							<h3 class="wpte-title"><?php _e('Create Your Trip', 'wp-travel-engine');?></h3>
							<a href="<?php echo admin_url().'post-new.php?post_type=trip';?>" class="obp-btn-fill"><?php _e('Click here', 'wp-travel-engine');?></a>
						</div>
					</div>
				</div>
			</div>
			<div class="obp-footer">
				<div class="wpte-left-block">
					<button class="obp-btn-transparent obp-prev-step"><?php _e('Back', 'wp-travel-engine');?></button>
					<a href="<?php echo admin_url();?>" class="obp-btn-link obp-hide"><?php _e('Return to dashboard', 'wp-travel-engine');?></a>
				</div>
				<div class="wpte-right-block">
					<button class="obp-btn-link obp-next-step"><?php _e('Skip', 'wp-travel-engine');?></button>
					<input type="submit" data-next-tab="obp-currency" class="obp-btn-fill obp-btn-submit-continue" value="<?php _e('Continue', 'wp-travel-engine');?>"> 
					<?php wp_nonce_field('obp_btn_submit_continue_action', 'obp_btn_submit_onboard_nonce_field'); ?>
					<div id="onboard-loader" style="display: none">
			            <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
			        </div>
				</div>
				<div class="obp-message-block toast"><div>
			</div>
		</div>
	</div>
	<script type="text/html" id="tmpl-wte-onboard-updated-recommendation">
	</script>
</div>