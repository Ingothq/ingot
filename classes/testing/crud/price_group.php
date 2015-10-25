<?php
/**
 * Table based CRUD for price test groups
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\crud;


class price_group extends table_crud {

	/**
	 * Name of this object
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected static $what = 'price_group';

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
			'type',
			'plugin'
		);

		return $required;
	}

	public static function get_items( $params ) {
		return false;
	}

	/**
	 * Necesarry, but not required fields of this object
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected static function needed() {
		$needed = array(
			'group_name',
			'sequences',
			'test_order',
			'initial',
			'threshold',
			'created',
			'modified',
			'current_sequence',
		);

		return $needed;
	}


	/**
	 * Validate item config
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @param array $data Item config
	 *
	 * @return bool|array Item config array if valid, false if not.
	 */
	protected static function validate_config( $data ) {
		$required = self::required();

		if( ! isset( $data[ 'type' ] ) || 'price' !== $data[ 'type' ] ){
			return new \WP_Error( 'ingot-invalid-price-group-type', __( 'For forward-compatibility reasons, price test groups must be created with the type "price"', 'ingot' )  );
		}

		if( ! isset( $data[ 'plugin' ] ) || ! in_array( $data[ 'plugin' ], ingot_accepted_plugins_for_price_tests() ) ){
			return new \WP_Error( 'ingot-invalid-price-group-plugin', __( 'Price test group plugin type is not supported', 'ingot' )  );
		}


		foreach( $required as $key ) {
			if ( ! isset( $data[ $key ] ) ) {
				return false;
			}

		}

		$data = self::fill_in( $data );

		if( isset( $data[ 'created' ] ) ) {
			$data[ 'created' ] = current_time( 'mysql' );
		}

		$fields = self::get_all_fields();
		foreach ( $fields as $key  ) {
			if ( 'test_order' == $key || 'sequences' == $key  ) {
				if ( ! is_array( $data[ $key ] ) ) {
					$data[ $key ] = array();
				}
			}elseif( is_int( $data[ $key ] ) || is_string( $data[ $key ] ) ){
				continue;
			} else  {
				return new \WP_Error( $key . '-invalid', __( 'Invalid data type', 'ingot' ), array( $key => $data[ $key ] ) );
			}
		}


		return $data;


	}



}
