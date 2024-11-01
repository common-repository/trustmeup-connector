<?php

namespace TrustMeUp\Ajax_Routes;

use \TrustMeUp\Options;

defined( 'ABSPATH' ) || exit;

class Fields_Save extends \TrustMeUp\Ajax_Route {
	/**
	 * Validate the /admin/fields-save/ AJAX request.
	 *
	 * @return true|\WP_Error
	 */
	public function is_valid() {
		if ( ! wp_verify_nonce( $this->get_request()->get_header( 'X-WP-Nonce' ), 'wp_rest' ) ) {
			return new \WP_Error( 'unauthorized_access', __( 'You can\'t do that.', 'trustmeup' ), [ 'status' => 403 ] );
		}

		if ( empty( $this->get_param( 'fields' ) ) ) {
			return new \WP_Error( 'missing_fields', __( 'You need to provide some fields.', 'trustmeup' ), [ 'status' => 401 ] );
		}

		return true;
	}

	/**
	 * Process a valid request: save fields.
	 *
	 * @return array
	 */
	public function process() {
		$fields = $this->get_param( 'fields' );

		foreach ( $fields as $name => $value ) {
			Options\update_plugin_option( sanitize_title( $name ), sanitize_text_field( $value ) );
		}

		return $this->response( $fields );
	}

	/**
	 * Maybe do stuff after fields saving and return a response object.
	 *
	 * @param array $fields
	 * @return array
	 */
	protected function response( $fields ) {
		if ( 
			false
			|| isset( $fields['api_client_id'], $fields['api_password'] )
			|| isset( $fields['api_client_id_beta'], $fields['api_password_beta'] )
		) {
			$token = apply_filters( 'tmu/fetch_merchant_token', null, true );

			if ( is_wp_error( $token ) || is_null( $token ) ) {
				return [
					'success' => false,
					'message' => __( 'TrustMeUp authentication failed. Please verify your client ID and password.', 'trustmeup' ),
					'fields'  => [
						'merchant_token' => null,
					]
				];
			}

			return [
				'success' => true,
				'message' => __( 'TrustMeUp authentication successful. Synchronization will start soon.', 'trustmeup' ),
				'fields'  => [
					'merchant_token' => $token,
				]
			];
		}

		return [
			'success' => true,
			'message' => __( 'Settings saved.', 'trustmeup' ),
		];
	}
}
