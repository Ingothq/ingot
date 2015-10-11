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


class sequence {

	/**
	 * Marks what object this is for.
	 *
	 * @since 0.0.7
	 *
	 * @var string
	 */
	private $what = 'sequence';


	/**
	 * Get sequences by
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

	public function args() {
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
			)
		);

		return $args;
	}


}
