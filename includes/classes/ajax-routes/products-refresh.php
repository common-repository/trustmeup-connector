<?php

namespace TrustMeUp\Ajax_Routes;

use \TrustMeUp\Scheduler;
use \TrustMeUp\Products_Connector;

defined( 'ABSPATH' ) || exit;

class Products_Refresh extends \TrustMeUp\Ajax_Route {
	/**
	 * Validate the /admin/products-refresh/ AJAX request.
	 *
	 * @return true|\WP_Error
	 */
	public function is_valid() {
		if ( ! wp_verify_nonce( $this->get_request()->get_header( 'X-WP-Nonce' ), 'wp_rest' ) ) {
			return new \WP_Error( 'unauthorized_access', __( 'You can\'t do that.', 'trustmeup' ), [ 'status' => 403 ] );
		}

		return true;
	}

	/**
	 * Process a valid request: save fields.
	 *
	 * @return array
	 */
	public function process() {
		return [
			'success' => true,
			'message' => __( 'Products list refreshed.', 'trustmeup' ),
			'fields'  => [
				'connected_products' => Products_Connector::get_trustmeup_products_with_sync_data(),
			],
		];
	}
}
