<?php

namespace TrustMeUp;

defined( 'ABSPATH' ) || exit;

abstract class Ajax_Route {
	protected $request = null;

	/**
	 * Constructor.
	 *
	 * @param \WP_REST_Request $request
	 */
	public function __construct( \WP_REST_Request $request ) {
		$this->request = $request;
	}

	/**
	 * Get WP_Rest_Request object.
	 *
	 * @return \WP_REST_Request
	 */
	public function get_request() {
		return $this->request;
	}

	/**
	 * Get a request parameter.
	 *
	 * @param string $param
	 * @return mixed
	 */
	public function get_param( $param ) {
		return $this->get_request()->get_param( $param );
	}

	/**
	 * Get all request parameters.
	 *
	 * @return array
	 */
	public function get_params() {
		return $this->get_request()->get_params();
	}
}
