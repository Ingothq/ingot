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


use ingot\testing\crud\group;
use ingot\testing\tests\click\destination\cookie;
use ingot\testing\tests\click\destination\init;
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
		register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)/tests', array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'session' ),
					'permission_callback' => array( $this, 'verify_session_nonce' ),
					'args'                => $this->args()
				),
			)

		);

		register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)/track', array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'update' ),
					'permission_callback' => array( $this, 'verify_session_nonce' ),
					'args'                => array(
						'ingot_session_nonce' => array(
							'type'     => 'string',
							'required' => true,
						),
						'click_url' => array(
							'type' => 'string',
							'required' => true,
							'sanitize_callback' => 'esc_url_raw'
						),
					)
				),
			)

		);

		register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)/cookies',
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'check_cookies' ],
					'permission_callback' => [ $this, 'verify_session_nonce' ],
					'args' => [
						'ingot_session_nonce' => [
							'type'     => 'string',
							'required' => true,
						],
						'cookies'             => [
							'type'              => 'array',
							'required'          => true,
							'sanitize_callback' => [ $this, 'prepare_cookie_key' ]
						],
					],
			]
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
		if( ingot_is_no_testing_mode() ) {
			$data[ 'ingot_ID' ] = $session[ 'ingot_ID' ];
			$data[ 'tests' ] = [];
			$data[ 'session_ID' ] = $session[ 'ID' ];
			$data[ 'cookies' ] = $this->cookie_initial_data();
			return $data;
		}

		$tests = [];
		if ( ! is_array( $session ) ) {
			return $session;
		} else {
			if ( \ingot\testing\crud\session::is_used( $session[ 'ID' ] ) ) {
				$new_session_args = [];
				if( 0 != $request->get_param( 'ingot_id' ) ){
					$new_session_args[ 'ingot_ID' ] = $request->get_param( 'ingot_id' );
				}elseif( 0 < absint( $session[ 'ingot_ID' ] ) ) {
					$new_session_args[ 'ingot_ID' ] = $session[ 'ingot_ID' ];
				}

				if( 0 != get_current_user_id() ) {
					$new_session_args[ 'uID' ] = get_current_user_id();
				}

				$data[ 'session_ID' ] = \ingot\testing\crud\session::create( $new_session_args, true );
				if ( is_numeric( $data[ 'session_ID' ] ) ) {
					$session = \ingot\testing\crud\session::read( (int) $data[ 'session_ID' ] );


					if ( ! empty( $request->get_param( 'test_ids' ) ) ) {
						foreach ( $request->get_param( 'test_ids' ) as $variant_id ) {
							$html = '';

							if( is_array( $group = group::get_by_variant_id( $variant_id ) ) ) {
								$html = ingot_click_test( $group );
							}

							$tests[] = [
								'html' => $html,
								'ID'   => $variant_id
							];
						}

					}
				}

			}else{
				\ingot\testing\crud\session::mark_used( $session[ 'ID' ] );
				$data[ 'session_ID' ] = $session[ 'ID' ];


			}

			$data[ 'ingot_ID' ] = $session[ 'ingot_ID' ];
			$data[ 'tests' ] = $tests;
			$data[ 'cookies' ] = $this->proccess_cookie_check( $request->get_param( 'cookies' ) );
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
	 * Track session results -- used for failed conversions.
	 *
	 * @since 0.3.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function update( $request ) {
		$session = $this->get_session_by_url_params( $request );
		if ( is_wp_error( $session ) ) {
			return $this->response( $session );
		}

		if ( ! empty( $request->get_param( 'click_url' ) ) && 'undefined' != $request->get_param( 'click_url' ) ) {
			$session[ 'click_url' ] = $request->get_param( 'click_url' );
			$session[ 'used' ] = true;
			if( 0 !== ( $userID = get_current_user_id() ) ) {
				$session[ 'click_url' ] = $userID;
			}

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
				'type'              => 'array',
				'default'           => array(),
				'sanitize_callback' => array( $this, 'make_array_values_numeric' )
			),
			'ingot_id'            => array(
				'type'              => 'integer',
				'default'           => 0,
				'sanitize_callback' => 'absint'
			),
			'cookies'             => [
				'type'              => 'array',
				'required'          => true,
				'sanitize_callback' => [ $this, 'prepare_cookie_key' ]
			],
		];
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
	public function verify_session_nonce( $request ) {
		$allowed = util::verify_session_nonce( $request );
		return (bool) $allowed;

	}

	/**
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return array|mixed|null|object|void
	 */
	protected function get_session_by_url_params( $request ) {
		$url = $request->get_url_params();
		$id  = helpers::v( 'id', $url, 0 );
		if ( 0 != absint( $id ) && is_array( $session = \ingot\testing\crud\session::read( $id ) ) ) {
			return $session;
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
			return ingot_rest_response( $session, 500 );

		} else {
			return ingot_rest_response( $session, 200 );
		}
	}

	/**
	 * Callback for cookie check route.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function check_cookies( \WP_REST_Request $request ) {
		$cookies = $request->get_param( 'cookies' );
		$response = $this->proccess_cookie_check( $cookies );
		return ingot_rest_response( $response );
	}

	/**
	 * Prepare and sanatize cookie param
	 *
	 * @since 1.1.1
	 *
	 * @param array $cookies
	 *
	 * @return array
	 */
	public function prepare_cookie_key( $cookies ) {
		if ( ! empty( $cookies ) ) {
			$_cookies = [ ];
			foreach ( $cookies as $i => $cookie ) {
				if ( isset( $cookie[ 'g' ], $cookie[ 'v' ] ) ) {
					$_cookies[ absint( $cookie[ 'g' ] ) ] = absint( $cookie[ 'v' ] );
				} else {
					unset( $cookies[ $i ] );
				}
			}

			$cookies = $_cookies;
		}

		return $cookies;
	}

	/**
	 * Handle the cookie check
	 *
	 * @since 1.1.0
	 *
	 * @param array $cookies
	 *
	 * @return array
	 */
	protected function proccess_cookie_check( $cookies ) {
		$should_be = init::get_tests();
		$response = array_fill_keys(
			[
				'remove',
				'add',
				'wrong_variant'
			], []
		);

		if( $should_be == $cookies ) {
			return $response;
		}

		$response = $this->cookie_initial_data();
		if ( ! empty( $cookies ) ) {
			foreach ( $cookies as $g => $v ) {
				if ( ! isset( $should_be[ $g ] ) ) {
					$response[ 'remove' ][ $g ] = $v;
				} elseif ( isset( $should_be[ $g ] ) && $v != $should_be[ $g ] ) {
					$response[ 'wrong_variant' ][ $g ] = $should_be[ $g ];
				}
			}
		}

		if ( ! empty( $should_be ) ) {
			foreach ( $should_be as $g => $v ) {
				if ( ! isset( $cookies[ $g ] ) ) {
					$response[ 'add' ] = $v;
				}
			}

			return $response;
		}

		return $response;

	}

	/**
	 * @return array
	 */
	protected function cookie_initial_data() {
		$response = [
			'remove'        => [ ],
			'add'           => [ ],
			'wrong_variant' => [ ],
			'length'        => ingot_cookie_time( true ),
			'path'          => COOKIEPATH,
			'domain'        => COOKIE_DOMAIN,
		];

		return $response;
	}
}
