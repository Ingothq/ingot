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
	 * Get a collection of groups
	 *
	 * @since 0.2.0
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function get_items( $request ) {

		$args = array(
			'page'  => $request->get_param( 'page' ),
			'limit' => $request->get_param( 'limit' )
		);

		$groups = price_group::get_items( $args );

		if ( empty( $groups ) ) {
			return rest_ensure_response( __( 'No matching groups found.', 'ingot' ), 404 );

		} else {
			$response = new \WP_REST_Response( $groups, 200 );
			$response->header( 'X-Ingot-Total', (int) price_group::total() );

			return $response;

		}

	}

	/**
	 * Get a single group
	 *
	 * @since 0.2.0
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function get_item( $request ) {
		$url = $request->get_url_params();
		$id  = helpers::v( 'id', $url, 0 );
		if ( $id ) {

			$group = price_group::read( $id );

			//hack for #65
			$group[ 'product' ] = $group[ 'product_ID' ];
			unset( $group[ 'product_ID' ] );
			if ( $group ) {
				return rest_ensure_response( $group );
			}

		} else {
			return new \WP_REST_Response( array(), 404 );
		}
	}


	/**
	 * Create an item
	 *
	 * @since 0.0.9
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Request
	 */
	public function create_item( $request ) {
		$params = $request->get_params();
		unset( $params[ 0 ] );
		unset( $params[ 1 ] );

		//this is a hack for #65
		$params[ 'product_ID' ] = $params[ 'product' ];

		if ( ! empty( $params[ 'tests' ] ) ) {
			foreach ( $params[ 'tests' ] as $test ) {
				$test_id = helpers::v( 'id', $test, 0 );

				unset( $test[ 'id' ] );
				if ( absint( $test_id ) > 0 ) {
					$test[ 'product_ID' ] = $params[ 'product_ID' ];
					$_id = price_test::update( $test, $test_id );
				} else {
					$test[ 'product_ID' ] = $params[ 'product_ID' ];
					$_id = price_test::create( $test );
				}

				if ( 0 != $_id && is_numeric( $_id ) ) {
					$params[ 'test_order' ][] = $_id;
				}


			}

		}

		unset( $params[ 'tests' ] );

		$created = price_group::create( $params );
		if ( ! is_wp_error( $created ) && is_numeric( $created ) ) {
			$item = price_group::read( $created );

			return rest_ensure_response( $item, 200 );
		} else {
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
	 *
	 * @return \WP_Error|\WP_REST_Request
	 */
	public function update_item( $request ) {
		$params   = $request->get_params();
		$url      = $request->get_url_params();
		$id       = helpers::v( 'id', $url, 0 );
		$existing = price_group::read( $id );
		if ( ! is_array( $existing ) ) {
			if ( is_wp_error( $existing ) ) {
				return $existing;
			}

			return rest_ensure_response( array(), 404 );
		}

		//test order
		//@todo deal with removals
		if ( ! empty( $params[ 'tests_update' ] ) ) {
			foreach ( $params[ 'tests_update' ] as $test ) {
				$_id = price_test::update( $test, helpers::v( 'ID', $test, 0, 'absint' ) );
			}
		}

		if ( ! empty( $params[ 'tests_new' ] ) ) {
			foreach ( $params[ 'tests_new' ] as $test ) {
				$data[ 'test_order' ] = $existing[ 'test_order' ];
				$new_test             = price_test::create( $test );;
				if ( ! is_wp_error( $new_test ) ) {
					$data[ 'test_order' ][] = $new_test;
				}
			}
		}

		//@todo allow for more fields to be updated
		foreach (
			array(
				'group_name',
				'initial',
				'threshold'
			) as $field
		) {
			if ( ! empty( $params[ $field ] ) ) {
				$data[ $field ] = $params[ $field ];
			}
		}

		$data = array_merge( $existing, $data );

		$updated = \ingot\testing\crud\price_group::update( $data, $id, true );

		//this is a massive violation of separation of concerns.
		if ( empty( $data[ 'sequences' ] ) ) {
			\ingot\testing\tests\sequence_progression::make_initial_sequence( $id, true );
		}
		if ( ! is_wp_error( $updated ) && is_numeric( $updated ) ) {
			$item = \ingot\testing\crud\price_group::read( $updated );

			return rest_ensure_response( $item, 200 );
		} else {
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
		$args = array(
			'id'               => array(
				'description'       => __( 'ID of group', 'ingot' ),
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
			),
			'type'             => array(
				'description'       => __( 'Type of Test Group', 'ingot' ),
				'type'              => 'string',
				'default'           => 'price',
				'sanitize_callback' => array( $this, 'strip_tags' ),
				'validate_callback' => array( $this, 'validate_type' ),
			),
			'plugin'           => array(
				'description'       => __( 'Plugin To Use For Price Test', 'ingot' ),
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'strip_tags' ),
				'validate_callback' => array( $this, 'validate_plugin' ),
				'required'          => 'true',
			),
			'group_name'       => array(
				'description'       => __( 'Name of Test Group', 'ingot' ),
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => array( $this, 'strip_tags' ),
				'required'          => true,
			),
			'sequences'        => array(
				'description'       => __( 'Sequences', 'ingot' ),
				'type'              => 'array',
				'default'           => array(),
				'sanitize_callback' => array( $this, 'make_array_values_numeric' ),
			),
			'tests'            => array(
				'description'       => __( 'Order of Tests', 'ingot' ),
				'type'              => 'array',
				'default'           => array(),
				'sanitize_callback' => array( $this, 'make_array_values_numeric' ),
			),
			'initial'          => array(
				'description'       => __( 'Number of times to run test at 50/50', 'ingot' ),
				'type'              => 'integer',
				'default'           => 50,
				'sanitize_callback' => 'absint',
			),
			'threshold'        => array(
				'description'       => __( 'Threshold to end a test.', 'ingot' ),
				'type'              => 'integer',
				'default'           => 20,
				'sanitize_callback' => 'absint',
			),
			'current_sequence' => array(
				'description'       => __( 'Current sequence', 'ingot' ),
				'type'              => 'integer',
				'default'           => 0,
				'sanitize_callback' => 'absint',
			),
			'product'       => array(
				'description'       => __( 'ID of product to test', 'ingot' ),
				'type'              => 'integer',
				'required'          => true,
				'sanitize_callback' => 'absint',
			),



		);

		if ( $require_id ) {
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
		if ( 'price' !== $value ) {
			return false;

		}

	}

	/**
	 * Validate plugin field
	 *
	 * @since 0.0.9
	 *
	 * @param $value
	 *
	 * @return bool
	 */
	public function validate_plugin( $value, $request ) {
		$valid = ingot_accepted_plugins_for_price_tests();
		if ( in_array( $value, $valid ) ) {
			return true;

		}
	}
}
