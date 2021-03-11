<?php
/**
 * Get Dashboard Template.
 */
$bookings_glance    = $args['bookings_glance'];
$biling_glance_data = $args['biling_glance_data'];
?>
<header class="wpte-lrf-header">
	<h2 class="wpte-lrf-title"><?php printf( __( 'Welcome %1$s', 'wp-travel-engine' ), esc_html( $current_user->display_name ) ); ?></h2>
	<div class="wpte-lrf-description">
		<p><?php _e( 'From your account dashboard you can view your recent bookings, manage your billing address and edit your password and account details.', 'wp-travel-engine' ); ?></p>
	</div>
</header>
<div class="wpte-lrf-block-wrap">
	<div class="wpte-lrf-block">
		<div class="wpte-lrf-block-title"><?php esc_html_e( 'My Bookings', 'wp-travel-engine' ); ?></div>
		<?php if ( ! empty( $bookings_glance ) && is_array( $bookings_glance ) ) : ?>
			<div class="wpte-lrf-block-desc">
				<?php echo sprintf( _nx( 'You have completed %1$s booking on our website.', 'You have completed %1$s bookings on our website.', count( $bookings_glance ), 'No. of bookings', 'wp-travel-engine' ), count( $bookings_glance ) ); ?>
			</div>
			<div class="wpte-lrf-btn-wrap">
				<a data-tab="bookings" class="wpte-lrf-btn-transparent wte-dbrd-tab" href="#"><?php _e( 'View Your Bookings', 'wp-travel-engine' ); ?></a>
			</div>
		<?php else : ?>
			<div class="wpte-lrf-block-desc"><?php _e( "You haven't booked any trips yet. Start to book some trips now.", "wp-travel-engine" ); ?></div>
		<?php endif; ?>
	</div>

	<div class="wpte-lrf-block">
		<div class="wpte-lrf-block-title"><?php _e( 'My Address', 'wp-travel-engine' ); ?></div>
		<?php 
			$billing_label = __( 'Edit', 'wp-travel-engine' );
		if ( empty( $biling_glance_data ) ) : 
			$billing_label = __( 'Add', 'wp-travel-engine' );	
		?>
			<div class="wpte-lrf-block-desc">
				<?php _e( "You haven't saved your billing address yet.", 'wp-travel-engine' ) ?>
			</div>
		<?php else : ?>
			<div class="wpte-lrf-block-desc">
				<?php printf( __( '%1$sAddress:%2$s %3$s', 'wp-travel-engine' ), '<strong>', '</strong>', $biling_glance_data['billing_address']  ); ?>
			</div>
			<div class="wpte-lrf-block-desc">
				<?php printf( __( '%1$sCity:%2$s %3$s', 'wp-travel-engine' ), '<strong>', '</strong>', $biling_glance_data['billing_city']  ); ?>
			</div>
			<div class="wpte-lrf-block-desc">
				<?php printf( __( '%1$sCountry:%2$s %3$s', 'wp-travel-engine' ), '<strong>', '</strong>', $biling_glance_data['billing_country']  ); ?>
			</div>
		<?php endif; ?>
			<div class="wpte-lrf-btn-wrap">
				<a data-tab="address" class="wpte-lrf-edit wte-dbrd-tab" href="#">
					<?php echo esc_html( $billing_label ); ?>
					<i class="fas fa-pen"></i>
				</a>
			</div>
	</div>

	<div class="wpte-lrf-block">
		<div class="wpte-lrf-block-title"><?php _e( 'My Account Info', 'wp-travel-engine' ); ?></div>
		<?php 
			$account_label = __( 'Edit', 'wp-travel-engine' );	
		?>
		<div class="wpte-lrf-block-desc"><?php echo esc_html( $current_user->user_email ); ?></div>
		<div class="wpte-lrf-btn-wrap">
			<a data-tab="account" class="wpte-lrf-edit wte-dbrd-tab" href="#">
				<?php echo esc_html( $account_label ); ?>
				<i class="fas fa-pen"></i>
			</a>
		</div>
	</div>
</div>
<?php
