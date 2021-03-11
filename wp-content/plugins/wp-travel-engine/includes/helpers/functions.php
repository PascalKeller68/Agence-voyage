<?php
/**
 * WTE Helper functions.
 */

use \Firebase\JWT\JWT;

/**
 * Gets value of provided index.
 *
 * @param array  $array Array to pick value from.
 * @param string $index Index.
 * @param any    $default Default Values.
 * @return mixed
 */
function wte_array_get( $array, $index = null, $default = null ) {
	if ( ! is_array( $array ) ) {
		return $default;
	}
	if ( is_null( $index ) ) {
		return $array;
	}
	$multi_label_indices = explode( '.', $index );
	$value               = $array;
	foreach ( $multi_label_indices as $key ) {
		if ( ! isset( $value[ $key ] ) ) {
			$value = $default;
			break;
		}
		$value = $value[ $key ];
	}
	return $value;
}

/**
 * Generates uniq ID.
 *
 * @return void
 */
function wte_uniqid( $length = 16 ) {
	if ( ! isset( $length ) || intval( $length ) <= 8 ) {
		$length = 32;
	}
	if ( function_exists( 'random_bytes' ) ) {
		return bin2hex( random_bytes( $length ) );
	}
	if ( function_exists( 'mcrypt_create_iv' ) ) {
		return bin2hex( mcrypt_create_iv( $length, MCRYPT_DEV_URANDOM ) );
	}
	if ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
		return bin2hex( openssl_random_pseudo_bytes( $length ) );
	}
	return uniqid();
}

/**
 * Generate JWT.
 *
 * @return void
 */
function wte_jwt( array $payload, string $key ) {
	return JWT::encode( $payload, $key );
}

/**
 * Decode JWT.
 */
function wte_jwt_decode( string $jwt, string $key ) {
	return JWT::decode( $jwt, $key, array( 'HS256' ) );
}

/**
 * WTE Log data in json format.
 *
 * @param mixed $data
 * @return void
 */
function wte_log( $data, $name = 'data', $dump = false ) {
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		$data = json_encode( array( $name => $data ), JSON_PRETTY_PRINT );
		error_log( $data ); // phpcs:ignore
		if ( $dump ) {
			var_dump( $data );
		} else {
			return $data;
		}
	}
};

/**
 * Returns Booking Email instance.
 *
 * @return WTE_Booking_Emails
 */
function wte_booking_email() {
	// Mail class.
	require_once plugin_dir_path( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/class-wp-travel-engine-emails.php';
	return new WTE_Booking_Emails();
}
