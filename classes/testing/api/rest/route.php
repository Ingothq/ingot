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

abstract class route extends \WP_REST_Controller  {

	/**
	 * Marks what object this is for.
	 *
	 * @since 0.0.5
	 *
	 * @var string
	 */
	private $what;


	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @since 0.0.6
	 */
	public function register_routes() {
		$namespace = $this->make_namespace();
		$base = str_replace( '_', '-', $this->what );
		register_rest_route( $namespace, '/' . $base, array(
			array(
				'methods'         => \WP_REST_Server::READABLE,
				'callback'        => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'            => $this->args()
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

		return current_user_can( permissions::get_for( $this->what, 'read' ) );
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
		return array();
	}

	/**
	 * Utility function to make all keys of an array integers
	 *
	 * Note: will remove 0
	 *
	 * @param $array
	 *
	 * @return array
	 */
	public function make_array_values_numeric( $array ) {
		if ( ! empty( $array ) ) {
			foreach( $array as $k => $v ) {
				if ( 0 == absint( $v ) ) {
					unset( $array[ $v ] );
				}else{
					$array[ $k ] = (int) $v;
				}

			}

		}

		if ( empty( $array ) ) {
			$array = array();
		}

		return $array;
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
	 * Ensure a boolen is a boolean
	 *
	 * @since 0.0.7
	 * @param $value
	 *
	 * @return bool
	 */
	public function validate_boolean( $value ) {
		if( in_array( $value, array( true, false, 'TRUE', 'FALSE', 'true', 'false', 1, 0, '1', '0' ) ) ){
			return true;
		}

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
		$version   = '1';
		$namespace = 'ingot/v' . $version;

		return $namespace;
	}
}
