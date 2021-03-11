<?php
class Wp_Travel_Engine_Tabs{

	function __construct(){
		add_action( 'add_meta_boxes', array( $this, 'wpte_add_trip_pricing_meta_boxes' ) );
		add_action( 'add_meta_boxes', array( $this, 'wpte_add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'wp_travel_engine_save_trip_price_meta_box_data' ) );
		// add_action( 'save_post', array( $this, 'wp_travel_engine_save_meta_box_data' ) );
		add_action( 'add_meta_boxes', array( $this, 'wpte_add_enquiry_meta_boxes' ) );
		// add_action( 'save_post', array( $this, 'wp_travel_engine_save_enquiry_meta_box_data' ) );
	}

	/**
	 * Adds metabox for trip pricing.
	 *
	 * @since 1.0.0
	 */
	function wpte_add_trip_pricing_meta_boxes(){
		$screens = array( 'trip' );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'trip_pricing_id',
				__( 'WP Travel Engine - Trip Settings', 'wp-travel-engine' ),
				array($this,'wp_travel_engine_trip_price_metabox_callback'),
				$screen,
				'normal',
				'high'
			);
		}
	}

	// Tab for notice listing and settings
	public function wp_travel_engine_trip_price_metabox_callback($tab_args){
		include plugin_dir_path( __FILE__ ) . 'meta-parts/trip-metas.php';
	}

	/**
	 * When the post is saved, saves our custom data.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	function wp_travel_engine_save_trip_price_meta_box_data( $post_id ) {

		// Alter post type for previews.
		if ( isset( $_POST['wp-preview'] ) && 'dopreview' === $_POST['wp-preview'] ) {
			$post_id = $_POST['post_ID'];
		}
		/*
		* We need to verify this came from our screen and with proper authorization,
		* because the save_post action can be triggered at other times.
		*/
		$wp_travel_engine_setting_saved = get_post_meta( $post_id, 'wp_travel_engine_setting', true );

		$obj = new Wp_Travel_Engine_Functions;

		if ( empty( $wp_travel_engine_setting_saved ) ) {
			$wp_travel_engine_setting_saved = array();
		}
		// Sanitize user input.
		if(isset($_POST['wp_travel_engine_setting'])) {

			$wp_travel_engine_setting_saved = $obj->recursive_html_entity_decode($wp_travel_engine_setting_saved);

			$meta_to_save = $_POST['wp_travel_engine_setting'];
			// Merge data.
			$metadata_merged_with_saved = array_merge( $wp_travel_engine_setting_saved, $meta_to_save );

			$trip_meta_checkboxes = apply_filters( 'wp_travel_engine_trip_meta_checkboxes', array(
				'trip_cutoff_enable',
				'min_max_age_enable',
				'minmax_pax_enable'
			) );

			foreach( $trip_meta_checkboxes as $checkbox ) {
				if ( isset( $metadata_merged_with_saved[$checkbox] ) && ! isset( $meta_to_save[$checkbox] ) ) {
					unset( $metadata_merged_with_saved[$checkbox] );
				}
			}

			$arrays_in_meta = array(
				'itinerary',
				'faq',
				'trip_facts',
				'trip_highlights'
			);

			$arrays_in_meta = apply_filters( 'wpte_trip_meta_array_key_bases', $arrays_in_meta );

			foreach( $arrays_in_meta as $arr_key ) {
				if ( isset( $meta_to_save[$arr_key] ) && ! is_array( $meta_to_save[$arr_key] ) ) {
					unset( $metadata_merged_with_saved[$arr_key] );
				}
			}

			$settings = $obj->wte_sanitize_array( $metadata_merged_with_saved );

			update_post_meta( $post_id, 'wp_travel_engine_setting', $settings );

			$cost = $settings['trip_price'];
			update_post_meta($post_id,'wp_travel_engine_setting_trip_price',$cost);

			$prev_cost = $settings['trip_prev_price'];
			update_post_meta($post_id,'wp_travel_engine_setting_trip_prev_price',$prev_cost);

			$duration = $settings['trip_duration'];
			update_post_meta($post_id,'wp_travel_engine_setting_trip_duration',$duration);

			if(	isset($settings['trip_price']) || isset($settings['trip_prev_price'])){
				if(isset($settings['multiple_pricing']['adult']['enable_sale']) && $settings['multiple_pricing']['adult']['enable_sale'] == '1'){
					update_post_meta($post_id,'wp_travel_engine_setting_trip_actual_price',$settings['trip_price']);
				}else{
					update_post_meta($post_id,'wp_travel_engine_setting_trip_actual_price',$settings['trip_prev_price']);
				}
			}

			if(isset($_POST['wpte_gallery_id'])) {
				update_post_meta($post_id, 'wpte_gallery_id', $_POST['wpte_gallery_id']);
			}

			// Update / Save gallery metas.
			if( isset( $_POST['wpte_vid_gallery'] ) ) {
				update_post_meta( $post_id, 'wpte_vid_gallery', stripslashes_deep( $_POST['wpte_vid_gallery'] ) );
			}

			if( isset( $_POST['wp_travel_engine_trip_min_age'] ) ) {
				update_post_meta( $post_id, 'wp_travel_engine_trip_min_age', $_POST['wp_travel_engine_trip_min_age'] );
			}

			if( isset( $_POST['wp_travel_engine_trip_max_age'] ) ) {
				update_post_meta( $post_id, 'wp_travel_engine_trip_max_age',$_POST['wp_travel_engine_trip_max_age'] );
			}
		}
	}

	/**
	 * Adds metabox for tabs.
	 *
	 * @since 1.0.0
	 */
	function wpte_add_meta_boxes(){
		$screens = array( 'trip' );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'trip_tab_id',
				__( '', 'wp-travel-engine' ),
				array($this,'wp_travel_engine_metabox_callback'),
				$screen,
				'normal',
				'high'
			);
		}
	}

	// Tab for notice listing and settings
	public function wp_travel_engine_metabox_callback($tab_args){
	}

	/**
	 * When the post is saved, saves our custom data.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	function wp_travel_engine_save_meta_box_data( $post_id ) {

		/*
		* We need to verify this came from our screen and with proper authorization,
		* because the save_post action can be triggered at other times.
		*/
		$wp_travel_engine_setting_saved = get_post_meta( $post_id, 'wp_travel_engine_setting', true );
		// Sanitize user input.
		if( isset( $_POST['wp_travel_engine_setting'] ) ){

			$meta_to_save = $_POST['wp_travel_engine_setting'];
			// Merge data.
			$metadata_merged_with_saved = wp_parse_args( $meta_to_save, $wp_travel_engine_setting_saved );

			$obj = new Wp_Travel_Engine_Functions;
			$settings = $obj->wte_sanitize_array( $metadata_merged_with_saved );

			update_post_meta( $post_id, 'wp_travel_engine_setting', $settings );
		}
	}


	/**
	 * Adds metabox for enquiries.
	 *
	 * @since 1.0.0
	 */
	function wpte_add_enquiry_meta_boxes(){
		$screens = array( 'enquiry' );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'enquiry_tab_id',
				__( 'Enquiry details', 'wp-travel-engine' ),
				array($this,'wp_travel_engine_enquiry_metabox_callback'),
				$screen,
				'normal',
				'high'
			);
		}
	}

	// Tab for notice listing and settings
	public function wp_travel_engine_enquiry_metabox_callback($tab_args){
		include WP_TRAVEL_ENGINE_BASE_PATH.'/admin/meta-parts/enquiry.php';
	}

	/**
	 * When the post is saved, saves our custom data.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	function wp_travel_engine_save_enquiry_meta_box_data( $post_id ) {

	    /*
	     * We need to verify this came from our screen and with proper authorization,
	     * because the save_post action can be triggered at other times.
	     */
	    // Sanitize user input.
	    if(isset($_POST['wp_travel_engine_setting']))
	    {
		    $obj = new Wp_Travel_Engine_Functions;
		    $settings = $obj->wte_sanitize_array( $_POST['wp_travel_engine_setting'] );
		    update_post_meta( $post_id, 'wp_travel_engine_setting', $settings );
	    }
	}
}

new Wp_Travel_Engine_Tabs();
