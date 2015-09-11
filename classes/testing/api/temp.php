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

namespace ingot\testing\api;


use ingot\testing\ingot;

class temp {

	public static function record_click_text() {
		if ( self::validate_input() ) {
			$data = self::data();
			$updated = ingot::increase_victory( $data[ 'test_id' ], $data[ 'sequence_id'] );
			if ( true == $updated ) {
				status_header( 200 );
			}elseif( false == $updated ) {
				status_header( 403 );
			}

		}else{
			status_header( 500 );

		}

		exit;
	}

	private static function validate_input() {
		if ( isset( $_POST[ 'ingot_nonce' ], $_POST[ 'ingot_test_id' ], $_POST[ 'ingot_sequence_id' ], $_POST[ 'ingot_test_type'] ) ) {
			if ( wp_verify_nonce( $_POST[ 'ingot_nonce' ], 'ingot_nonce' ) ) {
				return true;
			}

		}

	}

	protected static function data() {
		$data = array_fill_keys( self::$data_keys, 0 );
		foreach( self::$data_keys  as $key => $cb  ) {
			$data[ $key ] = call_user_func( $cb, $_POST[ "ingot_{$key}" ] );
		}

		return $data;
	}

	protected static $data_keys = array(
		'test_id' => 'absint',
		'sequence_id' => 'absint',
		'test_type' => 'strip_tags'
	);

}
