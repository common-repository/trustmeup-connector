<?php

namespace TrustMeUp\Ajax_Routes;

use \TrustMeUp\Scheduler;
use \TrustMeUp\Products_Connector;

defined( 'ABSPATH' ) || exit;

class Products_Resync extends \TrustMeUp\Ajax_Route {
	/**
	 * Validate the /admin/products-resync/ AJAX request.
	 *
	 * @return true|\WP_Error
	 */
	public function is_valid() {
		if ( ! wp_verify_nonce( $this->get_request()->get_header( 'X-WP-Nonce' ), 'wp_rest' ) ) {
			return new \WP_Error( 'unauthorized_access', __( 'You can\'t do that.', 'trustmeup' ), [ 'status' => 403 ] );
		}

		if ( ! trustmeup_can_run() ) {
			return new \WP_Error( 'missing_token', __( 'Token not found: please check your TrustMeUp credentials in the Settings tab.', 'trustmeup' ), [ 'status' => 403 ] );
		}

		return true;
	}

	/**
	 * Process a valid request: save fields.
	 *
	 * @return array
	 */
	public function process() {
		Scheduler\save_trustmeup_products_list();

		return [
			'success' => true,
			'message' => __( 'TrustMeUp products have been synchronized again.', 'trustmeup' ),
			'fields'  => [
				'connected_products' => Products_Connector::get_trustmeup_products_with_sync_data(),
			],
		];
	}
}
