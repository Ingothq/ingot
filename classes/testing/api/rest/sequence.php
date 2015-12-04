<?php
/**
 * REST API Endpoints
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\api\rest;


use ingot\testing\utility\helpers;

class sequence extends route {

	/**
	 * Marks what object this is for.
	 *
	 * @since 0.0.7
	 *
	 * @acces protected
	 *
	 * @var string
	 */
	protected $what = 'sequence';

	/**
	 * Get one sequence, by sequence ID
	 *
	 * @since 0.3.0
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function get_item( $request ){
		$url = $request->get_url_params();
		$id = helpers::v( 'id', $url, 0 );
		if( 0 == absint( $id ) || ! is_array( \ingot\testing\crud\sequence::read( $id )) ) {
			$response = new \WP_REST_Response(array(), 404 );
			return $response;
		}

		$sequence = \ingot\testing\crud\sequence::read( $id );
		if ( 'admin' == $request->get_param( 'context' ) ) {
			return rest_ensure_response( $sequence );

		}

		if( 'stats' == $request->get_param( 'context' ) ) {
			$stats = new \ingot\testing\object\stats( $sequence );
			return rest_ensure_response( $stats->get_stats() );

		}


	}


	/**
	 * Get sequences by page or group ID
	 *
	 * @since 0.0.7
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function get_items( $request ) {
		if( 0 != $request->get_param( 'group_ID' ) ){
			$args = array(
				'group_ID' => $request->get_param( 'group_ID' ),
				'limit' => -1
			);
		}else{
			$args = array(
				'page' => $request->get_param( 'page' ),
				'limit' => $request->get_param( 'limit' )
			);
		}

		$sequences = \ingot\testing\crud\sequence::get_items( $args );

		if( empty( $sequences ) ) {
			return rest_ensure_response( __( 'No matching sequences found.', 'ignot' ), 404 );

		}else{
			return rest_ensure_response( $sequences, 200 );

		}

	}

	/**
	 * Add the special extra routes for sequences API
	 *
	 * @since 0.3.0
	 *
	 * @access protected
	 */
	protected function register_more_routes() {
		$namespace = util::get_namespace();
		$base = $this->base();
	}

	/**
	 * Request params
	 *
	 * @since 0.0.7
	 *
	 * @param bool|true $require_id
	 *
	 * @return array
	 */
	public function args( $require_id = true ) {
		$args = array(
			'group_ID' => array(
				'type' => 'integer',
				'default'            => 0,
				'sanitize_callback'  => 'absint',
			),
			'page' => array(
				'type' => 'integer',
				'default'            => 1,
				'sanitize_callback'  => 'absint',
			),
			'limit' => array(
				'type' => 'integer',
				'default'            => 15,
				'sanitize_callback'  => 'absint',
			),
			'IDs_Only' => array(
				'type' => 'boolean',
				'default' => true,
				'validation_callback' => array( $this, 'validate_boolean' )
			),
			'context' => array(
				'type'                => 'string',
				'default'             => 'admin',
				'validation_callback' => function ( $value ) {
					if ( ! in_array( $value, [ 'stats', 'admin' ] ) ) {
						$value = 'admin';
					}

					return $value;

				}
			)
		);

		return $args;
	}


}
