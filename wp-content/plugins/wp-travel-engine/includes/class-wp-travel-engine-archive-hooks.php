<?php
/**
 *
 * This class defines all hooks for archive page of the trip.
 *
 * @since      1.0.0
 * @package    Wp_Travel_Engine
 * @subpackage Wp_Travel_Engine/includes
 * @author     WP Travel Engine <https://wptravelengine.com/>
 */
/**
* 
*/
class Wp_Travel_Engine_Archive_Hooks
{
	function __construct()
	{
		add_action( 'wp_travel_engine_trip_archive_outer_wrapper', array( $this, 'wp_travel_engine_trip_archive_wrapper' ) );
		add_action( 'wp_travel_engine_trip_archive_wrap', array( $this, 'wp_travel_engine_trip_archive_wrap' ) );
		add_action( 'wp_travel_engine_trip_archive_outer_wrapper_close', array( $this, 'wp_travel_engine_trip_archive_outer_wrapper_close' ) );
		add_action( 'wp_travel_engine_header_filters', array( $this, 'wp_travel_engine_header_filters_template' ) );
		add_action( 'wp_travel_engine_archive_header_block', array( $this, 'wp_travel_engine_archive_header_block' ) );
		add_action( 'wp_travel_engine_featured_trips_sticky', array( $this, 'wte_featured_trips_sticky' ), 10, 1 );
	}

	/**
	 * Featured Trips sticky section for WP Travel Engine Archives.
	 *
	 * @return void
	 */
	function wte_featured_trips_sticky( $view_mode ) {
		$trips_array = wte_get_featured_trips_array();
		if( empty( $trips_array ) ) return;

		$args = array(
			'post_type' => 'trip',
			'post__in'  => $trips_array
		);

		$featured_query = new WP_Query( $args );

		while( $featured_query->have_posts() ) : $featured_query->the_post();
			$details = wte_get_trip_details( get_the_ID() );
			wte_get_template( 'content-'.$view_mode.'.php', $details );
		endwhile;

	}
	/**
	 * Header filter section for WP Travel Engine Archives.
	 *
	 * @return void
	 */
	function wp_travel_engine_header_filters_template() {
		$view_mode = wp_travel_engine_get_archive_view_mode();
		$orderby   = isset( $_GET['wte_orderby'] ) && ! empty( $_GET['wte_orderby'] ) ? $_GET['wte_orderby'] : '';
		?>
			<div class="wp-travel-toolbar clearfix">
			<div class="wte-filter-foundposts">
				<h2 class="searchFoundPosts"></h2>
			</div>
				<div class="wp-travel-engine-toolbar wte-view-modes">
					<?php
						$current_url = '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
					?>
					<span><?php esc_html_e( 'View by :', 'wp-travel-engine' ); ?></span>
					<ul class="wte-view-mode-selection-lists">
						<li class="wte-view-mode-selection <?php echo ( 'grid' === $view_mode ) ? 'active' : ''; ?>" data-mode="grid" >
							<a href="<?php echo esc_url( add_query_arg( 'view_mode', 'grid', $current_url ) ); ?>">
								<i class="fas fa-th"></i>
							</a>
						</li>
						<li class="wte-view-mode-selection <?php echo ( 'list' === $view_mode ) ? 'active' : ''; ?>" data-mode="list" >
							<a href="<?php echo esc_url( add_query_arg( 'view_mode', 'list', $current_url ) ); ?>">
								<i class="fas fa-list"></i>
							</a>
						</li>
					</ul>
				</div>
				<div class="wp-travel-engine-toolbar wte-filterby-dropdown">
					<?php
						$wte_sorting_options = apply_filters( 'wp_travel_engine_archive_header_sorting_options', array(
							''           => __( 'Default Sorting', 'wp-travel-engine' ),
							'latest'     => __( 'Latest', 'wp-travel-engine' ),
							'rating'     => __( 'Most Reviewed', 'wp-travel-engine' ),
							'price'      => __( 'Price: low to high', 'wp-travel-engine' ),
							'price-desc' => __( 'Price: high to low', 'wp-travel-engine' ),
							'days'       => __( 'Days: low to high', 'wp-travel-engine' ),
							'days-desc'  => __( 'Days: high to low', 'wp-travel-engine' ),
							'name'       => __( 'Name in Ascending', 'wp-travel-engine' ),
							'name-desc'  => __( 'Name in Descending', 'wp-travel-engine' )
						) );
					?>
					<form class="wte-ordering" method="get">
						<span><?php esc_html_e( 'List by :', 'wp-travel-engine' ); ?></span>
						<select name="wte_orderby" class="orderby" aria-label="<?php esc_attr_e( 'Trip order', 'wp-travel-engine' ); ?>">
							<?php foreach ( $wte_sorting_options as $id => $name ) : ?>
							<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
							<?php endforeach; ?>
						</select>
						<input type="hidden" name="paged" value="1" />
							<?php wte_query_string_form_fields( null, array( 'wte_orderby', 'submit', 'paged' ) ); ?>
					</form>
				</div>
			</div>
		<?php 
	}

	/**
	 * Hook for the header block ( contains title and description )
	 *
	 * @return void
	 */
	function wp_travel_engine_archive_header_block() {
		$page_header = apply_filters( 'wte_trip_archive_description_page_header', true );
		if( $page_header ){
		?>
			<header class="page-header">
				<?php
					echo '<h1 class="page-title" itemprop="name">'.single_term_title( '', false ).'</h1>';

					$taxonomies = array( 'trip_types', 'destination', 'activities' );
					if( is_tax( $taxonomies ) ) {
						$image_id = get_term_meta ( get_queried_object()->term_id, 'category-image-id', true );
						$wte_global = get_option( 'wp_travel_engine_settings', true );
						$show_tax_image = isset( $image_id) && '' != $image_id 
						&& isset( $wte_global['tax_images'] ) ? true : false;
						if( $show_tax_image ) {
							$tax_banner_size = apply_filters('wp_travel_engine_template_banner_size', 'full');
							echo wp_get_attachment_image ( $image_id, $tax_banner_size );
						}
					}
					
					$show_archive_description = apply_filters( 'wte_trip_archive_description_below_title', true );
					if ( $show_archive_description ) {
						the_archive_description( '<div class="taxonomy-description" itemprop="description">', '</div>' );
					}
				?>
			</header><!-- .page-header -->
		<?php
		}
	}

	/**
     * Main wrap of the archive.
     *
     * @since    1.0.0
     */
	function wp_travel_engine_trip_archive_wrapper()
	{ ?>
		<div id="wte-crumbs">
            <?php
				do_action('wp_travel_engine_breadcrumb_holder');
            ?>
		</div>
		<div id="wp-travel-trip-wrapper" class="trip-content-area" itemscope itemtype="http://schema.org/ItemList">
			<?php 
				$header_block = apply_filters( 'wp_travel_engine_archive_header_block_display', true );
				if ( $header_block ) {
					do_action( 'wp_travel_engine_archive_header_block' );
				}
			?>
            <div class="wp-travel-inner-wrapper">
	<?php
	}

	/**
     * Inner wrap of the archive.
     *
     * @since    1.0.0
     */
	function wp_travel_engine_trip_archive_wrap()
	{ ?>
		<div class="wp-travel-engine-archive-outer-wrap">			
			<?php
				/**
				 * wp_travel_engine_archive_sidebar hook
				 * 
				 * @hooked wte_advanced_search_archive_sidebar - Trip Search addon
				 */
				do_action( 'wp_travel_engine_archive_sidebar' );
			?>
			<div class="wp-travel-engine-archive-repeater-wrap">
				<?php 
					/**
					 * Hook - wp_travel_engine_header_filters 
					 * Hook for the new archive filters on trip archive page.
					 * @hooked - wp_travel_engine_header_filters_template.
					 */
					do_action( 'wp_travel_engine_header_filters' );
				?>
				<div class="wte-category-outer-wrap">
					<?php
						$j = 1;
						$view_mode = wp_travel_engine_get_archive_view_mode();
						if ( 'grid' === $view_mode ) {
							$view_class = class_exists( 'Wte_Advanced_Search' ) ? 'col-2 category-grid' : 'col-3 category-grid';
						} else {
							$view_class = 'category-list';
						}
						echo '<div class="category-main-wrap '. esc_attr( $view_class ) .'">';						
							/**
							 * wp_travel_engine_featured_trips_sticky hook
							 * Hook for the featured trips sticky section
							 * @hooked wte_featured_trips_sticky
							 */
							do_action( 'wp_travel_engine_featured_trips_sticky', $view_mode );
						
							while( have_posts() ) : the_post();
								$details = wte_get_trip_details( get_the_ID() );
								$details['j'] = $j;
								wte_get_template( 'content-'.$view_mode.'.php', $details );
								$j++;
							endwhile;
						echo '</div>';
					?>
				</div>
				<div id="loader" style="display: none">
					<div class="table">
						<div class="table-grid">
							<div class="table-cell">
								<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
        <div class="trip-pagination">
			<?php
			the_posts_pagination( array(
				'prev_text'          => __( 'Previous', 'wp-travel-engine' ),
				'next_text'          => __( 'Next', 'wp-travel-engine' ),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'wp-travel-engine' ) . ' </span>',
			) );
			?>
        </div>
    <?php
    }
	/**
     * Oter wrap of the archive.
     *
     * @since    1.0.0
     */
	function wp_travel_engine_trip_archive_outer_wrapper_close()
	{ ?>

		</div><!-- wp-travel-inner-wrapper -->
		</div><!-- .wp-travel-trip-wrapper -->
	<?php
	}
}
new Wp_Travel_Engine_Archive_Hooks();
