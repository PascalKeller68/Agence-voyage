<?php
/**
* All the other required plugin functions
*/
class Travel_Agency_Companion_Functions{

    /**
     * Function to list post categories in customizer options
    */
    function travel_agency_get_categories( $select = true, $taxonomy = 'category', $slug = false ){
        
        /* Option list of all categories */
        $categories = array();
        if( $select ) $categories[''] = __( 'Choose Category', 'travel-agency-companion' );
        
        if( taxonomy_exists( $taxonomy ) ){
            $args = array( 
                'hide_empty' => false,
                'taxonomy'   => $taxonomy 
            );
            
            $catlists = get_terms( $args );
            
            foreach( $catlists as $category ){
                if( $slug ){
                    $categories[$category->slug] = $category->name;
                }else{
                    $categories[$category->term_id] = $category->name;    
                }        
            }
        }
        return $categories;
    }
    
    /**
     * Fuction to list Custom Post Type
    */
    function travel_agency_get_posts( $post_type = 'post' ){
        
        $args = array(
        	'posts_per_page'   => -1,
        	'post_type'        => $post_type,
        	'post_status'      => 'publish',
        	//'suppress_filters' => true 
        );
        $posts_array = get_posts( $args );
        
        // Initate an empty array
        $post_options = array();
        $post_options[''] = __( ' -- Choose -- ', 'travel-agency-companion' );
        if ( ! empty( $posts_array ) ) {
            foreach ( $posts_array as $posts ) {
                $post_options[ $posts->ID ] = $posts->post_title;
            }
        }
        return $post_options;
        wp_reset_postdata();
    }

    /**
     * Check if Wp Travel Engine Plugin is installed
    */
    function travel_agency_is_wpte_activated(){
        return class_exists( 'Wp_Travel_Engine' ) ? true : false;
    }
    
    /**
     * Returns image url
    */
    function get_image_url( $image_id ){
        if( ! is_numeric( $image_id ) ) return;
        
        return wp_get_attachment_image_url( $image_id, 'full' );            
    }
    
    /**
     * Returns posted on date
    */
    function travel_agency_posted_on( $icon = false ) {
    
        echo '<span class="posted-on">';
        
        if( $icon ) echo '<i class="fa fa-calendar" aria-hidden="true"></i>';
        
        printf( '<a href="%1$s" rel="bookmark"><time class="entry-date published updated" datetime="%2$s">%3$s</time></a>', esc_url( get_permalink() ), esc_attr( get_the_date( 'c' ) ), esc_html( get_the_date() ) );
        
        echo '</span>';

    }
    
    /**
     * Get Trip Currency
    */
    function get_trip_currency(){
        $currency = '';
        if( $this->travel_agency_is_wpte_activated() ){
            $obj = new Wp_Travel_Engine_Functions();
            $wpte_setting = get_option( 'wp_travel_engine_settings', true ); 
            $code = 'USD';
            if( isset( $wpte_setting['currency_code'] ) && $wpte_setting['currency_code']!= '' ){
                $code = $wpte_setting['currency_code'];
            } 

            $apiKey = isset($wpte_setting['currency_converter_api']) && $wpte_setting['currency_converter_api']!='' ? esc_attr($wpte_setting['currency_converter_api']) : '';

            if( class_exists( 'Wte_Trip_Currency_Converter_Init' ) && $apiKey != '' )
            { 
                $converter_obj = new Wte_Trip_Currency_Converter_Init();
                $code = $converter_obj->wte_trip_currency_code_converter( $code );
            }
            $currency = $obj->wp_travel_engine_currencies_symbol( $code );

        }
        return $currency;
    }

    /**
     *
     */
    function travel_agency_trip_symbol_options( $trip_id, $code, $currency, $show_prev_cost = false ){
        $obj = new Wp_Travel_Engine_Functions();
        $meta = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );
        $trip_post = get_post( $trip_id );

        $settings = get_option( 'wp_travel_engine_settings' ); 
        $option = isset( $settings['currency_option'] ) && $settings['currency_option']!='' ? esc_attr( $settings['currency_option'] ) : 'symbol';

        if( isset( $option ) && $option != 'symbol'){
            $currency = $code;
        }

        if( ( isset( $meta['trip_prev_price'] ) && $meta['trip_prev_price'] ) || ( isset( $meta['sale'] ) && $meta['sale'] && isset( $meta['trip_price'] ) && $meta['trip_price'] ) ){       
        
            echo '<span class="price-holder">';
                if( ( isset( $meta['trip_prev_price'] ) && $meta['trip_prev_price'] ) && ( isset( $meta['sale'] ) && $meta['sale'] ) && ( isset( $meta['trip_price'] ) && $meta['trip_price'] ) )
                {
                    $cost = wp_travel_engine_get_sale_price( $trip_id ); 
                    $prev_cost = wp_travel_engine_get_prev_price( $trip_id );

                    if( $show_prev_cost ){
                        echo '<span><strike>'. wp_travel_engine_get_formated_price_with_currency_code_symbol( $prev_cost ) .'</strike>';
                        echo wp_travel_engine_get_formated_price_with_currency_code_symbol( $cost );
                        echo '</span>';
                    }else{
                        echo '<span>'. wp_travel_engine_get_formated_price_with_currency_code_symbol( $cost ) .'</span>';
                    }                    
                } elseif( isset( $meta['trip_prev_price'] ) && $meta['trip_prev_price'] )
                {
                    $prev_cost = wp_travel_engine_get_prev_price( $trip_id );

                    echo '<span>'. wp_travel_engine_get_formated_price_with_currency_code_symbol( $prev_cost ) .'</span>';
                }
            echo '</span>';
        }                                 
    }    

    /**
     * Get information about available image sizes
     */
    function travel_agency_get_image_sizes( $size = '' ) {
     
        global $_wp_additional_image_sizes;
     
        $sizes = array();
        $get_intermediate_image_sizes = get_intermediate_image_sizes();
     
        // Create the full array with sizes and crop info
        foreach( $get_intermediate_image_sizes as $_size ) {
            if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
                $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
                $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
                $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );
            } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
                $sizes[ $_size ] = array( 
                    'width' => $_wp_additional_image_sizes[ $_size ]['width'],
                    'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                    'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']
                );
            }
        } 
        // Get only 1 size if found
        if ( $size ) {
            if( isset( $sizes[ $size ] ) ) {
                return $sizes[ $size ];
            } else {
                return false;
            }
        }
        return $sizes;
    }

    /**
     * Get Fallback SVG
    */
    function travel_agency_get_fallback_svg( $post_thumbnail, $dimension = false ) {
        if( ! $post_thumbnail ){
            return;
        }
        
        $image_size = array();

        if( $dimension ){
            $image_size['width']  = $post_thumbnail['width']; 
            $image_size['height'] = $post_thumbnail['height'];
        }else{
            $image_size = $this->travel_agency_get_image_sizes( $post_thumbnail );
        }
         
        if( $image_size ){ ?>
            <div class="svg-holder">
                 <svg class="fallback-svg" viewBox="0 0 <?php echo esc_attr( $image_size['width'] ); ?> <?php echo esc_attr( $image_size['height'] ); ?>" preserveAspectRatio="none">
                        <rect width="<?php echo esc_attr( $image_size['width'] ); ?>" height="<?php echo esc_attr( $image_size['height'] ); ?>" style="fill:#f2f2f2;"></rect>
                </svg>
            </div>
            <?php
        }
    }
}
new Travel_Agency_Companion_Functions;