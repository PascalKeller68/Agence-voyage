<?php
/**
 * Dates upsell
 */
global $post;
// Get post ID.
if ( ! is_object( $post ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
    $post_id  = $_POST['post_id'];
    $next_tab = $_POST['next_tab']; 
} else {
    $post_id = $post->ID;
}
?>
<div class="wpte-form-block">
    <div class="wpte-title-wrap">
        <h2 class="wpte-title"><?php _e( 'Fixed Departure Dates', 'wp-travel-engine' ); ?></h2>
    </div> <!-- .wpte-title-wrap -->
    <div class="wpte-info-block">
        <p>
            <?php
                echo sprintf( __( 'By default, this trip can be booked throughout the year. Do you have trips with fixed departure dates and want them booked only on these days? Trip Fixed Starting Dates extension allows you to set specific dates when the trips can be booked. %1$sGet Trip Fixed Starting Dates extension now%2$s.', 'wp-travel-engine' ), '<a target="_blank" href="https://wptravelengine.com/downloads/trip-fixed-starting-dates/?utm_source=setting&utm_medium=customer_site&utm_campaign=setting_addon">', '</a>' );
            ?>
        </p>
    </div>
</div>

<?php
if ( $next_tab ) : ?>
    <div class="wpte-field wpte-submit">
        <input data-tab="overview" data-post-id="<?php echo esc_attr( $post_id ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'wpte-trip-tab-save-continue' ) ); ?>" data-next-tab="<?php echo esc_attr( $next_tab['callback_function'] ); ?>" class="wpte_save_continue_link" type="submit" name="wpte_trip_tabs_save_continue" value="<?php _e( 'Continue', 'wp-travel-engine' ); ?>">
    </div>
<?php endif;