<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/wp-travel-engine/account/form-edit-account.php.
 *
 * HOWEVER, on occasion WP Travel will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://wptravelengine.com
 * @author  WP Travel Engine
 * @package WP Travel Engine/includes/templates
 * @version 1.3.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'wp_travel_engine_before_edit_account_form' ); ?>

<form method="post" class="wpte-lrf-form">

	<?php do_action( 'wp_travel_engine_edit_account_form_start' ); ?>

	<div class="wpte-lrf-field lrf-text">
		<label class="lrf-field-label" for="lrf-first-name"><?php _e( 'First Name:', 'wp-travel-engine' ); ?> </label>
		<input type="text" name="account_first_name" id="lrf-first-name" value="<?php echo esc_attr( $user->first_name ); ?>" />
	</div>

	<div class="wpte-lrf-field lrf-text">
		<label class="lrf-field-label" for="lrf-last-name"><?php _e( 'Last Name:', 'wp-travel-engine' ); ?></label>
		<input type="text" name="account_last_name" id="lrf-last-name" value="<?php echo esc_attr( $user->last_name ); ?>" />
	</div>

	<div class="wpte-lrf-field lrf-email">
		<label class="lrf-field-label" for="lrf-email">Email:</label>
		<input type="email" name="account_email" id="lrf-email" value="<?php echo esc_attr( $user->user_email ); ?>" />
	</div>

	<div class="wpte-lrf-field lrf-toggle">
		<label class="lrf-field-label"><?php _e( 'Change Password:', 'wp-travel-engine' ); ?></label>
		<label class="lrf-toggle-box" for="lrf-change-password">
			<span class="lrf-chkbx-txt"><?php _e( 'On', 'wp-travel-engine' ); ?></span>
			<span class="lrf-chkbx-txt"><?php _e( 'Off', 'wp-travel-engine' ); ?></span>
		</label>
	</div>

	<div class="wpte-lrf-popup">
		<div class="wpte-lrf-field lrf-text">
			<label class="lrf-field-label" for="lrf-current-password"><?php _e( 'Current Password:', 'wp-travel-engine' ); ?> </label>
			<input type="password" name="password_current" id="lrf-current-password" />
		</div>

		<div class="wpte-lrf-field lrf-text">
			<label class="lrf-field-label" for="lrf-new-password"><?php _e( 'New Password:', 'wp-travel-engine' ); ?> </label>
			<input type="password" name="password_1" id="lrf-new-password" />
			<span class="lrf-tooltip"><?php _e( 'Leave blank if you do not want to change password.', 'wp-travel-engine' ); ?></span>
		</div>

		<div class="wpte-lrf-field lrf-text">
			<label class="lrf-field-label" for="lrf-confirm-new-password"><?php _e( 'Confirm New Password:', 'wp-travel-engine' ); ?> </label>
			<input type="password" name="password_2" id="lrf-confirm-new-password" />
			<span class="lrf-tooltip"><?php _e( 'Leave blank if you do not want to change password.', 'wp-travel-engine' ); ?></span>
		</div>
	</div>

	<?php do_action( 'wp_travel_engine_edit_account_form' );

		wp_nonce_field( 'wp_travel_engine_save_account_details', 'wp_account_details_security' ); ?>
		<div class="wpte-lrf-field lrf-submit">
			<input type="submit" class="wpte-lrf-btn" name="wp_travel_engine_save_account_details" value="<?php esc_attr_e( 'Save changes', 'wp-travel-engine' ); ?>">
		</div>
		<input type="hidden" name="action" value="wp_travel_engine_save_account_details" />

	<?php do_action( 'wp_travel_engine_edit_account_form_end' ); ?>

</form>
<?php do_action( 'wp_travel_engine_after_edit_account_form' );
