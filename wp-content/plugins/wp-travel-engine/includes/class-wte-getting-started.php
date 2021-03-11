<?php
/**
 * Getting started handler class.
 * 
 * @package WP_Travel_Engine
 */

class WTE_Getting_Started {

    /**
     * Class constructor.
     * 
     */
    public function __construct() {
        
        $this->init_hooks();
    
    }

    /**
     * Load Admin Scripts.
     *
     * @return void
     */
    public function load_scripts () {

        global $pagenow;

        if ( isset( $_GET['page'] ) && 'class-wte-getting-started.php' === $_GET['page'] ) :

            wp_enqueue_style( 'wte_welcome', plugin_dir_url(WP_TRAVEL_ENGINE_FILE_PATH) . 'admin/css/wte-getting-started.css' );

        endif;

    }

    /**
     * Initilization hooks.
     *
     * @return void
     */
    public function init_hooks () {
        
        add_action( 'admin_menu', array( $this, 'admin_menus' ) );
        
        add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );

        add_action( 'admin_init', array( $this, 'setup_redirection' ) );
    
    }

    /**
     * Setup to getting started redirection.
     *
     * @return void
     */
    public function setup_redirection() {

        $redirect = get_transient( 'wte_show_getting_started_page' );

        if ( $redirect ) {

            set_transient( 'wte_getting_started_page_shown', true );
            set_transient( 'wte_show_getting_started_page', false );

                wp_safe_redirect( admin_url( 'index.php?page=class-wte-getting-started.php' ) );

            exit;

        }

    }

    /**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {
        
        add_dashboard_page( __( 'WP Travel Engine - Welcome', 'wp-travel-engine' ), '', 'manage_options', basename(__FILE__), array( $this, 'getting_started_template' ) );
    
    }
    
    /**
	 * Setup Wizard Header.
	 */
	public function setup_wizard_header() {
		
    }
    
    /**
     * Wizard content.
     *
     * @return void
     */
    public function setup_wizard_content() {

        ob_start();

        ?>
            <div class="wrap wte_welcome__container">

            <div class="wte_about__header">
                <div class="wte_about__header-title">
                    <h1>
                        <span><?php echo esc_html( WP_TRAVEL_ENGINE_VERSION ); ?></span>
                    </h1>
                    <span></span>
                </div>
                <div class="wte_about__header-badge">
                    <img src="https://ps.w.org/wp-travel-engine/assets/icon-256x256.png" alt="">
                </div>

                <div class="wte_about__header-text">
                <p>
                    <?php esc_html_e( 'WP Travel Engine - Major Update Release Notes', 'wp-travel-engine' ); ?>				
                </p>
                </div>

                <nav class="wte_about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="Secondary menu">
                    <a style="pointer-events:none;" href="#" class="nav-tab nav-tab-active" aria-current="page"><?php esc_html_e( 'What’s New', 'wp-travel-engine' ); ?></a>
                </nav>
            </div>

            <div class="wte_about__section changelog">
                <div class="column">
                    <h2><?php _e( 'Upadated booking process UI', 'wp-travel-engine' ); ?></h2>
                    <p>
                        <?php echo sprintf( __( 'After getting multiple requests from you, we are excited to announce that we have revamped our booking process with WP Travel Engine %1$s.', 'wp-travel-engine' ), WP_TRAVEL_ENGINE_VERSION ); ?>
                    </p>
                </div>
            </div>

            <hr>

            <div class="wte_about__section has-2-columns">
                <div class="column is-edge-to-edge">
                    <div class="about__image aligncenter">
                        <img src="<?php echo esc_url( plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'admin/css/images/process.png' ) ?>" alt="">
                    </div>
                </div>
                <div class="column is-vertically-aligned-center">
                    <h2><?php _e( 'Conversion Optimized Booking Process Indicator', 'wp-travel-engine' ); ?></h2>
                    <p>
                        <?php _e( 'This new feature allows users to view their booking process progress. It allows users to track how many steps they’ve completed and how much more details are needed to finish their booking process.', 'wp-travel-engine' ); ?>
                    </p>

                    <p>
                        <?php _e( 'With a booking process indicator, users can visualize how much longer they will need to spend on the website until the booking process is complete.', 'wp-travel-engine' ); ?> 
                    </p>

                    <p>
                        <?php _e( 'Implementing the booking process indicator will help you to create your website well optimized for excellent user experience and also helps to increase conversion rate i.e increase in the volume of bookings.', 'wp-travel-engine' ); ?>
                    </p>
                </div>
            </div>

            <div class="wte_about__section has-2-columns">
                <div class="column is-vertically-aligned-center">
                    <h2><?php _e( 'New Booking Form on the Trip Page', 'wp-travel-engine' ); ?></h2>
                    <p>
                        <?php _e( 'Beautifully designed booking form which helps users to have an optimal booking experience. This booking form is extremely user-friendly and responsive.', 'wp-travel-engine' ); ?> 
                    </p>

                    <p>
                        <?php _e( 'With the new booking form, users can select dates in the calendar, add travelers and select extra services which diminishes the hefty booking process.', 'wp-travel-engine' ); ?>
                    </p>
                </div>
                <div class="column is-edge-to-edge">
                    <div class="about__image aligncenter">
                        <img src="<?php echo esc_url( plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'admin/css/images/booking-form.png' ) ?>" alt="">
                    </div>
                </div>
            </div>

            <div class="wte_about__section has-subtle-background-color">
                <div class="column is-vertically-aligned-center">
                    <h2><?php _e( 'New Checkout Page', 'wp-travel-engine' ); ?></h2>
                    <p><?php _e( 'We have redesigned our checkout page and improved the user experience. With the new checkout page, we have removed unneeded steps and unnecessary form fields making the booking process fast and efficient.', 'wp-travel-engine' ); ?></p>

                    <p>
                    <?php _e( 'We have also placed the booking summary information on the right side of the checkout page. ', 'wp-travel-engine' ); ?></p>
                </div>
            </div>

            <div class="wte_about__section has-subtle-background-color">
                <div class="column is-edge-to-edge">
                    <div class="about__image aligncenter">
                        <img src="<?php echo esc_url( plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'admin/css/images/checkout-page.png' ) ?>" alt="">
                    </div>
                </div>
            </div>

            <hr>

            <div class="wte_about__section changelog">
                <div class="column">
                    <h2><?php _e( 'Important Notes:', 'wp-travel-engine' ); ?></h2>
                    <p>
                        <?php _e( 'If you are using any of the following addons,', 'wp-travel-engine' ); ?> <?php _e( 'we strongly recommend you to update the addons to the minimum required version to ensure the productivity with the new booking process and its features.', 'wp-travel-engine' ); ?>
                    </p>
                    <ol>
                        <li><?php _e( 'WP Travel Engine - Group Discount version 1.0.9 ', 'wp-travel-engine' ); ?></li>
                        <li><?php _e( 'WP Travel Engine - Extra Services version 1.0.2', 'wp-travel-engine' ); ?> </li>
                        <li><?php _e( 'WP Travel Engine - PayPal Express Gateway version 1.0.2', 'wp-travel-engine' ); ?></li>
                        <li><?php _e( 'WP Travel Engine - Stripe Payment Gateway version 1.0.6', 'wp-travel-engine' ); ?></li>
                        <li><?php _e( 'WP Travel Engine - Trip Fixed Starting Dates version 1.2.2', 'wp-travel-engine' ); ?></li>
                        <li><?php _e( 'WP Travel Engine - PayU Payment Gateway version 1.0.4', 'wp-travel-engine' ); ?></li>
                    </ol>
                    <p>
                        <strong><?php _e( 'Not ready to use the new booking flow yet?', 'wp-travel-engine' ); ?></strong><br><?php _e( 'We have got your back. If you are not ready to switch to the new improved, conversion-optimized and sales-oriented booking flow updates, we also provide backward compatibility with older versions.', 'wp-travel-engine' ); ?>
                    </p>
                    <p>
                        <?php _e( 'To revert back to the older booking process and checkout flow, please follow this link:', 'wp-travel-engine' ); ?>
                        <a href="https://wptravelengine.com/wp-travel-engine-3-0-backward-compatibility/" target="_blank"><?php _e( 'https://wptravelengine.com/wp-travel-engine-3-0-backward-compatibility', 'wp-travel-engine' ); ?>/</a>

                    </p>
                </div>
            </div>

            <hr>

            <div class="wte_about__section">
                <h2 class="is-section-header"><?php _e( 'Need further assistance?', 'wp-travel-engine' ); ?></h2>

                <div class="column">
                    <h3><?php _e( 'Contact Support', 'wp-travel-engine' ); ?></h3>
                    <p>
                        <?php _e( 'As always, if you have any queries regarding the features or any add-ons, just send us an email to', 'wp-travel-engine' ) ?> <a href="mailto:support@wptravelengine.com" target="_blank"><?php _e( 'support@wptravelengine.com', 'wp-travel-engine' ); ?></a> <?php _e( 'or raise a ticket at', 'wp-travel-engine' ); ?> <a href="https://wptravelengine.com/support-ticket/" target="_blank"><?php _e( 'https://wptravelengine.com/support-ticket/', 'wp-travel-engine' ); ?></a>

                    </p>
                </div>
            </div>

            <hr>

            <div class="return-to-dashboard">
                <a href="<?php echo esc_url( admin_url() ); ?>"><?php _e( 'Go to Dashboard → Home', 'wp-travel-engine' ); ?></a>
            </div>
            </div>

        <?php

        $data = ob_get_clean();

        return $data;

    }

	/**
	 * Setup Wizard Footer.
	 */
	public function setup_wizard_footer() {
		
	}

    /**
     * Template output.
     *
     * @return void
     */
    public function getting_started_template() {

        if ( empty( $_GET['page'] ) || 'class-wte-getting-started.php' !== $_GET['page'] ) { // WPCS: CSRF ok, input var ok.
			return;
		}

        $this->setup_wizard_header();
            
            echo $this->setup_wizard_content();
        
        $this->setup_wizard_footer();

    }

}
new WTE_Getting_Started();
