<?php

namespace TrustMeUp\Ajax_Routes;

use \TrustMeUp\Products_Connector;

defined( 'ABSPATH' ) || exit;

class Products_Disconnect extends \TrustMeUp\Ajax_Route {
	/**
	 * Validate the /admin/products-disconnect/ AJAX request.
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
	 * Process a valid request: reset products.
	 *
	 * @return array
	 */
	public function process() {
		$product_id = $this->get_param( 'product' );

		if ( empty( $product_id ) ) {
			$disconnected = Products_Connector::disconnect_all();

			return [
				'success' => true,
				'message' => sprintf( __( '%1$d products have been disconnected from their TrustMeUp equivalent.', 'trustmeup' ), count( $disconnected ) ),
				'fields'  => [
					'connected_products' => Products_Connector::get_trustmeup_products_with_sync_data(),
				],
			];
		} else {
			$disconnected = Products_Connector::disconnect( $product_id );
			$message      = sprintf( __( '%1$d WooCommerce products have been disconnected.', 'trustmeup' ), $disconnected );

			if ( ! $disconnected ) {
				$message = __( 'Error while trying to disconnect a product.', 'trustmeup' );
			}

			return [
				'success' => $disconnected,
				'message' => $message,
				'fields'  => [
					'connected_products' => Products_Connector::get_trustmeup_products_with_sync_data(),
				],
			];
		}
	}
}
