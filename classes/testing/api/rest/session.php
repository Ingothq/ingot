<?php
/**
 * REST API Endpoints for Ingot Session Tracking
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\api\rest;


use ingot\testing\utility\helpers;

class session extends route {

	/**
	 * Marks what object this is for.
	 *
	 * @since 0.3.0
	 *
	 * @var string
	 */
	protected $what = 'sessions';


	/**
	 * Add the "used" route
	 *
	 * @since 0.3.0
	 *
	 * @access protected
	 */
	protected function register_more_routes() {
		$namespace = $this->make_namespace();
		$base      = $this->base();
		register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)/session', array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'session' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => $this->args()
				),
			)

		);
	}

	/**
	 * Check if session has been used
	 *
	 * @since 0.3.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function session( $request ) {
		$session = $this->get_session_by_url_params( $request );
		$tests = [];
		if ( ! is_array( $session ) ) {
			return $session;
		} else {
			if ( \ingot\testing\crud\session::is_used( $session[ 'ID' ] ) ) {
				$data[ 'session_ID' ] = \ingot\testing\crud\session::create( array() );
				$session = \ingot\testing\crud\session::read( $data[ 'session_ID' ] );


				if ( ! empty( $request->get_param( 'test_ids' ) ) ) {
					foreach ( $request->get_param( 'test_ids' ) as $id ) {
						$tests[] = [
							'html' => ingot_click_test( $id ),
							'ID'   => $id
						];
					}

				}

			}else{
				\ingot\testing\crud\session::mark_used( $session[ 'ID' ] );
				$data[ 'session_ID' ] = $session[ 'ID' ];


			}

			$data[ 'ingot_ID' ] = $session[ 'ingot_ID' ];



			$data[ 'tests' ] = $tests;


			return rest_ensure_response( $data );


		}

	}


	/**
	 * Get session details
	 *
	 * @since 0.3.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_item( $request ) {
		$session = $this->get_session_by_url_params( $request );

		return $this->response( $session );

	}

	/**
	 * Update session details
	 *
	 * @since 0.3.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function update_item( $request ) {
		$session = $this->get_session_by_url_params( $request );
		if ( is_wp_error( $session ) ) {
			return $this->response( $session );
		}

		if ( ! empty( $request->get_param( 'click_url' ) ) && 'undefined' != $request->get_param( 'click_url' ) ) {
			$session[ 'click_url' ] = $request->get_param( 'click_url' );
			\ingot\testing\crud\session::update( $session, $session[ 'ID' ], true );
		}

		return $this->response( $session );

	}

	/**
	 * Request arguments
	 *
	 * @since 0.3.0
	 *
	 * @param bool|true $required
	 *
	 * @return array
	 */
	public function args( $required = true ) {
		return [
			'ingot_session_nonce' => array(
				'type'     => 'string',
				'required' => true,
			),
			'test_ids'            => array(
				'type'                => 'array',
				'default'             => array(),
				'sanitize_callback' => array( $this, 'prepare_test_id_array' )

			),
			'click_url' => array(
				'type' => 'string',
				'required' => false,
				'default' => '0'
			)

		];
	}

	/**
	 * Check if a given request has access to update a specific item
	 *
	 * @since 0.3.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|bool
	 */
	public function update_item_permissions_check( $request ) {
		return $this->verify_session_nonce( $request );
	}

	/**
	 * Check if a given request has access to get a specific item
	 *
	 * @since 0.3.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|bool
	 */
	public function get_item_permissions_check( $request ) {
		return $this->verify_session_nonce( $request );
	}


	/**
	 * Verify sessions nonce
	 *
	 * @since 0.3.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	protected function verify_session_nonce( $request ) {
		return util::verify_session_nonce( $request );

	}

	/**
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return array|mixed|null|object|void
	 */
	protected function get_session_by_url_params( $request ) {
		$url = $request->get_url_params();
		$id  = helpers::v( 'id', $url, 0 );
		if ( absint( $id ) && is_array( \ingot\testing\crud\session::read( $id ) ) ) {
			return \ingot\testing\crud\session::read( $id );
		} else {
			return new \WP_Error( 'ingot-invalid-session' );
		}

	}

	/**
	 * Ensure test ID array is a valid and clean array
	 *
	 * @since 0.3.0
	 *
	 * @param string|array $value
	 *
	 * @return array|string
	 */
	public function prepare_test_id_array( $value ) {
		if( empty( $value  ) ){
			$value = [];
		}elseif( is_numeric( $value ) ){
			$value = [ $value ];
		}elseif( false != strpos( $value, ',' ) ) {
			$value = implode( $value );
		}else{
			$value = [];
		}

		$value = $this->make_array_values_numeric( $value );
		return $value;

	}

	/**
	 * Prepare response for session data
	 *
	 * @since 0.3.0
	 *
	 * @access protected
	 *
	 * @param array|\WP_Error $session Session array or error.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	protected function response( $session ) {
		if ( is_wp_error( $session ) ) {
			return rest_ensure_response( new \WP_REST_Response( $session, 500 ) );

		} else {
			return rest_ensure_response( $session );
		}
	}
}
