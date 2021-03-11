<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       raratheme.com
 * @since      1.0.0
 *
 * @package    Travel_Agency_Companion
 * @subpackage Travel_Agency_Companion/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Travel_Agency_Companion
 * @subpackage Travel_Agency_Companion/public
 * @author     raratheme <raratheme.com>
 */
class Travel_Agency_Companion_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = TRAVEL_AGENCY_COMPANION_VERSION;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Travel_Agency_Companion_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Travel_Agency_Companion_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		// Use minified libraries if SCRIPT_DEBUG is false
	    $suffix           = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	
        wp_enqueue_style( 'odometer', plugin_dir_url( __FILE__ ) . 'css/odometer.min.css', null, '0.4.6', 'all' );
        wp_enqueue_style( 'owl-carousel', plugin_dir_url( __FILE__ ) . 'css/owl.carousel.min.css', null, '2.3.4' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/travel-agency-companion-public'. $suffix .'.css', array(), $this->version, 'all' );
		
        $bg_image = get_theme_mod( 'activities_bg_image', TRAVEL_AGENCY_COMPANION_URL . 'includes/images/img2.jpg' );

        if( $bg_image ){
            $custom_css = '
                    .activities:after{
                        background: url( ' . esc_url( $bg_image ) . ' ) no-repeat;
                    }';
            wp_add_inline_style( $this->plugin_name, $custom_css );
        }
        
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Travel_Agency_Companion_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Travel_Agency_Companion_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// Use minified libraries if SCRIPT_DEBUG is false
	    $suffix           = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		
		$owl_carousel = apply_filters('tac_owl_carousel_enqueue',true);
		if($owl_carousel == true)
		{
        	wp_enqueue_script( 'owl-carousel', plugin_dir_url( __FILE__ ) . 'js/owl.carousel.min.js', array( 'jquery' ), '2.3.4', true );
		}	
		
		$odometer = apply_filters('tac_odometer_enqueue',true);
		if($odometer == true)
		{
			wp_enqueue_script( 'odometer', plugin_dir_url( __FILE__ ) . 'js/odometer.min.js', array( 'jquery' ), '0.4.6', true );
		}

		$waypoint = apply_filters('tac_waypoint_enqueue',true);
		if($waypoint == true)
		{
			wp_enqueue_script( 'waypoint', plugin_dir_url( __FILE__ ) . 'js/waypoint.min.js', array( 'jquery' ), '2.0.3', true );
		}

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/travel-agency-companion-public'. $suffix .'.js', array( 'jquery' ), $this->version, true );
        
        $array = array( 'rtl' => is_rtl() );
    
        wp_localize_script( $this->plugin_name, 'tac_data', $array );

        $all = apply_filters('tac_all_enqueue',true);
		if($all == true)
		{
			wp_enqueue_script( 'all', plugin_dir_url( __FILE__ ) . 'js/fontawesome/all.min.js', array( 'jquery' ), '5.6.3', true );
		}

		$shims = apply_filters('tac_shims_enqueue',true);
		if($shims == true)
		{
			wp_enqueue_script( 'v4-shims', plugin_dir_url( __FILE__ ) . 'js/fontawesome/v4-shims.min.js', array( 'jquery' ), '5.6.3', true );
		}

		$owl_carousel_aria = apply_filters('tac_owl_carousel_aria_enqueue',true);
		if($owl_carousel_aria == true)
		{
        	wp_enqueue_script( 'owl-carousel-aria', plugin_dir_url( __FILE__ ) . 'js/owl.carousel.aria'. $suffix .'.js', array( 'jquery' ), '2.0.0', true );
		}

	}
    
    /**
	 * Add section in plugin to front page. 
	 *
	 * @since    1.0.0
	 */
    public function front_page_sections(){
        $sections      = array();
        $ed_banner     = get_theme_mod( 'ed_banner', true );
        $ed_about      = get_theme_mod( 'ed_about_section', true );
        $ed_activities = get_theme_mod( 'ed_activities_section', true );
        $ed_popular    = get_theme_mod( 'ed_popular_section', true );
        $ed_whyus      = get_theme_mod( 'ed_why_us_section', true );
        $ed_featured   = get_theme_mod( 'ed_feature_section', true );
        $ed_stat       = get_theme_mod( 'ed_stat_section', true );
        $ed_deal       = get_theme_mod( 'ed_deal_section', true );
        $ed_cta        = get_theme_mod( 'ed_cta_section', true );
        $ed_blog       = get_theme_mod( 'ed_blog_section', true );
        
        if( $ed_banner ) array_push( $sections, 'sections/banner' );
        if( $ed_about ) array_push( $sections, 'about' );
        if( $ed_activities ) array_push( $sections, 'activities' );
        if( $ed_popular ) array_push( $sections, 'popular' );
        if( $ed_whyus ) array_push( $sections, 'our-feature' );
        if( $ed_featured ) array_push( $sections, 'featured-trip' );
        if( $ed_stat ) array_push( $sections, 'stats' );
        if( $ed_deal ) array_push( $sections, 'deals' );
        if( $ed_cta ) array_push( $sections, 'cta' );
        if( $ed_blog ) array_push( $sections, 'sections/blog' );
        
        return $sections;
    }

    function travel_agency_companion_js_defer_files($tag)
	{
		$tac_assets = apply_filters('tac_public_assets_enqueue',true);

		if( is_admin() || $tac_assets == true ) return $tag;
		
		$async_files = apply_filters( 'travel_agency_companion_js_async_files', array( 
			plugin_dir_url( __FILE__ ) . 'js/owl.carousel.min.js',		
	        plugin_dir_url( __FILE__ ) . 'js/odometer.min.js',
	        plugin_dir_url( __FILE__ ) . 'js/waypoint.min.js',
	        plugin_dir_url( __FILE__ ) . 'js/travel-agency-companion-public.min.js',
	        plugin_dir_url( __FILE__ ) . 'js/fontawesome/all.min.js',
	        plugin_dir_url( __FILE__ ) . 'js/fontawesome/v4-shims.min.js',
	        plugin_dir_url( __FILE__ ) . 'js/owl.carousel.aria.min.js'

		 ) );
		
		$add_async = false;
		foreach( $async_files as $file ){
			if( strpos( $tag, $file ) !== false ){
				$add_async = true;
				break;
			}
		}

		if( $add_async ) $tag = str_replace( ' src', ' defer="defer" src', $tag );

		return $tag;
		
	}

}
