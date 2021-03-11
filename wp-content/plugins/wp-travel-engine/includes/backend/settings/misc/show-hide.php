<?php
/**
 * Show hide options.
 */
$wp_travel_engine_settings = get_option( 'wp_travel_engine_settings', true );
$feat_img                  = isset( $wp_travel_engine_settings['feat_img'] ) ? esc_attr( $wp_travel_engine_settings['feat_img'] ) : '0';
$show_trip_facts_sidebar   = isset( $wp_travel_engine_settings['show_trip_facts_sidebar'] ) && $wp_travel_engine_settings['show_trip_facts_sidebar'] != '' ? $wp_travel_engine_settings['show_trip_facts_sidebar'] : 'yes';
$hide_traveller_info       = isset( $wp_travel_engine_settings['travelers_information'] ) ? $wp_travel_engine_settings['travelers_information'] : 'yes';
?>
<div class="wpte-form-block-wrap">
	<div class="wpte-form-block">
		<div class="wpte-form-content">
			<div class="wpte-field wpte-checkbox advance-checkbox">
				<label class="wpte-field-label" for="wp_travel_engine_settings[booking]"><?php esc_html_e( 'Hide Booking Form', 'wp-travel-engine' ); ?></label>
				<div class="wpte-checkbox-wrap">
					<input type="checkbox" id="wp_travel_engine_settings[booking]" class="hide-booking" name="wp_travel_engine_settings[booking]" value="1"
					<?php
					if ( isset( $wp_travel_engine_settings['booking'] ) && $wp_travel_engine_settings['booking'] != '' ) {
						echo 'checked';}
					?>
					>
					<label for="wp_travel_engine_settings[booking]"></label>
				</div>
				<span class="wpte-tooltip"><?php esc_html_e( 'If checked, booking form in the trip detail page will be disabled.', 'wp-travel-engine' ); ?></span>
			</div>

			<div class="wpte-field wpte-checkbox advance-checkbox">
				<label class="wpte-field-label" for="wp_travel_engine_settings[enquiry]"><?php esc_html_e( 'Hide Enquiry Form', 'wp-travel-engine' ); ?></label>
				<div class="wpte-checkbox-wrap">
					<input type="checkbox" id="wp_travel_engine_settings[enquiry]" class="hide-enquiry" name="wp_travel_engine_settings[enquiry]" value="1"
					<?php
					if ( isset( $wp_travel_engine_settings['enquiry'] ) && $wp_travel_engine_settings['enquiry'] != '' ) {
						echo 'checked';}
					?>
					>
					<label for="wp_travel_engine_settings[enquiry]"></label>
				</div>
				<span class="wpte-tooltip"><?php esc_html_e( 'If checked, enquiry form in the trip detail page will be disabled.', 'wp-travel-engine' ); ?></span>
			</div>
			<?php
			// SHow form editor upsell.
			$form_editor_enabled = apply_filters( 'wpte_is_form_editor_addon_active', false );

			if ( ! $form_editor_enabled ) :
				?>
				<div style="margin-bottom: 40px;" class="wpte-info-block">
						<b><?php _e( 'Note:', 'wp-travel-engine' ); ?></b>
						<p>
							<?php
								echo sprintf( __( 'Need to edit the booking, enquiry and traveller information form as per your requirements? You can use Form Editor extension to edit fields and add your own. %1$sGet Form Editor extension now%2$s.', 'wp-travel-engine' ), '<a target="_blank" href="https://wptravelengine.com/downloads/form-editor/?utm_source=setting&utm_medium=customer_site&utm_campaign=setting_addon">', '</a>' );
							?>
						</p>
				</div>
			<?php endif; ?>
			<div class="wpte-field wpte-checkbox advance-checkbox">
				<label class="wpte-field-label" for="wp_travel_engine_settings[emergency]"><?php esc_html_e( 'Hide Emergency Contact Details', 'wp-travel-engine' ); ?></label>
				<div class="wpte-checkbox-wrap">
					<input type="checkbox" id="wp_travel_engine_settings[emergency]" class="hide-emergency" name="wp_travel_engine_settings[emergency]" value="1"
					<?php
					if ( isset( $wp_travel_engine_settings['emergency'] ) && $wp_travel_engine_settings['emergency'] != '' ) {
						echo 'checked';}
					?>
					>
					<label for="wp_travel_engine_settings[emergency]"></label>
				</div>
				<span class="wpte-tooltip"><?php esc_html_e( 'If checked, Emergency Contact Details of the travelers will be disabled from the Travelers Information Form.', 'wp-travel-engine' ); ?></span>
			</div>

			<div class="wpte-field wpte-checkbox advance-checkbox">
				<label class="wpte-field-label" for="wp_travel_engine_settings[feat_img]"><?php _e( 'Hide Trip Featured Image', 'wp-travel-engine' ); ?></label>
				<div class="wpte-checkbox-wrap">
					<input type="checkbox" id="wp_travel_engine_settings[feat_img]" name="wp_travel_engine_settings[feat_img]" value="1" <?php echo checked( '1', $feat_img ); ?>>
					<label for="wp_travel_engine_settings[feat_img]"></label>
				</div>
				<span class="wpte-tooltip"><?php esc_html_e( 'If checked, featured image in the trip detail page will be disabled.', 'wp-travel-engine' ); ?></span>
			</div>

			<div class="wpte-field wpte-checkbox advance-checkbox">
				<label class="wpte-field-label" for="wp_travel_engine_settings[travelers_information]"><?php _e( 'Hide Travelers Information', 'wp-travel-engine' ); ?></label>
				<div class="wpte-checkbox-wrap">
					<input type="hidden" name="wp_travel_engine_settings[travelers_information]" value="no">
					<input type="checkbox" id="wp_travel_engine_settings[travelers_information]" name="wp_travel_engine_settings[travelers_information]" value="yes" <?php checked( $hide_traveller_info, 'yes' ); ?>>
					<label for="wp_travel_engine_settings[travelers_information]"></label>
				</div>
				<span class="wpte-tooltip"><?php esc_html_e( 'If checked, information of all the travelers will be optional. After checkout, information of each of the travelers will not be asked to fill up.', 'wp-travel-engine' ); ?></span>
			</div>

			<div class="wpte-field wpte-checkbox advance-checkbox">
				<label class="wpte-field-label" for="wp_travel_engine_settings[tax_images]"><?php _e( 'Show Taxonomy Image', 'wp-travel-engine' ); ?></label>
				<div class="wpte-checkbox-wrap">
					<input type="checkbox" id="wp_travel_engine_settings[tax_images]" name="wp_travel_engine_settings[tax_images]" value="1"
					<?php
					if ( isset( $wp_travel_engine_settings['tax_images'] ) && $wp_travel_engine_settings['tax_images'] != '' ) {
						echo 'checked';}
					?>
					>
					<label for="wp_travel_engine_settings[tax_images]"></label>
				</div>
				<span class="wpte-tooltip"><?php esc_html_e( 'Enable to show taxonomy image in the taxonomy page.', 'wp-travel-engine' ); ?></span>
			</div>

			<?php
				/**
				 * Do action for related posts.
				 */
				do_action( 'wp_travel_engine_settings_related_posts' );
			?>
			<div class="wpte-field wpte-checkbox advance-checkbox">
				<label class="wpte-field-label" for="wp_travel_engine_settings[show_multiple_pricing_list_disp]"><?php _e( 'Display Multiple Prices List', 'wp-travel-engine' ); ?></label>
				<div class="wpte-checkbox-wrap">
					<input type="checkbox" id="wp_travel_engine_settings[show_multiple_pricing_list_disp]" name="wp_travel_engine_settings[show_multiple_pricing_list_disp]" value="1"
					<?php
					if ( isset( $wp_travel_engine_settings['show_multiple_pricing_list_disp'] ) && $wp_travel_engine_settings['show_multiple_pricing_list_disp'] != '' ) {
						echo 'checked';}
					?>
					>
					<label for="wp_travel_engine_settings[show_multiple_pricing_list_disp]"></label>
				</div>
				<span class="wpte-tooltip"><?php esc_html_e( 'If checked, multiple pricing options prices will be displayed on the trip page above the booking date selection area, if unchecked, Genereal or Adult prices will only be shown.', 'wp-travel-engine' ); ?></span>
			</div>

			<div class="wpte-field wpte-checkbox advance-checkbox">
				<label class="wpte-field-label" for="wp_travel_engine_settings[show_excerpt]"><?php _e( 'Show Excerpt', 'wp-travel-engine' ); ?></label>
				<div class="wpte-checkbox-wrap">
					<input type="checkbox" id="wp_travel_engine_settings[show_excerpt]" name="wp_travel_engine_settings[show_excerpt]" value="1"
					<?php
					if ( isset( $wp_travel_engine_settings['show_excerpt'] ) && $wp_travel_engine_settings['show_excerpt'] != '' ) {
						echo 'checked';}
					?>
					>
					<label for="wp_travel_engine_settings[show_excerpt]" class="checkbox-label"></label>
				</div>
				<span class="wpte-tooltip"><?php esc_html_e( 'Enable to display trip excerpt in the taxonomy pages.', 'wp-travel-engine' ); ?></span>
			</div>

			<div class="wpte-field wpte-checkbox advance-checkbox">
				<label class="wpte-field-label" for="wp_travel_engine_settings[show_trip_facts_sidebar]"><?php _e( 'Show Trip Infos on Sidebar', 'wp-travel-engine' ); ?></label>
				<div class="wpte-checkbox-wrap">
					<input type="hidden" name="wp_travel_engine_settings[show_trip_facts_sidebar]" value="no" >
					<input type="checkbox" id="wp_travel_engine_settings[show_trip_facts_sidebar]" name="wp_travel_engine_settings[show_trip_facts_sidebar]" value="yes" <?php checked( $show_trip_facts_sidebar, 'yes' ); ?>>
					<label for="wp_travel_engine_settings[show_trip_facts_sidebar]" class="checkbox-label"></label>
				</div>
				<span class="wpte-tooltip"><?php esc_html_e( 'Check to display the trip info section in the trip single sidebar.', 'wp-travel-engine' ); ?></span>
			</div>

			<div class="wpte-field wpte-checkbox advance-checkbox">
				<label class="wpte-field-label" for="wp_travel_engine_settings[hide_powered_by]"><?php esc_html_e( 'Hide Powered By Link', 'wp-travel-engine' ); ?></label>
				<div class="wpte-checkbox-wrap">
					<input type="hidden" name="wp_travel_engine_settings[hide_powered_by]" value="no" >
					<input type="checkbox" id="wp_travel_engine_settings[hide_powered_by]" class="hide-powered-by" value="yes" name="wp_travel_engine_settings[hide_powered_by]"
                    <?php
                    $hide_powered_by = isset( $wp_travel_engine_settings['hide_powered_by'] ) && 'yes' === $wp_travel_engine_settings['hide_powered_by'];
					echo $hide_powered_by ? 'checked="checked"' : '';
					?>
					>
					<label for="wp_travel_engine_settings[hide_powered_by]"></label>
				</div>
				<span class="wpte-tooltip"><?php esc_html_e( 'If checked, Powered by Link will be hidden from enquiry and booking emails.', 'wp-travel-engine' ); ?></span>
			</div>

			<div class="wpte-field wpte-checkbox advance-checkbox">
				<label class="wpte-field-label" for="wp_travel_engine_settings[disable_fa_icons_js]"><?php esc_html_e( 'Disable FontAwesome Icons JS', 'wp-travel-engine' ); ?></label>
				<div class="wpte-checkbox-wrap">
					<input type="hidden" name="wp_travel_engine_settings[disable_fa_icons_js]" value="no" >
					<input type="checkbox" id="wp_travel_engine_settings[disable_fa_icons_js]" class="hide-powered-by" value="yes" name="wp_travel_engine_settings[disable_fa_icons_js]"
                    <?php
                    $disable_fa_icons_js = isset( $wp_travel_engine_settings['disable_fa_icons_js'] ) && 'yes' === $wp_travel_engine_settings['disable_fa_icons_js'];
					echo $disable_fa_icons_js ? 'checked="checked"' : '';
					?>
					>
					<label for="wp_travel_engine_settings[disable_fa_icons_js]"></label>
				</div>
				<span class="wpte-tooltip"><?php esc_html_e( 'If checked, FontAwesome icons JS SVG replacement will be disabled. Check this if you are having icon conflicts ( fontawesome icons not shown ) with other plugins.', 'wp-travel-engine' ); ?></span>
			</div>

		</div>
	</div>
</div>
<?php
