<?php
/**
 * Itinerary Template
 *
 * This template can be overridden by copying it to yourtheme/wp-travel-engine/single-trip/trip-tabs/itinerary-tab.php.
 *
 * @package Wp_Travel_Engine
 * @subpackage Wp_Travel_Engine/includes/templates
 * @since @release-version //TODO: change after travel muni is live
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action( 'wte_before_itinerary_content' );

global $post;
$wp_travel_engine_tabs = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );
?>

<div class="post-data itinerary">
	<?php
		/**
		 * Hook - Display tab content title, left for themes.
		 */
		do_action( 'wte_itinerary_tab_title' );
	?>
	<?php
	$maxlen   = max( array_keys( $wp_travel_engine_tabs['itinerary']['itinerary_title'] ) );
	$arr_keys = array_keys( $wp_travel_engine_tabs['itinerary']['itinerary_title'] );
	foreach ( $arr_keys as $key => $value ) {
		if ( array_key_exists( $value, $wp_travel_engine_tabs['itinerary']['itinerary_title'] ) && ! empty( $value ) ) {
			?>
			<div class="itinerary-row">
				<div class="wte-itinerary-head-wrap">
					<div class="title">
						<?php
						_e( 'Day ', 'wp-travel-engine' );
						echo esc_attr( $value );
						?>
					</div>
				</div>
				<div class="itinerary-content">
					<div class="itinerary-title">
						<?php
						echo ( isset( $wp_travel_engine_tabs['itinerary']['itinerary_title'][ $value ] ) ? esc_attr( $wp_travel_engine_tabs['itinerary']['itinerary_title'][ $value ] ) : '' );
						?>
					</div>
				</div>
				<div class="itinerary-content">
					<div class="content">
						<p>
							<?php
							if ( isset( $wp_travel_engine_tabs['itinerary']['itinerary_content_inner'][ $value ] ) && $wp_travel_engine_tabs['itinerary']['itinerary_content_inner'][ $value ] != '' ) {
								$content_itinerary = wpautop( $wp_travel_engine_tabs['itinerary']['itinerary_content_inner'][ $value ] );
							} else {
								$content_itinerary = wpautop( $wp_travel_engine_tabs['itinerary']['itinerary_content'][ $value ] );
							}
							echo apply_filters( 'the_content', html_entity_decode( $content_itinerary, 3, 'UTF-8' ) );
							?>
						</p>
					</div>
				</div>
			</div>
			<?php
		}
	}
	?>
</div>

<?php
do_action( 'wte_after_itinerary_content' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
