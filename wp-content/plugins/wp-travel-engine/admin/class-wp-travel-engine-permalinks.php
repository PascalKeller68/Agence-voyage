<?php
/**
 * Adds settings to the permalinks admin settings page
 *
 * @class       WP_Travel_Engine_Admin_Permalink_Settings
 * @category    Admin
 * @package     wp-travel-engine/inc/admin
 * @version     2.2.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Travel_Engine_Admin_Permalink_Settings', false ) ) :

/**
 * WP_Travel_Engine_Admin_Permalink_Settings Class.
 */
class WP_Travel_Engine_Admin_Permalink_Settings {

	/**
	 * Permalink settings.
	 *
	 * @var array
	 */
	private $permalinks = array();

	/**
	 * class constructor.
	 */
	public function __construct() {

        add_action( 'admin_init', array( $this, 'settings_init' ) );
        add_action( 'admin_init', array( $this, 'settings_save' ) );
	
	}

	/**
	 * Init our settings.
	 */
	public function settings_init() {

		add_settings_section(
			'wte_custom_permalinks_rw',
			__( 'WP Travel Engine: Trip Slugs', 'wp-travel-engine' ),
			array( $this, 'wte_settings_section_perma' ),
			'permalink'
		);

		// Add our settings
		add_settings_field(
			'wp_travel_engine_trip_slug',            // id
			__( 'Trips base', 'wp-travel-engine' ),   // setting title
			array( $this, 'trip_slug_input' ),  // display callback
			'permalink',                        // settings page
			'wte_custom_permalinks_rw'                          // settings section
		);
		add_settings_field(
			'wp_travel_engine_trip_type_slug',            // id
			__( 'Trip Type base', 'wp-travel-engine' ),   // setting title
			array( $this, 'trip_type_slug_input' ),  // display callback
			'permalink',                        // settings page
			'wte_custom_permalinks_rw'                          // settings section
		);
		add_settings_field(
			'wp_travel_engine_destination_slug',            // id
			__( 'Trip Destination base', 'wp-travel-engine' ),   // setting title
			array( $this, 'destination_slug_input' ),  // display callback
			'permalink',                        // settings page
			'wte_custom_permalinks_rw'                          // settings section
		);
		add_settings_field(
			'wp_travel_engine_activity_slug',            // id
			__( 'Trip Activity base', 'wp-travel-engine' ),   // setting title
			array( $this, 'activity_slug_input' ),  // display callback
			'permalink',                        // settings page
			'wte_custom_permalinks_rw'                          // settings section
		);
		$this->permalinks = wp_travel_engine_get_permalink_structure();
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function wte_settings_section_perma( $args ) {
		echo '<p>' . esc_html__( 'You can change the permalink slug for WP Travel Engine Trip default archive and all trip custom taxonomy archives from the settings section below.', 'wp-travel-engine' ) . '</p>';
	}

	/**
	 * Show a slug input box.
	 */
	public function trip_slug_input() {
		
		?>
		<input name="wp_travel_engine_trip_base" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['wp_travel_engine_trip_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'trip', 'slug', 'wp-travel-engine' ) ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function trip_type_slug_input() {
		
		?>
		<input name="wp_travel_engine_trip_type_base" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['wp_travel_engine_trip_type_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'trip-types', 'slug', 'wp-travel-engine' ) ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function destination_slug_input() {
		
		?>
		<input name="wp_travel_engine_destination_base" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['wp_travel_engine_destination_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'destinations', 'slug', 'wp-travel-engine' ) ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function activity_slug_input() {
		
		?>
		<input name="wp_travel_engine_activity_base" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['wp_travel_engine_activity_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'activities', 'slug', 'wp-travel-engine' ) ?>" />
		<?php
	}

	/**
	 * Save the settings.
	 */
	public function settings_save() {
		if ( ! is_admin() ) {
			return;
		}		
		// We need to save the options ourselves; settings api does not trigger save for the permalinks page.
		if ( isset( $_POST['permalink_structure'] ) ) {

			$permalinks                                = (array) get_option( 'wp_travel_engine_permalinks', array() );
			$permalinks['wp_travel_engine_trip_base']         =  trim( $_POST['wp_travel_engine_trip_base'] );
			$permalinks['wp_travel_engine_trip_type_base']    =  trim( $_POST['wp_travel_engine_trip_type_base'] );
			$permalinks['wp_travel_engine_destination_base']  =  trim( $_POST['wp_travel_engine_destination_base'] );
			$permalinks['wp_travel_engine_activity_base']     =  trim( $_POST['wp_travel_engine_activity_base'] );
			
			update_option( 'wp_travel_engine_permalinks', $permalinks );
		}
	}
}

endif;

return new WP_Travel_Engine_Admin_Permalink_Settings();
