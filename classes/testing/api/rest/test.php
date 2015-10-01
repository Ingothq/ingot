<?php
/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\api\rest;


class test extends route {

	protected  $what = 'test';

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		$version = '1';
		$namespace = 'ingot/v' . $version;
		$base = 'test';
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

		$updated = \ingot\testing\crud\test::create( $params, $params[ 'ID' ] );
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
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Request
	 */
	public function create_item( $request ) {
		$params = $request->get_params();
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

}
