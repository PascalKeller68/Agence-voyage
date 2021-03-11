<?php
/**
 * Admin overview Tab content - Trip Meta
 * 
 * @package Wp_Travel_Engine/Admin/Meta_parts
 */
global $post;
// Get post ID.
$tab_key = isset( $tab['tab_key'] ) ? $tab['tab_key'] : '';

if ( ! is_object( $post ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
    $post_id  = $_POST['post_id'];
    $next_tab = $_POST['next_tab'];
    $tab_key  = $_POST['tab_details']['tab_key'];
} else {
    $post_id = $post->ID;
}
// Get settings meta.
$wp_travel_engine_setting = get_post_meta( $post_id, 'wp_travel_engine_setting', true );

$tab_content = isset( $wp_travel_engine_setting['tab_content'][$tab_key . '_wpeditor'] ) ? $wp_travel_engine_setting['tab_content'][$tab_key . '_wpeditor'] : '';

$tab_title = isset( $wp_travel_engine_setting['tab_' . $tab_key . '_title'] ) ? $wp_travel_engine_setting['tab_' . $tab_key . '_title'] : '';

?>
    <div class="wpte-form-block-wrap">

        <div class="wpte-form-block">
            <div class="wpte-title-wrap">
                <h2 class="wpte-title"><?php _e( 'Tab Content', 'wp-travel-engine' ); ?></h2>
            </div>
            <div class="wpte-form-content">
                <div class="wpte-field wpte-text wpte-floated">
                    <label class="wpte-field-label"><?php _e( 'Section Title', 'wp-travel-engine' ); ?></label>
                    <input type="text" name="wp_travel_engine_setting[tab_<?php echo esc_attr( $tab_key ) ?>_title]" value="<?php echo esc_attr( $tab_title ); ?>" placeholder="<?php esc_attr_e( 'Enter Here', 'wp-travel-engine' ); ?>">
                    <span class="wpte-tooltip"><?php _e( 'Enter the tab section title', 'wp-travel-engine' ); ?></span>
                </div>
                
                <div class="wpte-field wpte-textarea wpte-floated wpte-rich-textarea delay">
                <label for="wp_travel_engine_setting_tab_<?php echo esc_attr( $tab_key ) ?>_wpeditor" class="wpte-field-label"><?php _e( 'Tab Content', 'wp-travel-engine' ); ?></label>
                        <textarea 
                            placeholder="<?php _e('Tab Content', 'wp-travel-engine'); ?>"
                            name="wp_travel_engine_setting[tab_content][<?php echo esc_attr( $tab_key ) ?>_wpeditor]"
                            class="wte-editor-area wp-editor-area" id="wp_travel_engine_setting_tab_<?php echo esc_attr( $tab_key ) ?>_wpeditor"><?php echo wp_kses_post( $tab_content ); ?></textarea>
                        <span class="wpte-tooltip"><?php _e( 'Enter the content for the custom tab section.', 'wp-travel-engine' ) ?></span>
                    </div>
            </div>
        </div> <!-- .wpte-form-block -->
    </div> <!-- .wpte-form-block-wrap -->

    <?php
    if ( $next_tab ) : ?>
        <div class="wpte-field wpte-submit">
            <input data-tab="overview" data-post-id="<?php echo esc_attr( $post_id ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'wpte-trip-tab-save-continue' ) ); ?>" data-next-tab="<?php echo esc_attr( $next_tab['callback_function'] ); ?>" class="wpte_save_continue_link" type="submit" name="wpte_trip_tabs_save_continue" value="<?php _e( 'Save &amp; Continue', 'wp-travel-engine' ); ?>">
        </div>
    <?php endif;