<?php
/**
* Class for trip custom shortcodes.
*/
class WP_Travel_Engine_Custom_Shortcodes
{
    public function __construct() {

        add_shortcode( 'wte_trip', array( $this, 'wte_trip_shortcodes_callback' ) );
        add_shortcode( 'wte_trip_map', array( $this, 'wte_show_trip_map_shortcodes_callback' ) );
        add_shortcode( 'wte_trip_tax', array( $this, 'wte_trip_tax_shortcodes_callback' ) );
        add_shortcode( 'wte_video_gallery', array( $this, 'wte_video_gallery_output_callback' ) );
        add_action( 'wte_trip_content_action', array( $this, 'wte_trip_content' ) );
        add_filter( 'body_class', array( $this, 'wte_custom_shortcode_class' ) );

        /**
		 * Checkout Shortcodes.
		 *
		 * @since 2.2.6
		 * Shortcodes for new checkout process.
		 */
		$shortcodes = array(
			'wp_travel_engine_cart'      => __CLASS__ . '::cart',
			'wp_travel_engine_checkout'  => __CLASS__ . '::checkout',
			'wp_travel_engine_dashboard' => __CLASS__ . '::user_account',
		);

		$shortcode = apply_filters( 'wp_travel_engine_cart_shortcodes', $shortcodes );

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}

    }

    /**
     * Video gallery shortcode output
     */
    public function wte_video_gallery_output_callback( $atts ) {
        ob_start();
        global $post;
        $post_id = is_object( $post ) && isset( $post->ID ) ? $post->ID : false;

        $atts = shortcode_atts( array(
            'title'   => false,
            'trip_id' => $post_id,
            'type'    => 'popup',
            'label'   => __( 'Video Gallery', 'wp-travel-engine' ),
        ), $atts, 'wte_video_gallery' );

        // Bail if no trip ID found.
        if ( ! $atts['trip_id'] ) {
            esc_html_e( 'No Trip ID supplied. Gallery Unavailable.', 'wp-travel-engine' );
            $output = ob_get_clean();
            return $output;
        }

        $video_gallery = get_post_meta( $atts['trip_id'], 'wpte_vid_gallery', true );
        if ( ! empty( $video_gallery ) ) {
            if ( 'popup' === $atts['type'] ) {
                wte_get_template( 'single-trip/gallery-video-popup.php', array( 'label' => $atts['label'], 'title' => $atts['title'], 'trip_id' => $atts['trip_id'], 'gallery' => $video_gallery ) );
            } else if( 'slider' === $atts['type'] ) {
                wte_get_template( 'single-trip/gallery-video-slider.php', array( 'label' => $atts['label'], 'title' => $atts['title'], 'trip_id' => $atts['trip_id'], 'gallery' => $video_gallery ) );
            }
        }
        $output = ob_get_clean();
        return $output;
    }

    /**
	 * Cart page shortcode.
	 *
	 * @return string
	 */
	public static function cart() {
		return self::shortcode_wrapper( array( 'WTE_Cart', 'output' ) );
	}

	/**
	 * Checkout page shortcode.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function checkout( $atts ) {
		return false;
	}
	/**
	 * Add user Account shortcode.
	 *
	 * @return string
	 */
	public static function user_account() {
		return self::shortcode_wrapper( array( 'Wp_Travel_Engine_User_Account', 'output' ) );;
	}

	/**
	 * Shortcode Wrapper.
	 *
	 * @param string[] $function Callback function.
	 * @param array    $atts     Attributes. Default to empty array.
	 * @param array    $wrapper  Customer wrapper data.
	 *
	 * @return string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts = array(),
		$wrapper = array(
			'class'  => 'wp-travel',
			'before' => null,
			'after'  => null,
		)
	) {
		ob_start();

		// @codingStandardsIgnoreStart
		echo empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		call_user_func( $function, $atts );
		echo empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];
		// @codingStandardsIgnoreEnd

		return ob_get_clean();
	}

    function wte_custom_shortcode_class($classes){
        global $post;
        if ( is_object( $post ) )
        {
            if ( has_shortcode( $post->post_content, 'wte_trip_tax' ) || has_shortcode( $post->post_content, 'wte_trip' ) ) {
                $classes[] = 'archive';
            }
        }
        
        return $classes;
    }

    //function to display trips
    function wte_show_trip_map_shortcodes_callback($attr)
    {
        $attr = shortcode_atts( array(
            'id'   => '',
            'show' => 'both' 
            ), $attr, 'wte_trip_map' );
        $wp_travel_engine_setting = get_post_meta( $attr['id'],'wp_travel_engine_setting',true );
        ob_start();
        
        $map_image = isset( $wp_travel_engine_setting['map']['image_url'] ) && ! empty( $wp_travel_engine_setting['map']['image_url'] ) ? $wp_travel_engine_setting['map']['image_url'] : false;

        $map_iframe = isset( $wp_travel_engine_setting['map']['iframe'] ) && ! empty( $wp_travel_engine_setting['map']['iframe'] ) ? $wp_travel_engine_setting['map']['iframe'] : false;

        if ( $map_image && 'iframe' !== $attr['show'] ) {   
            $src = wp_get_attachment_image_src( $wp_travel_engine_setting['map']['image_url'],'full' );
            ?>
            <div class="trip-map image">
                <img src="<?php echo esc_url($src[0]); ?>">
            </div>
            <?php
        }
        if ( $map_iframe && 'image' !== $attr['show'] ) {
            ?>
                <div class="trip-map iframe">
                    <?php echo html_entity_decode($wp_travel_engine_setting['map']['iframe']); ?>
                </div>
            <?php
        }
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    //function to generate shortcode
    function wte_trip_shortcodes_callback( $attr )
    { 
        $attr = shortcode_atts( array(
            'ids'    => '',
            'layout' => 'grid',
            'postsnumber' => get_option( 'posts_per_page' ),
        ), $attr, 'wte_trip' );

        if ( ! empty( $attr['ids'] ) ) {
            $ids          = array();
            $ids          = explode(",", $attr['ids']);
            $attr['ids']  = $ids;
        }

        ob_start();

        do_action( 'wte_trip_content_action', $attr );

        $output = ob_get_contents();
        ob_end_clean();

        if ( $output != '' ) {
            return $output;
        }
    }

    //function to generate shortcode
    function wte_trip_tax_shortcodes_callback( $attr )
    { 
        $attr = shortcode_atts( array(
            'activities'  => '',
            'destination' => '',
            'trip_types'  => '',
            'layout'      => 'grid',
            'postsnumber' => get_option( 'posts_per_page' ),
        ), $attr, 'wte_trip_tax' );

        if ( ! empty( $attr['activities'] ) ) {
            $activities         = array();
            $activities         = explode(",", $attr['activities']);
            $attr['activities'] = $activities;
        }

        if ( ! empty( $attr['destination'] ) ) {
            $destination         = array();
            $destination         = explode(",", $attr['destination']);
            $attr['destination'] = $destination;
        }

        if ( ! empty( $attr['trip_types'] ) ) {
            $trip_types         = array();
            $trip_types         = explode(",", $attr['trip_types']);
            $attr['trip_types'] = $trip_types;
        }

        ob_start();

        do_action( 'wte_trip_content_action', $attr );

        $output = ob_get_contents();
        ob_end_clean();

        if ( $output != '' ) {
            return $output;
        }
    }

    function wte_trip_content($atts)
    {
        $args = array(
			'post_type'        => 'trip',
            'post_status'      => 'publish',
            'posts_per_page'   => $atts['postsnumber']
        );
        
        if ( ! empty( $atts['ids'] ) ) {
            $args['post__in'] = $atts['ids'];
            $args['orderby'] = 'post__in';
        }

        if(! empty( $atts['activities'] ) || ! empty( $atts['destination'] ) || ! empty( $atts['trip_types'] )) {
            $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

            // Query arguments.
            $args['posts_per_page'] = $atts['postsnumber'];
            $args['wpse_search_or_tax_query'] = true;
            $args['paged'] = $paged;

            $taxquery = array('relation' => 'OR');
            if ( ! empty( $atts['activities'] ) ) {
                array_push($taxquery,array(
                        'taxonomy'         => 'activities',
                        'field'            => 'term_id',
                        'terms'            => $atts['activities'],
                        'include_children' => false,
                    ));
            }
            if ( ! empty( $atts['destination'] ) ) {
                array_push($taxquery,array(
                        'taxonomy'         => 'destination',
                        'field'            => 'term_id',
                        'terms'            => $atts['destination'],
                        'include_children' => false,
                    ));
            }
            if ( ! empty( $atts['trip_types'] ) ) {
                array_push($taxquery,array(
                        'taxonomy'         => 'trip_types',
                        'field'            => 'term_id',
                        'terms'            => $atts['trip_types'],
                        'include_children' => false,
                    ));
            }

            if(!empty($taxquery))
            {
                $args['tax_query'] = $taxquery;
            }
        }

        $query = new WP_Query( $args );

        if( $query->have_posts() ) :
            ?>
            <div class="wte-category-outer-wrap">
                <?php
                $view_class = 'grid' === $atts['layout'] ? 'col-3 category-grid' : 'category-list';
                echo '<div class="category-main-wrap '. esc_attr( $view_class ) .'">';
                    while( $query->have_posts() ) : $query->the_post();
							$details = wte_get_trip_details( get_the_ID() );
							wte_get_template( 'content-'.$atts['layout'].'.php', $details );
                    endwhile;
                    wp_reset_postdata();
                echo '</div>';
                ?>
            </div>
            <?php             
        endif;
    }

}
new WP_Travel_Engine_Custom_Shortcodes;
