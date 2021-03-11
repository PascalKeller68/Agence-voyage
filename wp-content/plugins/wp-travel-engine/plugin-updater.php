<?php
if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( plugin_dir_path( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}
// retrieve our license key from the DB
$wp_travel_engine = get_option( 'wp_travel_engine_license' );

function wp_travel_engine_license_menu() {
	add_submenu_page( 'edit.php?post_type=booking', 'Extensions for WP Travel Engine', 'Plugin License', 'manage_options', WP_TRAVEL_ENGINE_PLUGIN_LICENSE_PAGE, 'wp_travel_engine_license_page' );
}
add_action('admin_menu', 'wp_travel_engine_license_menu');

function wp_travel_engine_license_page() {
	include_once plugin_dir_path( __FILE__ ) . '/includes/backend/plugin-license/license.php';
}

function wp_travel_engine_register_option() {
	// creates our settings in the options table
	register_setting('wp_travel_engine_license', 'wp_travel_engine_license', 'wpte_sanitize_license' );
}
add_action('admin_init', 'wp_travel_engine_register_option');

function wpte_sanitize_license( $new ) {
	$value = $_POST['addon_name'];
	$option = get_option( 'wp_travel_engine_license' );
	$addon_name = apply_filters( 'wp_travel_engine_addons', array() );
		$wte_fixed_departure_status  = isset( $option[$value.'_license_status'] ) ? esc_attr( $option[$value.'_license_status'] ):false;
		$arr = array();
		if(  $_POST['edd_license_activate'] && $_POST['edd_license_activate'] == 'Activate License' )
		{
			
					$new[$value.'_license_key'] = $option[$value.'_license_key'];
					$new[$value.'_license_status'] = 'valid';
			
		}
		if(  $_POST['edd_license_deactivate'] && $_POST['edd_license_deactivate'] == 'Deactivate License' )
		{
			
			$old = $option[$value.'_license_key'];
			if( $old && $old != $new[$value.'_license_key'] ) {
				$arr[$value.'_license_status'] = '';
				$wte_fixed_departure_status_new = array_merge_recursive( $option, $arr );
				update_option( 'wp_travel_engine_license', $wte_fixed_departure_status_new );
				$new[$value.'_license_key'] = $option[$value.'_license_key'];
				$new[$value.'_license_status'] = '';
			}
			
		}
		if( $_POST['submit'] ){
			foreach ($addon_name as $key => $val) {
				$new[$val.'_license_key'] = isset($_POST['wp_travel_engine_license'][$val.'_license_key']) ? esc_attr($_POST['wp_travel_engine_license'][$val.'_license_key']):false;
				$new[$val.'_license_status']  = isset( $option[$val.'_license_status'] ) ? esc_attr( $option[$val.'_license_status'] ):false;
			}
		}
	return $new;
}

function wp_travel_engine_activate_license() {
	// listen for our activate button to be clicked
	if( isset( $_POST['edd_license_activate'] ) ) {
		// run a quick security check
	 	//if( ! check_admin_referer( 'wp_travel_engine_license_nonce', 'wp_travel_engine_license_nonce' ) )
// 			return; // get out if we didn't click the Activate button
		$wp_travel_engine = get_option( 'wp_travel_engine_license');

		$addon_name = $_POST['addon_name'];
		$addon_id = apply_filters( 'wp_travel_engine_addons_id', array() );
		// retrieve the license from the database
		$wte_fixed_departure_license = isset($wp_travel_engine[$addon_name.'_license_key']) ? esc_attr($wp_travel_engine[$addon_name.'_license_key']):false;
		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $wte_fixed_departure_license,
			'item_id'    => $addon_id[$addon_name], // The ID of the item in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( WP_TRAVEL_ENGINE_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			$message =  ( is_wp_error( $response ) && ( $response->get_error_message() )!='' ) ? $response->get_error_message() : __( 'An error occurred, please try again.', 'wp-travel-engine' );

		} else {

			$wte_fixed_departure_license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $wte_fixed_departure_license_data->success ) {

				switch( $wte_fixed_departure_license_data->error ) {

					case 'expired' :

						$message = sprintf(
							__( 'Your license key expired on %s.', 'wp-travel-engine' ),
							date_i18n( get_option( 'date_format' ), strtotime( $wte_fixed_departure_license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'disabled' :
					case 'revoked' :

						$message = __( 'Your license key has been disabled.', 'wp-travel-engine' );
						break;

					case 'missing' :

						$message = __( 'Invalid license key supplied. Please check if you have entered correct license key.', 'wp-travel-engine' );
						break;

					case 'invalid' :
					case 'site_inactive' :

						$message = __( 'Your license is not active for this URL.', 'wp-travel-engine' );
						break;

					case 'item_name_mismatch' :

						$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'wp-travel-engine' ), EDD_SAMPLE_ITEM_NAME );
						break;

					case 'no_activations_left':

						$message = __( 'Your license key has reached its activation limit.', 'wp-travel-engine' );
						break;

					default :

						$message = __( 'An error occurred, please try again.', 'wp-travel-engine' );
						break;
				}

			}

		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			$base_url = admin_url( 'edit.php?post_type=booking&page=wp_travel_engine_license_page' );
			// $redirect = add_query_arg( array( 'sl_activation' => 'false' ), $base_url );

			$redirect_url = add_query_arg( array( 'wte_license_error_msg' => urlencode( $message ), 'wte_addon_name' => $addon_name ), $base_url );
			// echo $message;
			wp_safe_redirect( $redirect_url );
			exit();
		}

		// $wte_fixed_departure_license_data->license will be either "valid" or "invalid"
		$options = get_option( 'wp_travel_engine_license' );
		// echo $options[$addon_name.'_license_status'];
		// die;
		$wte_fixed_departure_status  = isset( $options[$addon_name.'_license_status'] ) ? esc_attr( $options[$addon_name.'_license_status'] ):false;
		if( $wte_fixed_departure_status!= 'valid' )
		{
			$arr = array();
			
			$arr[$addon_name.'_license_status'] = $wte_fixed_departure_license_data->license;
			$wte_fixed_departure_status_new = array_merge_recursive( $options, $arr );
			update_option( 'wp_travel_engine_license', $wte_fixed_departure_status_new );
			set_site_transient( 'update_plugins', null );

			// Decode license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			update_option( $addon_name. '_license_active', $license_data );
		}

		wp_redirect( admin_url( 'edit.php?post_type=booking&page=wp_travel_engine_license_page' ) );
		exit();
	}
}
add_action('admin_init', 'wp_travel_engine_activate_license');

function wp_travel_engine_deactivate_license() {

	if( isset( $_POST['edd_license_deactivate'] ) ) {
		// run a quick security check
	 	if( ! check_admin_referer( 'wp_travel_engine_license_nonce', 'wp_travel_engine_license_nonce' ) )
			return;

		// Run on deactivate button press
		// if ( isset( $_POST[ $_POST['addon_name'] . '_license_key_deactivate'] ) ) {

			$wp_travel_engine = get_option( 'wp_travel_engine_license');

			$addon_name = $_POST['addon_name'];
			$addon_id = apply_filters( 'wp_travel_engine_addons_id', array() );
			// retrieve the license from the database
			$wte_fixed_departure_license = isset($wp_travel_engine[$addon_name.'_license_key']) ? esc_attr($wp_travel_engine[$addon_name.'_license_key']):false;

			// data to send in our API request
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $wte_fixed_departure_license,
				'item_id'    => $addon_id[$addon_name], // The ID of the item in EDD
				'url'        => home_url()
			);

			// Call the custom API.
			$response = wp_remote_post( WP_TRAVEL_ENGINE_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
			$options = get_option( 'wp_travel_engine_license' );

			$wte_fixed_departure_status  = isset( $options[$addon_name.'_license_status'] ) ? esc_attr( $options[$addon_name.'_license_status'] ):false;
			if( $wte_fixed_departure_status == 'valid' )
			{
				$arr = array();
				
				$arr[$addon_name.'_license_status'] = '';
				$arr[$addon_name.'_license_key'] = '';
				$wte_fixed_departure_status_new = array_merge_recursive( $options, $arr );
				update_option( 'wp_travel_engine_license', $wte_fixed_departure_status_new );
			}

			wp_redirect( admin_url( 'edit.php?post_type=booking&page=wp_travel_engine_license_page' ) );
			exit();

		// }
	}
}
add_action('admin_init', 'wp_travel_engine_deactivate_license');


/**
 * This is a means of catching errors from the activation method above and displaying it to the customer
 */
function wp_travel_engine_license_admin_notices() {
	if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {
		switch( $_GET['sl_activation'] ) {
			case 'false':
				$message = urldecode( $_GET['message'] );
				?>
				<div class="error">
					<p><?php echo $message; ?></p>
				</div>
				<?php
				break;
			case 'true': ?>
				<div id="message" class="updated inline"><p><?php _e( 'Your license has been activated.', 'wp-travel-engine'); ?></p></div>
			<?php
			break;
		}
	}
}
add_action( 'admin_notices', 'wp_travel_engine_license_admin_notices' );


/**
 * Admin notices for errors
 *
 * @return  void
 */
function notices() {

	$messages = array();

	$option = get_option( 'wp_travel_engine_license' );
	$addon_name = apply_filters( 'wp_travel_engine_addons', array() );
	$a_name = apply_filters( 'wp_travel_engine_licenses', array() );
	$b_name = apply_filters( 'wp_travel_engine_addons_id', array() );

	$i = 0;
	foreach ($addon_name as $key => $value) {
		if(  isset( $option[$value.'_license_status'] ) && $option[$value.'_license_status'] != 'valid' || isset($option[$value.'_license_key']) && $option[$value.'_license_key'] =='' )
		{
			$messages[] = sprintf(
				__( 'You have invalid or expired license keys for WP Travel Engine. Please go to the <a href="%s">Licenses page</a> to correct this issue.', 'wp-travel-engine' ),
				admin_url( 'edit.php?post_type=booking&page=wp_travel_engine_license_page' )
			);

			$showed_invalid_message = true;
		}
	}
	
	if( ! empty( $messages ) && is_array( $messages ) ) {

			echo '<div class="error">';
				echo '<p>' . end($messages) . '</p>';
			echo '</div>';
	}

}
add_action( 'admin_notices', 'notices' );


/**
 * show update nofication row -- needed for multisite subsites, because WP won't tell you otherwise!
 *
 * @param string  $file
 * @param array   $plugin
 */
function show_update_notification( $file, $value1='' ) {
	$update_cache = get_site_transient( 'update_plugins' );

	$update_cache = is_object( $update_cache ) ? $update_cache : new stdClass();
	$a_name = apply_filters( 'wp_travel_engine_licenses', array() );
	$b_name = apply_filters( 'wp_travel_engine_addons_id', array() );
	$addon_name = apply_filters( 'wp_travel_engine_addons', array() );
	$option = get_option( 'wp_travel_engine_license' );
	$addon_id = apply_filters( 'wp_travel_engine_addons_id', array() );

	$real_addons = wp_list_pluck( $a_name, 'item_id' );

	$addon_id = array_filter( $addon_id, function( $id ) use ($real_addons) {
		return in_array( $id, $real_addons );
	 });

	$addon_slugs = array_keys( $addon_id );

	$addon_name = array_filter( $addon_name, function( $slug, $name ) use ($addon_slugs ) {
		return in_array( $slug, $addon_slugs );
	}, ARRAY_FILTER_USE_BOTH);

	$i = 0;
	foreach ($addon_name as $key => $value) {
		$value1 = $value;
		$value1 = str_replace('_', '-', $value1);
		if($value1 == 'wte-advance-search')
		{
			$aaa = 'wte-advanced-search/wte-advanced-search.php';
		}

		elseif($value1 == 'wte-fixed-starting-dates'){
			$aaa = 'trip-fixed-starting-dates/wte-trip-fixed-departure-dates.php';
		}

		elseif($value1 == 'wte-partial-payment'){
			$aaa = 'wp-travel-engine-partial-payment/wte-partialpayment.php';
		}
		
		elseif($value1 == 'wte_group_discount'){
			$aaa = 'wp-travel-engine-group-discount/wp-travel-engine-group-discount.php';
		}

		elseif($value1 == 'wte-paypal-express'){
			$aaa = 'wp-travel-engine-paypal-express-gateway/wte-paypalexpress.php';
		}

		else{

			$aaa = $value1.'/'.$value1.'.php';
		}
		if( isset( $option[$value.'_license_status'] ) && $option[$value.'_license_status'] == 'valid' && isset($a_name[$i]['version']) ) {
			if ( empty( $update_cache->response ) || empty( $update_cache->response[ $aaa ] ) && isset($option[$value.'_license_key']) && $option[$value.'_license_key']!='' ) {
				$key1 = substr($key, strpos($key, "-") + 1); 
				$key1 = str_replace(' ', '+', $key1);
				$key1 = ltrim($key1,'+');
				$api_params = array(
					'edd_action' => 'get_version',
					'license'    => $option[$value.'_license_key'],
					'item_name'  => '',
					'item_id'    => $addon_id[$value],
					'version'    => $a_name[$i]['version'],
					'slug'       => $value,
					'author'     => 'WP Travel Engine',
					'url'        => home_url(),
					'beta'       => '',
				);
				$verify_ssl = verify_ssl();
				$request    = wp_remote_post( 'https://wptravelengine.com/', array( 'timeout' => 15, 'sslverify' => $verify_ssl, 'body' => $api_params ) );
				$api_params1 = array(
					'edd_action' => 'check_license',
					'license'    => $option[$value.'_license_key'],
					'item_name'  => $key1,
					'item_id'    => $addon_id[$value],
					'version'    => $a_name[$i]['version'],
					'slug'       => $value,
					'author'     => 'WP Travel Engine',
					'url'        => home_url(),
					'beta'       => '',
				);
				$request1    = wp_remote_post( 'https://wptravelengine.com/', array( 'timeout' => 15, 'sslverify' => $verify_ssl, 'body' => $api_params1 ) );

				if ( ! is_wp_error( $request ) ) {
					$request = json_decode( wp_remote_retrieve_body( $request ) );
				}

				if ( $request && isset( $request->sections ) ) {
					$request->sections = maybe_unserialize( $request->sections );
				} else {
					$request = false;
				}

				if ( $request && isset( $request->banners ) ) {
					$request->banners = maybe_unserialize( $request->banners );
				}

				if( ! empty( $request->sections ) ) {
					foreach( $request->sections as $key => $section ) {
						$request->$key = (array) $section;
					}
				}
				// $version_info = get_cached_version_info();
				$version_info = $request;
				if ( false === $version_info ) {

					//set_version_info_cache( $version_info );
				}

				if ( ! is_object( $version_info ) ) {
					return;
				}
				if ( version_compare( $a_name[$i]['version'], $version_info->new_version, '<' ) ) {

					$update_cache->response[ $value ] = $version_info;

				}

				$update_cache->last_checked = current_time( 'timestamp' );
				$update_cache->checked[ $aaa ] = $a_name[$i]['version'];

				set_site_transient( 'update_plugins', $update_cache );
			} else {
				$version_info = $update_cache->response[ $aaa ];
			}
			
			$version = $a_name[$i]['version'];

			$plugin_license = isset( $request1['body'] ) ? json_decode($request1['body']) : new stdclass();
			
			if( isset( $plugin_license->license ) && $plugin_license->license == 'expired' )
			{
					$value = str_replace('_', '-', $value);
					
					$table = '<tr class="plugin-update-tr" id="' . $value . '-update" data-slug="' . $value . '" data-plugin="' . $aaa . '">';

					$table.= '<td colspan="3" class="plugin-update colspanchange">';

					$changelog_link = self_admin_url( 'index.php?edd_sl_action=view_plugin_changelog&plugin=' . $value . '&slug=' . $value . '&TB_iframe=true&width=772&height=911' );
					if ( !empty( $version_info->download_link ) ) {
						$table .= sprintf(
							__( 'The license of %1$s has expired. %2$sRenew Now%3$s', 'wp-travel-engine' ),
							esc_html( $version_info->name ),
							'<a href="https://wptravelengine.com/downloads/category/add-ons/" target="_blank">',
							'</a>'
						);
					}

					$table .= '</td></tr>';
					echo '<div class="update-message notice inline notice-warning notice-alt">';
						echo '<p>' . $table . '</p>';
					echo '</div>';
			}
			else{
			// Restore our filter
				if ( version_compare( $version, $version_info->new_version, '<' ) && isset( $_GET['page'] ) && $_GET['page'] == 'wp_travel_engine_license_page' ) {
					// 

					$value = str_replace('_', '-', $value);
					
					$table = '<tr class="plugin-update-tr" id="' . $value . '-update" data-slug="' . $value . '" data-plugin="' . $aaa . '">';

					$table.= '<td colspan="3" class="plugin-update colspanchange">';

					$changelog_link = self_admin_url( 'index.php?edd_sl_action=view_plugin_changelog&plugin=' . $value . '&slug=' . $value . '&TB_iframe=true&width=772&height=911' );
					if ( !empty( $version_info->download_link ) ) {
						$table .= sprintf(
							__( 'There is a new version of %1$s available. Download and replace the older version. %2$sGet it Now%3$s', 'wp-travel-engine' ),
							esc_html( $version_info->name ),
							'<a href="' . esc_url(  $version_info->download_link  ) .'">',
							'</a>'
						);
					}

					$table .= '</td></tr>';
					echo '<div class="update-message notice inline notice-warning notice-alt">';
						echo '<p>' . $table . '</p>';
					echo '</div>';
				}
			}
			$i++;
		}
	}
}
if( isset($_GET['page']) && $_GET['page'] == 'wp_travel_engine_license_page' )
{
	add_action('admin_notices','show_update_notification');
}

function verify_ssl() {
	return (bool) apply_filters( 'edd_sl_api_request_verify_ssl', true );
}