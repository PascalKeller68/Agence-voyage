<?php
/**
 * Plugin License page.
 */
$wp_travel_engine = get_option( 'wp_travel_engine_license' );
$addon_name = apply_filters( 'wp_travel_engine_addons', array() );
?>
<div class="wpte-main-wrap wte-license-key">
    <div class="wpte-tab-sub wpte-horizontal-tab">
        <form method="post" action="options.php">
            <?php wp_nonce_field( 'wp_travel_engine_license_nonce', 'wp_travel_engine_license_nonce' ); ?>

            <?php settings_fields('wp_travel_engine_license'); ?>
            <div class="wpte-tab-wrap">
                <a href="javascript:void(0);" class="wpte-tab wte-addons current"><?php esc_html_e( 'WP Travel Engine Addons', 'wp-travel-engine' ); ?></a>
            </div>

            <div class="wpte-tab-content-wrap">
                <div class="wpte-tab-content wte-addons-content current">
                    <div class="wpte-title-wrap">
                        <h2 class="wpte-title"><?php esc_html_e( 'License Keys', 'wp-travel-engine' ); ?></h2>
                        <div class="settings-note">
                            <?php esc_html_e( 'All of the premium addon installed and activated on your website has been listed below. You can add/edit and manage your License keys for each addon individually.', 'wp-travel-engine' ) ?>
                        </div>
                    </div> <!-- .wpte-title-wrap -->

                    <div class="wpte-block-content">
                        <input type="hidden" name="addon_name" class="addon_name" type="text" value="" />
                    <?php 
                        if ( sizeof( $addon_name ) == 0 ) {
                            echo '<h3 class="active-msg" style="color:#CA4A1F;">'.__('Premium Extensions not Found!', 'wp-travel-engine').'</h3>';
                        }

                        foreach ($addon_name as $key => $value) {
                            $wte_fixed_departure_license = isset($wp_travel_engine[$value.'_license_key']) ? esc_attr($wp_travel_engine[$value.'_license_key']):false;
                            $wte_fixed_departure_status  = isset( $wp_travel_engine[$value.'_license_status'] ) ? esc_attr( $wp_travel_engine[$value.'_license_status'] ):false;

                            $active_class = $wte_fixed_departure_status == 'valid' ? 'wte-license-activate' : '';

                            $addon_name_expld = explode( ' - ', $key );

                            $addon_real_name = isset( $addon_name_expld['1'] ) && ! empty( $addon_name_expld['1'] ) ? $addon_name_expld['1'] : $key;

                            $activation_message = sprintf( __( 'Enter your license key for %1$s addon.', 'wp-travel-engine' ), $addon_real_name );
                            $msg_color = '';

                            if ( $wte_fixed_departure_license && $wte_fixed_departure_status == 'valid' ) {
                                $activation_message = sprintf( __( 'Your license key for %1$s addon is activated on this site.', 'wp-travel-engine' ), $addon_real_name );
                                $msg_color = 'style="color:#11b411"';
                            } elseif(  $wte_fixed_departure_license && $wte_fixed_departure_status != 'valid' ) {
                                $activation_message = isset( $_GET['wte_license_error_msg'] ) && $_GET['wte_addon_name'] === $value ? $_GET['wte_license_error_msg'] : sprintf( __( 'Your license key for %1$s addon is not activated on this site yet. Please activate.', 'wp-travel-engine' ), $addon_real_name );
                                $msg_color = 'style="color:#f66757"';
                            }
                        ?>

                            <div class="wpte-floated <?php echo esc_attr( $active_class ); ?>">
                                <label for="wp_travel_engine_license[<?php echo $value;?>_license_key]" class="wpte-field-label"><?php echo esc_html( $addon_real_name ); ?></label>
                                <div class="wpte-field wpte-password">
                                    <input id="<?php echo $value;?>" class="wp_travel_engine_addon_license_key" name="wp_travel_engine_license[<?php echo $value;?>_license_key]" type="text" class="regular-text" value="<?php echo  $wte_fixed_departure_license; ?>" />
                                    <span <?php echo $msg_color; ?> class="wpte-tooltip"><?php echo esc_html( $activation_message ); ?></span>
                                    <?php if( 'valid' == $wte_fixed_departure_status ) : ?>
                                        <span class="wte-license-active">
                                            <i class="fas fa-check"></i>
                                        <?php esc_html_e( 'Activated', 'wp-travel-engine' ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if( $wte_fixed_departure_license ) { ?>
                                    <div class="wpte-btn-wrap">
                                    <?php if( $wte_fixed_departure_status == 'valid' ) { ?>
                                        <input type="submit" class="wpte-btn wpte-btn-deactive deactivate-license" data-id="<?php echo $value; ?>" name="edd_license_deactivate" value="<?php echo 'Deactivate License'; ?>"/>
                                    <?php } else {  ?>
                                        <input type="submit" class="wpte-btn wpte-btn-active activate-license" data-id="<?php echo $value; ?>" name="edd_license_activate" value="<?php echo 'Activate License'; ?>"/>
                                    <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php 	
                if ( sizeof( $addon_name ) != 0 ) {
                        ?>
                            <div class="wpte-field wpte-submit">
								<input id="submit" type="submit" name="submit" value="<?php echo esc_attr__( 'Save Changes', 'wp-travel-engine' ); ?>">
							</div>
                        <?php
                } else{
                    echo '<a target="_blank" href="https://wptravelengine.com/downloads/category/add-ons/" class="button button-primary">'.__('Get Now','wp-travel-engine').'</a>';
                }
            ?>
        </form>
    </div>
</div><!-- .wpte-main-wrap -->
<?php 
echo 
"<script>
( function( $ ){
    $('body').on('click', '.activate-license, .deactivate-license', function (e){
        var val = $(this).attr('data-id');
        $('.addon_name').attr('value',val);
    });
}( jQuery ) );
</script>";
