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
					'callback'            => array( $this, 'session_status' ),
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
		if ( ! is_array( $session ) ) {
			return $session;
		} else {
			if ( \ingot\testing\crud\session::is_used( $session[ 'ID' ] ) ) {
				\ingot\testing\crud\session::mark_used( $session[ 'ID' ] );
				$data[ 'session_ID' ] = $session[ 'ID' ];

			}else{
				$data[ 'session_ID' ] = \ingot\testing\crud\session::create( array() );
				$session = \ingot\testing\crud\session::read( $data[ 'session_ID' ] );

			}

			$data[ 'ingot_ID' ] = $session[ 'ingot_ID' ];

			$tests = [];

			if ( ! empty( $request->get_param( 'test_ids' ) ) ) {
				foreach ( $request->get_param( 'test_ids' ) as $id ) {
					$tests[] = [
						'html' => ingot_click_test( $id ),
						'ID'   => $id
					];
				}

			}

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

		if ( ! empty( $request->get_param( 'click_url' ) ) ) {
			$session[ 'click_url' ] = $request->get_param( 'click_url ' );
			\ingot\testing\crud\session::update( $session, $session[ 'ID' ] );
		}

		return $this->response( $session );

	}

	public function args() {
		return [
			'ingot_session_ID'    => array(
				'type'     => 'string',
				'required' => true,
			),
			'ingot_session_nonce' => array(
				'type'     => 'string',
				'required' => true,
			),
			'test_ids'            => array(
				'type'                => 'array',
				'default'             => array(),
				'validation_callback' => array( $this, 'make_array_values_numeric' )

			),
			'click_url' => array(
				'type' => 'string',
				'required' => false,
				'default' => '0'
			)

		];
	}

	public function update_item_permissions_check( $request ) {
		return $this->verify_session_nonce( $request );
	}

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
		$nonce = $request->get_param( 'ingot_session_nonce' );

		return ingot_verify_session_nonce( $nonce );

	}

	/**
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return array|mixed|null|object|void
	 */
	protected function get_session_by_url_params( $request ) {
		$url = $request->get_url_params();
		$id  = helpers::v( 'ID', $url, 0 );
		if ( absint( $id ) && is_array( \ingot\testing\crud\session::read( $id ) ) ) {
			return \ingot\testing\crud\session::read( $id );
		} else {
			return new \WP_Error( 'ingot-invalid-session' );
		}

	}

	/**
	 * @param $session
	 *
	 * @return mixed
	 */
	protected function response( $session ) {
		if ( is_wp_error( $session ) ) {
			return rest_ensure_response( new \WP_REST_Response( $session, 500 ) );

		} else {
			return rest_ensure_response( $session );
		}
	}
}
