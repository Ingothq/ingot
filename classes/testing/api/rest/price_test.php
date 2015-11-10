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


use ingot\testing\utility\helpers;

class price_test extends route {

	/**
	 * Identify object type for this route collection
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $what = 'price_test';

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

		$params = $this->prepare_click_test_meta( $params );
		$created = \ingot\testing\crud\price_test::create( $params );
		if ( ! is_wp_error( $created ) && is_numeric( $created ) ) {
			$item = \ingot\testing\crud\price_test::read( $created );
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
		unset( $params[0] );
		unset( $params[1] );
		unset( $params['id'] );
		$params = $this->prepare_click_test_meta( $params );
		$updated = \ingot\testing\crud\price_test::update( $params, $id );
		if ( ! is_wp_error( $updated ) && is_numeric( $updated ) ) {
			$item = \ingot\testing\crud\price_test::read( $updated );
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
			'product_ID'               => array(
				'description'        => __( 'ID of product this is for', 'ingot' ),
				'type'               => 'integer',
				'default'            => 0,
				'sanitize_callback'  => 'absint',
				'required'           => 'true',
			),
			'default'  => array(
				'description'        => __( 'Default price change percentage expressed as a float.', 'ingot' ),
				'type'               => 'float',
				'default'            => array(),
				'sanitize_callback'  => function( $value ) {
					return (float) $value;
				},
			),
			'variable_prices'  => array(
				'description'        => __( 'Variable price change percentages expressed as a float', 'ingot' ),
				'type'               => 'array',
				'default'            => array(),
				'sanitize_callback'  => array( $this, 'make_array_values_floats' ),
			)

		);

		if ( $require_id ){
			$args[ 'ID' ][ 'required' ] = true;
		}

		return $args;
	}


}
