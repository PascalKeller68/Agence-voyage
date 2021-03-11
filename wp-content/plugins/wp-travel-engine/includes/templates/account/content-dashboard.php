<?php
/**
 * User dashboard template.
 *
 * @package WP_Travel
 */

// Print Errors / Notices.
wp_travel_engine_print_notices();

// Set User.
$current_user         = $args['current_user'];
$user_dashboard_menus = wp_travel_engine_sort_array_by_priority( $args['dashboard_menus'] );
$bookings             = get_user_meta( $current_user->ID, 'wp_travel_engine_user_bookings', true );
$bookings_glance      = false;
// Resverse Chronological Order For Bookings.
if ( ! empty( $bookings ) && is_array( $bookings ) ) {
	$bookings        = array_reverse( $bookings );
	$bookings_glance = array_slice( $bookings, 0, 5 );
}

$biling_glance_data = get_user_meta( $current_user->ID, 'wp_travel_engine_customer_billing_details', true );

?>
<div class="wpte-lrf-wrap wpte-dashboard">
	<div class="wpte-lrf-head">
		<div class="wpte-lrf-userprogile">
			<a href="javascript:void(0);">
				<?php echo get_avatar( $current_user->user_email ); ?>
				<b><?php echo esc_html( $current_user->display_name ); ?></b>
				<i class="fas fa-caret-down"></i>
			</a>
			<div class="lrf-userprofile-popup">
				<a class="lrf-userprofile-logout" href="<?php echo esc_url( wp_logout_url( wp_travel_engine_get_page_permalink_by_id( wp_travel_engine_get_dashboard_page_id() ) ) ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="13.845" height="13.845" viewBox="0 0 13.845 13.845"><path d="M7.672,1a6.672,6.672,0,1,0,6.247,8.992A.278.278,0,1,0,13.4,9.8a6.116,6.116,0,1,1,0-4.257.278.278,0,1,0,.521-.191A6.668,6.668,0,0,0,7.672,1ZM7.36,5.17a.278.278,0,0,0-.165.078L4.97,7.473a.278.278,0,0,0,0,.4L7.195,10.1a.283.283,0,1,0,.4-.4L5.848,7.951h8.219a.278.278,0,1,0,0-.556H5.848L7.594,5.648A.278.278,0,0,0,7.36,5.17Z" transform="translate(-0.75 -0.75)" fill="#000000" stroke="#000000" stroke-width="0.5"/></svg>
					<?php esc_html_e( 'Log Out', 'wp-travel-engine' ); ?>
				</a>
			</div>
		</div>
		<?php if ( has_custom_logo() ) : ?>
			<div class="wpte-lrf-logo">
				<?php the_custom_logo(); ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="wpte-lrf-content-area">
		<div class="wpte-lrf-sidebar">
			<?php foreach ( $user_dashboard_menus as $key => $menu ) : ?>
				<?php if($menu['menu_class']=='lrf-bookings' && isset($_GET['action']) && $_GET['action'] =='partial-payment'){
					$cndtnl_active_class = 'active';
				 }else if($menu['menu_class']=='lrf-dashboard' && !isset($_GET['action'])){
					$cndtnl_active_class = 'active';
				}else{
					$cndtnl_active_class = '';
				}?>
				<a class="wpte-lrf-tab <?php echo esc_attr( $menu['menu_class'] ); ?> <?php echo $cndtnl_active_class;?>" href="Javascript:void(0);"><?php echo esc_html( $menu['menu_title'] ); ?></a>
			<?php endforeach; ?>
		</div><!-- .wpte-lrf-sidebar -->

		<div class="wpte-lrf-main">
			<?php foreach ( $user_dashboard_menus as $key => $menu ) : ?>
				<?php if($menu['menu_class']=='lrf-bookings' && isset($_GET['action']) && $_GET['action'] =='partial-payment'){
					$cndtnl_active_class = 'active';
				 }else if($menu['menu_class']=='lrf-dashboard' && !isset($_GET['action'])){
					$cndtnl_active_class = 'active';
				}else{
					$cndtnl_active_class = '';
				}?>
				<div class="wpte-lrf-tab-content lrf-<?php echo esc_attr( $key ); ?>-content <?php echo esc_attr( $menu['menu_class'] ); ?> <?php echo $cndtnl_active_class;?>">
					<?php
						if ( ! empty( $menu['menu_content_cb'] ) ) {
							$args['bookings_glance']    = $bookings_glance;
							$args['biling_glance_data'] = $biling_glance_data;
							$args['bookings']           = $bookings;
							call_user_func( $menu['menu_content_cb'], $args );
						}
					?>
				</div><!-- .lrf-dashboard-content -->
			<?php endforeach; ?>
		</div><!-- .wpte-lrf-main -->
	</div><!-- .wpte-lrf-content-area -->
</div><!-- .wpte-lrf-wrap -->
<?php
