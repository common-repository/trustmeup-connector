<?php

namespace TrustMeUp\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Get a specific option value.
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function get_plugin_option( $key, $default = null ) {
	return get_option( "trustmeup_{$key}", $default );
}

/**
 * Update a specific option.
 *
 * @param string $key
 * @param mixed $value
 * @param boolean $autoload
 * @return void
 */
function update_plugin_option( $key, $value, $autoload = false ) {
	update_option( "trustmeup_{$key}", $value, $autoload );
}

/**
 * Delete a specific option.
 *
 * @param string $key
 * @return void
 */
function delete_plugin_option( $key ) {
	delete_option( "trustmeup_{$key}" );
}

/**
 * Do we already have a value for a specific option?
 *
 * @param string $key
 * @return boolean
 */
function has_plugin_option( $key ) {
	return ! empty( get_plugin_option( $key ) );
}
