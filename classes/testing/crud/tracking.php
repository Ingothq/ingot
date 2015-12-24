<?php
/**
 * CRUD for tracking table
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\crud;


use ingot\testing\utility\helpers;

class tracking extends crud {

	/**
	 * Name of this object
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected static $what = 'tracking';

	/**
	 * Get the type of object we are CRUDing
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return string
	 */
	protected static function what() {
		return self::$what;

	}

	/**
	 * Validate item config
	 *
	 * @since 0.4.0
	 *
	 * @access protected
	 *
	 * @param array $data Item config
	 *
	 * @return \WP_Error|array Item config array if valid, WP_Error if not.
	 */
	protected static function validate_config( $data ) {
		$required = static::required();
		foreach( $required as $key ) {
			if ( ! isset( $data[ $key ] ) ) {
				return new \WP_Error( 'ingot-invalid-config', __( sprintf( 'Groups require the field %s', $key ), 'ingot'  ), $data );
			}

		}

		if( ! isset( $data[ 'IP' ] ) )  {
			$data[ 'IP' ] = ingot_get_ip();
		}

		if( isset( $data[ 'UTM' ] ) && ( $data[ 'UTM' ] ) ) {
			$data[ 'UTM' ] = helpers::sanitize( $data[ 'UTM' ] );
		}

		$data[ 'time' ]= self::date_validation( $data[ 'time'] );

		return $data;
	}

	/**
	 * Fill in needed, but not required keys
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @param $data
	 *
	 * @return array
	 */
	protected static function fill_in( $data ) {
		if( ! isset( $data[ 'time' ] ) || 0 == strtotime( $data[ 'time' ] ) ) {
			$data[ 'time' ] = current_time( 'mysql' );
		}


		foreach( static::needed() as $field ) {
			if( ! isset( $data[ $field ] ) && ! empty( $data[ $field ]  ) ) {
				$data[ $field ] = '';
			}
		}

		return $data;
	}


	/**
	 * Required fields of this object
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected static function required() {
		$required = array(
			'test_ID',
		);

		return $required;
	}

	/**
	 * Neccasary, but not required fields of this object
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected static function needed() {
		$needed = array(
			'group_ID',
			'ingot_ID',
			'IP',
			'referrer',
			'UTM',
			'browser',
			'meta',
			'user_agent',
			'time',
		);

		return $needed;
	}


}
