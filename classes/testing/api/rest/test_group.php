<?php
/**
 * API route for test groups.
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\api\rest;


use ingot\testing\crud\group;
use ingot\testing\crud\test;
use ingot\testing\utility\helpers;

class test_group extends route {

	/**
	 * Identify object type for this route collection
	 *
	 * @since unknown
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $what = 'group';

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		$version = '1';
		$namespace = 'ingot/v' . $version;
		$base = 'test-group';
		register_rest_route( $namespace, '/' . $base, array(
			array(
				'methods'         => \WP_REST_Server::READABLE,
				'callback'        => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'            => array(

				),
			),
			array(
				'methods'         => \WP_REST_Server::CREATABLE,
				'callback'        => array( $this, 'create_item' ),
				'permission_callback' => array( $this, 'create_item_permissions_check' ),
				'args'            => $this->args( false )
			),
		) );
		register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)', array(
			array(
				'methods'         => \WP_REST_Server::READABLE,
				'callback'        => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'            => array(
					'context'          => array(
						'default'      => 'view',
					),
				),
			),
			array(
				'methods'         => \WP_REST_Server::EDITABLE,
				'callback'        => array( $this, 'update_item' ),
				'permission_callback' => array( $this, 'update_item_permissions_check' ),
				'args'            => $this->args( false )
			),
			array(
				'methods'  => \WP_REST_Server::DELETABLE,
				'callback' => array( $this, 'delete_item' ),
				'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				'args'     => array(
					'force'    => array(
						'default'      => false,
					),
					'all' => array(
						'default' => false,
					),
					'id' => array(
						'default' => 0,
						'sanatization_callback' => 'absint'
					)
				),
			),
		) );
		register_rest_route( $namespace, '/' . $base . '/schema', array(
			'methods'         => \WP_REST_Server::READABLE,
			'callback'        => array( $this, 'get_public_item_schema' ),
		) );
	}

	/**
	 * Update one item from the collection
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Request
	 */
	public function update_item( $request ) {

		$url = $request->get_url_params( );
		$id = helpers::v( 'id', $url, 0 );
		$existing = group::read( $id );
		if( ! is_array( $existing ) ){
			if ( is_wp_error( $existing ) ) {
				return $existing;
			}

			return rest_ensure_response( array(), 404 );
		}

		$params = $request->get_params();


		if( ! empty( $params[ 'tests' ] ) ) {
			foreach( $params[ 'tests' ] as $test ) {
				$test_id = helpers::v( 'id', $test, 0 );
				$test = $this->prepare_click_test_meta( $test );
				if( absint( $test_id ) > 0 ) {
					$_id = test::update( $test, $test_id );
				}else{
					$_id = test::create( $test );
				}

				if(  is_numeric( $_id ) && ! in_array( $_id, $existing[ 'order' ] ) ){
					$data[ 'order' ][] = $_id;
				}

			}

		}

		//@todo allow for more fields to be updated
		foreach( array(
			'name',
			'click_type',
			'link'
		) as $field ){
			if( ! empty( $params[ $field ] ) ){
				$data[ $field ] = $params[ $field ];
			}
		}

		$data = array_merge(  $existing, $data );
		$updated = group::update( $data, $id );
		if ( ! is_wp_error( $updated ) && $updated ) {
			$item = group::read( $updated );
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
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Request
	 */
	public function create_item( $request ) {
		$params = $request->get_params();
		unset( $params[0] );
		unset( $params[1] );

		if( ! empty( $params[ 'tests' ] ) ) {
			foreach( $params[ 'tests' ] as $test ) {
				$test_id = helpers::v( 'id', $test, 0 );
				$test = $this->prepare_click_test_meta( $test );
				unset( $test[ 'id' ] );
				if( absint( $test_id ) > 0 ) {
					$_id = test::update( $test, $test_id );
				}else{

					$_id = test::create( $test );
				}


				if ( 0 != $_id && is_numeric( $_id ) ) {
					$params['order'][] = $_id;
				}


			}

		}

		foreach( $params[ 'order' ] as $i => $_id ) {
			if( 0 == absint( $_id ) ) {
				unset( $params[ 'order' ][ $i ] );
			}
		}

		$params[ 'order' ] = array_values( $params[ 'order' ] );

		unset( $params['tests'] );
		$created = group::create( $params );
		if ( ! is_wp_error( $created ) && is_numeric( $created ) ) {
			$item = group::read( $created );
			return rest_ensure_response( $item, 200 );
		}else{
			if ( ! is_wp_error( $created ) ) {
				$created = __( 'FAIL', 'ingot' );
			}

			return rest_ensure_response( $created, 500 );
		}

	}

	/**
	 * Delete a group
	 *
	 * @since 0.0.5
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Request
	 */
	public function delete_item( $request ) {
		if( $request->get_param( 'all' ) ) {
			$id = 'all';
		}else{
			$id = $request->get_url_params( 'id' );
		}

		$deleted = group::delete( $id );
		if( $deleted || is_array( $deleted ) ) {
			return rest_ensure_response( $id );
		}else{
			return rest_ensure_response( new \WP_Error( 'unknown-item', __( 'Can not delete a non-existent item', 'ingot' ) ), 404 );
		}

	}



	public function args( $require_id = true ) {
		$args =  array(
			'id'                   => array(
				'description'        => __( 'ID of test group', 'ingot' ),
				'type'               => 'integer',
				'default'            => 1,
				'sanitize_callback'  => 'absint',
			),
			'type'               => array(
				'description'        => __( 'Type of Test Group', 'ingot' ),
				'type'               => 'string',
				'default'            => 'link',
				'sanitize_callback'  => array( $this, 'strip_tags' ),
				'required'           => 'true',
			),
			'name'              => array(
				'description'        => __( 'Name of Test Group', 'ingot' ),
				'type'               => 'string',
				'default'            => '',
				'sanitize_callback'  => array( $this, 'strip_tags' ),
				'required'           => true,
			),
			'sequences'  => array(
				'description'        => __( 'Sequences', 'ingot' ),
				'type'               => 'array',
				'default'            => array(),
				'sanitize_callback'  => array( $this, 'make_array_values_numeric' ),
			),
			'order' => array(
				'description'        => __( 'Order of Tests', 'ingot' ),
				'type'               => 'array',
				'default'            => array(),
				'sanitize_callback'  => array( $this, 'make_array_values_numeric' ),
			),
			'initial'  => array(
				'description'        => __( 'Number of times to run test at 50/50', 'ingot' ),
				'type'               => 'integer',
				'default'            => 50,
				'sanitize_callback'  => 'absint',
			),
			'selector'  => array(
				'description'        => __( 'Selector for text tests.', 'ingot' ),
				'type'               => 'text',
				'default'            => '',
				'sanitize_callback'  => array( $this, 'strip_tags' ),
			),
			'threshold'  => array(
				'description'        => __( 'Threshold to end a test.', 'ingot' ),
				'type'               => 'integer',
				'default'            => 20,
				'sanitize_callback'  => 'absint',
			),
			'click_type' => array(
				'description'        => __( 'Type of click test', 'ingot' ),
				'type'               => 'text',
				'default'            => 'text',
				'sanitize_callback'  => array( $this, 'strip_tags' ),
				'required'           => true,
			),
			'link' => array(
				'description'        => __( 'Type of click test', 'ingot' ),
				'type'               => 'text',
				'default'            => '',
				'sanitize_callback'  => array( $this, 'url' ),
				'required'           => true,
			),
			'page' => array(
				'description'        => __( 'Page of resu.', 'ingot' ),
				'type'               => 'integer',
				'default'            => 1,
				'sanitize_callback'  => 'absint',
			),
			'button_color' => array(
				'description'        => __( 'Default button color for button tests', 'ingot' ),
				'type'               => 'string',
				'default'            => array(),
				'sanitize_callback'  => array( 'ingot\testing\utility\helpers', 'prepare_color' ),
			),
			'tests' => array(
				'description'       => __( 'Tests to add to group', 'ingot' ),
				'type'              => 'array',
				'default'           => array()
			)

		);

		if ( $require_id ){
			$args[ 'ID' ][ 'required' ] = true;
		}

		return $args;
	}

}
