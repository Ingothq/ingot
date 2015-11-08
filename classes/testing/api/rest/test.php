<?php
/**
 * REST API Route for tests
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */


namespace ingot\testing\api\rest;


use ingot\testing\crud\crud;
use ingot\testing\crud\group;
use ingot\testing\crud\sequence;
use ingot\testing\ingot;
use ingot\testing\tests\flow;



class test extends route {

	/**
	 * What this endpoint is for
	 *
	 * @since 0.0.6
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected  $what = 'test';

	/**
	 * Update one item from the collection
	 *
	 * @since 0.0.6
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Request
	 */
	public function _update_item( $request ) {
		$params = $request->get_params();
		unset( $params[0] );
		unset( $params[1] );

		$updated = \ingot\testing\crud\test::update( $params, $params[ 'id' ] );
		if ( ! is_wp_error( $updated ) && $updated ) {
			$item = \ingot\testing\crud\test::read( $updated );
			return rest_ensure_response( $item, 200 );
		}else{
			if ( ! is_wp_error( $updated ) ) {
				$updated = __( 'FAIL', 'ingot' );
			}
			return rest_ensure_response( $updated, 500 );

		}


	}

	/**
	 * Create one item from the collection
	 *
	 * @since 0.0.6
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Request
	 */
	public function _create_item( $request ) {
		$params = $request->get_params();
		unset( $params[0] );
		unset( $params[1] );
		$created = \ingot\testing\crud\test::create( $params );
		if ( ! is_wp_error( $created ) && is_numeric( $created ) ) {
			$item = \ingot\testing\crud\test::read( $created );
			return rest_ensure_response( $item, 200 );
		}else{
			if ( ! is_wp_error( $created ) ) {
				$created = __( 'FAIL', 'ingot' );
			}

			return rest_ensure_response( $created, 500 );
		}

	}

	/**
	 * Params for most requests
	 *
	 * @todo improve so works with schema
	 *
	 * @since 0.0.6
	 *
	 * @param bool|true $require_id
	 *
	 * @return array
	 */
	public function args( $require_id = true ) {
		$args = array(
			'id'                   => array(
				'description'        => __( 'ID of Test', 'ingot' ),
				'type'               => 'integer',
				'default'            => 1,
				'sanitize_callback'  => 'absint',
			),
			'name'              => array(
				'description'        => __( 'Name of Test ', 'ingot' ),
				'type'               => 'string',
				'default'            => '',
				'sanitize_callback'  => array( $this, 'strip_tags' ),
				'required'           => true,
			),
			'text'              => array(
				'description'        => __( 'Text to use', 'ingot' ),
				'type'               => 'string',
				'default'            => '',
				'sanitize_callback'  => array( $this, 'strip_tags' ),
				'required'           => true,
			),

		);

		if ( $require_id ){
			$args[ 'ID' ][ 'required' ] = true;
		}

		return $args;
	}

	/**
	 * Params for the /click endpoint
	 *
	 * @todo improve so works with schema
	 *
	 * @since 0.0.6
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
			'sequence'              => array(
				'description'        => __( 'ID of sequence', 'ingot' ),
				'type'               => 'integer',
				'required'            => true,
				'sanitize_callback'  => 'absint',
			),
			'click_nonce'              => array(
				'description'        => __( 'Nonce for verifying click', 'ingot' ),
				'type'               => 'string',
				'default'            => rand(),
				'sanitize_callback'  => array( $this, 'strip_tags' ),
				'required'           => true,
			),

		);

		return $args;
	}

	/**
	 * Process click
	 *
	 * @since 0.0.6
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Request
	 */
	public function register_click( $request ){
		$id = $request->get_param( 'id' );
		$sequence = $request->get_param( 'sequence' );
		$increased = flow::increase_victory( $id, $sequence  );
		if( is_wp_error( $increased ) ) {
			return rest_ensure_response( $increased, 500 );
		}else{
			rest_ensure_response( __( 'Click registered.', 'ingot' ), 200 );
		}

	}

	/**
	 * Verify click nonce from request
	 *
	 * @since 0.0.6
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return bool
	 */
	public function verify_test_nonce( $request ) {
		$sequence_id = $request->get_param( 'sequence' );
		$sequence = sequence::read( $sequence_id );
		if( is_array( $sequence ) ) {
			$group = $sequence[ 'group_ID' ];
		}else{
			return false;
		}

		$verify = \ingot\ui\util::verify_click_nonce(
			$request->get_param( 'click_nonce' ),
			$request->get_param( 'id' ),
			$sequence_id,
			$group
		);

		return $verify;

	}

	/**
	 * Register extra routes
	 *
	 * @since 0.0.8
	 *
	 * @access protected
	 */
	protected function register_more_routes() {
		$namespace = $this->make_namespace();
		$base = $this->what;
		register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)/click', array(
			array(
				'methods'         => 'POST',
				'callback'        => array( $this, 'register_click' ),
				'permission_callback' => array( $this, 'verify_test_nonce' ),
				'args'            => $this->win_args()
			)
		) );
	}



}
