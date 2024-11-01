<?php

namespace TrustMeUp;

use \TrustMeUp\Config;
use \TrustMeUp\Options;

defined( 'ABSPATH' ) || exit;

/**
 * This class handles communications with the TrustMeUp API.
 */
class API_TrustMeUp {
	/**
	 * Are we communicating with the BETA or PROD environment?
	 *
	 * @var boolean
	 */
	protected $beta = false;

	/**
	 * Request arguments.
	 *
	 * @var object
	 */
	protected $args = null;

	/**
	 * Response object.
	 *
	 * @var object
	 */
	protected $response = null;

	/**
	 * Construct the API object.
	 */
	public function __construct( $args = [] ) {
		$this->args = $args;
		$this->beta = ( Options\get_plugin_option( 'api_environment', 'prod' ) === 'beta' );
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
	 * Get base API URL.
	 *
	 * @return string
	 */
	public function get_api_url() {
		if ( defined( 'TRUSTMEUP_API_URL' ) ) {
			return TRUSTMEUP_API_URL;
		}

		return $this->beta ? Config::TRUSTMEUP_API_URL_BETA : Config::TRUSTMEUP_API_URL;
	}

	/**
	 * Get all argument.
	 *
	 * @return mixed
	 */
	public function get_args() {
		return $this->args;
	}

	/**
	 * Get a specific argument.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function get_arg( $key ) {
		return isset( $this->args->{$key} ) ? $this->args->{$key} : null;
	}

	/**
	 * Get the whole API response object, or just a key.
	 *
	 * @param string|null $key
	 * @return mixed
	 */
	public function get_response( $key = null ) {
		if ( $key ) {
			return isset( $this->response->{$key} ) ? $this->response->{$key} : null;
		}

		return $this->response;
	}

	/**
	 * Get merchant auth method.
	 *
	 * @return array
	 */
	protected function get_merchant_auth() {
		return [ 'token' => Options\get_plugin_option( 'merchant_token', null ) ];
	}

	/**
	 * Get customer auth method.
	 *
	 * @return array
	 */
	protected function get_customer_auth() {
		return [ 'otp' => isset( $_COOKIE[ Config::OTP_COOKIE_NAME ] ) ? sanitize_text_field( $_COOKIE[ Config::OTP_COOKIE_NAME ] ) : null ];
	}

	//=========================
	//                         
	//    ###    #####   ##  
	//   ## ##   ##  ##  ##  
	//  ##   ##  #####   ##  
	//  #######  ##      ##  
	//  ##   ##  ##      ##  
	//                         
	//=========================

	/**
	 * Send a request to the TrustMeUp API.
	 *
	 * @param string $route
	 * @param array $original_body
	 * @param string $method
	 * @param array $auth Array with 'token' or 'otp' key.
	 * @param boolean $cascade Allow this request to get a new merchant token if it's invalid.
	 * @return object|\WP_Error
	 */
	protected function request( $route = '', $original_body = [], $method = 'POST', $auth = [], $cascade = true ) {
		// Construct body.
		$body = apply_filters( 'tmu/trustmeup_api/request_body', $original_body );

		if ( $method !== 'GET' ) {
			$body = wp_json_encode( $body );
		}

		// Construct headers.
		$headers = [ 'Content-Type' => 'application/json' ];

		if ( isset( $auth['token'] ) ) {
			$headers['Authorization'] = sprintf( 'Token %1$s', $auth['token'] );
		}

		if ( isset( $auth['otp'] ) ) {
			$headers['OTP'] = $auth['otp'];
		}

		$args = apply_filters( 'tmu/trustmeup_api/request_args', [
			'timeout' => 15,
			'body'    => $body,
			'method'  => $method,
			'headers' => $headers,
		] );

		$url             = sprintf( '%1$s%2$s', trailingslashit( $this->get_api_url() ), $route );
		$response        = wp_remote_request( $url, $args );
		$pretty_response = json_decode( wp_remote_retrieve_body( $response ) );

		// Invalid token? Fetch new one.
		if (
			isset( $auth['token'] )
			&& (int) wp_remote_retrieve_response_code( $response ) === 401
			&& isset( $pretty_response->detail )
			&& $pretty_response->detail === 'Invalid token.'
			&& $cascade
			&& ! defined( 'TRUSTMEUP_TOKEN_REFETCHED' )
		) {
			defined( 'TRUSTMEUP_TOKEN_REFETCHED', true );
			$new_token = apply_filters( 'tmu/fetch_merchant_token', null, false );

			if ( ! is_wp_error( $new_token ) && isset( $auth['token'] ) ) {
				$auth['token'] = $new_token;
			}

			$this->request( $route, $original_body, $method, $auth, false );
		}

		do_action( 'tmu/api_response', $pretty_response, $response, $route );

		return is_wp_error( $response ) ? $response : $pretty_response;
	}

	//===================================================================================
	//                                                                                   
	//  ###    ###  #####  #####     ####  ##   ##    ###    ##     ##  ######   ####  
	//  ## #  # ##  ##     ##  ##   ##     ##   ##   ## ##   ####   ##    ##    ##     
	//  ##  ##  ##  #####  #####    ##     #######  ##   ##  ##  ## ##    ##     ###   
	//  ##      ##  ##     ##  ##   ##     ##   ##  #######  ##    ###    ##       ##  
	//  ##      ##  #####  ##   ##   ####  ##   ##  ##   ##  ##     ##    ##    ####   
	//                                                                                   
	//===================================================================================

	/**
	 * Login a Merchant to retrieve an API token.
	 *
	 * @param string $client_id
	 * @param string $password
	 * @return \WP_Error|string
	 */
	public function get_merchant_token( $client_id = '', $password = '' ) {
		$response = $this->request( 'auth/login/', [
			'client_id' => $client_id,
			'password'  => $password,
		] );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( ! isset( $response->token ) ) {
			return new \WP_Error( 'trustmeup_absent_token', __( 'No token found in response.', 'trustmeup' ), [ 'response' => $response ] );
		}

		return $response->token;
	}

	/**
	 * Get a Merchant list of products.
	 *
	 * @param array $args
	 * @return \WP_Error|array
	 */
	public function get_merchant_products( $args = [] ) {
		$products = [];
		$defaults = [ 'limit' => Config::API_PRODUCTS_LIMIT, 'offset' => 0 ];
		$args     = wp_parse_args( $args, $defaults );

		do {
			$response = $this->request(
				add_query_arg( $args, 'merchants/products/' ),
				[],
				'GET',
				$this->get_merchant_auth()
			);

			$products = array_merge( $products, isset( $response->results ) ? (array) $response->results : [] );

			// Fetch next page.
			$args['offset']++;
		} while ( ! is_wp_error( $response ) && isset( $response->next ) && ! is_null( $response->next ) );

		return $products;
	}

	//===============================================================================
	//                                                                               
	//   ####  ##   ##   ####  ######   #####   ###    ###  #####  #####     ####  
	//  ##     ##   ##  ##       ##    ##   ##  ## #  # ##  ##     ##  ##   ##     
	//  ##     ##   ##   ###     ##    ##   ##  ##  ##  ##  #####  #####     ###   
	//  ##     ##   ##     ##    ##    ##   ##  ##      ##  ##     ##  ##      ##  
	//   ####   #####   ####     ##     #####   ##      ##  #####  ##   ##  ####   
	//                                                                               
	//===============================================================================

	/**
	 * Get orders
	 *
	 * @return object
	 */
	public function get_orders() {
		return $this->request(
			'orders/provision/',
			[],
			'GET',
			array_merge( $this->get_merchant_auth(), $this->get_customer_auth() )
		);
	}

	/**
	 * Get user details.
	 *
	 * @return object
	 */
	public function get_user_details() {
		return $this->request(
			'users/user/',
			[],
			'GET',
			array_merge( $this->get_merchant_auth(), $this->get_customer_auth() )
		);
	}

	/**
	 * Delete the user cart.
	 *
	 * @return object
	 */
	public function delete_cart() {
		return $this->request(
			'orders/void-cart/',
			[],
			'POST',
			array_merge( $this->get_merchant_auth(), $this->get_customer_auth() )
		);
	}

	/**
	 * Create an order from the current cart (create provision).
	 *
	 * @param array $cart_items
	 * @return object
	 */
	public function create_order( $cart_items = [] ) {
		return $this->request(
			'orders/orders/',
			[
				'redirect_to' => add_query_arg( [ 'recalc_tmu_discount' => 1 ], wc_get_cart_url() ),
				'cart_items'  => $cart_items,
			],
			'POST',
			array_merge( $this->get_merchant_auth(), $this->get_customer_auth() )
		);
	}

	/**
	 * Initiate checkout: freeze the current order PACs.
	 *
	 * @return object
	 */
	public function initiate_checkout() {
		return $this->request(
			'orders/create-provision/',
			[],
			'POST',
			array_merge( $this->get_merchant_auth(), $this->get_customer_auth() )
		);
	}

	/**
	 * Delete past orders (release orders provisions).
	 *
	 * @return object
	 */
	public function delete_past_orders() {
		return $this->request(
			'orders/release-provision/',
			[],
			'POST',
			array_merge( $this->get_merchant_auth(), $this->get_customer_auth() )
		);
	}

	/**
	 * Complete an order so that PAC are burned.
	 *
	 * @return object
	 */
	public function complete_order() {
		return $this->request(
			'orders/checkout/',
			[],
			'POST',
			array_merge( $this->get_merchant_auth(), $this->get_customer_auth() )
		);
	}
}
