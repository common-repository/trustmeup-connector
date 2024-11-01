<?php

namespace TrustMeUp\Ajax_Routes;

use \TrustMeUp\Products_Connector;
use \TrustMeUp\Helpers;

defined( 'ABSPATH' ) || exit;

class Products_Connect extends \TrustMeUp\Ajax_Route {
	/**
	 * Validate the /admin/products-connect/ AJAX request.
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
		$trustmeup_product_id = $this->get_param( 'trustmeup_product' );
		$woo_products_ids     = $this->get_param( 'woo_products' );
		$connected            = 0;

		// Disconnect all Woo products from this TMU product first.
		Products_Connector::disconnect( $trustmeup_product_id );

		// Save the new connections.
		foreach ( $woo_products_ids as $woo_product_id ) {
			$connection = Products_Connector::connect( $woo_product_id, $trustmeup_product_id );

			if ( $connection ) {
				$connected++;
			}
		}

		if ( $connected > 0 ) {
			Helpers\log( sprintf( __( '%1$d WooCommerce products have been connected to %2$s.', 'trustmeup' ), $connected, $trustmeup_product_id ), 'info' );
		}

		return [
			'success' => true,
			'message' => sprintf( __( '%1$d product(s) successfully connected.', 'trustmeup' ), $connected ),
			'fields'  => [
				'connected_products' => Products_Connector::get_trustmeup_products_with_sync_data(),
			],
		];
	}
}
