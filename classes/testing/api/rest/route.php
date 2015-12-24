<?php
/**
 * Abstract class for making API routes
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\api\rest;


use ingot\permissions;
use ingot\testing\ingot;
use ingot\testing\types;

abstract class route  {

	/**
	 * Marks what object this is for.
	 *
	 * @since 0.0.5
	 *
	 * @var string
	 */
	protected $what;


	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @since 0.0.6
	 */
	public function register_routes() {
		$namespace = $this->make_namespace();
		$base = $this->base();
		register_rest_route( $namespace, '/' . $base, array(
			array(
				'methods'         => \WP_REST_Server::READABLE,
				'callback'        => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'            => array(
					'page' => array(
						'default' => 1,
						'sanitize_callback'  => 'absint',
					),
					'limit' => array(
						'default' => 10,
						'sanitize_callback'  => 'absint',
					)
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
					)
				),
			),
			array(
				'methods'         => \WP_REST_Server::EDITABLE,
				'callback'        => array( $this, 'update_item' ),
				'permission_callback' => array( $this, 'update_item_permissions_check' ),
				'args'            => $this->args( false )
			),
			array(
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_item' ),
				'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				'args'                => array(
					'force' => array(
						'default'  => false,
						'required' => false,
					),
					'all'   => array(
						'default'  => false,
						'required' => false,
					),
					'id'    => array(
						'default'               => 0,
						'sanatization_callback' => 'absint'
					)
				),
			),
		) );
		register_rest_route( $namespace, '/' . $base . '/schema', array(
			'methods'         => \WP_REST_Server::READABLE,
			'callback'        => array( $this, 'get_public_item_schema' ),
		) );

		$this->register_more_routes();


	}

	/**
	 * Get a collection of items
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_items( $request ) {
		return $this->not_yet_response();

	}

	/**
	 * Get one item from the collection
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_item( $request ) {
		return $this->not_yet_response();

	}

	/**
	 * Create one item from the collection
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Request
	 */
	public function create_item( $request ) {
		return $this->not_yet_response();

	}

	/**
	 * Update one item from the collection
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Request
	 */
	public function update_item( $request ) {
		return $this->not_yet_response();

	}

	/**
	 * Delete one item from the collection
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Request
	 */
	public function delete_item( $request ) {
		return $this->not_yet_response();

	}

	/**
	 * Check if a given request has access to get items
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|bool
	 */
	public function get_items_permissions_check( $request ) {
		if ( INGOT_DEV_MODE ) {
			return true;

		}


		$cap = permissions::get_for( $this->what, 'read' );
		$can = current_user_can(  $cap );
		return $can;
	}

	/**
	 * Check if a given request has access to get a specific item
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|bool
	 */
	public function get_item_permissions_check( $request ) {
		if ( INGOT_DEV_MODE ) {
			return true;

		}

		return $this->get_items_permissions_check( $request );
	}

	/**
	 * Check if a given request has access to create items
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|bool
	 */
	public function create_item_permissions_check( $request ) {
		if ( INGOT_DEV_MODE ) {
			return true;

		}

		return current_user_can( permissions::get_for( $this->what, 'create' ) );

	}

	/**
	 * Check if a given request has access to update a specific item
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|bool
	 */
	public function update_item_permissions_check( $request ) {
		return $this->create_item_permissions_check( permissions::get_for( $this->what, 'update' ) );
	}

	/**
	 * Check if a given request has access to delete a specific item
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|bool
	 */
	public function delete_item_permissions_check( $request ) {
		if ( INGOT_DEV_MODE ) {
			return true;

		}

		return $this->create_item_permissions_check( permissions::get_for( $this->what, 'delete' ) );
	}

	protected function not_yet_response() {
		$error = new \WP_Error( 'not-implemented-yet', __( 'Route Not Yet Implemented :(', 'ingot' )  );
		return new \WP_REST_Response( $error, 501 );

	}

	/**
	 * Prepare the item for create or update operation
	 *
	 * @param \WP_REST_Request $request Request object
	 * @return \WP_Error|object $prepared_item
	 */
	protected function prepare_item_for_database( $request ) {
		return array();
	}

	/**
	 * Prepare the item for the REST response
	 *
	 * @param mixed $item WordPress representation of the item.
	 * @param \WP_REST_Request $request Request object.
	 * @return mixed
	 */
	public function prepare_item_for_response( $item, $request ) {
		return $item;
	}

	/**
	 * Utility function to make all keys of an array integers (recursively)
	 *
	 * @since 0.0.6
	 *
	 * @param $array
	 *
	 * @return array
	 */
	public function make_array_values_numeric( $array ) {
		return \ingot\testing\utility\helpers::make_array_values_numeric( $array );
	}

	public function strip_tags( $value, $request, $field ) {
		return strip_tags( $value );
	}

	/**
	 * Get the query params for collections
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return $this->args();
	}

	public function args( $require_id = true ) {
		return array();
	}

	public function url( $value, $request, $field ) {
		$url =  wp_sanitize_redirect( $value );
		return $url;
	}

	/**
	 * Ensure a boolean is a boolean
	 *
	 * @since 0.0.7
	 * @param $value
	 *
	 * @return bool
	 */
	public function validate_boolean( $value ) {
		if( in_array( $value, array( true, false, 'TRUE', 'FALSE', 'true', 'false', 1, 0, '1', '0' ) ) ){
			return true;
		}else{
			return false;
		}

	}

	/**
	 * Utility function to make all keys of an array floats (recursively)
	 *
	 * @since 0.0.6
	 *
	 * @param array $array
	 *
	 * @return array
	 */
	public function make_array_values_floats( $array ){
		if ( ! empty( $array ) ) {
			foreach( $array as $k => $v ) {
				if ( ! is_array( $v ) ) {
					if ( ! is_numeric( $v ) ) {
						$array[ $k ] = 0;
					} else {
						$array[ $k ] = (float) $v;
					}
				}else{
					$array[ $k ] = $this->make_array_values_floats( $v );
				}

			}

		}

		if ( empty( $array ) ) {
			$array = array();
		}

		return $array;
	}

	/**
	 * Generic method, to be used in subclass to add extra routes.
	 *
	 * @since 0.0.8
	 *
	 * @access protected
	 */
	protected function register_more_routes() {

	}

	/**
	 * Make namespace for routes
	 *
	 * @since 0.0.8
	 *
	 * @access protected
	 *
	 * @return string
	 */
	protected function make_namespace() {
		return util::get_namespace();
	}

	/**
	 * Handle fields that go in meta for a click test
	 *
	 * @since 0.1.1
	 *
	 * @access protected
	 *
	 * @param array $params Request params
	 *
	 * @return array Request params
	 */
	protected function prepare_click_test_meta( $params ) {
		if( ! isset( $params[ 'meta' ] ) || empty( $params[ 'meta' ] ) ){
			return $params;
		}
		foreach ( array( 'color', 'background_color', 'color_test_text' ) as $meta ) {
			if ( isset( $params[ 'meta' ][ $meta ] ) && ! empty( $params[ 'meta' ][ $meta ] ) ) {
				$params['meta'][ $meta ] = strip_tags( $params[ 'meta' ][ $meta ] );
			}


		}

		return $params;

	}

	/**
	 * Create route base
	 *
	 * @since 0.3.0
	 *
	 * @return string
	 */
	protected function base() {
		$base = str_replace( '_', '-', $this->what );

		return $base;
	}

	/**
	 * Validate test type
	 *
	 * @since 0.2.0
	 *
	 * @param string $value
	 *
	 * @return bool
	 */
	public function allowed_type( $value ) {
		return in_array( $value, types::allowed_types() );
	}

	/**
	 * Validate sub_type click type
	 *
	 * @since 0.4.0
	 *
	 * @param $value
	 *
	 * @return bool
	 */
	public function allowed_sub_tupe( $value, $request ) {
		$type = $request->get_param( 'type' );
		if( 'click' == $type ) {
			return in_array( $value, types::allowed_click_types() );
		}else{
			return in_array( $value, types::allowed_price_types() );
		}

	}

}
