<?php

namespace TrustMeUp;

defined( 'ABSPATH' ) || exit;

class Ajax {
	/**
	 * Store our admin routes slugs and classes.
	 *
	 * @var array
	 */
	protected $admin_routes = [
		'fields-save'         => \TrustMeUp\Ajax_Routes\Fields_Save::class,
		'products-connect'    => \TrustMeUp\Ajax_Routes\Products_Connect::class,
		'products-disconnect' => \TrustMeUp\Ajax_Routes\Products_Disconnect::class,
		'products-refresh'    => \TrustMeUp\Ajax_Routes\Products_Refresh::class,
		'products-resync'     => \TrustMeUp\Ajax_Routes\Products_Resync::class,
	];

	/**
	 * Initialize the public API routes.
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Get the slugs/classes admin routes.
	 *
	 * @return array
	 */
	protected function get_admin_routes() {
		return (array) $this->admin_routes;
	}

	/**
	 * Instantiate the API class in charge of validating and processing the action.
	 *
	 * @param \WP_REST_Request $request
	 * @return void
	 */
	protected function get_admin_route_corresponding_class( $request ) {
		$class = $this->get_admin_routes()[ $request->get_param( 'action' ) ];

		return new $class( $request );
	}

	/**
	 * Register the API routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			'trustmeup/v1',
			'admin/(?P<action>[a-zA-Z0-9-]+)',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'process_admin_routes' ],
				'permission_callback' => [ $this, 'protect_admin_routes' ],
				'validate_callback'   => [ $this, 'validate_admin_routes_args' ],
			]
		);
	}

	/**
	 * Protect the admin route.
	 *
	 * @return boolean
	 */
	public function protect_admin_routes() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Validate admin routes arguments.
	 *
	 * @param \WP_REST_Request $request
	 * @return boolean
	 */
	public function validate_admin_routes_args( \WP_REST_Request $request ) {
		return ! in_array( false, [
			'action' => in_array( $request->get_param( 'action' ), array_keys( $this->get_admin_routes() ), true ),
		], true );
	}

	/**
	 * Process the /admin/<action> requests.
	 *
	 * @param \WP_REST_Request $request
	 * @return void
	 */
	public function process_admin_routes( \WP_REST_Request $request ) {
		$api_class = $this->get_admin_route_corresponding_class( $request );
		$valid     = $api_class->is_valid();

		if ( is_wp_error( $valid ) ) {
			$response = $valid;
		} else {
			$response = $api_class->process();
		}

		return new \WP_REST_Response( $this->shape_response( $response ) );
	}

	/**
	 * Shape a response: flatten a WP_Error, or send the success object as is.
	 *
	 * @param mixed $response
	 * @return array
	 */
	protected function shape_response( $response ) {
		if ( is_wp_error( $response ) ) {
			$response = [
				'success' => false,
				'error'   => true,
				'message' => $response->get_error_message(),
			];
		}

		if ( isset( $response['message'] ) ) {
			$response['message'] = html_entity_decode( $response['message'] );
		}

		return $response;
	}
}
