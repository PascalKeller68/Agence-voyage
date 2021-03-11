<?php
/**
 * Settings section of the plugin.
 *
 * Maintain a list of functions that are used for settings purposes of the plugin
 *
 * @package    WP Travel Engine
 * @subpackage WP_Travel_Engine/includes
 * @author    code wing
 */
class Wp_Travel_Engine_Settings {

	/**
	 * Settings Tabs.
	 *
	 * @since    1.0.0
	 */
	public function get_global_settings_tabs() {
		// Global Tabs.
		$global_tabs = array(
			'wpte-general'       => array(
				'label'    => __( 'General', 'wp-travel-engine' ),
				'sub_tabs' => array(
					'page_settings' => array(
						'label'        => __( 'Page Settings', 'wp-travel-engine' ),
						'content_path' => plugin_dir_path( __FILE__ ) . 'backend/settings/general/page-settings.php',
						'current'      => true,
					),
					'trip_tabs'     => array(
						'label'        => __( 'Trip Tabs Settings', 'wp-travel-engine' ),
						'content_path' => plugin_dir_path( __FILE__ ) . 'backend/settings/general/trip-tabs.php',
						'current'      => false,
					),
					'trip_info'     => array(
						'label'        => __( 'Trip Info', 'wp-travel-engine' ),
						'content_path' => plugin_dir_path( __FILE__ ) . 'backend/settings/general/trip-info.php',
						'current'      => false,
					),
				),
				'current'  => true,
			),
			'wpte-emails'        => array(
				'label'    => __( 'Emails', 'wp-travel-engine' ),
				'sub_tabs' => array(
					'booking_notifications' => array(
						'label'        => __( 'Admin Email Templates', 'wp-travel-engine' ),
						'content_path' => plugin_dir_path( __FILE__ ) . 'backend/settings/emails/booking-notifications.php',
						'current'      => false,
					),
					'purchase_receipt'      => array(
						'label'        => __( 'Customer Email Templates', 'wp-travel-engine' ),
						'content_path' => plugin_dir_path( __FILE__ ) . 'backend/settings/emails/purchase-receipt.php',
						'current'      => true,
					),
				),
			),
			'wpte-miscellaneous' => array(
				'label'    => __( 'Miscellaneous', 'wp-travel-engine' ),
				'sub_tabs' => array(
					'currency'  => array(
						'label'        => __( 'Currency Settings', 'wp-travel-engine' ),
						'content_path' => plugin_dir_path( __FILE__ ) . 'backend/settings/misc/currency.php',
						'current'      => true,
					),
					'show-hide' => array(
						'label'        => __( 'Display Settings', 'wp-travel-engine' ),
						'content_path' => plugin_dir_path( __FILE__ ) . 'backend/settings/misc/show-hide.php',
						'current'      => true,
					),
					'misc'      => array(
						'label'        => __( 'Miscellaneous Settings', 'wp-travel-engine' ),
						'content_path' => plugin_dir_path( __FILE__ ) . 'backend/settings/misc/miscellaneous.php',
						'current'      => true,
					),
				),
			),
			'wpte-payment'       => array(
				'label'    => __( 'Payments', 'wp-travel-engine' ),
				'sub_tabs' => array(
					'payment-general' => array(
						'label'        => __( 'Payment General Settings', 'wp-travel-engine' ),
						'content_path' => plugin_dir_path( __FILE__ ) . 'backend/settings/payments/general.php',
						'current'      => true,
					),
					'paypal-standard' => array(
						'label'        => __( 'PayPal Standard', 'wp-travel-engine' ),
						'content_path' => plugin_dir_path( __FILE__ ) . 'backend/settings/payments/paypal-standard.php',
						'current'      => false,
					),
					'bacs-payment'    => array(
						'label'        => __( 'Direct bank transfer', 'wp-travel-engine' ),
						'content_path' => plugin_dir_path( __FILE__ ) . 'backend/settings/payments/bacs-payment.php',
						'current'      => false,
					),
					'check-payment'   => array(
						'label'        => __( 'Check Payments', 'wp-travel-engine' ),
						'content_path' => plugin_dir_path( __FILE__ ) . 'backend/settings/payments/check-payment.php',
						'current'      => false,
					),
				),
			),
			'wpte-dashboard'     => array(
				'label'    => __( 'Dashboard', 'wp-travel-engine' ),
				'sub_tabs' => array(
					'user-dashboard' => array(
						'label'        => __( 'User Dashboard Settings', 'wp-travel-engine' ),
						'content_path' => plugin_dir_path( __FILE__ ) . 'backend/settings/dashboard/general.php',
						'current'      => true,
					),
				),
			),
			'wpte-extensions'    => array(
				'label'    => __( 'Extensions', 'wp-travel-engine' ),
				'sub_tabs' => $this->get_extensions_tabs(),
			),
		);

		return apply_filters( 'wpte_settings_get_global_tabs', $global_tabs );
	}


	/**
	 * Settings panel of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function wp_travel_engine_backend_settings() {
		$wte_global_settings_tabs = $this->get_global_settings_tabs();
		wp_enqueue_script( 'magnific-popup' );
		wp_enqueue_style( 'magnific-popup' );
		?>
		<div class="wpte-main-wrap wpte-settings">
			<header class="wpte-header">
				<div class="wpte-left-block">
					<h1 class="wpte-plugin-title">
						<svg xmlns="http://www.w3.org/2000/svg" fill="#1A84EE" viewBox="0 0 576 512"><path d="M288 0c-69.59 0-126 56.41-126 126 0 56.26 82.35 158.8 113.9 196.02 6.39 7.54 17.82 7.54 24.2 0C331.65 284.8 414 182.26 414 126 414 56.41 357.59 0 288 0zM20.12 215.95A32.006 32.006 0 0 0 0 245.66v250.32c0 11.32 11.43 19.06 21.94 14.86L160 448V214.92c-8.84-15.98-16.07-31.54-21.25-46.42L20.12 215.95zM288 359.67c-14.07 0-27.38-6.18-36.51-16.96-19.66-23.2-40.57-49.62-59.49-76.72v182l192 64V266c-18.92 27.09-39.82 53.52-59.49 76.72-9.13 10.77-22.44 16.95-36.51 16.95zm266.06-198.51L416 224v288l139.88-55.95A31.996 31.996 0 0 0 576 426.34V176.02c0-11.32-11.43-19.06-21.94-14.86z"/></svg>
						<?php esc_html_e( 'WP Travel Engine', 'wp-travel-engine' ); ?>
					</h1>
					<span class="wpte-page-name"><?php esc_html_e( 'Settings', 'wp-travel-engine' ); ?></span>
				</div>
			</header><!-- .wpte-header -->

			<div class="wpte-tab-main wpte-horizontal-tab">
				<?php if ( ! empty( $wte_global_settings_tabs ) ) : ?>
					<div class="wpte-tab-wrap">
						<?php
						foreach ( $wte_global_settings_tabs as $key => $tab ) :
							$class_names = '';
							if ( in_array( $key, array( 'wpte-emails', 'wpte-miscellaneous' ), ! 0 ) ) {
								$class_names = 'wte_note_430';
							}
							?>
							<a href="javascript:void(0);" data-wte-update="<?php echo esc_attr( $class_names ); ?>" data-content-key="<?php echo esc_attr( $key ); ?>" data-tab-data="<?php echo esc_attr( json_encode( $tab ) ); ?>" class="wpte-tab <?php echo esc_attr( $key ); ?> <?php echo isset( $tab['current'] ) ? 'current content_loaded' : ''; ?> wpte_load_global_settings_tab"><?php echo esc_html( $tab['label'] ); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<div class="wpte-tab-content-wrap wpte-global-settings-tbswrp">
					<?php
					foreach ( $wte_global_settings_tabs as $key => $tab ) :
						if ( ! isset( $tab['current'] ) || ! $tab['current'] ) {
							continue;
						}
						?>
						<div class="wpte-tab-content <?php echo esc_attr( $key ); ?>-content <?php echo isset( $tab['current'] ) ? 'current content_loaded' : ''; ?> wpte-global-settngstab">
							<div class="wpte-block-content">
								<?php
									$sub_tabs = isset( $tab['sub_tabs'] ) && ! empty( $tab['sub_tabs'] ) ? $tab['sub_tabs'] : array();

								if ( ! empty( $sub_tabs ) ) :
									?>
										<div class="wpte-tab-sub wpte-horizontal-tab">
											<div class="wpte-tab-wrap">
										<?php
											$current = 1;
										foreach ( $sub_tabs as $key => $tab ) :
											?>
												<a href="javascript:void(0);" class="wpte-tab <?php echo esc_attr( $key ); ?> <?php echo 1 === $current ? 'current' : ''; ?>"><?php echo esc_html( $tab['label'] ); ?></a>
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
									<input data-tab="<?php echo esc_attr( $key ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'wpte-global-setting-save' ) ); ?>" class="wpte-save-global-settings" type="submit" name="wpte_save_global_settings" value="<?php esc_attr_e( 'Save & Continue', 'wp-travel-engine' ); ?>">
								</div>
							</div> <!-- .wpte-block-content -->
						</div>
					<?php endforeach; ?>
				</div> <!-- .wpte-tab-content-wrap -->
				<div style="display:none;" class="wpte-loading-anim"></div>
			</div> <!-- .wpte-tab-main -->
		</div><!-- .wpte-main-wrap -->
		<?php
	}

	/**
	 * Get extensions settings tabs
	 */
	public function get_extensions_tabs() {
		return apply_filters( 'wpte_get_global_extensions_tab', array() );
	}
}
