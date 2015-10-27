<?php
/**
 * REST API Route for Price Test Groups
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\api\rest;


use ingot\testing\crud\price_group;
use ingot\testing\crud\price_test;
use ingot\testing\utility\helpers;

class price_test_group extends route {

	/**
	 * Identify object type for this route collection
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $what = 'price_group';


	/**
	 * Create an item
	 *
	 * @since 0.0.9
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Request
	 */
	public function create_item( $request ) {
		$params = $request->get_params();
		unset( $params[0] );
		unset( $params[1] );
		$created = price_group::create( $params );
		if ( ! is_wp_error( $created ) && is_numeric( $created ) ) {
			$item = price_group::read( $created );
			return rest_ensure_response( $item, 200 );
		}else{
			if ( ! is_wp_error( $created ) ) {
				$created = __( 'FAIL', 'ingot' );
			}

			return rest_ensure_response( $created, 500 );
		}

	}

	/**
	 * Update one item
	 *
	 * @since 0.0.9
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Request
	 */
	public function update_item( $request ) {
		$params = $request->get_params();
		$url = $request->get_url_params( );
		$id = helpers::v( 'id', $url, 0 );
		$existing = price_group::read( $id );
		if( ! is_array( $existing ) ){
			if ( is_wp_error( $existing ) ) {
				return $existing;
			}

			return rest_ensure_response( array(), 404 );
		}

		//test order
		//@todo deal with removals
		if( ! empty( $params[ 'tests_update' ] ) ){
			foreach( $params[ 'tests_update' ] as $test ){
				$_id = price_test::update( $test, helpers::v( 'ID', $test, 0, 'absint' ) );
			}
		}

		if( ! empty( $params[ 'tests_new' ] ) ){
			foreach( $params[ 'tests_new' ] as $test ){
				$data[ 'test_order' ] = $existing[ 'test_order' ];
				$new_test = price_test::create( $test );;
				if ( ! is_wp_error( $new_test ) ) {
					$data['test_order'][] = $new_test;
				}
			}
		}

		//@todo allow for more fields ot be updated
		foreach( array(
			'group_name',
			'initial',
			'threshold'
		) as $field ){
			if( ! empty( $params[ $field ] ) ){
				$data[ $field ] = $params[ $field ];
			}
		}

		$data = array_merge(  $existing, $data );


		$updated = \ingot\testing\crud\price_group::update( $data, $id, true );
		if ( ! is_wp_error( $updated ) && is_numeric( $updated ) ) {
			$item = \ingot\testing\crud\price_group::read( $updated );
			return rest_ensure_response( $item, 200 );
		}else{
			if ( ! is_wp_error( $updated ) ) {
				$created = __( 'FAIL', 'ingot' );
			}

			return rest_ensure_response( $created, 500 );
		}

	}

	/**
	 * Set arguments for create/update/ queries
	 *
	 * @since 0.0.9
	 *
	 * @param bool|true $require_id
	 *
	 * @return array
	 */
	public function args( $require_id = true ) {
		$args =  array(
			'id'                   => array(
				'description'        => __( 'ID of group', 'ingot' ),
				'type'               => 'integer',
				'default'            => 1,
				'sanitize_callback'  => 'absint',
			),
			'type'               => array(
				'description'        => __( 'Type of Test Group', 'ingot' ),
				'type'               => 'string',
				'default'            => 'link',
				'sanitize_callback'  => array( $this, 'strip_tags' ),
				'validate_callback'  => array( $this, 'validate_type' ),
				'required'           => 'true',
			),
			'plugin'               => array(
				'description'        => __( 'Plugin To Use For Price Test', 'ingot' ),
				'type'               => 'string',
				'default'            => 'link',
				'sanitize_callback'  => array( $this, 'strip_tags' ),
				'validate_callback'  => array( $this, 'validate_plugin' ),
				'required'           => 'true',
			),
			'group_name'              => array(
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
			'test_order' => array(
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
			'threshold'  => array(
				'description'        => __( 'Threshold to end a test.', 'ingot' ),
				'type'               => 'integer',
				'default'            => 20,
				'sanitize_callback'  => 'absint',
			),
			'current_sequence'  => array(
				'description'        => __( 'Current sequence', 'ingot' ),
				'type'               => 'integer',
				'default'            => 0,
				'sanitize_callback'  => 'absint',
			),
			'tests_new' => array(
				'description'        => __( 'New tests to add.', 'ingot' ),
				'type'               => 'array',
				'default'            => array(),
			),
			'tests_update' => array(
				'description'        => __( 'Tests to update.', 'ingot' ),
				'type'               => 'array',
				'default'            => array(),
			),


		);

		if ( $require_id ){
			$args[ 'ID' ][ 'required' ] = true;
		}

		return $args;
	}

	/**
	 * Validate type
	 *
	 * @since 0.0.9
	 *
	 * @param string $value
	 *
	 * @return bool
	 */
	public function validate_type( $value ) {
		if( 'price' !== $value ) {
			return false;

		}

	}

	public function validate_plugin( $value ) {
		if( ! in_array( $value, ingot_accepted_plugins_for_price_tests() ) ){
			return false;

		}
	}
}
