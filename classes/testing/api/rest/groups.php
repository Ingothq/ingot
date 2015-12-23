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
use ingot\testing\crud\sequence;
use ingot\testing\crud\test;
use ingot\testing\crud\variant;
use ingot\testing\types;
use ingot\testing\utility\helpers;
use ingot\testing\api\rest\util;

class groups extends route {

	/**
	 * Identify object type for this route collection
	 *
	 * @since 0.4.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $what = 'groups';

	/**
	 * Register routes
	 *
	 * @since 0.4.0
	 */
	public function register_routes() {
		parent::register_routes();
		$namespace = $this->make_namespace();
		$base = $this->base();
		register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)/stats', array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_stats' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(),
				),
			)
		);
	}

	/**
	 * Get groups
	 *
	 * @since 0.4.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_items( $request ) {
		$args = array(
			'page' => $request->get_param( 'page' ),
			'limit' => $request->get_param( 'limit' )
		);

		$groups = group::get_items( $args );
		if( ! empty( $groups ) ) {
			foreach( $groups as $i => $group ) {
				$groups[ $i ] = $this->prepare_group( $group, $request->get_param( 'context' ) );
			}
		}

		return ingot_rest_response( $groups, 200, (int) group::total() );

	}

	/**
	 * Create a group
	 *
	 * @since 0.4.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function create_item( $request ) {

		$group_args = $this->prepare_item_for_database( $request );

		$variants = helpers::v_sanitized( 'variants', $group_args, []  );
		$variants_ids = [];
		if( ! empty( $variants ) ) {
			foreach( $variants as $variant ) {
				$_variant_id = variant::create( $variant );
				if( is_wp_error( $_variant_id ) ) {
					return $_variant_id;
				}

				$variants_ids[] = $_variant_id;
			}

		}
		$group_args[ 'variants' ] = $variants_ids;
		$id = group::create( $group_args );
		$item = group::read( $id );
		if ( is_array( $item ) ) {
			$item = $this->prepare_group( $item, $request->get_param( 'context' ) );
		}

		return ingot_rest_response( $item, 201, 1 );

	}

	/**
	 * Get a group
	 *
	 * @since 0.4.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_item( $request ){

		$url = $request->get_url_params( );
		$id = helpers::v( 'id', $url, 0 );
		$group = group::read( $id );
		if( ! is_array( $group ) ){
			if ( is_wp_error( $group ) ) {
				return $group;
			}

			return ingot_rest_response(
				[ 'message' => esc_html__( 'No group found', 'ingot') ]
			);
		}


		return ingot_rest_response(
			$this->prepare_group( $group, $request->get_param( 'context') ),
			201 );

	}


	/**
	 * Update a group
	 *
	 * @since 0.4.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function update_item( $request ){
		$url = $request->get_url_params( );
		$id = helpers::v( 'id', $url, 0 );
		$existing = group::read( $id );
		if( ! is_array( $existing ) ){
			if ( is_wp_error( $existing ) ) {
				return $existing;
			}

			return ingot_rest_response(
				[ 'message' => esc_html__( 'No group found', 'ingot') ]
			);
		}

		//doing this since it runs parse_args
		$obj = new \ingot\testing\object\group( $existing );
		$group_args = $this->prepare_item_for_database( $request );

		$obj->update_group( $group_args );
		return ingot_rest_response(
			$this->prepare_group( $obj->get_group_config(), $request->get_param( 'context' ) ), 201 );

	}


	/**
	 * Delete a group
	 *
	 * @since 0.4.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function delete_item( $request ){
		$url = $request->get_url_params( );
		$id = helpers::v( 'id', $url, 0 );
		$existing = group::read( $id );
		if( ! is_array( $existing ) ){
			if ( is_wp_error( $existing ) ) {
				return $existing;
			}

			return ingot_rest_response(
				[ 'message' => esc_html__( 'No group found', 'ingot') ]
			);
		}

		$deleted = group::delete( $id );
		if( $deleted ) {
			return ingot_rest_response(
				[ 'message' => esc_html__( 'Group Deleted', 'ingot') ],
				204
			);
		}

	}

	/**
	 * Get stats for a group
	 *
	 * @since 0.4.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_stats( $request ){
		$url = $request->get_url_params( );
		$id = helpers::v( 'id', $url, 0 );
		$existing = group::read( $id );
		if( ! is_array( $existing ) ){
			if ( is_wp_error( $existing ) ) {
				return $existing;
			}

			return ingot_rest_response(
				[ 'message' => esc_html__( 'No group found', 'ingot') ]
			);
		}

		$obj = new \ingot\testing\object\group( $existing );
		return ingot_rest_response( $obj->get_stats(), 200 );

	}



	public function args( $require_id = true ) {
		$args =  array(
			'id'                   => array(
				'description'        => __( 'ID of group', 'ingot' ),
				'type'               => 'integer',
				'default'            => 1,
				'sanitize_callback'  => 'absint',
			),
			'name'              => array(
				'description'        => __( 'Name of Test Group', 'ingot' ),
				'type'               => 'string',
				'default'            => '',
				'sanitize_callback'  => array( $this, 'strip_tags' ),
				'required'           => true,
			),
			'type'               => array(
				'description'        => __( 'Type of Test Group', 'ingot' ),
				'type'               => 'string',
				'default'            => 'click',
				'validation_callback' => array( $this, 'allowed_type' ),
				'sanitize_callback'  => array( $this, 'strip_tags' ),
				'required'           => 'true',
			),
			'link' => array(
				'description'        => __( 'Link for content tests', 'ingot' ),
				'type'               => 'text',
				'default'            => '',
				'sanitize_callback'  => array( $this, 'url' ),
				'required'           => true,
			),
			'page' => array(
				'description'        => __( 'Page of results.', 'ingot' ),
				'type'               => 'integer',
				'default'            => 1,
				'sanitize_callback'  => 'absint',
			),
			'variants' => array(
				'description'       => __( 'Tests to add to group', 'ingot' ),
				'type'              => 'array',
				'default'           => array(),
			),
			'meta' => array(
				'description'       => __( 'Meta data for group', 'ingot' ),
				'type'              => 'array',
				'default'           => array(),
			),


		);

		if ( $require_id ){
			$args[ 'ID' ][ 'required' ] = true;
		}

		return $args;
	}



	protected function prepare_group( $group, $context = 'view' ){
		unset( $group[ 'levers' ] );
		if ( 'admin' == $context ) {
			$group = $this->prepare_group_in_admin_context( $group );
		}

		return $group;
	}

	/**
	 * Prepare a group if in admin context
	 *
	 * @since 0.2.0
	 *
	 * @access protected
	 *
	 * @param array $group Group Config
	 *
	 * @return array Group config with additional data for making admin
	 */
	protected function prepare_group_in_admin_context( $group ) {
		return $group;
	}



	protected function allowed_fields(){
		$fields = \ingot\testing\crud\group::get_all_fields();
		unset( $fields[ 'levers' ] );
		return $fields;
	}

	/**
	 * Prepare the item for create or update operation
	 *
	 * @since 0.4.0
	 *
	 * @param \WP_REST_Request $request Request object
	 * @return \WP_Error|object $prepared_item
	 */
	protected function prepare_item_for_database( $request ) {
		$group_args = $request->get_params();
		$allowed    = $this->allowed_fields();
		foreach ( $group_args as $key => $value ) {
			if ( is_numeric( $key ) || ! in_array( $key, $allowed ) ) {
				unset( $group_args[ $key ] );
			}
		}

		unset( $group_args[ 'levers' ] );


		return $group_args;
	}


}
