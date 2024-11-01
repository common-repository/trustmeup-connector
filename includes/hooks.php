<?php

namespace TrustMeUp\Hooks;

defined( 'ABSPATH' ) || exit;

/**
 * Handle custom trustmeup_id/has_trustmeup_id params for wc_get_products();
 *
 * @param array $query_args - WP_Query args
 * @param array $query_vars - WC_Product_Query args
 * @return array $query_args
 */
function register_custom_wc_query_parameters( $query_args, $query_vars ) {
	if ( ! empty( $query_vars['trustmeup_id'] ) ) {
		$query_args['meta_query'][] = [
			'key'     => 'trustmeup_product_id',
			'value'   => sanitize_text_field( $query_vars['trustmeup_id'] ),
			'compare' => '=',
		];
	}

	if ( 
		isset( $query_vars['has_trustmeup_id'] )
		&& (
			(bool) $query_vars['has_trustmeup_id'] === false
			|| ! empty( $query_vars['has_trustmeup_id'] )
		)
	) {
		$query_args['meta_query'][] = [
			'key'     => 'trustmeup_product_id',
			'compare' => (bool) $query_vars['has_trustmeup_id'] ? 'EXISTS' : 'NOT EXISTS',
		];
	}

	return $query_args;
}
add_filter( 'woocommerce_product_data_store_cpt_get_products_query', __NAMESPACE__ . '\\register_custom_wc_query_parameters', 10, 2 );
