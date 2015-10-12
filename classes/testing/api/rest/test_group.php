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

class test_group extends route {

	private $what = 'group';

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
		$params = $request->get_params();
		unset( $params[0]);
		unset( $params[1] );
		$id = $params[ 'id' ];
		foreach( $params as $param => $value ) {
			if( empty( $value ) ) {
				unset( $params[ $param ] );
			}

		}

		$updated = group::update( $params, $id );
		if ( ! is_wp_error( $updated ) && $updated ) {
			$item = group::read( $updated );
			return rest_ensure_response( $item, 200 );
		}else{
			if ( ! is_wp_error( $updated ) ) {
				$updated = __( 'FAIL', 'ingot' );
			}


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
		);

		if ( $require_id ){
			$args[ 'ID' ][ 'required' ] = true;
		}

		return $args;
	}

}
