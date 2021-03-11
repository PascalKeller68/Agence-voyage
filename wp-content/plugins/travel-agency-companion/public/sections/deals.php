<?php
/**
 * Our Deals Section
 * 
 * @package Travel_Agency
 */

$defaults    = new Travel_Agency_Companion_Dummy_Array;
$obj         = new Travel_Agency_Companion_Functions;
$ed_demo     = get_theme_mod( 'ed_deal_demo', true );
$title       = get_theme_mod( 'deal_title', __( 'Deals and Discounts', 'travel-agency-companion' ) );
$content     = get_theme_mod( 'deal_desc', __( 'how the special deals and discounts to your customers here. You can customize this section from Appearance > Customize > Home Page Settings > Deals Section.', 'travel-agency-companion' ) );
$post_one    = get_theme_mod( 'deal_post_one' );
$post_two    = get_theme_mod( 'deal_post_two' );
$post_three  = get_theme_mod( 'deal_post_three' );
$label       = get_theme_mod( 'deal_readmore', __( 'Book Now', 'travel-agency-companion' ) );
$view_all    = get_theme_mod( 'deal_view_all_label', __( 'View All Deals', 'travel-agency-companion' ) );
$view_url    = get_theme_mod( 'deal_view_all_url', '#' );
$posts       = array( $post_one, $post_two, $post_three );
$posts       = array_diff( array_unique( $posts ), array('') );

$args = array( 
    'post_type'      => 'trip',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'post__in'       => $posts,
    'orderby'        => 'post__in'
);

$qry = new WP_Query( $args );

if( $title || $content || ( travel_agency_is_wpte_activated() && $posts && $qry->have_posts() ) ){ ?>

<section class="our-deals" id="deal_section">
	<div class="container">
		
        <?php if( $title || $content ){ ?>
        <header class="section-header wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s">
			<?php 
                if( $title ) echo '<h2 class="section-title">' . esc_html( travel_agency_companion_get_deal_title() ) . '</h2>';
                if( $content ) echo '<div class="section-content">' . wp_kses_post( travel_agency_companion_get_deal_content() ) . '</div>'; 
            ?>
		</header>
        <?php } ?>
        
        <?php 
        if( travel_agency_is_wpte_activated() && $posts && $qry->have_posts() ){ 
            $currency = $obj->get_trip_currency();
            $new_obj  = new Wp_Travel_Engine_Functions(); ?>
            <div class="grid grid-latest wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s">
    		<?php 
                while( $qry->have_posts() ){ 
                    $qry->the_post();
                    $code = $new_obj->trip_currency_code( get_post() );
                    $meta = get_post_meta( get_the_ID(), 'wp_travel_engine_setting', true );
                    if( function_exists( 'wte_get_the_tax_term_list' ) ){
                        $destinations  = wte_get_the_tax_term_list( get_the_ID(), 'destination', '', ', ', '' );
                        $destination   = '';
                        if ( ! empty( $destinations ) && ! is_wp_error( $destinations ) ){
                            $destination = $destinations;
                        }
                    }else{
                        $destination = '';
                    }
                    ?>                    
                    <div class="col">
                        <div class="holder"> 
            				<div class="img-holder">
            					<a href="<?php the_permalink(); ?>">
                                <?php 
                                    if( has_post_thumbnail() ){
                                        the_post_thumbnail( 'travel-agency-blog' );
                                    }else{ 
                                        $obj->travel_agency_get_fallback_svg( 'travel-agency-blog' );
                                    }
                                ?>                        
                                </a>
            					<?php 
                                    $obj->travel_agency_trip_symbol_options( get_the_ID(), $code, $currency, true );
                                    
                                    if( ( isset( $meta['trip_prev_price'] ) && $meta['trip_prev_price'] ) || ( isset( $meta['sale'] ) && $meta['sale'] && isset( $meta['trip_price'] ) && $meta['trip_price'] ) )
                                    {
                                                                           
                                        if( isset( $meta['sale'] ) && $meta['sale'] && isset( $meta['trip_prev_price'] ) && $meta['trip_prev_price'] && isset( $meta['trip_price'] ) && $meta['trip_price'] ){
                                            $diff = (int)( $meta['trip_prev_price'] - $meta['trip_price'] );
                                            $perc = (float)( ( $diff / $meta['trip_prev_price'] ) * 100 );                                    
                                            echo '<span class="discount-holder"><span>';
                                            printf( __( '%1$s&percnt; Off', 'travel-agency-companion' ), round( $perc ) ); 
                                            echo '</span></span>';
                                        }
                                    }

                                    if( function_exists( 'wte_is_group_discount_enabled' ) && wte_is_group_discount_enabled( get_the_ID() ) ){ ?>
                                        <span class="group-discount">
                                            <span class="tooltip">
                                                <?php _e( 'You have group discount in this trip.', 'travel-agency-companion' ) ?>
                                            </span>
                                                <span class="pop-trip-grpavil-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="17.492" height="14.72" viewBox="0 0 17.492 14.72">
                                                        <g id="Group_898" data-name="Group 898" transform="translate(-452 -737)">
                                                            <g id="Group_757" data-name="Group 757" transform="translate(12.114)">
                                                                <g id="multiple-users-silhouette" transform="translate(439.886 737)">
                                                                    <path id="Path_23387" data-name="Path 23387" d="M10.555,8.875a3.178,3.178,0,0,1,1.479,2.361,2.564,2.564,0,1,0-1.479-2.361ZM8.875,14.127a2.565,2.565,0,1,0-2.566-2.565A2.565,2.565,0,0,0,8.875,14.127Zm1.088.175H7.786A3.289,3.289,0,0,0,4.5,17.587v2.662l.007.042.183.057a14.951,14.951,0,0,0,4.466.72,9.168,9.168,0,0,0,3.9-.732l.171-.087h.018V17.587A3.288,3.288,0,0,0,9.963,14.3Zm4.244-2.648h-2.16a3.162,3.162,0,0,1-.976,2.2,3.9,3.9,0,0,1,2.788,3.735v.82a8.839,8.839,0,0,0,3.443-.723l.171-.087h.018V14.938A3.288,3.288,0,0,0,14.207,11.654Zm-9.834-.175a2.548,2.548,0,0,0,1.364-.4A3.175,3.175,0,0,1,6.931,9.058c0-.048.007-.1.007-.144a2.565,2.565,0,1,0-2.565,2.565Zm2.3,2.377a3.163,3.163,0,0,1-.975-2.19c-.08-.006-.159-.012-.241-.012H3.285A3.288,3.288,0,0,0,0,14.938V17.6l.007.041L.19,17.7a15.4,15.4,0,0,0,3.7.7v-.8A3.9,3.9,0,0,1,6.677,13.856Z" transform="translate(0 -6.348)" fill="#fff"></path>
                                                                </g>
                                                            </g>
                                                        </g>
                                                    </svg>
                                            </span>
                                        </span>
                                        <?php
                                    }
                                ?>
                                <?php if ( function_exists( 'wte_is_trip_featured' ) && wte_is_trip_featured( get_the_ID() ) ) : ?>
                                <div class="category-feat-ribbon">
                                    <span class="category-feat-ribbon-txt"><?php _e( 'Featured', 'travel-agency-companion' );?></span>
                                    <span class="cat-feat-shadow"></span>
                                </div>
                                <?php endif; ?>
            				</div>
            				<div class="text-holder">
                                <h3 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>    
                                <div class="category-trip-detail-wrap">
                                    <div class="category-trip-desti">
                                        <?php if( ! empty( $destination ) ) : ?>
                                        <span class="category-trip-loc">
                                            <i>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="11.213" height="15.81" viewBox="0 0 11.213 15.81">
                                                    <path id="Path_23393" data-name="Path 23393" d="M5.607,223.81c1.924-2.5,5.607-7.787,5.607-10.2a5.607,5.607,0,0,0-11.213,0C0,216.025,3.682,221.31,5.607,223.81Zm0-13.318a2.492,2.492,0,1,1-2.492,2.492A2.492,2.492,0,0,1,5.607,210.492Zm0,0" transform="translate(0 -208)" opacity="0.8"/>
                                                </svg>
                                            </i>
                                            <span><?php echo wp_kses_post( $destination ); ?></span>
                                        </span>
                                        <?php endif; ?>

                                        <div class="meta-info">
                                            <?php
                                                $trip_duration = isset( $meta['trip_duration'] ) && ! empty( $meta['trip_duration'] ) ? $meta['trip_duration']: false;
                                                $trip_duration_nights = isset( $meta['trip_duration_nights'] ) && ! empty( $meta['trip_duration_nights'] ) ? $meta['trip_duration_nights']: false;

                                                if( $trip_duration ) { 
                                                    echo '<span class="time"><i>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="17.332" height="15.61" viewBox="0 0 17.332 15.61">
                                                            <g id="Group_773" data-name="Group 773" transform="translate(283.072 34.13)">
                                                            <path id="Path_23383" data-name="Path 23383" d="M-283.057-26.176h.1c.466,0,.931,0,1.4,0,.084,0,.108-.024.1-.106-.006-.156,0-.313,0-.469a5.348,5.348,0,0,1,.066-.675,5.726,5.726,0,0,1,.162-.812,5.1,5.1,0,0,1,.17-.57,9.17,9.17,0,0,1,.383-.946,10.522,10.522,0,0,1,.573-.96c.109-.169.267-.307.371-.479a3.517,3.517,0,0,1,.5-.564,6.869,6.869,0,0,1,1.136-.97,9.538,9.538,0,0,1,.933-.557,7.427,7.427,0,0,1,1.631-.608c.284-.074.577-.11.867-.162a7.583,7.583,0,0,1,1.49-.072c.178,0,.356.053.534.062a2.673,2.673,0,0,1,.523.083c.147.038.3.056.445.1.255.07.511.138.759.228a6.434,6.434,0,0,1,1.22.569c.288.179.571.366.851.556a2.341,2.341,0,0,1,.319.259c.3.291.589.592.888.882a4.993,4.993,0,0,1,.64.85,6.611,6.611,0,0,1,.71,1.367c.065.175.121.352.178.53s.118.348.158.526c.054.242.09.487.133.731.024.14.045.281.067.422a.69.69,0,0,1,.008.1c0,.244.005.488,0,.731s-.015.5-.04.745a4.775,4.775,0,0,1-.095.5c-.04.191-.072.385-.128.572-.094.312-.191.625-.313.926a7.445,7.445,0,0,1-.43.9c-.173.3-.38.584-.579.87a8.045,8.045,0,0,1-1.2,1.26,5.842,5.842,0,0,1-.975.687,8.607,8.607,0,0,1-1.083.552,11.214,11.214,0,0,1-1.087.36c-.19.058-.386.1-.58.137-.121.025-.245.037-.368.052a12.316,12.316,0,0,1-1.57.034,3.994,3.994,0,0,1-.553-.065c-.166-.024-.33-.053-.5-.082a1.745,1.745,0,0,1-.21-.043c-.339-.1-.684-.189-1.013-.317a7,7,0,0,1-1.335-.673c-.2-.136-.417-.263-.609-.415a6.9,6.9,0,0,1-.566-.517.488.488,0,0,1-.128-.331.935.935,0,0,1,.1-.457.465.465,0,0,1,.3-.223.987.987,0,0,1,.478-.059.318.318,0,0,1,.139.073c.239.185.469.381.713.559a5.9,5.9,0,0,0,1.444.766,5.073,5.073,0,0,0,.484.169c.24.062.485.1.727.154a1.805,1.805,0,0,0,.2.037c.173.015.346.033.52.036.3.006.6.01.9,0a3.421,3.421,0,0,0,.562-.068c.337-.069.676-.139,1-.239a6.571,6.571,0,0,0,.783-.32,5.854,5.854,0,0,0,1.08-.663,5.389,5.389,0,0,0,.588-.533,8.013,8.013,0,0,0,.675-.738,5.518,5.518,0,0,0,.749-1.274,9.733,9.733,0,0,0,.366-1.107,4.926,4.926,0,0,0,.142-.833c.025-.269.008-.542.014-.814a4.716,4.716,0,0,0-.07-.815,5.8,5.8,0,0,0-.281-1.12,5.311,5.311,0,0,0-.548-1.147,9.019,9.019,0,0,0-.645-.914,9.267,9.267,0,0,0-.824-.788,3.354,3.354,0,0,0-.425-.321,5.664,5.664,0,0,0-1.048-.581c-.244-.093-.484-.2-.732-.275a6.877,6.877,0,0,0-.688-.161c-.212-.043-.427-.074-.641-.109a.528.528,0,0,0-.084,0c-.169,0-.338,0-.506,0a5.882,5.882,0,0,0-1.177.1,6.79,6.79,0,0,0-1.016.274,6.575,6.575,0,0,0-1.627.856,6.252,6.252,0,0,0-1.032.948,6.855,6.855,0,0,0-.644.847,4.657,4.657,0,0,0-.519,1.017c-.112.323-.227.647-.307.979a3.45,3.45,0,0,0-.13.91,4.4,4.4,0,0,1-.036.529c-.008.086.026.1.106.1.463,0,.925,0,1.388,0a.122.122,0,0,1,.08.028c.009.009-.005.051-.019.072q-.28.415-.563.827c-.162.236-.33.468-.489.705-.118.175-.222.359-.339.535-.1.144-.2.281-.3.423-.142.2-.282.41-.423.615-.016.023-.031.047-.048.069-.062.084-.086.083-.142,0-.166-.249-.332-.5-.5-.746-.3-.44-.6-.878-.9-1.318q-.358-.525-.714-1.051c-.031-.045-.063-.09-.094-.134Z" transform="translate(0 0)"></path>
                                                            <path id="Path_23384" data-name="Path 23384" d="M150.612,112.52c0,.655,0,1.31,0,1.966a.216.216,0,0,0,.087.178,4.484,4.484,0,0,1,.358.346.227.227,0,0,0,.186.087q1.616,0,3.233,0a.659.659,0,0,1,.622.4.743.743,0,0,1-.516,1.074,1.361,1.361,0,0,1-.323.038q-1.507,0-3.013,0a.248.248,0,0,0-.216.109,1.509,1.509,0,0,1-.765.511,1.444,1.444,0,0,1-1.256-2.555.218.218,0,0,0,.09-.207q0-1.916,0-3.831a.784.784,0,0,1,.741-.732.742.742,0,0,1,.761.544.489.489,0,0,1,.015.127Q150.612,111.547,150.612,112.52Z" transform="translate(-423.686 -141.471)"></path>
                                                            </g>
                                                        </svg>
                                                    </i>';
                                                    printf( esc_html( _nx( '%1$s Day', '%1$s Days', absint( $trip_duration ), 'trip duration', 'travel-agency-companion' ) ), absint( $trip_duration ) );
                                                    
                                                    if( $trip_duration_nights ) {
                                                        printf( esc_html( _nx( ' - %1$s Night', ' - %1$s Nights', absint( $trip_duration_nights ), 'trip duration night', 'travel-agency-companion' ) ), absint( $trip_duration_nights ) );
                                                    }
                                                    echo '</span>'; 
                                                }
                                            ?>
                                        </div>
                                    </div>
                                    <?php if( class_exists('Wte_Trip_Review_Init' )){ ?>
                                        <div class="star-holder">
                                            <?php
                                                $comments = get_comments( array(
                                                    'post_id' => get_the_ID(),
                                                    'status' => 'approve',
                                                ) );

                                                if ( !empty( $comments ) ){
                                                    echo '<div class="review-wrap"><div class="average-rating">';
                                                    $sum = 0;
                                                    $i = 0;
                                                    foreach($comments as $comment) {
                                                        $rating = get_comment_meta( $comment->comment_ID, 'stars', true );
                                                        $sum = $sum+$rating;
                                                        $i++;
                                                    }
                                                    $aggregate = $sum/$i;
                                                    $aggregate = round($aggregate,2);

                                                    echo 
                                                    '<script>
                                                        jQuery(document).ready(function($){
                                                            $("#deals-agg-rating-'.get_the_ID().'").rateYo({
                                                                rating: '.floatval( $aggregate ).'
                                                            });
                                                        });
                                                    </script>';
                                                    echo '<div id="deals-agg-rating-'.get_the_ID().'" class="agg-rating"></div><div class="aggregate-rating">
                                                    <span class="rating-star">'.$aggregate.'</span><span >'.$i.'</span> '. esc_html( _nx( 'review', 'reviews', $i, 'reviews count', 'travel-agency-companion' ) ) .'</div>';
                                                    echo '</div></div><!-- .review-wrap -->';
                                                }
                                            ?>  
                                        </div>
                                    <?php } ?>  
                                </div>
                                <?php
                                    if( class_exists('WTE_Fixed_Starting_Dates') && function_exists( 'wp_travel_engine_get_fixed_departure_dates' ) ){
                                        $trip_starting_dates = wp_travel_engine_get_fixed_departure_dates( get_the_ID() );
                                        $wp_travel_engine_setting_option_setting = get_option('wp_travel_engine_settings', true);
                                        $num = isset($wp_travel_engine_setting_option_setting['trip_dates']['number']) ? $wp_travel_engine_setting_option_setting['trip_dates']['number'] : 3;

                                        $trip_starting_dates = array_slice( $trip_starting_dates, 0, $num );
                                        if ( ! empty( $trip_starting_dates ) && is_array( $trip_starting_dates ) ) { ?>
                                            <div class="next-trip-info">
                                                <h3><?php esc_html_e( 'Next Departure', 'travel-agency-companion' ); ?></h3>
                                                <ul class="next-departure-list">
                                                    <?php
                                                        foreach( $trip_starting_dates as $key => $date ) {
                                                            echo '<li><span class="left"><i class="fa fa-clock-o"></i>'. date_i18n( get_option( 'date_format' ), strtotime(  $date['start_date'] ) ).'</span><span class="right">'. sprintf( __( '%1$s Available', 'travel-agency-companion' ), $date['seats_left'] ) .'</span></li>';
                                                        }
                                                    ?>
                                                </ul>
                                            </div>
                                            <?php 
                                        }
                                    }
                                ?>
                                <?php if( $label ){ ?>
                                    <div class="btn-holder">
                						<a href="<?php the_permalink(); ?>" class="btn-more"><?php echo esc_html( travel_agency_companion_get_dealbtn_label() ); ?></a>
                					</div>
                                <?php } ?>
            				</div>
                        </div>
        			</div>			
        			<?php 
                }
                wp_reset_postdata();     
            ?>
    		</div>
            <?php 
        }elseif( $ed_demo ){
            //default
            $deals = $defaults->default_trip_deal_posts(); ?>
            <div class="grid wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s">
				<?php foreach( $deals as $v ){ ?>
                <div class="col">
                    <div class="holder">
    					<div class="img-holder">
    						<a href="#"><img src="<?php echo esc_url( $v['img'] ); ?>" alt="<?php echo $v['title']; ?>"></a>
    						<span class="price-holder"><span><strike><?php echo esc_html( $v['old_price'] ); ?></strike><?php echo esc_html( $v['sale_price'] ); ?></span></span>
    						<span class="discount-holder"><span><?php echo esc_html( $v['discount'] ); ?></span></span>
    					</div>
    					<div class="text-holder">
    						<h3 class="title"><a href="#"><?php echo esc_html( $v['title'] ); ?></a></h3>
    						<div class="meta-info">
    							<span class="time"><i class="fa fa-clock-o"></i><?php echo esc_html( $v['days'] ); ?></span>
    						</div>
    						<div class="btn-holder">
    							<a href="#" class="btn-more"><?php esc_html_e( 'Book Now', 'travel-agency-companion' ); ?></a>
    						</div>
    					</div>
                    </div>
				</div>				
				<?php } ?>
			</div>
            <?php
        } 
        
        if( $view_all && $view_url ){
            echo '<div class="btn-holder deal-btn-holder"><a href="' . esc_url( $view_url ) . '" class="btn-more deal-btn-more">' . travel_agency_companion_get_deal_view_all_label() . '</a></div>';
        }
        ?>
	</div>
</section>
<?php
}
