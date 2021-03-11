<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Wp_Travel_Engine
 * @subpackage Wp_Travel_Engine/admin
 */
class Wp_Travel_Engine_Admin {


	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		add_image_size( 'trip-thumb-size', 374, 226, true ); // 260 pixels wide by 210 pixels tall, hard crop mode
		add_image_size( 'destination-thumb-size', 300, 275, true ); // 260 pixels wide by 210 pixels tall, hard crop mode
		add_image_size( 'destination-thumb-trip-size', 410, 250, true );
		add_image_size( 'activities-thumb-size', 300, 405, true ); // 260 pixels wide by 210 pixels tall, hard crop mode
		add_image_size( 'trip-single-size', 990, 490, true ); // 800 pixels wide by 284 pixels tall, hard crop mode
		// remove_filter( 'the_content', 'wpautop' );
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Travel_Triping_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Travel_Triping_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$screen = get_current_screen();

		// if ( $screen->id == 'booking_page_class-wp-travel-engine-admin' ) {
		// wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-travel-engine-admin.css', array(), $this->version, 'all');
		// wp_enqueue_style('select2', plugin_dir_url(__FILE__) . 'css/select2.css', array(), $this->version, 'all');
		// }
		if ( 'booking_page_wp_travel_engine_license_page' === $screen->id ) {
			wp_enqueue_style( $this->plugin_name . '_license_page', plugin_dir_url( __FILE__ ) . 'css/license-page.css', array(), $this->version, 'all' );
		}
		wp_register_style( $this->plugin_name . '_core_ui', plugin_dir_url( __FILE__ ) . 'css/wte-admin-ui.css', array(), $this->version, 'all' );

		if ( $screen->post_type == 'trip' || $screen->post_type == 'enquiry' || $screen->post_type == 'booking' || $screen->post_type == 'customer' || $screen->post_type == 'wte-coupon' || isset( $_GET['page'] ) && $_GET['page'] == 'class-wp-travel-engine-admin.php' || $screen->id == 'trip_page_class-wp-travel-engine-admin' || $screen->post_type == 'downloadfile' ) {
			wp_enqueue_style( 'toastr.min.css', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'public/css/toastr.min.css', array(), $this->version, 'all' );

			wp_enqueue_style( $this->plugin_name . '_core_ui' );

			wp_enqueue_style( 'select2', plugin_dir_url( __FILE__ ) . 'css/select2.css', array(), $this->version, 'all' );
		}
		if ( $screen->post_type == 'booking' || $screen->post_type == 'customer' || $screen->post_type == 'wte-coupon' || isset( $_GET['page'] ) && $_GET['page'] == 'reviews' ) {
			wp_enqueue_style( 'datepicker-style', plugin_dir_url( __FILE__ ) . 'css/datepicker-style.css', array(), $this->version, 'all' );
		}

		wp_register_style( 'magnific-popup', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'public/css/magnific-popup.min.css', array(), $this->version, 'all' );

		if ( 'edit-enquiry' === $screen->id ) {
			wp_enqueue_style( 'wte-enquiry-css', plugin_dir_url( __FILE__ ) . 'css/wte-enquiry-css.css', array( 'magnific-popup' ), null, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Travel_Triping_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Travel_Triping_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$asset_script_path = '/dist/';
		$version_prefix    = '-' . WP_TRAVEL_ENGINE_VERSION;

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$asset_script_path = '/';
			$version_prefix    = '';
		}

		$screen = get_current_screen();

		$wte_post_types = array( 'trip', 'enquiry', 'booking', 'customer', 'wte-coupon', 'downloadfile' );
		$wte_page_ids   = array( 'trip_page_class-wp-travel-engine-admin' );

		if ( in_array( $screen->post_type, $wte_post_types ) || isset( $_GET['page'] ) && $_GET['page'] == 'class-wp-travel-engine-admin.php' || in_array( $screen->id, $wte_page_ids ) ) {

			wp_enqueue_editor();
			wp_enqueue_media();

			wp_register_script( 'toastr.min.js', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . '/public/js/lib/toastr.min.js', array( 'jquery' ), null, true );

			wp_register_script( 'parsley', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'public/js/lib/parsley-min.js', array( 'jquery' ), null, true );

			wp_register_script( 'magnific-popup', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'public/js/lib/magnific-popup.min.js', array( 'jquery' ), '2.9.2', true );

			wp_register_script( 'select2', plugin_dir_url( __FILE__ ) . 'js' . $asset_script_path . 'select2' . $version_prefix . '.js', array( 'jquery' ), '5.6.3', true );

			wp_enqueue_script( 'font-awesome', plugin_dir_url( __FILE__ ) . 'js/fontawesome/all.js', array( 'jquery' ), '5.6.3', true );

			wp_enqueue_script( 'v4-shims', plugin_dir_url( __FILE__ ) . 'js/fontawesome/v4-shims.js', array( 'jquery' ), '5.6.3', true );

			wp_enqueue_script( $this->plugin_name . '_ui', plugin_dir_url( __FILE__ ) . 'js' . $asset_script_path . 'wte-admin-ui' . $version_prefix . '.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-tabs', 'jquery-ui-sortable', 'toastr.min.js', 'parsley', 'select2' ), $this->version, true );

			$wte_core_ui_translations = array(
				'suretodel'        => __( 'Sure to delete? This action cannot be reverted.', 'wp-travel-engine' ),
				'validation_error' => __( 'Validation Error. Settings could not be saved.', 'wp-travel-engine' ),
				'copied'           => __( 'Text copied to clipboard.', 'wp-travel-engine' ),
				'novid'            => __( 'No video URL supplied.', 'wp-travel-engine' ),
				'invalid_url'      => __( 'Invalid URL supplied. Please make sure to add valid YouTube or Vimeo video URL', 'wp-travel-engine' ),
				'version'          => __( str_replace( '.', '', $this->version ), 'wp-travel-engine' ),
			);

			wp_localize_script( $this->plugin_name . '_ui', 'WTE_UI', $wte_core_ui_translations );

			wp_enqueue_script( $this->plugin_name . 'media-logo-upload', plugin_dir_url( __FILE__ ) . 'js' . $asset_script_path . 'media-upload' . $version_prefix . '.js', array( 'jquery' ), $this->version, true );

			if ( 'edit-enquiry' === $screen->id ) {
				wp_enqueue_script( 'wte-enquiry-script', plugin_dir_url( __FILE__ ) . 'js/wte-enquiry-scripts.js', array( 'jquery', 'magnific-popup' ), null, true );
			}

			if ( $screen->post_type == 'trip' || $screen->post_type == 'booking' || $screen->post_type == 'customer' ) {
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_script( $this->plugin_name . 'custom', plugin_dir_url( __FILE__ ) . 'js' . $asset_script_path . 'custom' . $version_prefix . '.js', array( 'jquery' ), $this->version, true );
			}

			if ( $screen->post_type == 'trip' ) {
				wp_enqueue_script( $this->plugin_name . 'gallery-metabox', plugin_dir_url( __FILE__ ) . 'js' . $asset_script_path . 'gallery-metabox' . $version_prefix . '.js', array( 'jquery' ), $this->version, true );
			}
		}
	}


	/**
	 * Register a Trip post type.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	function wp_travel_engine_register_trip() {
		$permalink = wp_travel_engine_get_permalink_structure();

		$labels = array(
			'name'               => _x( 'Trips', 'post type general name', 'wp-travel-engine' ),
			'singular_name'      => _x( 'Trip', 'post type singular name', 'wp-travel-engine' ),
			'menu_name'          => _x( 'Trips', 'admin menu', 'wp-travel-engine' ),
			'name_admin_bar'     => _x( 'Trip', 'add new on admin bar', 'wp-travel-engine' ),
			'add_new'            => _x( 'Add New', 'Trip', 'wp-travel-engine' ),
			'add_new_item'       => __( 'Add New Trip', 'wp-travel-engine' ),
			'new_item'           => __( 'New Trip', 'wp-travel-engine' ),
			'edit_item'          => __( 'Edit Trip', 'wp-travel-engine' ),
			'view_item'          => __( 'View Trip', 'wp-travel-engine' ),
			'all_items'          => __( 'All Trips', 'wp-travel-engine' ),
			'search_items'       => __( 'Search Trips', 'wp-travel-engine' ),
			'parent_item_colon'  => __( 'Parent Trips:', 'wp-travel-engine' ),
			'not_found'          => __( 'No Trips found.', 'wp-travel-engine' ),
			'not_found_in_trash' => __( 'No Trips found in Trash.', 'wp-travel-engine' ),
		);

		$WTE_TRIP_SVG = base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 23.45 22.48"><title>Asset 2</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1" fill="#fff"><path d="M6.71,9.25c-.09.65-.17,1.27-.27,1.89s-.28,1.54-.4,2.31a.36.36,0,0,0,.07.22c.47.73.93,1.47,1.42,2.18a2.27,2.27,0,0,1,.39,1c.18,1.43.38,2.86.57,4.29a1,1,0,1,1-2,.3C6.3,20.31,6.13,19.14,6,18a3.19,3.19,0,0,0-.59-1.62C5,15.76,4.6,15.11,4.18,14.5a.7.7,0,0,0-.26-.22,1.58,1.58,0,0,1-1-1.69q.5-3.54,1-7.06A1.61,1.61,0,0,1,7.19,6a.82.82,0,0,0,.09.41c.19.39.4.77.62,1.14a.82.82,0,0,0,.35.29c1,.37,2.06.71,3.09,1.07a1,1,0,0,1,.35,1.61.83.83,0,0,1-.85.22c-1.32-.44-2.62-.9-3.93-1.35Z"/><path d="M2.4,3.38A1.36,1.36,0,0,1,3.75,5c-.23,1.6-.46,3.2-.71,4.79a3,3,0,0,1-.26,1,1.3,1.3,0,0,1-1.57.63,1.33,1.33,0,0,1-1-1.5Q.61,7.22,1,4.58A1.38,1.38,0,0,1,2.4,3.38Z"/><path d="M3.05,14.2a2.41,2.41,0,0,1,.75.39,14.73,14.73,0,0,1,.91,1.32c-.07.32-.17.63-.22.95a8.43,8.43,0,0,1-1.11,2.42C2.92,20.15,2.43,21,2,21.87a1,1,0,1,1-1.8-1L2.29,17a1.74,1.74,0,0,0,.14-.38c.19-.78.38-1.55.58-2.33Z"/><path d="M8.34,2a2,2,0,0,1-4,0,2,2,0,0,1,4,0Z"/><path d="M10.6,10.94l.56.07c0,.36,0,.73-.06,1.1,0,.68-.11,1.37-.15,2.05-.14,2-.27,4-.4,6L10.43,22c0,.35-.11.51-.31.5s-.28-.16-.25-.52c.11-1.76.23-3.51.34-5.27.1-1.51.19-3,.28-4.53C10.52,11.76,10.56,11.36,10.6,10.94Z"/><path d="M11.31,8.57c-.54-.14-.54-.14-.52-.64s.06-.9.1-1.34c0-.19.1-.31.3-.3s.27.15.26.33C11.4,7.27,11.36,7.91,11.31,8.57Z"/><path d="M18.16,9.25c-.1.65-.17,1.27-.28,1.89s-.27,1.54-.4,2.31a.37.37,0,0,0,.08.22c.47.73.93,1.47,1.42,2.18a2.27,2.27,0,0,1,.39,1c.18,1.43.38,2.86.57,4.29a1,1,0,1,1-2,.3c-.16-1.17-.33-2.34-.47-3.51a3.18,3.18,0,0,0-.58-1.62c-.44-.59-.82-1.24-1.23-1.85a.7.7,0,0,0-.26-.22,1.58,1.58,0,0,1-1-1.69q.5-3.54,1-7.06A1.59,1.59,0,0,1,17.2,4.2,1.62,1.62,0,0,1,18.64,6a.82.82,0,0,0,.08.41q.3.59.63,1.14a.82.82,0,0,0,.35.29c1,.37,2.06.71,3.08,1.07a1,1,0,0,1,.35,1.61.83.83,0,0,1-.85.22c-1.31-.44-2.62-.9-3.92-1.35Z"/><path d="M13.84,3.38A1.36,1.36,0,0,1,15.2,5c-.23,1.6-.47,3.2-.71,4.79a3,3,0,0,1-.26,1,1.3,1.3,0,0,1-1.57.63,1.33,1.33,0,0,1-1-1.5q.38-2.65.77-5.29A1.37,1.37,0,0,1,13.84,3.38Z"/><path d="M14.49,14.2a2.36,2.36,0,0,1,.76.39c.34.41.61.88.91,1.32-.08.32-.17.63-.22.95a8.7,8.7,0,0,1-1.11,2.42c-.46.87-.95,1.72-1.43,2.59a1,1,0,0,1-1.44.47,1,1,0,0,1-.35-1.46L13.74,17a2.46,2.46,0,0,0,.14-.38c.19-.78.38-1.55.58-2.33A.19.19,0,0,1,14.49,14.2Z"/><path d="M19.79,2a2,2,0,1,1-2-2A2,2,0,0,1,19.79,2Z"/><path d="M22.05,10.94l.56.07-.06,1.1c-.05.68-.11,1.37-.16,2.05l-.39,6c-.05.61-.08,1.23-.12,1.85,0,.35-.11.51-.31.5s-.28-.16-.26-.52c.12-1.76.24-3.51.35-5.27.1-1.51.19-3,.28-4.53C22,11.76,22,11.36,22.05,10.94Z"/><path d="M22.76,8.57c-.54-.14-.55-.14-.52-.64s.06-.9.09-1.34c0-.19.11-.31.3-.3a.26.26,0,0,1,.26.33C22.85,7.27,22.8,7.91,22.76,8.57Z"/></g></g></svg>' );

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'wp-travel-engine' ),
			'public'             => true,
			'menu_icon'          => 'data:image/svg+xml;base64,' . $WTE_TRIP_SVG,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => false,
			'query_var'          => true,
			'rewrite'            => array(
				'slug'       => $permalink['wp_travel_engine_trip_base'],
				'with_front' => true,
			),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 31,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
		);

		register_post_type( 'trip', $args );
		flush_rewrite_rules();
	}

	/**
	 * Register a Enquiry post type.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	function wp_travel_engine_register_enquiry() {
		$labels = array(
			'name'               => _x( 'Enquiries', 'post type general name', 'wp-travel-engine' ),
			'singular_name'      => _x( 'Enquiry', 'post type singular name', 'wp-travel-engine' ),
			'menu_name'          => _x( 'Enquiries', 'admin menu', 'wp-travel-engine' ),
			'name_admin_bar'     => _x( 'Enquiry', 'add new on admin bar', 'wp-travel-engine' ),
			'add_new'            => _x( 'Add New', 'Enquiry', 'wp-travel-engine' ),
			'add_new_item'       => __( 'Add New Enquiry', 'wp-travel-engine' ),
			'new_item'           => __( 'New Enquiry', 'wp-travel-engine' ),
			'edit_item'          => __( 'Edit Enquiry', 'wp-travel-engine' ),
			'view_item'          => __( 'View Enquiry', 'wp-travel-engine' ),
			'all_items'          => __( 'All Enquiries', 'wp-travel-engine' ),
			'search_items'       => __( 'Search Enquiries', 'wp-travel-engine' ),
			'parent_item_colon'  => __( 'Parent Enquiries:', 'wp-travel-engine' ),
			'not_found'          => __( 'No Enquiries found.', 'wp-travel-engine' ),
			'not_found_in_trash' => __( 'No Enquiries found in Trash.', 'wp-travel-engine' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'wp-travel-engine' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=booking',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'enquiry' ),
			'capability_type'    => 'post',
			'capabilities'       => array(
				'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
			),
			'map_meta_cap'       => true, // Set to `false`, if users are not allowed to edit/delete existing posts
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 40,
			'supports'           => array( 'title' ),
		);

		register_post_type( 'enquiry', $args );
		flush_rewrite_rules();
	}

	/**
	 * Register a Booking History post type.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	function wp_travel_engine_register_booking() {
		$labels = array(
			'name'               => _x( 'Bookings', 'post type general name', 'wp-travel-engine' ),
			'singular_name'      => _x( 'Booking', 'post type singular name', 'wp-travel-engine' ),
			'menu_name'          => _x( 'WP Travel Engine', 'admin menu', 'wp-travel-engine' ),
			'name_admin_bar'     => _x( 'Booking', 'add new on admin bar', 'wp-travel-engine' ),
			'add_new'            => _x( 'Add New', 'Booking', 'wp-travel-engine' ),
			'add_new_item'       => __( 'Add New Booking', 'wp-travel-engine' ),
			'new_item'           => __( 'New Booking', 'wp-travel-engine' ),
			'edit_item'          => __( 'Edit Booking', 'wp-travel-engine' ),
			'view_item'          => __( '', 'wp-travel-engine' ),
			'all_items'          => __( 'All Bookings', 'wp-travel-engine' ),
			'search_items'       => __( 'Search Bookings', 'wp-travel-engine' ),
			'parent_item_colon'  => __( 'Parent Bookings:', 'wp-travel-engine' ),
			'not_found'          => __( 'No Bookings found.', 'wp-travel-engine' ),
			'not_found_in_trash' => __( 'No Bookings found in Trash.', 'wp-travel-engine' ),
		);

		$WTE_SVG = base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 32.1"><title>Asset 1</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1" fill="#fff"><path d="M11.87,12.19l.24-5.86c-.67.13-1.34.26-2,.37l-1.29.21c-.25,0-.43.09-.42.4s-.08.32-.07.48c0,.33-.08.51-.47.51L7.66,7.13H7.21L7.14,7l.38-.36L7,5.7c.3-.21.49-.21.73.11.47.65.5.62,1.22.25l2.34-1.13a3.35,3.35,0,0,0,.3-.19L8.09.15c.21-.21.38-.19.62,0l4.6,3.43a1.38,1.38,0,0,0,1.33.18,7.29,7.29,0,0,1,3.08-.29,1.27,1.27,0,0,1,.27.11,4.09,4.09,0,0,1-1.45,1.15c-.65.32-1.35.57-2,.93-.2.11-.29.45-.36.71-.34,1.13-.65,2.28-1,3.41-.21.71-.45,1.42-.68,2.12C12.44,12.17,12.22,12.25,11.87,12.19Z"/><path d="M14.1,30.79a1.31,1.31,0,0,1-1.3,1.31h-.17q-.33,0-.66-.09c-.57-.1-1.15-.14-1.71-.29a13.9,13.9,0,0,1-7.54-5A10.72,10.72,0,0,1,1.1,23.87a21.8,21.8,0,0,1-.73-2.2C.19,20.94.15,20.19,0,19.44c0-.08,0-.16,0-.25v-.71a14.51,14.51,0,0,1,.2-1.63c.19-.84.42-1.67.68-2.49a11.31,11.31,0,0,1,1.58-3A14.1,14.1,0,0,1,5.69,8l.75-.52c.15-.09.32-.2.47.08l-.85.6a10.87,10.87,0,0,0-3.37,4,15.48,15.48,0,0,0-.87,2.42A11.35,11.35,0,0,0,1.51,19a11.72,11.72,0,0,0,.7,2.89A11.92,11.92,0,0,0,4.14,25.3a10.84,10.84,0,0,0,3.85,3,13,13,0,0,0,2.34.89,10.59,10.59,0,0,0,2.32.28h.29a1.29,1.29,0,0,1,1.1.9h0a.29.29,0,0,1,0,.09h0A.92.92,0,0,1,14.1,30.79Z"/><path d="M12.94,29.5h0Z"/><path d="M19.29,13.13c-.07.2-.11.39-.18.56s-.21.54-.32.81-.2.46-.3.69a.3.3,0,0,0,0,.16c.06.16.14.31.19.47s.23.26.4.32l.93.35c.23.09.46.18.68.29s.34.21.34.5c0,.47,0,.95,0,1.43a.44.44,0,0,1-.28.47c-.36.17-.72.33-1.09.48l-.74.29c-.17.06-.19.21-.25.33a1.11,1.11,0,0,0-.16.39.71.71,0,0,0,.08.31l.75,1.76a.27.27,0,0,1-.07.33L18,24.27a.32.32,0,0,1-.32.08c-.34-.11-.69-.22-1-.35s-.64-.27-1-.39a.51.51,0,0,0-.3,0,3.2,3.2,0,0,0-.43.17.38.38,0,0,0-.23.25c-.08.27-.19.52-.29.78s-.21.5-.32.75a3.54,3.54,0,0,1-.19.33.27.27,0,0,1-.26.15H12a.37.37,0,0,1-.34-.23c-.15-.31-.3-.63-.44-.95s-.25-.62-.39-.93a.29.29,0,0,0-.16-.12,3.89,3.89,0,0,0-.51-.21c-.17-.07-.31.05-.46.11-.34.13-.68.29-1,.43l-.58.23a.43.43,0,0,1-.49-.09q-.52-.54-1.08-1.05a.49.49,0,0,1-.13-.59c.24-.61.5-1.21.74-1.82a.23.23,0,0,0,0-.14,5.44,5.44,0,0,1-.22-.5c0-.17-.18-.21-.31-.26l-.91-.34L5,19.3a.79.79,0,0,1-.25-.15.45.45,0,0,1-.12-.25c0-.55,0-1.11,0-1.66A.29.29,0,0,1,4.76,17c.3-.15.59-.3.9-.43s.75-.29,1.11-.46c.09,0,.14-.19.19-.3s.11-.24.16-.37a.17.17,0,0,0,0-.12c-.18-.41-.38-.82-.56-1.23a6.92,6.92,0,0,1-.28-.76A.3.3,0,0,1,6.37,13l1.15-1.12a.43.43,0,0,1,.5-.1c.29.12.6.2.89.31s.63.27.95.4a.44.44,0,0,0,.25,0,5.55,5.55,0,0,0,.56-.24.24.24,0,0,0,.13-.13c.14-.33.25-.68.39-1s.28-.61.43-.91A.33.33,0,0,1,12,10h1.58a.42.42,0,0,1,.41.23c.16.32.32.65.46,1s.25.63.39.93c0,.08.18.12.28.17a2.5,2.5,0,0,0,.38.15.33.33,0,0,0,.22,0c.41-.16.81-.35,1.22-.51a6.06,6.06,0,0,1,.75-.27.36.36,0,0,1,.36.1l1.17,1.15A.91.91,0,0,1,19.29,13.13Zm-6.58,1.68a3,3,0,0,0-1.48.39,3.2,3.2,0,0,0-1.67,2.32,3.05,3.05,0,0,0,.49,2.26,3.22,3.22,0,0,0,2.18,1.41A3.27,3.27,0,0,0,16,18.52a3,3,0,0,0-.31-1.94,3.1,3.1,0,0,0-1.46-1.42A3.4,3.4,0,0,0,12.71,14.81Z"/></g></g></svg>' );

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'wp-travel-engine' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			// 'show_in_menu'       => 'edit.php?post_type=trip',
			'menu_icon'          => 'data:image/svg+xml;base64,' . $WTE_SVG,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'booking' ),
			'capability_type'    => 'post',
			// 'capabilities' => array(
			// 'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
			// ),
			'map_meta_cap'       => true, // Set to `false`, if users are not allowed to edit/delete existing posts
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 31,
			'supports'           => array( 'title' ),
		);

		register_post_type( 'booking', $args );
		flush_rewrite_rules();
	}


	/**
	 * Register a Customer History post type.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	function wp_travel_engine_register_customer() {
		 $labels = array(
			 'name'               => _x( 'Customers', 'post type general name', 'wp-travel-engine' ),
			 'singular_name'      => _x( 'Customer', 'post type singular name', 'wp-travel-engine' ),
			 'menu_name'          => _x( 'Customers', 'admin menu', 'wp-travel-engine' ),
			 'name_admin_bar'     => _x( 'Customer', 'add new on admin bar', 'wp-travel-engine' ),
			 'add_new'            => _x( 'Add New', 'Customer', 'wp-travel-engine' ),
			 'add_new_item'       => __( 'Add New Customer', 'wp-travel-engine' ),
			 'new_item'           => __( 'New Customer', 'wp-travel-engine' ),
			 'edit_item'          => __( 'Edit Customer', 'wp-travel-engine' ),
			 'view_item'          => __( '', 'wp-travel-engine' ),
			 'all_items'          => __( 'All Customers', 'wp-travel-engine' ),
			 'search_items'       => __( 'Search Customers', 'wp-travel-engine' ),
			 'parent_item_colon'  => __( 'Parent Customers:', 'wp-travel-engine' ),
			 'not_found'          => __( 'No Customers found.', 'wp-travel-engine' ),
			 'not_found_in_trash' => __( 'No Customers found in Trash.', 'wp-travel-engine' ),
		 );

		 $args = array(
			 'labels'             => $labels,
			 'description'        => __( 'Description.', 'wp-travel-engine' ),
			 'public'             => false,
			 'menu_icon'          => 'dashicons-location-alt',
			 'publicly_queryable' => false,
			 'show_ui'            => true,
			 'show_in_menu'       => 'edit.php?post_type=booking',
			 'query_var'          => true,
			 'rewrite'            => array( 'slug' => 'customer' ),
			 'capability_type'    => 'post',
			 'capabilities'       => array(
				 'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
			 ),
			 'map_meta_cap'       => true, // Set to `false`, if users are not allowed to edit/delete existing posts
			 'has_archive'        => true,
			 'hierarchical'       => false,
			 'menu_position'      => 50,
			 'supports'           => array( 'title' ),
		 );

		 register_post_type( 'customer', $args );
		 flush_rewrite_rules();
	}

	/**
	 * Remove column author and date and add trip id, trip name, travelers and cost column.
	 *
	 * @since 1.0.0
	 */
	function wp_travel_engine_booking_cpt_columns( $columns ) {
		unset(
			$columns['author'],
			$columns['date']
		);
		$new_columns = array(
			'booking_date'   => __( 'Date', 'wp-travel-engine' ),
			'tname'          => __( 'Trip Name', 'wp-travel-engine' ),
			'travelers'      => __( 'Travelers', 'wp-travel-engine' ),
			'booking_status' => __( 'Booking Status', 'wp-travel-engine' ),
			'paid'           => __( 'Total Paid', 'wp-travel-engine' ),
			'remaining'      => __( 'Remaining Payment', 'wp-travel-engine' ),
			'cost'           => __( 'Total Cost', 'wp-travel-engine' ),
		);
		return array_merge( $columns, $new_columns );
	}

	/**
	 * Add Enquiry column in the enquiry list.
	 *
	 * @since    1.0.0
	 */
	function wp_travel_engine_enquiry_cpt_columns( $columns ) {
		 $new_columns = array(
			 'enquiry_date' => __( 'Date', 'wp-travel-engine' ),
			 'email'        => __( 'Email', 'wp-travel-engine' ),
			 'preview'      => __( 'Preview', 'wp-travel-engine' ),
		 );
		 unset( $columns['date'] );
		 return array_merge( $columns, $new_columns );
	}

	/**
	 * Remove enquiry edit links.
	 *
	 * @return void
	 */
	function enquiry_remove_row_actions( $actions ) {
		if ( get_post_type() === 'enquiry' ) {
			unset( $actions['edit'] );
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}

	/**
	 * Show value to the corresponsing columns for booking post type.
	 *
	 * @since    1.0.0
	 */
	function wp_travel_engine_enquiry_custom_columns( $column, $post_id ) {
		 $wp_travel_engine_setting = get_post_meta( $post_id, 'wp_travel_engine_setting', true );
		$screen                    = get_current_screen();
		if ( $screen && $screen->post_type == 'enquiry' ) {
			switch ( $column ) {
				case 'email':
					echo isset( $wp_travel_engine_setting['enquiry']['email'] ) ? esc_attr( $wp_travel_engine_setting['enquiry']['email'] ) : '-';
					break;

				case 'preview':
					echo '<span data-enquiryid="' . $post_id . '" class="wte-preview-enquiry dashicons dashicons-welcome-view-site"></span>';
					break;
				case 'enquiry_date':
					$enquiry_date = wte_get_human_readable_diff_post_published_date( $post_id );
					// SHow the date.
					echo $enquiry_date;
					break;
			}
		}
	}

	/**
	 * Show value to the corresponsing columns for booking post type.
	 *
	 * @since    1.0.0
	 */
	function wp_travel_engine_booking_custom_columns( $column, $post_id ) {
		$terms = get_post_meta( $post_id, 'wp_travel_engine_booking_setting', true );

		$booking = get_post( $post_id );

		$order_trip = null;
		if ( 'booking' === $booking->post_type && isset( $booking->order_trips ) ) {
			$order_trips = $booking->order_trips;
			$order_trip = (object) array_shift( $order_trips );
		}

		$wp_travel_engine_setting_option_setting = get_option( 'wp_travel_engine_settings', true );
		$screen                                  = get_current_screen();

		$column_actions = array(
			'cost'           => function( $booking ) use ( $order_trip ) {
				if ( empty( $booking->payments ) ) { // legacy.
					$terms = get_post_meta( $booking->ID, 'wp_travel_engine_booking_setting', true );
					$code = 'USD';
					if ( isset( $wp_travel_engine_setting_option_setting['currency_code'] ) && $wp_travel_engine_setting_option_setting['currency_code'] != '' ) {
						$code = $wp_travel_engine_setting_option_setting['currency_code'];
					}
					$obj = new Wp_Travel_Engine_Functions();
					$currency = $obj->wp_travel_engine_currencies_symbol( $code );
					echo esc_attr( $currency ) . ' ';

					if ( isset( $terms['place_order']['cost'] ) ) {
						echo floatval( $terms['place_order']['cost'] ) + floatval( $terms['place_order']['due'] );
					}
				} else { // booking_version 2.0.0
					echo wte_get_formated_price( $booking->cart_info['total'], $booking->cart_info['currency'], '', true );
				}
			},
			'remaining'      => function( $booking ) use ( $order_trip ) {
				if ( ! empty( $booking->payments ) ) {
					echo wte_get_formated_price( $booking->due_amount, $booking->cart_info['currency'], '', true );
					return;
				}
				$terms = get_post_meta( $booking->ID, 'wp_travel_engine_booking_setting', true );

				if ( isset( $terms['place_order']['due'] ) && $terms['place_order']['due'] != '' ) {
					$code = 'USD';
					if ( isset( $wp_travel_engine_setting_option_setting['currency_code'] ) && $wp_travel_engine_setting_option_setting['currency_code'] != '' ) {
						$code = $wp_travel_engine_setting_option_setting['currency_code'];
					}
					$obj = new Wp_Travel_Engine_Functions();
					$currency = $obj->wp_travel_engine_currencies_symbol( $code );
					echo esc_attr( $currency ) . ' ';
					echo esc_attr( $terms['place_order']['due'] );
					return;
				}
				echo '-';
			},
			'paid'           => function( $booking ) use ( $order_trip ) {
				if ( ! empty( $booking->payments ) ) {
					echo wte_get_formated_price( $booking->paid_amount, $booking->cart_info['currency'], '', true );
					return;
				}
				$terms = get_post_meta( $booking->ID, 'wp_travel_engine_booking_setting', true );

				if ( isset( $terms['place_order']['due'] ) && $terms['place_order']['due'] != '' ) {
					$code = 'USD';
					if ( isset( $wp_travel_engine_setting_option_setting['currency_code'] ) && $wp_travel_engine_setting_option_setting['currency_code'] != '' ) {
						$code = $wp_travel_engine_setting_option_setting['currency_code'];
					}
					$obj = new Wp_Travel_Engine_Functions();
					$currency = $obj->wp_travel_engine_currencies_symbol( $code );
					echo esc_attr( $currency ) . ' ';
					echo floatval( $terms['place_order']['cost'] ) + floatval( $terms['place_order']['due'] ) - floatval( $terms['place_order']['due'] );
				} else {
					$code = 'USD';
					if ( isset( $wp_travel_engine_setting_option_setting['currency_code'] ) && $wp_travel_engine_setting_option_setting['currency_code'] != '' ) {
						$code = $wp_travel_engine_setting_option_setting['currency_code'];
					}
					$obj = new Wp_Travel_Engine_Functions();
					$currency = $obj->wp_travel_engine_currencies_symbol( $code );
					echo esc_attr( $currency ) . ' ';
					echo isset( $terms['place_order']['cost'] ) ? esc_attr( $terms['place_order']['cost'] ) : '';
				}
			},
			'booking_status' => function( $booking ) {
				$status = wp_travel_engine_get_booking_status();
				$label_key = get_post_meta( $booking->ID, 'wp_travel_engine_booking_status', true );
				$label_key = ! empty( $label_key ) ? $label_key : 'booked';
				?>
					<span style="margin:10px;padding:10px;font-weight:700;color:#ffffff;background-color:<?php echo esc_attr( $status[ $label_key ]['color'] ); ?>" ><?php echo esc_html( $status[ $label_key ]['text'] ); ?></span>
				<?php
			},
			'travelers'      => function( $booking ) use ( $order_trip ) {
				$terms = get_post_meta( $booking->ID, 'wp_travel_engine_booking_setting', true );

				if ( isset( $terms['place_order']['traveler'] ) ) {
					echo esc_attr( $terms['place_order']['traveler'] );
				} else {
					echo ! is_null( $order_trip ) && is_array( $order_trip->pax ) ? array_sum( $order_trip->pax ) : 0;
				}
			},
			'tname'          => function( $booking ) use ( $order_trip ) {
				if ( ! is_null( $order_trip ) && isset( $order_trip->ID ) ) {
					echo '<a href="' . get_edit_post_link( $order_trip->ID, 'display' ) . '">' . $order_trip->title . '</a>';
					echo count( $booking->order_trips ) > 1 ? esc_html( ' +' . ( count( $booking->order_trips ) - 1 ) ) : '';
					return;
				}
				$terms = get_post_meta( $booking->ID, 'wp_travel_engine_booking_setting', true );

				if ( isset( $terms['place_order']['tid'] ) ) {
					echo '<a href="' . get_edit_post_link( $terms['place_order']['tid'], 'display' ) . '">' . get_the_title( $terms['place_order']['tid'] ) . '</a>';
				}
			},
			'booking_date'   => function( $booking ) {
				echo wte_get_human_readable_diff_post_published_date( $booking->ID );
			},
		);

		if ( in_array( get_current_screen()->post_type, array( 'booking', 'customer' ) ) ) {
			if ( isset( $column_actions[ $column ] ) ) {
				call_user_func( $column_actions[ $column ], $booking );
			}
		}

	}

	/**
	 * Add column Thumbnail.
	 *
	 * @since    1.0.0
	 */
	function wp_travel_engine_trip_types_columns( $columns ) {
		$columns['thumb_id'] = __( 'Thumbnail', 'wp-travel-engine' );
		$columns['tax_id']   = __( 'Term ID', 'wp-travel-engine' );
		$columns['featured'] = __( 'Featured', 'wp-travel-engine' );
		return $columns;
	}

	/**
	 * Show thumbnail.
	 *
	 * @since    1.0.0
	 */
	function wp_travel_engine_trip_types_custom_columns( $content, $column_name, $term_id ) {
		switch ( $column_name ) {
			case 'thumb_id':
				$image_id = get_term_meta( $term_id, 'category-image-id', true );
				if ( $image_id ) {
					echo wp_get_attachment_image( $image_id, 'thumb' );
				}
				break;

			case 'tax_id':
				echo $term_id;
				break;

			case 'featured':
				$featured = get_term_meta( $term_id, 'wte_trip_tax_featured', true );
				$featured = ( isset( $featured ) && '' != $featured ) ? $featured : 'no';

				$icon_class = ' dashicons-star-empty ';
				if ( ! empty( $featured ) && 'yes' === $featured ) {
					$icon_class = ' dashicons-star-filled ';
				}
				$nonce = wp_create_nonce( 'wte_trip_tax_featured_nonce' );
				printf( '<a href="#" class="wp-travel-engine-featured-term dashicons %s" data-term-id="%d"  data-nonce="%s"></a>', $icon_class, $term_id, $nonce );
				break;
		}
	}

	/**
	 * Remove column author and date and add customer id, country, bookings, total spent and created column.
	 *
	 * @since    1.0.0
	 */
	function wp_travel_engine_customer_cpt_columns( $columns ) {
		unset( $columns['date'] );
		$new_columns = array(
			'cid'      => __( 'Customer ID', 'wp-travel-engine' ),
			'country'  => __( 'Country', 'wp-travel-engine' ),
			'bookings' => __( 'Bookings', 'wp-travel-engine' ),
			'spent'    => __( 'Total Spent', 'wp-travel-engine' ),
			'created'  => __( 'Created', 'wp-travel-engine' ),
		);
		return array_merge( $columns, $new_columns );
	}

	/**
	 * Show value to the corresponsing columns for customer post type.
	 *
	 * @since    1.0.0
	 */
	function wp_travel_engine_customer_custom_columns( $column, $post_id ) {
		$screen = get_current_screen();
		if ( $screen && ( $screen->post_type == 'booking' || $screen->post_type == 'customer' ) ) {
			$terms = get_post_meta( $post_id, 'wp_travel_engine_booking_setting', true );
			$var   = false;
			if ( isset( $terms['place_order']['booking']['email'] ) ) {
				$var = get_page_by_title( $terms['place_order']['booking']['email'], OBJECT, 'customer' );
			}
			if ( $var && isset( $var->ID ) ) {
				$wp_travel_engine_booked_settings = get_post_meta( $var->ID, 'wp_travel_engine_booked_trip_setting', true );
				$size                             = isset( $wp_travel_engine_booked_settings['traveler'] ) ? sizeof( $wp_travel_engine_booked_settings['traveler'] ) : '';
			}
			$wp_travel_engine_setting_option_setting = get_option( 'wp_travel_engine_settings', true );

			switch ( $column ) {
				case 'cid':
					echo esc_attr( $post_id );
					break;

				case 'country':
					if ( isset( $terms['place_order']['booking']['country'] ) ) {
						echo esc_attr( $terms['place_order']['booking']['country'] );
					}
					break;

				case 'bookings':
					echo $size;
					break;

				case 'spent':
					(int) $tot = null;
					foreach ( $wp_travel_engine_booked_settings['cost'] as $key => $value ) {
						$value = str_replace( ',', '', $value );
						$tot   = $tot + $value;
					}
					$code = 'USD';
					if ( isset( $wp_travel_engine_setting_option_setting['currency_code'] ) && $wp_travel_engine_setting_option_setting['currency_code'] != '' ) {
						$code = $wp_travel_engine_setting_option_setting['currency_code'];
					}
					$obj      = new Wp_Travel_Engine_Functions();
					$currency = $obj->wp_travel_engine_currencies_symbol( $code );
					echo esc_attr( $currency . $obj->wp_travel_engine_price_format( $tot ) . ' ' . $code );
					break;

				case 'created':
					echo end( $wp_travel_engine_booked_settings['datetime'] );
					break;
			}
		}
	}

	/**
	 * Register a taxonomy, 'destination' for the post type "trip".
	 *
	 * @link https://codex.wordpress.org/Function_Reference/register_taxonomy
	 */
	// create taxonomy, destination for the post type "trip"
	function wp_travel_engine_create_destination_taxonomies() {
		 $permalink = wp_travel_engine_get_permalink_structure();
		// Add new taxonomy, make it hierarchical (like destination)
		$labels = array(
			'name'              => _x( 'Destinations', 'taxonomy general name', 'wp-travel-engine' ),
			'singular_name'     => _x( 'Destinations', 'taxonomy singular name', 'wp-travel-engine' ),
			'search_items'      => __( 'Search Destinations', 'wp-travel-engine' ),
			'all_items'         => __( 'All Destinations', 'wp-travel-engine' ),
			'parent_item'       => __( 'Parent Destinations', 'wp-travel-engine' ),
			'parent_item_colon' => __( 'Parent Destinations', 'wp-travel-engine' ),
			'edit_item'         => __( 'Edit Destinations', 'wp-travel-engine' ),
			'update_item'       => __( 'Update Destinations', 'wp-travel-engine' ),
			'add_new_item'      => __( 'Add New Destinations', 'wp-travel-engine' ),
			'new_item_name'     => __( 'New Destinations Name', 'wp-travel-engine' ),
			'menu_name'         => __( 'Destinations', 'wp-travel-engine' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_in_rest'      => false,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'         => $permalink['wp_travel_engine_destination_base'],
				'hierarchical' => true,
			),
		);

		register_taxonomy( 'destination', array( 'trip' ), $args );
	}

	/**
	 * Register a taxonomy, 'activities' for the post type "trip".
	 *
	 * @link https://codex.wordpress.org/Function_Reference/register_taxonomy
	 */
	// create taxonomy, destination for the post type "trip"
	function wp_travel_engine_create_activities_taxonomies() {
		$permalink = wp_travel_engine_get_permalink_structure();
		// Add new taxonomy, make it hierarchical (like destination)
		$labels = array(
			'name'              => _x( 'Activities', 'taxonomy general name', 'wp-travel-engine' ),
			'singular_name'     => _x( 'Activities', 'taxonomy singular name', 'wp-travel-engine' ),
			'search_items'      => __( 'Search Activities', 'wp-travel-engine' ),
			'all_items'         => __( 'All Activities', 'wp-travel-engine' ),
			'parent_item'       => __( 'Parent Activities', 'wp-travel-engine' ),
			'parent_item_colon' => __( 'Parent Activities', 'wp-travel-engine' ),
			'edit_item'         => __( 'Edit Activities', 'wp-travel-engine' ),
			'update_item'       => __( 'Update Activities', 'wp-travel-engine' ),
			'add_new_item'      => __( 'Add New Activities', 'wp-travel-engine' ),
			'new_item_name'     => __( 'New Activities Name', 'wp-travel-engine' ),
			'menu_name'         => __( 'Activities', 'wp-travel-engine' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_in_rest'      => false,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'         => $permalink['wp_travel_engine_activity_base'],
				'hierarchical' => true,
			),
		);

		register_taxonomy( 'activities', array( 'trip' ), $args );
	}


	/**
	 * Register a taxonomy, 'trip types' for the post type "trip".
	 *
	 * @link https://codex.wordpress.org/Function_Reference/register_taxonomy
	 */
	// create taxonomy, destination for the post type "trip"
	function wp_travel_engine_create_trip_types_taxonomies() {
		$permalink = wp_travel_engine_get_permalink_structure();
		// Add new taxonomy, make it hierarchical (like destination)
		$labels = array(
			'name'              => _x( 'Trip Type', 'taxonomy general name', 'wp-travel-engine' ),
			'singular_name'     => _x( 'Trip Type', 'taxonomy singular name', 'wp-travel-engine' ),
			'search_items'      => __( 'Search Trip Type', 'wp-travel-engine' ),
			'all_items'         => __( 'All Trip Type', 'wp-travel-engine' ),
			'parent_item'       => __( 'Parent Trip Type', 'wp-travel-engine' ),
			'parent_item_colon' => __( 'Parent Trip Type', 'wp-travel-engine' ),
			'edit_item'         => __( 'Edit Trip Type', 'wp-travel-engine' ),
			'update_item'       => __( 'Update Trip Type', 'wp-travel-engine' ),
			'add_new_item'      => __( 'Add New Trip Type', 'wp-travel-engine' ),
			'new_item_name'     => __( 'New Trip Type Name', 'wp-travel-engine' ),
			'menu_name'         => __( 'Trip Type', 'wp-travel-engine' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_in_rest'      => false,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'         => $permalink['wp_travel_engine_trip_type_base'],
				'hierarchical' => true,
			),
		);

		register_taxonomy( 'trip_types', array( 'trip' ), $args );
	}

	/**
	 * Registers settings page for Trip.
	 *
	 * @since 1.0.0
	 */
	public function wp_travel_engine_settings_page() {
		add_submenu_page( 'edit.php?post_type=booking', 'WP Travel Engine Admin Settings', 'Settings', 'manage_options', basename( __FILE__ ), array( $this, 'wp_travel_engine_callback_function' ) );
	}

	public function messages_page() {
		$menu_title = 'Messages';
		$args       = array(
			'timeout'     => 30,
			'httpversion' => '1.1',
		);
		$url        = 'https://wptravelengine.com/wp-json/wp/v2/wte_messages';
		$date       = get_option( 'wte_messages_latest_post_date' );

		if ( false !== $date ) {
			$url .= "?after={$date}";
		}

		$count    = 0;
		$response = wp_safe_remote_head( $url, $args );
		if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
			$count = wp_remote_retrieve_header( $response, 'x-wp-total' );
			if ( '0' !== $count ) {
				$menu_title .= " <span class=\"update-plugins count-{$count}\"><span class=\"plugin-count\">{$count}</span></span>";
				wte_purge_transients( 'wte_messages_' );
			}
		}

		add_submenu_page( 'edit.php?post_type=booking', 'WP Travel Engine Admin Messages', $menu_title, 'manage_options', 'wte-messages', array( $this, 'display_messages_page' ) );
	}

	public function display_messages_page() {
		$message_list = new Wp_Travel_Engine_Messages_List();
		require_once plugin_dir_path( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/backend/settings/messages.php';
	}

	/**
	 * Registers settings for WP travel Engine.
	 *
	 * @since 1.0.0
	 */
	public function wp_travel_engine_register_settings() {
		// The third parameter is a function that will validate input values.
		register_setting( 'wp_travel_engine_settings', 'wp_travel_engine_settings', '' );
	}

	/**
	 * Update actual prices.
	 *
	 * @return void
	 */
	public function wte_update_actual_prices_for_filter() {

		$updated_actual_price = get_option( 'wpte_updated_actual_price_for_filter', false );

		if ( $updated_actual_price ) {
			return false;
		}

		$wte_trp_args         = array(
			'post_type'      => 'trip',
			'posts_per_page' => -1,
			'order'          => 'ASC',
		);
		$wte_doc_tax_post_qry = new WP_Query( $wte_trp_args );
		$cost                 = 0;
		if ( $wte_doc_tax_post_qry->have_posts() ) :
			while ( $wte_doc_tax_post_qry->have_posts() ) :
				$wte_doc_tax_post_qry->the_post();

				$actual_price = wp_travel_engine_get_actual_trip_price( get_the_ID(), true );
				update_post_meta( get_the_ID(), 'wp_travel_engine_setting_trip_actual_price', $actual_price );
			endwhile;
			wp_reset_postdata();
		endif;
		wp_reset_query();

		// Update filter.
		update_option( 'wpte_updated_actual_price_for_filter', true );

		return;
	}

	/**
	 * Sanitize as well as merge with old settings.
	 */
	public function sanitize_settings( $settings ) {
		if ( isset( $_POST['wp_travel_engine_settings'] ) ) {
			$new_settings = $_POST['wp_travel_engine_settings'];
			$old_settings = get_option( 'wp_travel_engine_settings' );

			if ( is_array( $old_settings ) ) {
				$new_settings = array_merge( $old_settings, $new_settings );
			}

			return $new_settings;
		}

		return $settings;
	}

	/**
	 *
	 * Retrives saved settings from the database if settings are saved. Else, displays fresh forms   for settings.
	 *
	 * @since 1.0.0
	 */
	function wp_travel_engine_callback_function() {
		 require plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-travel-engine-settings.php';
		$Wp_Travel_Engine_Settings = new Wp_Travel_Engine_Settings();
		$Wp_Travel_Engine_Settings->wp_travel_engine_backend_settings();
		$option = get_option( 'wp_travel_engine_settings' );
	}

	/**
	 *
	 * HTML template for tabs
	 *
	 * @since 1.0.0
	 */
	function wp_travel_engine_tabs_template() {
		?>
		<div id="trip-template">
			<li id="trip-tabs{{index}}" data-id="{{index}}" class="trip-row">
				<span class="tabs-handle"><span></span></span>
				<span class="delete-icon delete-tab"><i class="far fa-trash-alt delete-icon" data-id="{{index}}"></i></span>

				<div class="tabs-content">
					<div class="tabs-id">
						<input type="hidden" class="trip-tabs-id" name="wp_travel_engine_settings[trip_tabs][id][{{index}}]" id="wp_travel_engine_settings[trip_tabs][id][{{index}}]" value="{{index}}">
					</div>
					<div class="tabs-field">
						<input type="hidden" class="trip-tabs-id" name="wp_travel_engine_settings[trip_tabs][field][{{index}}]" id="wp_travel_engine_settings[trip_tabs][field][{{index}}]" value="wp_editor">
					</div>
					<div class="tabs-name">
						<input type="text" class="trip-tabs-name" name="wp_travel_engine_settings[trip_tabs][name][{{index}}]" id="wp_travel_engine_settings[trip_tabs][name][{{index}}]" required>
					</div>
					<div class="tabs-icon">
						<input type="text" class="trip-tabs-icon" name="wp_travel_engine_settings[trip_tabs][icon][{{index}}]" id="wp_travel_engine_settings[trip_tabs][icon][{{index}}]" placeholder="search icon...">
					</div>
				</div>
			</li>
		</div>
		<style type="text/css">
			#trip-template {
				display: none;
			}
		</style>
		<?php
	}

	function hide_publishing_actions() {
		$my_post_type = 'customer';
		global $post;
		if ( $post->post_type == $my_post_type ) {
			echo '
                <style type="text/css">
                    #minor-publishing{
                        display:none;
                    }
                </style>
            ';
		}

		$my_post_type = 'booking';
		if ( $post->post_type == $my_post_type ) {
			echo '
                <style type="text/css">
					#visibility,#minor-publishing-actions, #misc-publishing-actions .misc-pub-section.misc-pub-post-status, #misc-publishing-actions .misc-pub-section.misc-pub-curtime {
						display:none;
					}
                </style>
            ';
		}

		$my_post_type = 'enquiry';
		if ( $post->post_type == $my_post_type ) {
			echo '
                <style type="text/css">
                    #postbox-container-1{
                        display:none;
                    }
                </style>
            ';
		}

		$my_post_type = 'customer';
		if ( $post->post_type == $my_post_type ) {
			echo '
                <style type="text/css">
                    #postbox-container-1{
                        display:none;
                    }
                </style>
            ';
		}
	}

	/**
	 * Booking publish metabox
	 *
	 * @return void
	 */
	public function wte_publish_metabox() {
		global $post;
		if ( get_post_type( $post ) === 'booking' ) {
			?>
			<div class="misc-pub-section misc-pub-booking-status">
				<?php
				$status    = wp_travel_engine_get_booking_status();
				$label_key = get_post_meta( $post->ID, 'wp_travel_engine_booking_status', true );
				$label_key = ! empty( $label_key ) ? $label_key : 'booked';

				if ( 'refunded' === $label_key || 'canceled' === $label_key ) {
					?>
						<label for="wp_travel_engine_booking_status"><?php esc_html_e( 'Booking Status', 'wp-travel-engine' ); ?></label>
						<span style="margin:10px;padding:10px;font-weight:700;color:#ffffff;background-color:<?php echo esc_attr( $status[ $label_key ]['color'] ); ?>" ><?php echo esc_html( $status[ $label_key ]['text'] ); ?></span>
						<input type="hidden" name="wp_travel_engine_booking_status" value="<?php echo esc_attr( $label_key ); ?>">
					<?php
				} else {
					?>
					<label for="wp_travel_engine_booking_status"><?php esc_html_e( 'Booking Status', 'wp-travel-engine' ); ?></label>
					<select id="wp_travel_engine_booking_status" name="wp_travel_engine_booking_status" >
					<?php foreach ( $status as $value => $st ) : ?>
						<option value="<?php echo esc_html( $value ); ?>" <?php selected( $value, $label_key ); ?>>
							<?php echo esc_html( $status[ $value ]['text'] ); ?>
						</option>
					<?php endforeach; ?>
					</select>
					<?php
				}
				?>
			</div>
			<?php
		}
	}

	/**
	 * List out font awesome icon list
	 */
	function wp_travel_engine_get_icon_list() {
		 require_once WP_TRAVEL_ENGINE_BASE_PATH . '/includes/assets/fontawesome.php';
		echo '<div class="wp-travel-engine-font-awesome-list-template">';
		// echo '<input class="wpte-ico-search" type="text" placeholder="Search icon" value="" />';
		echo '<div class="wpte-font-awesome-list"><input class="wpte-ico-search" type="text" placeholder="Search icon" value="" /><ul class="rara-font-group">';
		if ( isset( $fontawesome ) ) :
			foreach ( $fontawesome as $font ) {
				echo '<li><i class="' . esc_attr( $font ) . '"></i></li>';
			}
		endif;
		echo '</ul></div></div>';
		echo '<style>.wp-travel-engine-font-awesome-list-template{display:none;}</style>';
	}

	/**
	 * Trip facts template.
	 */
	function trip_facts_template() {
		?>
		<div id="trip_facts_outer_template">
			<div id="trip_facts_inner_template">
				<li id="trip_facts_template-{{tripfactsindex}}" data-id="{{tripfactsindex}}" class="trip_facts">
					<span class="tabs-handle">
						<span></span>
					</span>
					<div class="form-builder">
						<div class="fid">
							<label for="wp_travel_engine_settings[trip_facts][fid][{{tripfactsindex}}]"></label>
							<input type="hidden" name="wp_travel_engine_settings[trip_facts][fid][{{tripfactsindex}}]" value="{{tripfactsindex}}">
						</div>
						<div class="field-id">
							<input type="text" name="wp_travel_engine_settings[trip_facts][field_id][{{tripfactsindex}}]" required>
						</div>
						<div class="field-icon">
							<input class="trip-tabs-icon" type="text" name="wp_travel_engine_settings[trip_facts][field_icon][{{tripfactsindex}}]" value="">
						</div>
						<div class="field-type custom-class">
							<div class="select-holder">
								<select id="wp_travel_engine_settings[trip_facts][field_type][{{tripfactsindex}}]" name="wp_travel_engine_settings[trip_facts][field_type][{{tripfactsindex}}]" data-placeholder="<?php esc_attr_e( 'Choose a field type&hellip;', 'wp-travel-engine' ); ?>" class="wc-enhanced-select" required>
									<option value=" "><?php _e( 'Choose input type&hellip;', 'wp-travel-engine' ); ?></option>
									<?php
									$obj    = new Wp_Travel_Engine_Functions();
									$fields = $obj->trip_facts_field_options();
									foreach ( $fields as $key => $val ) {
										echo '<option value="' . ( ! empty( $key ) ? esc_attr( $key ) : 'text' ) . '"' . selected( ' ', $val, false ) . '>' . esc_html( $key ) . '</option>';
									}
									?>
								</select>
							</div>
						</div>
						<div class="select-options" style="display: none;">
							<textarea id="wp_travel_engine_settings[trip_facts][select_options][{{tripfactsindex}}]" name="wp_travel_engine_settings[trip_facts][select_options][{{tripfactsindex}}]" placeholder="<?php _e( 'Enter drop-down values separated by commas', 'wp-travel-engine' ); ?>" rows="2" cols="25" required></textarea>
						</div>
						<div class="input-placeholder">
							<input type="text" name="wp_travel_engine_settings[trip_facts][input_placeholder][{{tripfactsindex}}]" value="">
						</div>
					</div>
					<a href="#" class="del-li"><i class="far fa-trash-alt"></i></a>
				</li>
			</div>
		</div>
		<style>
			#trip_facts_outer_template {
				display: none !important;
			}
		</style>
		<?php
	}

	/**
	 * Trip facts ajax callback.
	 */
	function wp_add_trip_info() {
		$wp_travel_engine_option_settings = get_option( 'wp_travel_engine_settings', true );
		$trip_facts                       = $wp_travel_engine_option_settings['trip_facts'];
		$id                               = $_POST['val'];
		$key                              = array_search( $_POST['val'], $trip_facts['field_id'] );

		$value = $trip_facts['field_type'][ $key ];
		$nonce = $_POST['nonce'];

		$response = '<div class="wpte-repeater-block wpte-sortable wpte-trip-fact-row"><div class="wpte-field wpte-floated"><label for="wp_travel_engine_setting[trip_facts][' . $key . '][' . $key . ']" class="wpte-field-label">' . __( $id . ' ', 'wp-travel-engine' ) . '</label>';

		$response .= '<input type="hidden" name="wp_travel_engine_setting[trip_facts][field_id][' . $key . ']" value="' . $id . '">';
		$response .= '<input type="hidden" name="wp_travel_engine_setting[trip_facts][field_type][' . $key . ']" value="' . $value . '">';

		switch ( $value ) {
			case 'select':
				$options = $trip_facts['select_options'][ $key ];
				$options = explode( ',', $options );

				$response .= '<select id="wp_travel_engine_setting[trip_facts][' . $key . '][' . $key . ']" name="wp_travel_engine_setting[trip_facts][' . $key . '][' . $key . ']" data-placeholder="' . __( 'Choose a field type&hellip;', 'wp-travel-engine' ) . '">';
				$response .= '<option value=" ">' . __( 'Choose input type&hellip;', 'wp-travel-engine' ) . '</option>';
				foreach ( $options as $key => $val ) {
					$response .= '<option value="' . ( ! empty( $val ) ? esc_attr( $val ) : 'Please select' ) . '">' . esc_html( $val ) . '</option>';
				}
				$response .= '</select>';
				break;
			case 'duration':
				$response .= '<input type="number" min="1" placeholder = "' . __( 'Number of days', 'wp-travel-engine' ) . '" class="duration" id="wp_travel_engine_setting[trip_facts][' . $key . '][' . $key . ']" name="wp_travel_engine_setting[trip_facts][' . $key . '][' . $key . ']" value=""/>';

				break;
			case 'number':
				$placeholder = isset( $trip_facts['input_placeholder'][ $key ] ) ? esc_attr( $trip_facts['input_placeholder'][ $key ] ) : '';

				$response .= '<input  type="number" min="1" id="wp_travel_engine_setting[trip_facts][' . $key . '][' . $key . ']" name="wp_travel_engine_setting[trip_facts][' . $key . '][' . $key . ']" value="">';
				break;

			case 'text':
				$placeholder = isset( $trip_facts['input_placeholder'][ $key ] ) ? esc_attr( $trip_facts['input_placeholder'][ $key ] ) : '';

				$response .= '<input type="text" id="wp_travel_engine_setting[trip_facts][' . $key . '][' . $key . ']" name="wp_travel_engine_setting[trip_facts][' . $key . '][' . $key . ']" value="" placeholder="' . esc_attr( $placeholder ) . '">';
				break;

			case 'textarea':
				$placeholder = isset( $trip_facts['input_placeholder'][ $key ] ) ? esc_attr( $trip_facts['input_placeholder'][ $key ] ) : '';

				$response .= '<textarea id="wp_travel_engine_setting[trip_facts][' . $key . '][' . $key . ']" name="wp_travel_engine_setting[trip_facts][' . $key . '][' . $key . ']" placeholder="' . $placeholder . '"></textarea>';

				break;
			default:
				$placeholder = isset( $trip_facts['input_placeholder'][ $key ] ) ? esc_attr( $trip_facts['input_placeholder'][ $key ] ) : '';

				$response .= '<input type="text" id="wp_travel_engine_setting[trip_facts][' . $key . '][' . $key . ']" name="wp_travel_engine_setting[trip_facts][' . $key . '][' . $key . ']" value="" placeholder="' . esc_attr( $placeholder ) . '">';
				break;
		}
		$response .= '<button class="wpte-delete wpte-remove-trp-fact"></button></div></div>';
		echo $response;
		die;
	}

	/**
	 * Destination template.
	 */
	function wpte_get_destination_template( $template ) {
		$post          = get_post();
		$page_template = get_post_meta( $post->ID, '_wp_page_template', true );
		if ( $page_template == 'templates/template-destination.php' ) {
			$template_path = wte_locate_template( 'template-destination.php' );
			return $template_path;
		}
		if ( $page_template == 'templates/template-activities.php' ) {
			$template_path = wte_locate_template( 'template-activities.php' );
			return $template_path;
		}
		if ( $page_template == 'templates/template-trip_types.php' ) {
			$template_path = wte_locate_template( 'template-trip_types.php' );
			return $template_path;
		}
		if ( $page_template == 'templates/template-trip-listing.php' ) {
			$template_path = wte_locate_template( 'template-trip-listing.php' );
			return $template_path;
		}
		return $template;
	}

	/**
	 * Destination template returned.
	 */
	function wpte_filter_admin_page_templates( $templates ) {
		$templates['templates/template-destination.php']  = __( 'Destination Template', 'wp-travel-engine' );
		$templates['templates/template-activities.php']   = __( 'Activities Template', 'wp-travel-engine' );
		$templates['templates/template-trip_types.php']   = __( 'Trip Types Template', 'wp-travel-engine' );
		$templates['templates/template-trip-listing.php'] = __( 'Trip Listing Template', 'wp-travel-engine' );
		return $templates;
	}

	/**
	 * Destination template added.
	 */
	function wpte_add_destination_templates() {
		 // If REST_REQUEST is defined (by WordPress) and is a TRUE, then it's a REST API request.
		$is_rest_route = ( defined( 'REST_REQUEST' ) && REST_REQUEST );
		if (
			( is_admin() && ! $is_rest_route ) || // admin and AJAX (via admin-ajax.php) requests
			( ! is_admin() && $is_rest_route )    // REST requests only
		) {
			add_filter( 'theme_page_templates', array( $this, 'wpte_filter_admin_page_templates' ) );
		} else {
			add_filter( 'page_template', array( $this, 'wpte_get_destination_template' ) );
		}
	}

	/*
	* Itinerary template
	*/
	function wpte_add_itinerary_template() {
		$screen = get_current_screen();
		if ( $screen && $screen->post_type == 'trip' ) {
			?>
			<div id="itinerary-template">
				<li id="itinerary-tabs{{index}}" data-id="{{index}}" class="itinerary-row">
					<span class="tabs-handle"><span></span></span>
					<i class="dashicons dashicons-no-alt delete-faq delete-icon" data-id="{{index}}"></i>
					<div class="itinerary-holder">
						<a class="accordion-tabs-toggle" href="javascript:void(0);"><span class="day-count">
						<?php
						_e( 'Day', 'wp-travel-engine' );
																											echo '-{{index}}';
						?>
																											</span></a>
						<div class="itinerary-content">
							<div class="title">
								<input placeholder="<?php _e( 'Itinerary Title:', 'wp-travel-engine' ); ?>" type="text" class="itinerary-title" name="wp_travel_engine_setting[itinerary][itinerary_title][{{index}}]" id="wp_travel_engine_setting[itinerary][itinerary_title][{{index}}]">
							</div>
							<div class="content">
								<textarea placeholder="<?php _e( 'Itinerary Content:', 'wp-travel-engine' ); ?>" rows="5" cols="32" class="itinerary-content" name="wp_travel_engine_setting[itinerary][itinerary_content][{{index}}]" id="wp_travel_engine_setting[itinerary][itinerary_content][{{index}}]"></textarea>
								<textarea rows="5" cols="32" class="itinerary-content-inner" name="wp_travel_engine_setting[itinerary][itinerary_content_inner][{{index}}]" id="wp_travel_engine_setting[itinerary][itinerary_content_inner][{{index}}]"></textarea>
							</div>
						</div>
					</div>
				</li>
			</div>
			<style type="text/css">
				#itinerary-template {
					display: none !important;
				}
			</style>
			<?php
		}
	}

	/*
	* Itinerary template
	*/
	function wpte_add_faq_template() {
		$screen = get_current_screen();
		if ( $screen && $screen->post_type == 'trip' ) {
			?>
			<div id="faq-template">
				<li id="faq-tabs{{index}}" data-id="{{index}}" class="faq-row">
					<span class="tabs-handle"><span></span></span>
					<i class="dashicons dashicons-no-alt delete-faq delete-icon" data-id="{{index}}"></i>
					<div class="content-holder">
						<a class="accordion-tabs-toggle" href="javascript:void(0);"><span class="day-count">
						<?php
						_e( 'FAQ', 'wp-travel-engine' );
																											echo '-{{index}}';
						?>
																											</span></a>
						<div class="faq-content">
							<div class="title">
								<input placeholder="<?php _e( 'Question:', 'wp-travel-engine' ); ?>" type="text" class="faq-title" name="wp_travel_engine_setting[faq][faq_title][{{index}}]" id="wp_travel_engine_setting[faq][faq_title][{{index}}]">
							</div>
							<div class="content">
								<textarea placeholder="<?php _e( 'Answer:', 'wp-travel-engine' ); ?>" rows="3" cols="78" name="wp_travel_engine_setting[faq][faq_content][{{index}}]" id="wp_travel_engine_setting[faq][faq_content][{{index}}]"></textarea>
							</div>
						</div>
					</div>
				</li>
			</div>
			<style type="text/css">
				#faq-template {
					display: none !important;
				}
			</style>
			<?php
		}
	}

	/**
	 * Paypal activation notice.
	 *
	 * @since 1.1.1
	 */
	function wp_travel_engine_rating_notice() {
		 global $current_user;
		$user_id = $current_user->ID;
		if ( get_user_meta( $user_id, 'wp-travel-engine-rating-notice', true ) != 'true' ) {
			$link_plugin = '<a href="https://wordpress.org/plugins/wp-travel-engine/" target="_blank">WP Travel Engine</a>';
			$link_rating = '<a href="https://wordpress.org/support/plugin/wp-travel-engine/reviews/#new-post" target="_blank">WordPress.org</a>';
			$message     = sprintf( esc_html__( 'Thank you for using %1$s. Please rate us on %2$s.', 'wp-travel-engine' ), $link_plugin, $link_rating );
			printf( '<div class="updated notice"><p>%1$s <a href="?wp-travel-engine-rating-notice=1">Dismiss</a></p></div>', wp_kses_post( $message ) );
		}
	}

	function wp_travel_engine_notice_ignore() {
		global $current_user;

		$user_id = $current_user->ID;
		if ( isset( $_GET['wp-travel-engine-rating-notice'] ) && $_GET['wp-travel-engine-rating-notice'] = '1' ) {
			add_user_meta( $user_id, 'wp-travel-engine-rating-notice', 'true', true );
		}
	}

	/**
	 * Paypal settings form.
	 *
	 * @since 1.1.1
	 */
	function wte_paypal_form() {
		$wp_travel_engine_settings = get_option( 'wp_travel_engine_settings' );
		?>
		<div class="wte-paypal-gateway-form">
			<label for="wp_travel_engine_settings[paypal_id]"><?php _e( 'PayPal ID : ', 'wp-travel-engine' ); ?> <span class="tooltip" title="Enter a valid Merchant account ID (strongly recommend) or PayPal account email address. All payments will go to this account."><i class="fas fa-question-circle"></i></span></label>
			<input type="text" id="wp_travel_engine_settings[paypal_id]" name="wp_travel_engine_settings[paypal_id]" value="<?php echo isset( $wp_travel_engine_settings['paypal_id'] ) ? esc_attr( $wp_travel_engine_settings['paypal_id'] ) : ''; ?>">
		</div>
		<?php
	}

	/**
	 * Payment Details.
	 *
	 * @since 1.1.1
	 */
	function wpte_trip_pay_add_meta_boxes() {
		$screens = array( 'booking' );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'pay_id',
				__( 'Paypal Payment Details', 'wp-travel-engine' ),
				array( $this, 'wp_travel_engine_pay_metabox_callback' ),
				$screen,
				'side',
				'high'
			);
		}
	}

	// Tab for notice listing and settings
	public function wp_travel_engine_pay_metabox_callback() {
		include WP_TRAVEL_ENGINE_BASE_PATH . '/includes/backend/booking/pay.php';
	}

	/**
	 * When the post is saved, saves our custom data.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	function wp_travel_engine_trip_pay_meta_box_data( $post_id ) {
		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */
		if ( ! is_admin() || 'booking' !== get_current_screen()->id ) {
			return;
		}
		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */
		// Sanitize user input.
		if ( isset( $_POST['wp_travel_engine_booking_setting'] ) ) {
			$settings = $_POST['wp_travel_engine_booking_setting'];
			update_post_meta( $post_id, 'wp_travel_engine_booking_setting', $settings );
		}
	}

	/**
	 * Registers extensions page for Trip.
	 *
	 * @since 1.1.7
	 */
	public function wp_travel_engine_extensions_page() {
		add_submenu_page( 'edit.php?post_type=booking', 'Extensions for WP Travel Engine.', 'Extensions', 'manage_options', 'extensions', array( $this, 'wp_travel_engine_extensions_callback_function' ) );
	}

	/**
	 * Dashboard page.
	 *
	 * @return void
	 */
	public function wp_travel_engine_dashboard_menu() {
		// add_menu_page( __( 'WP Travel Engine', 'wte' ), __( 'WP Travel Engine', 'wte' ), 'manage_options', 'wp-travel-engine-dashboard', array( $this, 'wp_travel_engine_dashboard' ), null, 40 );
		global $submenu;
		unset( $submenu['edit.php?post_type=booking'][10] ); // Removes 'Add New'.
	}

	/**
	 * Dashboard page.
	 *
	 * @return void
	 */
	public function wp_travel_engine_dashboard() {
		?>
			<div id="wte-dashbard-analytics"></div>
		<?php
	}

	/**
	 *
	 * Displays themes.
	 *
	 * @since 1.1.7
	 */
	function wp_travel_engine_extensions_callback_function() {
		require plugin_dir_path( dirname( __FILE__ ) ) . 'includes/backend/submenu/extensions.php';
	}


	/**
	 * Registers themes page for Trip.
	 *
	 * @since 1.1.7
	 */
	public function wp_travel_engine_themes_page() {
		add_submenu_page( 'edit.php?post_type=booking', 'Themes for WP Travel Engine.', 'Themes', 'manage_options', 'themes', array( $this, 'wp_travel_engine_themes_callback_function' ) );
	}

	/**
	 *
	 * Displays extensions.
	 *
	 * @since 1.1.7
	 */
	function wp_travel_engine_themes_callback_function() {
		require plugin_dir_path( dirname( __FILE__ ) ) . 'includes/backend/submenu/themes.php';
	}

	function wte_tinymce_config( $init ) {
		// Don't remove line breaks
		$init['remove_linebreaks'] = false;
		// Convert newline characters to BR tags
		$init['convert_newlines_to_brs'] = true;
		// Do not remove redundant BR tags
		$init['remove_redundant_brs'] = false;

		// Pass $init back to WordPress
		return $init;
	}

	/**
	 * Add Enquiry column in the enquiry list.
	 *
	 * @since    1.0.0
	 */
	function wp_travel_engine_trip_cpt_columns( $columns ) {
		$new_columns = array(
			'tid' => __( 'Trip ID', 'wp-travel-engine' ),
		);
		return array_merge( $columns, $new_columns );
	}

	/**
	 * Show value to the corresponsing columns for booking post type.
	 *
	 * @since    1.0.0
	 */
	function wp_travel_engine_trip_custom_columns( $column, $post_id ) {
		$wp_travel_engine_setting = get_post_meta( $post_id, 'wp_travel_engine_setting', true );
		$screen                   = get_current_screen();
		if ( $screen && $screen->post_type == 'trip' ) {
			switch ( $column ) {

				case 'tid':
					echo $post_id;
					break;
			}
		}
	}

	/**
	 * Display incompatible plugins list in the plugin update message.
	 *
	 * @param [type] $plugin_data
	 * @param [type] $response
	 * @return void
	 */
	public function in_plugin_update_message( $plugin_data, $response ) {
		$compatibility_check = new WP_Travel_Engine_Compatibility_Check();
		if ( $compatibility_check->requires_backward_processs() || ! empty( $compatibility_check->updated_addons_actives() ) ) {
			echo '<style>#wp-travel-engine-update .update-message.notice.inline.notice-warning.notice-alt p:last-child {display: none;}</style>';
			$update_messages = $compatibility_check->update_messages( $response );
		}
	}

	/**
	 * Display admin notices.
	 *
	 * @return void
	 */
	public function admin_notices() {
		$this->display_opt_in_notice_for_message_feature();
	}

	/**
	 * Display opt-in notice for message feature.
	 *
	 * @return void
	 */
	private function display_opt_in_notice_for_message_feature() {
		// Bail early if the messages is feature is set.
		$messages_enabled = get_option( 'wte_messages_enabled' );
		if ( false !== $messages_enabled ) {
			return;
		}

		// Set the message to enable or dismiss. '1' for enable and '0' for dismiss
		if ( isset( $_GET['wte-message-enabled'] ) && in_array( $_GET['wte-message-enabled'], array( '0', '1' ) ) ) {
			update_option( 'wte_messages_enabled', $_GET['wte-message-enabled'] );
			return;
		}

		// Construct agree and dismiss url based on the query string.
		if ( empty( $_SERVER['QUERY_STRING'] ) ) {
			$agree_url   = $_SERVER['REQUEST_URI'] . '?wte-message-enabled=1';
			$dismiss_url = $_SERVER['REQUEST_URI'] . '?wte-message-enabled=0';
		} else {
			$agree_url   = $_SERVER['REQUEST_URI'] . '&wte-message-enabled=1';
			$dismiss_url = $_SERVER['REQUEST_URI'] . '&wte-message-enabled=0';
		}
		?>

		<div class="notice notice-info is-dismissible" style="padding-bottom: 10px;">
			<p><strong><?php esc_html_e( 'WP Travel Engine Message: ', 'wp-travel-engine' ); ?></strong><?php esc_html_e( 'Get messages about new update releases, upcoming features, and exciting offers from WP Travel Engine?', 'wp-travel-engine' ); ?></p>
			<p><i><?php esc_html_e( 'Note: By clicking yes, you will get an additional messages menu inside Trips menu that shows release notes, update notifications and new offers with helpful links. This will also let the plugin anonymously collect usage information to help WP Travel Engine team improve the product.', 'wp-travel-engine' ); ?></i></p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'wp-travel-engine' ); ?></span>
			</button>
			<a href="<?php echo esc_url( $agree_url ); ?>" class="button button-primary">
				<?php esc_html_e( 'Yes, I\'m in', 'wp-travel-engine' ); ?>
			</a>
			<a href="<?php echo esc_url( $dismiss_url ); ?>" class="button">
				<?php esc_html_e( 'No Thanks', 'wp-travel-engine' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Function to call Advanced itinerary template up on front or default parent template
	 *
	 * @return void
	 */
	public function wte_itinerary_setting() {
		$itinerary_settings = apply_filters( 'wte_trip_itinerary_setting_path', WP_TRAVEL_ENGINE_BASE_PATH . '/admin/meta-parts/tabs-inner/itinerary-setting.php' );
		include $itinerary_settings;
	}

	/**
	 * Add customer bulk action for migration from post type to users.
	 *
	 * @return void
	 */
	public function wte_add_customer_bulk_actions( $bulk_array ) {
		$bulk_array['wte_migrate_customers'] = __( 'Migrate to users', 'wp-travel-engine' );
		return $bulk_array;
	}

	/**
	 * Handle customers to users bulk action.
	 *
	 * @param [type] $redirect
	 * @param [type] $doaction
	 * @param [type] $object_ids
	 * @return void
	 */
	public function wte_add_customer_bulk_action_handler( $redirect, $doaction, $object_ids ) {

		// let's remove query args first
		$redirect = remove_query_arg( array( 'wte_bulk_customers_to_users_done' ), $redirect );

		// do something for "Migrate to users" bulk action.
		if ( $doaction == 'wte_migrate_customers' ) {
			foreach ( $object_ids as $post_id ) {

				$customer_bookings = get_post_meta( $post_id, 'wp_travel_engine_bookings', true );
				$booking_details   = get_post_meta( $post_id, 'wp_travel_engine_booking_setting', true );
				$user_email        = get_the_title( $post_id );
				$username          = sanitize_user( current( explode( '@', $user_email ) ), true );

				// Ensure username is unique.
				$append     = 1;
				$o_username = $username;

				while ( username_exists( $username ) ) {
					$username = $o_username . $append;
					$append++;
				}

				// Bail if user already exists.
				if ( username_exists( $username ) || email_exists( $user_email ) ) {
					continue;
				}

				$password_generated = wp_generate_password();

				$new_customer_data = apply_filters(
					'wp_travel_engine_new_customer_data',
					array(
						'user_login' => $username,
						'user_pass'  => $password_generated,
						'user_email' => $user_email,
						'role'       => 'wp-travel-engine-customer',
					)
				);
				$customer_id       = wp_insert_user( $new_customer_data );

				update_user_meta( $customer_id, 'wp_travel_engine_user_bookings', $customer_bookings );

				update_user_meta( $customer_id, 'wp_travel_engine_customer_booking_details', $booking_details );

				do_action( 'wp_travel_engine_created_customer', $customer_id, $new_customer_data, $password_generated, $template = 'emails/customer-migrated.php' );
			}
			// do not forget to add query args to URL because we will show notices later
			$redirect = add_query_arg(
				'wte_bulk_customers_to_users_done', // just a parameter for URL (we will use $_GET['wte_bulk_customers_to_users_done'] )
				count( $object_ids ), // parameter value - how much posts have been affected
				$redirect
			);
		}
		return $redirect;
	}

	/**
	 * Add notice after completion of user migration.
	 *
	 * @return void
	 */
	public function customer_bulk_action_notices() {
		// first of all we have to make a message,
		// of course it could be just "Posts updated." like this:
		if ( ! empty( $_REQUEST['wte_bulk_customers_to_users_done'] ) ) {
			echo '<div id="message" class="updated notice is-dismissible">
				<p>' . esc_html__( 'Selected users migrated.', 'wp-travel-engine' ) . '</p>
			</div>';
		}
	}

	/**
	 * Add data to custom column.
	 *
	 * @param  String $column_name Custom column name.
	 * @param  int    $id          Post ID.
	 */
	public function wte_itineraries_manage_columns( $column_name, $id ) {
		switch ( $column_name ) {
			case 'featured':
				$featured = get_post_meta( $id, 'wp_travel_engine_featured_trip', true );
				$featured = ( isset( $featured ) && '' != $featured ) ? $featured : 'no';

				$icon_class = ' dashicons-star-empty ';
				if ( ! empty( $featured ) && 'yes' === $featured ) {
					$icon_class = ' dashicons-star-filled ';
				}
				$nonce = wp_create_nonce( 'wp_travel_engine_featured_trip_nonce' );
				printf( '<a href="#" class="wp-travel-engine-featured-trip dashicons %s" data-post-id="%d"  data-nonce="%s"></a>', $icon_class, $id, $nonce );
				break;
			default:
				break;
		} // end switch
	}

	/**
	 * Customize Admin column.
	 *
	 * @param  Array $booking_columns List of columns.
	 * @return Array                  [description]
	 */
	function wp_travel_engine_trips_columns( $itinerary_columns ) {
		$itinerary_columns['featured'] = __( 'Featured', 'wp-travel-engine' );
		return $itinerary_columns;
	}

	/**
	 * Ajax for adding featured trip meta
	 * */
	public function wp_travel_engine_featured_trip_admin_ajax() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'wp_travel_engine_featured_trip_nonce' ) ) {
			exit( 'invalid' );
		}

		header( 'Content-Type: application/json' );
		$post_id         = intval( $_POST['post_id'] );
		$featured_status = esc_attr( get_post_meta( $post_id, 'wp_travel_engine_featured_trip', true ) );
		$new_status      = $featured_status == 'yes' ? 'no' : 'yes';
		update_post_meta( $post_id, 'wp_travel_engine_featured_trip', $new_status );
		echo json_encode(
			array(
				'ID'         => $post_id,
				'new_status' => $new_status,
			)
		);
		die();
	}

	/**
	 * Ajax for adding featured trip meta
	 * */
	public function wp_travel_engine_featured_term_admin_ajax() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'wte_trip_tax_featured_nonce' ) ) {
			exit( 'invalid' );
		}

		header( 'Content-Type: application/json' );
		$post_id         = intval( $_POST['post_id'] );
		$featured_status = esc_attr( get_term_meta( $post_id, 'wte_trip_tax_featured', true ) );
		$new_status      = $featured_status == 'yes' ? 'no' : 'yes';
		update_term_meta( $post_id, 'wte_trip_tax_featured', $new_status );
		echo json_encode(
			array(
				'ID'         => $post_id,
				'new_status' => $new_status,
			)
		);
		die();
	}

	/**
	 * Get Enquiry preview.
	 *
	 * @return void
	 */
	public function wte_get_enquiry_preview_action() {
		if ( isset( $_POST['enquiry_id'] ) ) {
			$enquiry_id                        = $_POST['enquiry_id'];
			$wp_travel_engine_setting          = get_post_meta( $enquiry_id, 'wp_travel_engine_setting', true );
			$wp_travel_engine_enquiry_formdata = get_post_meta( $enquiry_id, 'wp_travel_engine_enquiry_formdata', true );
			$wte_old_enquiry_details           = isset( $wp_travel_engine_setting['enquiry'] ) ? $wp_travel_engine_setting['enquiry'] : array();
			ob_start();
			?>
				<div style="background-color:#ffffff" class="wpte-main-wrap wpte-edit-enquiry">
					<div class="wpte-block-wrap">
						<div class="wpte-block">
							<div class="wpte-block-content">
								<ul class="wpte-list">
									<?php
									if ( ! empty( $wp_travel_engine_enquiry_formdata ) ) :
										foreach ( $wp_travel_engine_enquiry_formdata as $key => $data ) :
											$data       = is_array( $data ) ? implode( ', ', $data ) : $data;
											$data_label = wp_travel_engine_get_enquiry_field_label_by_name( $key );

											if ( 'package_name' === $key ) {
												$data_label = __( 'Package Name', 'wp-travel-engine' );
											}
											?>
												<li>
													<b><?php echo esc_html( $data_label ); ?></b>
													<span>
													<?php echo wp_kses_post( $data ); ?>
													</span>
												</li>
											<?php
											endforeach;
										else :
											if ( ! empty( $wte_old_enquiry_details ) ) :
												if ( isset( $wte_old_enquiry_details['pname'] ) ) :
													?>
														<li>
															<b><?php _e( 'Package Name', 'wp-travel-engine' ); ?></b>
															<span>
																<?php echo wp_kses_post( $wte_old_enquiry_details['pname'] ); ?>
															</span>
														</li>
													<?php
												endif;
												if ( isset( $wte_old_enquiry_details['name'] ) ) :
													?>
														<li>
															<b><?php _e( 'Name', 'wp-travel-engine' ); ?></b>
															<span>
																<?php echo wp_kses_post( $wte_old_enquiry_details['name'] ); ?>
															</span>
														</li>
													<?php
												endif;
												if ( isset( $wte_old_enquiry_details['email'] ) ) :
													?>
														<li>
															<b><?php _e( 'Email', 'wp-travel-engine' ); ?></b>
															<span>
																<?php echo wp_kses_post( $wte_old_enquiry_details['email'] ); ?>
															</span>
														</li>
													<?php
												endif;
												if ( isset( $wte_old_enquiry_details['country'] ) ) :
													?>
														<li>
															<b><?php _e( 'Country', 'wp-travel-engine' ); ?></b>
															<span>
																<?php echo wp_kses_post( $wte_old_enquiry_details['country'] ); ?>
															</span>
														</li>
													<?php
												endif;
												if ( isset( $wte_old_enquiry_details['contact'] ) ) :
													?>
														<li>
															<b><?php _e( 'Contact', 'wp-travel-engine' ); ?></b>
															<span>
																<?php echo wp_kses_post( $wte_old_enquiry_details['contact'] ); ?>
															</span>
														</li>
													<?php
												endif;
												if ( isset( $wte_old_enquiry_details['adults'] ) ) :
													?>
														<li>
															<b><?php _e( 'Adults', 'wp-travel-engine' ); ?></b>
															<span>
																<?php echo wp_kses_post( $wte_old_enquiry_details['adults'] ); ?>
															</span>
														</li>
													<?php
												endif;
												if ( isset( $wte_old_enquiry_details['children'] ) ) :
													?>
														<li>
															<b><?php _e( 'Children', 'wp-travel-engine' ); ?></b>
															<span>
																<?php echo wp_kses_post( $wte_old_enquiry_details['children'] ); ?>
															</span>
														</li>
													<?php
												endif;
												if ( isset( $wte_old_enquiry_details['message'] ) ) :
													?>
														<li>
															<b><?php _e( 'Message', 'wp-travel-engine' ); ?></b>
															<span>
																<?php echo wp_kses_post( $wte_old_enquiry_details['message'] ); ?>
															</span>
														</li>
													<?php
												endif;
											endif;
										endif;
										?>
								</ul>
							</div>
						</div> <!-- .wpte-block -->
					</div> <!-- .wpte-block-wrap -->
				</div><!-- .wpte-main-wrap -->
			<?php
			$data = ob_get_clean();

			wp_send_json_success(
				array(
					'message' => __( 'Data Fetched', 'wp-travel-engine' ),
					'html'    => $data,
				)
			);
		}
		wp_send_json_error( array( 'message' => __( 'Enquiry ID is missing', 'wp-travel-engine' ) ) );
	}

	/**
	 * Load tab ajax callback.
	 *
	 * @return void
	 */
	function wpte_admin_load_tab_content_callback() {

		$tab_details = isset( $_POST['tab_details'] ) ? $_POST['tab_details'] : false;

		if ( $tab_details ) {

			$content_path = isset( $tab_details['content_path'] ) ? base64_decode( $tab_details['content_path'] ) : '';

			ob_start();
			if ( file_exists( $content_path ) ) {
				?>
						<div data-trigger="<?php echo esc_attr( $tab_details['content_key'] ); ?>" class="wpte-tab-content <?php echo esc_attr( $tab_details['content_key'] ); ?>-content ">
							<div class="wpte-title-wrap">
								<h2 class="wpte-title"><?php echo esc_html( $tab_details['tab_heading'] ); ?></h2>
							</div> <!-- .wpte-title-wrap -->
							<div class="wpte-block-content">
							<?php
								// load template.
								include $content_path;
							?>
							</div>
						</div>
					<?php
			}
			$data = ob_get_clean();

			wp_send_json_success(
				array(
					'message' => __( 'Data Fetched', 'wp-travel-engine' ),
					'html'    => $data,
				)
			);
		}
		wp_send_json_error( array( 'message' => __( 'Invalid Tab Data', 'wp-travel-engine' ) ) );
	}

	/**
	 * Load global settings tab ajax callback.
	 *
	 * @return void
	 */
	function wpte_global_settings_load_tab_content_callback() {

		$tab_details     = isset( $_POST['tab_details'] ) ? $_POST['tab_details'] : false;
		$tab_content_key = isset( $_POST['content_key'] ) ? $_POST['content_key'] : false;

		if ( $tab_details ) {
			ob_start();
			?>
			<div class="wpte-tab-content <?php echo esc_attr( $tab_content_key ); ?>-content wpte-global-settngstab">
				<div class="wpte-block-content">
					<?php
						$sub_tabs = isset( $tab_details['sub_tabs'] ) && ! empty( $tab_details['sub_tabs'] ) ? $tab_details['sub_tabs'] : array();

					if ( ! empty( $sub_tabs ) ) :
						?>
							<div class="wpte-tab-sub wpte-horizontal-tab">
								<div class="wpte-tab-wrap">
							<?php
								$current = 1;
							foreach ( $sub_tabs as $key => $tab ) :
								$class_names = '';
								if ( in_array( $key, array( 'currency', 'booking_notifications', 'purchase_receipt' ), ! 0 ) ) {
									$class_names = 'wte_note_430';
								}
								?>
									<a href="javascript:void(0);" data-wte-update="<?php echo esc_attr( $class_names ); ?>" class="wpte-tab <?php echo esc_attr( $key ); ?> <?php echo 1 === $current ? 'current' : ''; ?>"><?php echo esc_html( $tab['label'] ); ?></a>
								<?php
								$current++;
								endforeach;
							?>
								</div>

								<div class="wpte-tab-content-wrap">
								<?php
								$current = 1;
								foreach ( $sub_tabs as $key => $tab ) :
									?>
									<div class="wpte-tab-content <?php echo esc_attr( $key ); ?>-content <?php echo 1 === $current ? 'current' : ''; ?>">
										<div class="wpte-block-content">
										<?php
										if ( file_exists( $tab['content_path'] ) ) {
											include $tab['content_path'];
										}
										?>
										</div>
									</div>
									<?php
									$current++;
								endforeach;
								?>
								</div>
							</div>
						<?php
						else :
							?>
							<div class="wpte-alert"><?php echo sprintf( __( 'There are no <b>WP Travel Engine Addons</b> installed on your site currently. To extend features and get additional functionality settings,  <a target="_blank" href="%1$s">Get Addons Here</a>', 'wp-travel-engine' ), WP_TRAVEL_ENGINE_STORE_URL . '/downloads/category/add-ons/' ); ?></div>
							<?php
						endif;
						?>
					<div class="wpte-field wpte-submit">
						<input data-tab="<?php echo esc_attr( $tab_content_key ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'wpte-global-setting-save' ) ); ?>" class="wpte-save-global-settings" type="submit" name="wpte_save_global_settings" value="<?php esc_attr_e( 'Save & Continue', 'wp-travel-engine' ); ?>">
					</div>
				</div> <!-- .wpte-block-content -->
			</div>
			<?php
			$data = ob_get_clean();

			wp_send_json_success(
				array(
					'message' => __( 'Data Fetched', 'wp-travel-engine' ),
					'html'    => $data,
				)
			);
		}
		wp_send_json_error( array( 'message' => __( 'Invalid Tab Data', 'wp-travel-engine' ) ) );
	}

	/**
	 * Save and continue button callback.
	 *
	 * @return void
	 */
	function wpte_tab_trip_save_and_continue_callback() {
		if ( ! isset( $_POST['post_id'] ) || empty( $_POST['post_id'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Post ID not found', 'wp-travel-engine' ) ) );
		}
		$post_id = $_POST['post_id'];
		if ( isset( $_POST['action'] ) && 'wpte_tab_trip_save_and_continue' === $_POST['action'] ) {
			if ( ! isset( $_POST['nonce'] ) || empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wpte-trip-tab-save-continue' ) ) {
				wp_send_json_error( array( 'message' => __( 'Security Error! Nonce verification failed', 'wp-travel-engine' ) ) );
			}
			$obj                            = new Wp_Travel_Engine_Functions();
			$wp_travel_engine_setting_saved = get_post_meta( $post_id, 'wp_travel_engine_setting', true );

			if ( empty( $wp_travel_engine_setting_saved ) ) {
				$wp_travel_engine_setting_saved = array();
			}
			$wp_travel_engine_setting_saved = $obj->recursive_html_entity_decode( $wp_travel_engine_setting_saved );
			$meta_to_save                   = isset( $_POST['wp_travel_engine_setting'] ) ? $_POST['wp_travel_engine_setting'] : array();

			// Merge data.
			$metadata_merged_with_saved = array_merge( $wp_travel_engine_setting_saved, $meta_to_save );

			$checkboxes_array = array(
				'general' => array(
					'trip_cutoff_enable',
					'min_max_age_enable',
					'minmax_pax_enable',
				),
				'pricing' => array(
					'sale',
				),
				'gallery' => array(
					'enable_video_gallery',
				),
			);

			$trip_meta_checkboxes = apply_filters( 'wp_travel_engine_trip_meta_checkboxes', $checkboxes_array );

			$active_tab = $_POST['tab'];

			if ( isset( $trip_meta_checkboxes[ $active_tab ] ) ) {
				foreach ( $trip_meta_checkboxes[ $active_tab ] as $checkbox ) {
					if ( isset( $metadata_merged_with_saved[ $checkbox ] ) && ! isset( $meta_to_save[ $checkbox ] ) ) {
						unset( $metadata_merged_with_saved[ $checkbox ] );
					}
				}
			}

			$arrays_in_meta = array(
				'itinerary',
				'faq',
				'trip_facts',
				'trip_highlights',
			);

			$arrays_in_meta = apply_filters( 'wpte_trip_meta_array_key_bases', $arrays_in_meta );

			foreach ( $arrays_in_meta as $arr_key ) {
				if ( isset( $meta_to_save[ $arr_key ] ) && ! is_array( $meta_to_save[ $arr_key ] ) ) {
					unset( $metadata_merged_with_saved[ $arr_key ] );
				}
			}

			$settings = $obj->wte_sanitize_array( $metadata_merged_with_saved );

			// if ( isset( $_POST['overview_editor_content'] ) ) {
			// $settings['tab_content']['1_wpeditor'] = $_POST['overview_editor_content'];
			// }

			update_post_meta( $post_id, 'wp_travel_engine_setting', $settings );

			/**
			 * Hook for Save& Continue support on addons.
			 */
			do_action( 'wpte_save_and_continue_additional_meta_data', $post_id, $_POST );

			if ( isset( $settings['trip_price'] ) ) {
				$cost = $settings['trip_price'];
				update_post_meta( $post_id, 'wp_travel_engine_setting_trip_price', $cost );
			}

			if ( isset( $settings['trip_prev_price'] ) ) {
				$prev_cost = $settings['trip_prev_price'];
				update_post_meta( $post_id, 'wp_travel_engine_setting_trip_prev_price', $prev_cost );
			}

			if ( isset( $settings['trip_duration'] ) ) {
				$duration = $settings['trip_duration'];
				update_post_meta( $post_id, 'wp_travel_engine_setting_trip_duration', $duration );
			}

			if ( isset( $_POST['wpte_gallery_id'] ) ) {
				update_post_meta( $post_id, 'wpte_gallery_id', $_POST['wpte_gallery_id'] );
			}

			// Update / Save gallery metas.
			if ( isset( $_POST['wpte_vid_gallery'] ) ) {
				update_post_meta( $post_id, 'wpte_vid_gallery', stripslashes_deep( $_POST['wpte_vid_gallery'] ) );
			}

			if ( isset( $_POST['wp_travel_engine_trip_min_age'] ) ) {
				update_post_meta( $post_id, 'wp_travel_engine_trip_min_age', $_POST['wp_travel_engine_trip_min_age'] );
			}

			if ( isset( $_POST['wp_travel_engine_trip_max_age'] ) ) {
				update_post_meta( $post_id, 'wp_travel_engine_trip_max_age', $_POST['wp_travel_engine_trip_max_age'] );
			}

			if ( isset( $settings['trip_price'] ) || isset( $settings['trip_prev_price'] ) ) {
				if ( isset( $settings['multiple_pricing']['adult']['enable_sale'] ) && $settings['multiple_pricing']['adult']['enable_sale'] == '1' ) {
					update_post_meta( $post_id, 'wp_travel_engine_setting_trip_actual_price', $settings['trip_price'] );
				} else {
					update_post_meta( $post_id, 'wp_travel_engine_setting_trip_actual_price', $settings['trip_prev_price'] );
				}
			}

			wp_send_json_success( array( 'message' => 'Trip settings saved successfully.' ) );
		}
	}

	/**
	 * Callback for global tabs data save action.
	 *
	 * @return void
	 */
	function wpte_global_tabs_save_data_callback() {

		if ( isset( $_POST['action'] ) && 'wpte_global_tabs_save_data' === $_POST['action'] ) {
			if ( ! isset( $_POST['nonce'] ) || empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wpte-global-setting-save' ) ) {
				wp_send_json_error( array( 'message' => __( 'Security Error! Nonce verification failed', 'wp-travel-engine' ) ) );
			}

			$global_settings_saved = wp_travel_engine_get_settings();

			$global_settings_to_save = isset( $_POST['wp_travel_engine_settings'] ) ? $_POST['wp_travel_engine_settings'] : array();

			// Merge data.
			$global_settings_merged_with_saved = array_merge( $global_settings_saved, $global_settings_to_save );

			$global_checkboxes_array = array(
				'wpte-emails' => array(
					'email' => 'disable_notif',
					'email' => 'cust_notif',
				),
			);

			$global_settings_checkboxes = apply_filters( 'wp_travel_engine_global_settings_checkboxes', $global_checkboxes_array );

			$active_tab = $_POST['tab'];

			if ( isset( $global_settings_checkboxes[ $active_tab ] ) ) {
				foreach ( $global_settings_checkboxes[ $active_tab ] as $key => $checkbox ) {
					if ( isset( $global_settings_merged_with_saved[ $key ][ $checkbox ] ) && ! isset( $global_settings_to_save[ $key ][ $checkbox ] ) ) {
						unset( $global_settings_merged_with_saved[ $key ][ $checkbox ] );
					}
				}
			}

			$add_checkbox = array(
				'wpte-miscellaneous' => array(
					'booking',
					'enquiry',
					'emergency',
					'feat_img',
					'travelers_information',
					'tax_images',
					'show_multiple_pricing_list_disp',
					'show_excerpt',
				),
				'wpte-payment'       => array(
					'payment_debug',
				),
				'wpte-dashboard'     => array(
					'enable_checkout_customer_registration',
					'disable_my_account_customer_registration',
					'generate_username_from_email',
					'generate_user_password',
				),
			);

			$add_checkbox = apply_filters( 'wpte_global_add_checkboxes', $add_checkbox );

			if ( isset( $add_checkbox[ $active_tab ] ) ) {
				foreach ( $add_checkbox[ $active_tab ] as $checkbox ) {
					if ( isset( $global_settings_merged_with_saved[ $checkbox ] ) && ! isset( $global_settings_to_save[ $checkbox ] ) ) {
						unset( $global_settings_merged_with_saved[ $checkbox ] );
					}
				}
			}

			if ( 'wpte-payment' === $active_tab ) {
				// Payment checkboxes.
				$payment_gateways = wp_travel_engine_get_available_payment_gateways();

				foreach ( $payment_gateways as $key => $gateway ) {
					if ( isset( $global_settings_merged_with_saved[ $key ] ) && ! isset( $global_settings_to_save[ $key ] ) ) {
						unset( $global_settings_merged_with_saved[ $key ] );
					}
				}
			}

			update_option( 'wp_travel_engine_settings', wp_unslash( $global_settings_merged_with_saved ) );

			/**
			 * Hook for addons global settings.
			 */
			do_action( 'wpte_after_save_global_settings_data', $_POST );

			wp_send_json_success( array( 'message' => 'Settings Saved Successfully.' ) );
		}

	}

	/**
	 * Display Trip Code Section
	 */
	function wpte_display_trip_code_section() {
		global $post;

		// Edit Trip Code filter
		$trip_code_edit = apply_filters( 'wpte_edit_trip_code', false );

		if ( $trip_code_edit ) {

			/**
			 * wp_travel_engine_edit_trip_code hook
			 *
			 * @hooked wte_edit_trip_code_section - Trip Code Addon
			 */
			do_action( 'wp_travel_engine_edit_trip_code' );

		} else {

			?>
				<div class="wpte-field wpte-trip-code wpte-floated">
					<label class="wpte-field-label"><?php _e( 'Trip Code', 'wp-travel-engine' ); ?></label>
					<span class="wpte-trip-code-box"><?php echo esc_html( sprintf( __( 'WTE-%1$s', 'wp-travel-engine' ), $post->ID ) ); ?></span>
					<div class="wpte-info-block">
						<p>
							<?php
								echo sprintf( __( 'Need to edit trip code to set your own? Trip Code extension allows you to add unique trip code to your trips. %1$sGet Trip Code extension now%2$s.', 'wp-travel-engine' ), '<a target="_blank" href="https://wptravelengine.com/downloads/trip-code/?utm_source=setting&utm_medium=customer_site&utm_campaign=setting_addon">', '</a>' );
							?>
						</p>
					</div>
				</div>
			<?php

		}
	}

	/**
	 * Display Extension Notes
	 */
	function wpte_display_extension_upsell_notes() {

		/**
		 * wte_after_pricing_options_section hook
		 *
		 * @hooked wte_add_group_discount_pricing - Group Discount Addon
		 * @hooked wpte_partial_payment_add_meta_boxes - Partial Payment Addon
		 */
		do_action( 'wte_after_pricing_options_section' );

		if ( ! class_exists( 'Wte_Partial_Payment_Admin' ) ) {
			?>
				<div class="wpte-form-block">
					<div class="wpte-title-wrap">
						<h2 class="wpte-title"><?php _e( 'Partial Payment', 'wp-travel-engine' ); ?></h2>
					</div> <!-- .wpte-title-wrap -->
					<div class="wpte-info-block">
						<p>
							<?php
								echo sprintf( __( 'Want to collect upfront or partial payment? Partial Payment extension allows you to set upfront payment in percentage or fixed amount which travellers can pay when booking a tour. %1$sGet Partial Payment extension now%2$s.', 'wp-travel-engine' ), '<a target="_blank" href="https://wptravelengine.com/downloads/partial-payment/?utm_source=setting&utm_medium=customer_site&utm_campaign=setting_addon">', '</a>' );
							?>
						</p>
					</div>
				</div>
			<?php
		}

		if ( ! class_exists( 'Wp_Travel_Engine_Group_Discount' ) ) {
			?>
				<div class="wpte-form-block">
					<div class="wpte-title-wrap">
						<h2 class="wpte-title"><?php _e( 'Group Discount', 'wp-travel-engine' ); ?></h2>
					</div> <!-- .wpte-title-wrap -->
					<div class="wpte-info-block">
						<p>
							<?php
								echo sprintf( __( 'Want to provide group discounts and increase sales? Group Discount extension allows you to provide group discount on the basis of number booking a tour. %1$sGet Group Discount extension now%2$s.', 'wp-travel-engine' ), '<a target="_blank" href="https://wptravelengine.com/downloads/group-discount/?utm_source=setting&utm_medium=customer_site&utm_campaign=setting_addon">', '</a>' );
							?>
						</p>
					</div>
				</div>
			<?php
		}

	}

	/** Add class to the body for all trip pages */
	function wpte_body_class_before_header_callback( $classes ) {
		$screen = get_current_screen();

		if ( $screen->id == 'booking_page_class-wp-travel-engine-admin' || $screen->post_type == 'trip' || $screen->post_type == 'booking' || $screen->post_type == 'customer' || $screen->id == 'trip_page_class-wp-travel-engine-admin' || ( isset( $_GET['page'] ) && $_GET['page'] == 'class-wp-travel-engine-admin.php' ) || 'edit-wte-coupon' === $screen->id || 'wte-coupon' === $screen->id ) {
			$classes .= 'wpte-activated';
		} else {
			$classes .= '';
		}
		return $classes;
	}

	/** Add Custom Info inside the trip tab section */

	function wp_travel_engine_trip_custom_info() {
		ob_start();
		?>
		<div style="margin-top:40px;" class="wpte-form-block">
			<div class="wpte-title-wrap">
				<h2 class="wpte-title"><?php _e( 'Itinerary Downloader', 'wp-travel-engine' ); ?></h2>
			</div> <!-- .wpte-title-wrap -->
			<div class="wpte-info-block">
				<b><?php _e( 'Note:', 'wp-travel-engine' ); ?></b>
				<p>
					<?php _e( 'Want travellers to download the tour details in PDF format and read later?', 'wp-travel-engine' ); ?>
					<?php
					if ( ! class_exists( 'Wte_Itinerary_Downloader' ) ) {
						echo sprintf( __( '%1$sGet Itinerary Downloader extension now%2$s.', 'wp-travel-engine' ), '<a target="_blank" href="https://wptravelengine.com/downloads/itinerary-downloader/?utm_source=setting&utm_medium=customer_site&utm_campaign=setting_addon">', '</a>' );
					} else {
						_e( 'You can configure Itinerary Downloader via <b>WP Travel Engine > Settings > Extensions > Itinerary Downloader</b>.', 'wp-travel-engine' );
					}
					?>
				</p>
			</div>
			<?php
			if ( class_exists( 'Wte_Itinerary_Downloader' ) ) {
				$page_shortcode = '[wte_itinerary_downloader]';
				?>
					<div class="wpte-shortcode">
						<span class="wpte-tooltip"><?php esc_html_e( 'To display Itinerary Downloader in posts/pages/tabs/widgets use the following', 'wp-travel-engine' ); ?> <b><?php esc_html_e( 'Shortcode.', 'wp-travel-engine' ); ?></b></span>
						<div class="wpte-field wpte-field-gray wpte-floated">
							<input id="wpte-iten-down-code" readonly type="text" value="<?php esc_attr_e( $page_shortcode, 'wp-travel-engine' ); ?>">
							<button data-copyid="wpte-iten-down-code" class="wpte-copy-btn"><?php esc_html_e( 'Copy', 'wp-travel-engine' ); ?></button>
						</div>
					</div>
					<?php
			}
			?>
		</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		apply_filters( 'wp_travel_engine_filtered_trip_custom_info', $output );

		echo $output;
	}
}
