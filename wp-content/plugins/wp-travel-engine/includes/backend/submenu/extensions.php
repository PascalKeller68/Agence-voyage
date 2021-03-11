<?php
/**
 * Extentions EDD Fetcah products showcase
 */
// Get addons data from marketplace.
$addons_data = get_transient( 'wp_travel_engine_store_addons_list' );

if ( ! $addons_data ) {
    $addons_data = wp_safe_remote_get( WP_TRAVEL_ENGINE_STORE_URL . '/edd-api/v2/products/?category=add-ons&number=-1' );

    if( is_wp_error( $addons_data ) )
        return;

    $addons_data = wp_remote_retrieve_body( $addons_data );
    set_transient( 'wp_travel_engine_store_addons_list', $addons_data, 48 * HOUR_IN_SECONDS );
}

if ( ! empty( $addons_data ) ) :

    $addons_data = json_decode( $addons_data );
    $addons_data = $addons_data->products;

endif;
?>
<div class="wrap" id="wpte-add-ons">
<?php if ( $addons_data ) : ?>
    <h1 class="wp-heading-inline"><?php _e('Extensions','wp-travel-engine');?></h1>
    <hr class="wp-header-end">
    <br>
    <h2>
        <span>
            <a href="https://wptravelengine.com/downloads/category/add-ons/" class="button-primary" target="_blank"><?php _e('View All Extensions','wp-travel-engine');?></a>
        </span>
    </h2>
    <p><?php _e('These extensions add functionality to your travel booking website.','wp-travel-engine');?></p>
    <div id="tab_container">
        <?php
            foreach ( $addons_data as $key => $product ) :
                $prod_info = $product->info;
        ?>
                <div class="wpte-extension">
                    <div class="inner-wrap">
                        <a href="<?php echo esc_url( $prod_info->link ); ?>" title="<?php echo esc_html( $prod_info->title ); ?>" target="_blank">
                            <img src="<?php echo esc_url( $prod_info->thumbnail ); ?>" class="attachment-showcase wp-post-image" alt="<?php echo esc_html( $prod_info->title ); ?>" title="<?php echo esc_html( $prod_info->title ); ?>">
                            <h3 class="wpte-extension-title"><?php echo esc_html( $prod_info->title ); ?></h3>
                        </a>
                        <p><?php echo esc_html( $prod_info->excerpt ); ?></p>
                        <a href="<?php echo esc_url( $prod_info->link ); ?>" title="<?php echo esc_html( $prod_info->title ); ?>" class="button-secondary" target="_blank"><?php _e('Get the Extension!','wp-travel-engine');?></a>
                    </div>
                </div>
        
        <?php endforeach; ?>

        <div class="clear"></div>
        <div class="wpte-add-ons-footer">
            <a href="https://wptravelengine.com/downloads/category/add-ons/" class="button-primary" target="_blank"><?php _e('View All Extensions','wp-travel-engine');?></a>
        </div>
    </div>
<?php endif; ?>
</div>
