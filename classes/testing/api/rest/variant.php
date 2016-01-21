<?php
/**
 * REST API route for variants
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\api\rest;


use ingot\testing\utility\helpers;

class variant extends route {
	/**
	 * Identify object type for this route collection
	 *
	 * @since 0.4.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $what = 'variants';

	/**
	 * Register routes
	 *
	 * @since 0.4.0
	 */
	public function register_routes() {

		$namespace = $this->make_namespace();
		$base      = $this->base();
		register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)/conversion', array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'conversion' ),
					'permission_callback' => array( $this, 'check_session_nonce' ),
					'args'                => $this->win_args()
				),
			)
		);

		register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)', array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'check_session_nonce' ),
					'args'                => array(
						'context'             => array(
							'default' => 'view',
						),
						'id'                  => array(
							'description'       => __( 'ID of variant', 'ingot' ),
							'type'              => 'integer',
							'default'           => 1,
							'sanitize_callback' => 'absint',
							'required'          => true
						),
						'ingot_session_nonce' => array(
							'type'     => 'string',
							'required' => true,
							'default'  => '0',
						),

					),
				),
			)
		);
	}


	/**
	 * Get a variant's HTML
	 *
	 * @since 0.4.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Request
	 */
	public function get_item( $request ) {
		return $this->not_yet_response();
		$context = $request->get_param( 'context' );
		$url = $request->get_url_params();
		$id = helpers::v( 'ID', $url, 0 );
		if( 0 == absint( $id ) || ! is_array( \ingot\testing\crud\variant::read( $id ) ) ) {
			return new \WP_Error( 'ingot-invalid-test' );
		}elseif( 'context' != 'view' ) {
			return new \WP_Error( 'ingot-test-context-invalid' );
		}else{
			if ( 'view' == $context ) {
				$test = \ingot\testing\crud\variantP::read( $id );
				$html = ingot_click_test( $test );

				return ingot_rest_response( $html );
			}
		}



	}

	/**
	 * Record a conversion
	 *
	 * @since 0.4.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Request
	 */
	public function conversion( $request ){
		if( ! ingot_is_no_testing_mode() ) {
			$id = $request->get_param( 'id' );
			ingot_register_conversion( $id, $request->get_param( 'ingot_session_ID' ) );
		}

		return ingot_rest_response(
			[ 'message' => ':)' ],
		    200
		);

	}



	/**
	 * Params for the /click endpoint
	 *
	 * @since 0.4.0
	 *
	 * @return array
	 */
	public function win_args() {
		$args = array(
			'id'                   => array(
				'description'        => __( 'ID of Test', 'ingot' ),
				'type'               => 'integer',
				'required'            => true,
				'sanitize_callback'  => 'absint',
			),
			'ingot_session_nonce' => array(
				'type'     => 'string',
				'required' => true,
				'default' => '0'
			),
			'ingot_session_ID' => array(
				'type' => 'string',
				'required' => true,
				'default' => '0'
			)

		);

		return $args;
	}
	/**
	 * Permissions check for get_item.
	 *
	 * Always returns true if request is for context view. Requires permission if not.
	 *
	 * @since 0.2.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return bool
	 */
	public function get_item_permissions_check( $request ) {
		$context = $request->get_param( 'context' );
		if ( 'view' == $context ) {
			return true;

		}else{
			return $this->get_items_permissions_check( $request );

		}
	}

	/**
	 * Verify session nonce when registering a click
	 *
	 * @since 0.4.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return bool
	 */
	public function check_session_nonce( $request ){
		$verified = ingot_verify_session_nonce( $request->get_param('ingot_session_nonce' ) );
		return $verified;
	}


}
