<?php
/**
 * Trip info tab settings / content.
 *
 * @package WP_Travel_Engine
 */
// Get settings.
$wp_travel_engine_settings = get_option( 'wp_travel_engine_settings',true );
?>
<div class="wpte-repeater-wrap">
    <div class="wpte-repeater-heading">
        <div class="wpte-repeater-title"><?php esc_html_e( 'Field Name', 'wp-travel-engine' ) ?></div>
        <div class="wpte-repeater-title"><?php esc_html_e( 'Field Icon', 'wp-travel-engine' ) ?></div>
        <div class="wpte-repeater-title"><?php esc_html_e( 'Field Type', 'wp-travel-engine' ) ?></div>
        <div class="wpte-repeater-title"><?php esc_html_e( 'Field Placeholder', 'wp-travel-engine' ) ?></div>
        <div class="wpte-repeater-title"></div>
    </div>

    <div class="wpte-repeater-block-holder wpte-glb-trp-infos-holdr">
        <?php
            if( isset( $wp_travel_engine_settings['trip_facts'] ) ) {

                // Get vars
                $trip_facts = $wp_travel_engine_settings['trip_facts'];
                $arr_keys   = array_keys( $trip_facts['field_id'] );
                $len        = sizeof( $wp_travel_engine_settings['trip_facts']['field_id'] );
                $i          = 1;

                foreach ( $arr_keys as $key => $value ) {
                    $fact_icon = isset($wp_travel_engine_settings['trip_facts']['field_icon'][$value]) ? esc_attr( $wp_travel_engine_settings['trip_facts']['field_icon'][$value] ): '';
                    ?>
                        <div class="wpte-repeater-block wpte-sortable wpte-glb-trp-infos-row">
                            <div class="wpte-field">
                                <input type="hidden" name="wp_travel_engine_settings[trip_facts][fid][<?php echo $value;?>]" value="<?php echo isset($wp_travel_engine_settings['trip_facts']['fid'][$value]) ? esc_attr( $wp_travel_engine_settings['trip_facts']['fid'][$value] ): '';?>">
                                <input type="text" name="wp_travel_engine_settings[trip_facts][field_id][<?php echo $value;?>]" value="<?php echo isset($wp_travel_engine_settings['trip_facts']['field_id'][$value]) ? esc_attr( $wp_travel_engine_settings['trip_facts']['field_id'][$value] ): '';?>" required>
                            </div>
                            <div class="wpte-icons-holder wpte-floated">
                                <button class="wpte-add-icon"><?php echo ! empty( $fact_icon ) ? esc_html__( 'Update fact icon', 'wp-travel-engine' ) : esc_html__( 'Add fact icon', 'wp-travel-engine' ); ?></button>
                                <span class="wpte-icon-preview">
                                    <span class="wpte-icon-holdr">
                                        <i class="<?php echo ! empty( $fact_icon ) ? esc_attr( $fact_icon ) : ''; ?>"></i>
                                    </span>
                                    <button class="wpte-remove-icn-btn"><?php echo esc_html( 'Remove' ); ?></button>
                                </span>
                                <input class="trip-tabs-icon" type="hidden" name="wp_travel_engine_settings[trip_facts][field_icon][<?php echo $value;?>]" value="<?php echo  $fact_icon ?>">
                            </div>
                            <div class="wpte-trp-inf-fieldtyp wpte-field">
                                <select id="wp_travel_engine_settings[trip_facts][field_type][<?php echo $value;?>]" name="wp_travel_engine_settings[trip_facts][field_type][<?php echo $value;?>]" data-placeholder="<?php esc_attr_e( 'Choose a field type&hellip;', 'wp-travel-engine' ); ?>" class="wc-enhanced-select">
                                        <option value=" "><?php _e( 'Choose input type&hellip;', 'wp-travel-engine' ); ?></option>
                                    <?php
                                        $obj = new Wp_Travel_Engine_Functions();
                                        $fields = $obj->trip_facts_field_options();
                                        $selected_field = esc_attr( $wp_travel_engine_settings['trip_facts']['field_type'][$value] );
                                        foreach ( $fields as $key => $val ) {
                                        echo '<option value="' .( !empty($key)?esc_attr( $key ):"Please select")  . '" ' . selected( $selected_field, $val, false ) . '>' . esc_html( $key ) . '</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="wpte-field">
                                <div class="select-options">
                                    <textarea id="wp_travel_engine_settings[trip_facts][select_options][<?php echo $value;?>]" name="wp_travel_engine_settings[trip_facts][select_options][<?php echo $value;?>]" rows="2" cols="25" required placeholder="<?php _e( 'Enter drop-down values separated by commas','wp-travel-engine' );?>"><?php echo isset( $wp_travel_engine_settings['trip_facts']['select_options'][$value] ) ? esc_attr( $wp_travel_engine_settings['trip_facts']['select_options'][$value] ): '';?></textarea>
                                </div>
                                <div class="input-placeholder">
                                    <input type="text" name="wp_travel_engine_settings[trip_facts][input_placeholder][<?php echo $value;?>]" value="<?php echo isset( $wp_travel_engine_settings['trip_facts']['input_placeholder'][$value] ) ? esc_attr( $wp_travel_engine_settings['trip_facts']['input_placeholder'][$value] ): '';?>">
                                </div>
                            </div>
                            <div class="wpte-system-btns">
                                <button class="wpte-delete wpte-remove-glb-ti"></button>
                            </div>
                        </div>
                    <?php
                }
            }
        ?>
    </div>
</div> <!-- .wpte-repeater-wrap -->
<div class="wpte-add-btn-wrap">
    <button class="wpte-add-btn wpte-add-glb-trp-info"><?php esc_html_e( 'Add trip info', 'wp-travel-engine' ); ?></button>
</div>
<script type="text/html" id="tmpl-wpte-add-trip-info-block">
    <div class="wpte-repeater-block wpte-sortable wpte-glb-trp-infos-row">
        <div class="wpte-field">
            <input type="hidden" name="wp_travel_engine_settings[trip_facts][fid][{{data.key}}]" value="{{data.key}}">
            <input type="text" name="wp_travel_engine_settings[trip_facts][field_id][{{data.key}}]" value="" required>
        </div>
        <div class="wpte-icons-holder wpte-floated">
            <button class="wpte-add-icon"><?php echo esc_html__( 'Add fact icon', 'wp-travel-engine' ); ?></button>
            <span class="wpte-icon-preview">
                <span class="wpte-icon-holdr">
                    <i class=""></i>
                </span>
                <button class="wpte-remove-icn-btn"><?php echo esc_html( 'Remove' ); ?></button>
            </span>
            <input class="trip-tabs-icon" type="hidden" name="wp_travel_engine_settings[trip_facts][field_icon][{{data.key}}]" value="">
        </div>
        <div class="wpte-trp-inf-fieldtyp wpte-field">
            <select id="wp_travel_engine_settings[trip_facts][field_type][{{data.key}}]" name="wp_travel_engine_settings[trip_facts][field_type][{{data.key}}]" data-placeholder="<?php esc_attr_e( 'Choose a field type&hellip;', 'wp-travel-engine' ); ?>" class="wc-enhanced-select">
                    <option value=" "><?php _e( 'Choose input type&hellip;', 'wp-travel-engine' ); ?></option>
                <?php
                    $obj = new Wp_Travel_Engine_Functions();
                    $fields = $obj->trip_facts_field_options();
                    foreach ( $fields as $key => $val ) {
                    echo '<option value="' .( !empty($key)?esc_attr( $key ):"Please select")  . '">' . esc_html( $key ) . '</option>';
                    }
                ?>
            </select>
        </div>
        <div class="wpte-field">
            <div style="display:none" class="select-options">
                <textarea id="wp_travel_engine_settings[trip_facts][select_options][{{data.key}}]" name="wp_travel_engine_settings[trip_facts][select_options][{{data.key}}]" rows="2" cols="25" required placeholder="<?php _e( 'Enter drop-down values separated by commas','wp-travel-engine' );?>"></textarea>
            </div>
            <div class="input-placeholder">
                <input type="text" name="wp_travel_engine_settings[trip_facts][input_placeholder][{{data.key}}]" value="">
            </div>
        </div>
        <div class="wpte-system-btns">
            <button class="wpte-delete wpte-remove-glb-ti"></button>
        </div>
    </div>
</script>
<div>
	<h2 class="wpte-lrf-title"><?php _e( 'Minimum/Maximum Age Icon', 'wp-travel-engine' ); ?></h2>
	<?php
		if (isset($wp_travel_engine_settings['trip_minimum_age_icon']) && !empty($wp_travel_engine_settings['trip_minimum_age_icon'])) {
			$trip_minimum_age_icon = esc_attr( $wp_travel_engine_settings['trip_minimum_age_icon'] );
		}else if (!isset($wp_travel_engine_settings['trip_minimum_age_icon'])) {
			$trip_minimum_age_icon = 'fas fa-child';
		}else{
			$trip_minimum_age_icon = '';
		}

		if (isset($wp_travel_engine_settings['trip_maximum_age_icon']) && !empty($wp_travel_engine_settings['trip_maximum_age_icon'])) {
			$trip_maximum_age_icon = esc_attr( $wp_travel_engine_settings['trip_maximum_age_icon'] );
		}else if (!isset($wp_travel_engine_settings['trip_maximum_age_icon'])) {
			$trip_maximum_age_icon = 'fas fa-male';
		}else{
			$trip_maximum_age_icon = '';
		}
	?>
	<div class="wpte-field wpte-icons-holder wpte-floated">
		<label class="wpte-field-label" for="wp_travel_engine_settings[trip_minimum_age_icon]"><?php _e('Minimum Age Icon','wp-travel-engine');?></label>
		<button class="wpte-add-icon"><?php echo esc_html__( 'Minimum Age Icon', 'wp-travel-engine' ); ?></button>
		<span class="wpte-icon-preview">
            <span class="wpte-icon-holdr">
				<i class="<?php echo $trip_minimum_age_icon;?>"></i>
            </span>
            <button class="wpte-remove-icn-btn"><?php echo esc_html( 'Remove' ); ?></button>
		</span>
		<input type="hidden" class="trip-tabs-icon" name="wp_travel_engine_settings[trip_minimum_age_icon]" value="<?php echo $trip_maximum_age_icon;?>">
	</div>
	<div class="wpte-field wpte-icons-holder wpte-floated">
		<label class="wpte-field-label" for="wp_travel_engine_settings[trip_maximum_age_icon]"><?php _e('Maximum Age Icon', 'wp-travel-engine');?></label>
		<button class="wpte-add-icon"><?php echo esc_html__( 'Maximum Age Icon', 'wp-travel-engine' ); ?></button>
		<span class="wpte-icon-preview">
			<span class="wpte-icon-holdr">
				<i class="<?php echo $trip_maximum_age_icon;?>"></i>
            </span>
            <button class="wpte-remove-icn-btn"><?php echo esc_html( 'Remove' ); ?></button>
		</span>
		<input type="hidden" class="trip-tabs-icon" name="wp_travel_engine_settings[trip_maximum_age_icon]" value="<?php echo $trip_maximum_age_icon;?>">
	</div>
</div>
<?php
