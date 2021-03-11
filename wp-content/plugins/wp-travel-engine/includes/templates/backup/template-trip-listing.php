<?php
   /**
    * The template for displaying trips trip listing page
    *
    * @package Wp_Travel_Engine
    * @subpackage Wp_Travel_Engine/includes/templates
    * @since 1.0.0
    */
   	get_header(); ?>
    <div id="wte-crumbs">
        <?php
        do_action('wp_travel_engine_breadcrumb_holder');
        ?>
    </div>
    <?php
    $wte_trip_tax_post_args = array(
        'post_type'      => 'trip',
        'posts_per_page' => -1,
        'order'          => apply_filters('wpte_trip_listing_order','DESC'),
        'orderby'        => apply_filters('wpte_trip_listing_order_by','date'),
    );

    $options = get_option( 'wp_travel_engine_settings', array() );
    if( isset( $options['reorder']['flag'] ) ){
        $wte_trip_tax_post_args['order'] = 'ASC';
        $wte_trip_tax_post_args['orderby'] = 'menu_order';
    }
    
    $wte_trip_tax_post_qry = new WP_Query($wte_trip_tax_post_args);
    global $post;
    $obj  = new Wp_Travel_Engine_Functions();
    if($wte_trip_tax_post_qry->have_posts()) : ?>
    <div class="archive">
        <div id="wp-travel-trip-wrapper" class="trip-content-area" itemscope itemtype="http://schema.org/ItemList">
            <div class="wp-travel-inner-wrapper">
                <div class="wp-travel-engine-archive-outer-wrap">
                    <div class="page-header">
                        <!-- <h1 class="page-title"><?php the_title(); ?></h1> -->
                        <div class="page-feat-image">
                            <?php
                            $image_id = get_post_thumbnail_id( $post->ID );
                            $activities_banner_size = apply_filters('wp_travel_engine_template_banner_size', 'full');
                            echo wp_get_attachment_image ( $image_id, $activities_banner_size );
                            ?> 
                        </div>
                        <div class="page-content">
                            <p>
                                <?php  
                                $content = apply_filters('the_content', $post->post_content); 
                                echo $content;?>
                            </p>
                        </div>
                    </div>
                    <div class="category-main-wrap category-grid col-3 grid">
                        <?php
                        $j = 1;
                        while($wte_trip_tax_post_qry->have_posts()) :
                            $wte_trip_tax_post_qry->the_post(); 
                            $details = wte_get_trip_details( get_the_ID() );
                            $details['j'] = $j;
                            wte_get_template( 'content-grid.php', $details );
                            $j++;
                            
                        endwhile; 
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
endif;
get_footer();