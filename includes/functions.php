<?php

/**
 * No namespace for this file, so we can directly access some utility functions.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Can we run the TrustMeUp API logic?
 *
 * @return boolean
 */
function trustmeup_can_run() {
	$merchant_token = \TrustMeUp\Options\get_plugin_option( 'merchant_token', null );

	return ( ! empty( $merchant_token ) && \TrustMeUp\meets_requirements() );
}

/**
 * Get the TrustMeUp product ID of a WC_Product.
 *
 * @param WC_Product|integer $product
 * @return mixed
 */
function trustmeup_get_trustmeup_product_id( $product ) {
	if ( is_numeric( $product ) ) {
		$product = wc_get_product( $product );

		if ( ! $product ) {
			return null;
		}
	}

	// If it's a variation, get parent product.
	if ( $product->is_type( 'variation' ) ) {
		$product = wc_get_product( $product->get_parent_id() );
	}

	$id = $product->get_meta( 'trustmeup_product_id', true );

	return ! empty( $id ) ? $id : null;
}

/**
 * Get the API Client ID from options (BETA or PROD).
 *
 * @param string $type 'client_id' or 'password'
 * @return string
 */
function trustmeup_get_api_client( $type = 'client_id' ) {
	// Default keys are for production.
	$key = sprintf( 'api_%1$s', sanitize_text_field( $type ) );

	if ( \TrustMeUp\Options\get_plugin_option( 'api_environment', 'prod' ) === 'beta' ) {
		$key .= '_beta';
	}

	return \TrustMeUp\Options\get_plugin_option( $key );
}