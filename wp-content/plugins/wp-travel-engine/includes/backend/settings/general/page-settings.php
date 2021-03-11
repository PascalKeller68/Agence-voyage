<?php
/**
 * Page Settings tab for Global Setting
 */
$options = get_option('wp_travel_engine_settings', true);
$pages   = array(
    'wte-checkout-page' => array(
        'label' => __( 'Checkout Page', 'wp-travel-engine' ),
        'name'  => 'wp_travel_engine_settings[pages][wp_travel_engine_place_order]',
        'selected' => isset($options['pages']['wp_travel_engine_place_order']) ? esc_attr($options['pages']['wp_travel_engine_place_order']) : '',
        'tooltip' => __( 'This is the checkout page where buyers will complete their order. The <b>[WP_TRAVEL_ENGINE_PLACE_ORDER]</b> shortcode must be on this page.', 'wp-travel-engine' ),
    ),
    'wte-terms-page' => array(
        'label' => __( 'Terms and Conditions', 'wp-travel-engine' ),
        'name'  => 'wp_travel_engine_settings[pages][wp_travel_engine_terms_and_conditions]',
        'selected' => isset($options['pages']['wp_travel_engine_terms_and_conditions']) ? esc_attr($options['pages']['wp_travel_engine_terms_and_conditions']) : '',
        'tooltip' => __( 'This is the terms and conditions page where trip bookers will see the terms and conditions for booking.', 'wp-travel-engine' ),
    ),
    'wte-thankyou-page' => array(
        'label' => __( 'Thank You Page', 'wp-travel-engine' ),
        'name'  => 'wp_travel_engine_settings[pages][wp_travel_engine_thank_you]',
        'selected' => isset($options['pages']['wp_travel_engine_thank_you']) ? esc_attr($options['pages']['wp_travel_engine_thank_you']) : '',
        'tooltip' => __( 'This is the thank you page where trip bookers will get the payment confirmation message. The <b>[WP_TRAVEL_ENGINE_THANK_YOU]</b> shortcode must be on this page.', 'wp-travel-engine' ),
    ),
    'wte-confirmation-page' => array(
        'label' => __( 'Confirmation Page', 'wp-travel-engine' ),
        'name'  => 'wp_travel_engine_settings[pages][wp_travel_engine_confirmation_page]',
        'selected' => isset($options['pages']['wp_travel_engine_confirmation_page']) ? esc_attr($options['pages']['wp_travel_engine_confirmation_page']) : '',
        'tooltip' => __( 'This is the confirmation page where trip bookers will fill the full form of the travelers. The <b>[WP_TRAVEL_ENGINE_BOOK_CONFIRMATION]</b> shortcode must be on this page.', 'wp-travel-engine' ),
    ),
    'wte-dashboard-page' => array(
        'label' => __( 'User Dashboard Page', 'wp-travel-engine' ),
        'name'  => 'wp_travel_engine_settings[pages][wp_travel_engine_dashboard_page]',
        'selected' => isset($options['pages']['wp_travel_engine_dashboard_page']) ? esc_attr($options['pages']['wp_travel_engine_dashboard_page']) : wp_travel_engine_get_page_id( 'my-account' ),
        'tooltip' => __( 'This is the dasbhboard page that lets your users to login and interact to bookings from frontend. The <b>[wp_travel_engine_dashboard]</b> shortcode must be on this page.', 'wp-travel-engine' ),
    ),
    'wte-enquiry-thank-you' => array(
        'label' => __( 'Enquiry Thank You Page', 'wp-travel-engine' ),
        'name'  => 'wp_travel_engine_settings[pages][enquiry]',
        'selected' => isset($options['pages']['enquiry']) ? esc_attr($options['pages']['enquiry']) : '',
        'tooltip' => __( 'This is the thankyou page where user will be redirected after successful enquiry.', 'wp-travel-engine' ),
    ),
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
                        'name'              => $page['name'],
                        'echo'              => 1,
                        'class'             => 'wpte-enhanced-select',
                        'show_option_none'  => __( '&mdash; Select &mdash;', 'wp-travel-engine' ),
                        'option_none_value' => '0',
                        'selected'          => $page['selected'],
					)
				);
            if ( isset( $page['tooltip'] ) && ! empty( $page['tooltip'] ) ) : ?>
                <span class="wpte-tooltip"><?php echo html_entity_decode( $page['tooltip'] ); ?></span>
            <?php 
            endif; ?>
        </div>
        <?php
    endforeach;
endif;