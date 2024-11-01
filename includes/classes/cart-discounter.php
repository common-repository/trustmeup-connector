<?php

namespace TrustMeUp;

use \TrustMeUp\Config;
use \TrustMeUp\API_TrustMeUp;

defined( 'ABSPATH' ) || exit;

class Cart_Discounter {
	protected $cart           = null;
	protected $transient_key  = null;
	protected $trustmeup_cart = [];

	/**
	 * Construct the object with a WooCommerce cart.
	 *
	 * @param \WC_Cart $cart
	 */
	public function __construct( \WC_Cart $cart ) {
		$this->cart = $cart;

		$this->prepare_data();
	}

	//===========================================================
	//                                                           
	//   ####    #####  ######  ######  #####  #####     ####  
	//  ##       ##       ##      ##    ##     ##  ##   ##     
	//  ##  ###  #####    ##      ##    #####  #####     ###   
	//  ##   ##  ##       ##      ##    ##     ##  ##      ##  
	//   ####    #####    ##      ##    #####  ##   ##  ####   
	//                                                           
	//===========================================================

	/**
	 * Get cart.
	 *
	 * @return \WC_Cart
	 */
	protected function get_cart() {
		return $this->cart;
	}

	/**
	 * Get customer OTP.
	 *
	 * @return string|null
	 */
	protected function get_otp() {
		return isset( $_COOKIE[ Config::OTP_COOKIE_NAME ] ) ? sanitize_text_field( $_COOKIE[ Config::OTP_COOKIE_NAME ] ) : null;
	}

	/**
	 * Construct the TMU cart array argument with product/price/quanitity/external_product_id/external_product_name keys.
	 *
	 * @return void
	 */
	protected function get_trustmeup_cart() {
		return $this->trustmeup_cart;
	}

	/**
	 * Get the transient name where the TrustMeUp order data is stored.
	 *
	 * @return string
	 */
	public static function get_trustmeup_order_transient_name( $transient_key ) {
		return sprintf( '%1$s%2$s', Config::TRUSTMEUP_ORDER_TRANSIENT_PREFIX, $transient_key );
	}

	/**
	 * Get the TrustMeUp order data for this cart, stored in a transient, if existent.
	 *
	 * @return object|null
	 */
	public function get_trustmeup_order() {
		$data = get_transient( self::get_trustmeup_order_transient_name( $this->transient_key ) );

		if ( empty( $data ) || ! isset( $data->pac_amount ) ) {
			return null;
		}

		return $data;
	}

	//===========================================================
	//                                                           
	//    ###     ####  ######  ##   #####   ##     ##   ####  
	//   ## ##   ##       ##    ##  ##   ##  ####   ##  ##     
	//  ##   ##  ##       ##    ##  ##   ##  ##  ## ##   ###   
	//  #######  ##       ##    ##  ##   ##  ##    ###     ##  
	//  ##   ##   ####    ##    ##   #####   ##     ##  ####   
	//                                                           
	//===========================================================

	/**
	 * Construct the TrustMeUp cart argument (for API) and save the transient name of this cart.
	 *
	 * @return void
	 */
	protected function prepare_data() {
		$transient_name       = [ 'trustmeup_api_order' ];
		$this->trustmeup_cart = [];

		foreach ( $this->get_cart()->get_cart_contents() as $item_key => $item ) {
			$product           = $item['data'];
			$product_id        = (int) $product->get_id();
			$quantity          = (int) $item['quantity'];
			$price_per_product = ( $item['line_subtotal'] + $item['line_subtotal_tax'] ) / $quantity;
			$trustmeup_id      = trustmeup_get_trustmeup_product_id( $product );

			if ( is_null( $trustmeup_id ) ) {
				continue;
			}

			$trustmeup_product_id = $trustmeup_id;
			$external_product_id  = $product->get_sku();

			if ( empty( $external_product_id ) ) {
				$external_product_id = $product->get_id();
			}

			$this->trustmeup_cart[] = (object) [
				'product'               => $trustmeup_product_id,
				'price'                 => round( $price_per_product, 2 ),
				'quantity'              => $quantity,
				'external_product_id'   => $external_product_id,
				'external_product_name' => $product->get_name(),
			];

			$transient_name[] = "{$quantity}x{$product_id}";
		}

		$this->transient_key = md5( implode( '_', $transient_name ) . $this->get_otp() );
	}

	/**
	 * Update the cart based on the user OTP + products in the cart and create the TMU order.
	 *
	 * @return void
	 */
	public function create_trustmeup_order() {
		// Do we have a Merchant Token, a Customer OTP and a non-empty TMU cart?
		if (
			! trustmeup_can_run()
			|| is_null( $this->get_otp() )
			|| empty( $this->get_trustmeup_cart() )
			|| ( class_exists( 'WC_Subscriptions_Cart' ) && \WC_Subscriptions_Cart::cart_contains_subscription() )
		) {
			return;
		}

		// If we already have a transient for this cart AND we should not force refresh, do nothing.
		if ( ! isset( $_GET['recalc_tmu_discount'] ) && ! is_null( $this->get_trustmeup_order() ) ) {
			return;
		}

		$api = new API_TrustMeUp();

		//$api->delete_past_orders();

		$order = $api->create_order( $this->get_trustmeup_cart() );

		if ( ! is_wp_error( $order ) && isset( $order->pac_amount ) ) {
			// Create provision (freeze PACs).
			//$api->initiate_checkout();

			$this->save_order_in_session( $order );

			do_action( 'tmu/trustmeup_order_created', $order, $this->transient_key );
		} else {
			$this->delete_session();
		}
	}

	/**
	 * Save TrustMeUp Order details in session.
	 *
	 * @param object $order
	 * @return void
	 */
	public function save_order_in_session( $order ) {
		unset( $order->cart_items );

		set_transient( self::get_trustmeup_order_transient_name( $this->transient_key ), $order, DAY_IN_SECONDS );
		WC()->session->set( 'trustmeup_order_key', $this->transient_key );
	}

	/**
	 * Delete current session.
	 *
	 * @return void
	 */
	public function delete_session() {
		delete_transient( self::get_trustmeup_order_transient_name( $this->transient_key ) );
		WC()->session->set( 'trustmeup_order_key', null );
	}

	/**
	 * Void TrustMeUp Cart and delete local transient + session key.
	 *
	 * @param string $transient_key
	 * @return void
	 */
	public static function clear_user_cart( $transient_key = null ) {
		$api = new API_TrustMeUp();
		$api->delete_past_orders();
		$api->delete_cart();

		// Clear transient.
		$trustmeup_order_key = ! is_null( $transient_key ) ? $transient_key : WC()->session->get( 'trustmeup_order_key' );

		if ( ! empty( $trustmeup_order_key ) ) {
			delete_transient( self::get_trustmeup_order_transient_name( $trustmeup_order_key ) );
			WC()->session->set( 'trustmeup_order_key', null );
		}
	}
}
