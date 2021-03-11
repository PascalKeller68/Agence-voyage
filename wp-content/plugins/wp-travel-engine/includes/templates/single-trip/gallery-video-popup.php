<?php
/**
 * gallery popup video template.
 * 
 * @package WP_Travel_Engine
 */
wp_enqueue_style( 'magnific-popup' );
wp_enqueue_script( 'magnific-popup' );
wp_enqueue_script( 'wte-video-popup-trigger' );

    if ( $args['title'] ) :
?>
        <h3><?php echo esc_html( $args['title'] ); ?></h3>
<?php 
    endif;

if( ! empty( $args['gallery'] ) ) : 
    $random = rand();
?>
<span class="wp-travel-engine-vid-gal-popup">
    <a data-galtarget="#wte-video-gallary-popup-<?php echo esc_attr( $args['trip_id'] ) . $random ?>" href="#wte-video-gallary-popup-<?php echo esc_attr( $args['trip_id'] ) . $random ?>" class="wte-trip-vidgal-popup-trigger"><?php echo esc_html( $args['label'] ); ?></a>
</span>
<div id="wte-video-gallary-popup-<?php echo esc_attr( $args['trip_id'] ) . $random ?>" class="hidden">
    <?php foreach( $args['gallery'] as $key => $gallery_item ) : 
        $video_id  = $gallery_item['id'];
        $video_url = 'youtube' === $gallery_item['type'] ? 'https://www.youtube.com/watch?v=' . $video_id : 'https://vimeo.com/' . $video_id;
    ?>
        <a href="<?php echo esc_url( $video_url ); ?>"></a>
	<?php endforeach; ?>
</div>
<?php
endif;
