<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/wp-travel-engine/account/form-lostpassword.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Notices.
wp_travel_engine_print_notices();
?>
<div class="wpte-lrf-wrap wpte-forgot-pass">
	<div class="wpte-lrf-top">
		<div class="wpte-lrf-head">
			<?php if ( has_custom_logo() ) : ?>
				<div class="wpte-lrf-logo">
					<?php the_custom_logo(); ?>
				</div>
			<?php endif; ?>
			<div class="wpte-lrf-desc">
				<p><?php echo apply_filters( 'wp_travel_engine_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'wp-travel-engine' ) ); ?></p><?php // @codingStandardsIgnoreLine ?>
			</div>
		</div>
		<form method="post" class="wpte-lrf">
			<div class="wpte-lrf-field lrf-email">
				<input required type="text" name="user_login" id="user_login" value="" placeholder="<?php echo esc_attr__('email or username', 'wp-travel-engine'); ?>">
			</div>
			<?php do_action( 'wp_travel_engine_lostpassword_form' ); ?>
			<input type="hidden" name="wp_travel_engine_reset_password" value="true" />
			<div class="wpte-lrf-field lrf-submit">
				<input type="submit" name="wp_travel_engine_reset_password_submit" value="<?php echo esc_attr__('Reset Password', 'wp-travel-engine'); ?>">
			</div>
			<?php wp_nonce_field( 'wp_travel_engine_lost_password' ); ?>
		</form>
	</div>
</div>
<?php
