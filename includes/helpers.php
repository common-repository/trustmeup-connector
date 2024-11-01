<?php

namespace TrustMeUp\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Write something in wp-content/debug.log.
 *
 * @param mixed ...$logs
 * @return void
 */
function debug( ...$logs ) {
	if ( defined( 'WP_DEBUG_LOG' ) && true === WP_DEBUG_LOG ) {
		$emojis = [ '🔎 ', '💡 ', '🔦 ', '🔌 ', '🔍 ', '🔧 ', '🔩 ', '🔨 ', '🚧 ', '⚡ ' ];
		$title = str_repeat( $emojis[ array_rand( $emojis ) ], 3 );

		foreach ( $logs as $log ) {
			error_log( '█░█░█░█░█░█░█░█░█░█░█░█░█░█ ' . $title . ' █░█░█░█░█░█░█░█░█░█░█░█░█░█' );
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( print_r( $log, true ) );
			} else {
				error_log( $log );
			}
		}
	}
}

/**
 * Log a specific message
 *
 * @param mixed $message
 * @param string $type
 * @param mixed $extra_data
 * @return void
 */
function log( $message, $type = 'info', $extra_data = null ) {
	$scalar_message = is_scalar( $message ) ? $message : wc_print_r( $message, true );

	if ( function_exists( 'wc_get_logger' ) ) {
		$logger = wc_get_logger();

		if ( method_exists( $logger, $type ) ) {
			$logger->{$type}( $scalar_message, [ 'source' => TMU_BASENAME ] );
		}
	}

	if ( class_exists( 'MSK') ) {
		\MSK::debug( $message );

		if ( $extra_data ) {
			\MSK::debug( $extra_data );
		}
	} else {
		error_log( print_r( $message, true ) );

		if ( $extra_data ) {
			error_log( print_r( $extra_data, true ) );
		}
	}
}

/**
 * Get current URL
 *
 * @return string
 */
function get_current_url() {
	return ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" ) . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
}

/**
 * Insert element after a specific key in array
 *
 * @param string $key
 * @param array $array
 * @param string $new_key
 * @param mixed $new_value
 * @return array
 */
function array_insert_before( $key, array &$array, $new_key, $new_value ) {
	if ( array_key_exists( $key, $array ) ) {
		$new = [];

		foreach ( $array as $k => $value ) {
			if ( $k === $key ) {
				$new[ $new_key ] = $new_value;
			}

			$new[ $k ] = $value;
		}

		return $new;
	}

	return $array;
}

/**
 * Insert element after a specific key in array
 *
 * @param string $key
 * @param array $array
 * @param string $new_key
 * @param mixed $new_value
 * @return array
 */
function array_insert_after( $key, array &$array, $new_key, $new_value ) {
	if ( array_key_exists( $key, $array ) ) {
		$new = [];

		foreach ( $array as $k => $value ) {
			$new[ $k ] = $value;

			if ( $k === $key ) {
				$new[ $new_key ] = $new_value;
			}
		}

		return $new;
	}

	return $array;
}