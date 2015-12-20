<?php
/**
 * Group CRUD
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\crud;


use ingot\testing\utility\helpers;

class group extends crud {


	/**
	 * Name of this object
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected static $what = 'group';

	protected static function what() {
		return 'group';
	}

	/**
	 * Save levers for a group
	 *
	 * @since 0.4.0
	 *
	 * @param int $id Group ID
	 * @param array $levers Array of \MaBandit\Lever objects
	 *
	 * @return bool|int
	 */
	public static function save_levers( $id, $levers ) {
		$table_name = static::get_table_name();
		if( self::can( $id, true ) ) {
			global $wpdb;
			$wpdb->update(
				$table_name,
				array(
					'levers' => serialize( $levers )
				),
				array( 'ID' => $id )

			);


			$id =  $wpdb->insert_id;

			return $id;

		}else{
			return false;

		}

	}

	/**
	 * Get a group's levers
	 *
	 * @since 0.4.0
	 *
	 * @param int|array $group Group ID or array
	 *
	 * @return array
	 */
	public static function get_levers( $group ) {
		if( is_numeric( $group ) ) {
			return self::get_levers_by_id( $group );
		}elseif( is_array( $group ) && ! empty( $group[ 'levers' ] ) ) {
			return  $group[ 'levers' ];

		}

	}

	/**
	 * Get a group's levers by ID
	 *
	 * @since 0.4.0
	 *
	 * @param int $group Group ID
	 *
	 * @return array
	 */
	protected static function get_levers_by_id( $id ) {
		$table_name = static::get_table_name();
		global $wpdb;
		$sql = sprintf( 'SELECT `levers` FROM %s WHERE `ID` = %d', $table_name, $id );
		$results = $wpdb->get_results( $sql, ARRAY_N );
		if ( ! empty( $results ) ) {
			foreach( $results as $i => $result ) {
				$result = maybe_unserialize( $result );
				if ( ! empty( $result ) ) {
					$result = helpers::make_array_values_numeric( $result, true );
				}

				$results[ $i ] = $result;

			}

			return $results;

		}

		return array();

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
	 * @return array|\WP_Error Item config array if valid, WP_Error if not
	 */
	protected static function validate_config( $data ) {
		foreach( self::required() as $key ) {
			if ( ! isset( $data[ $key ] ) ) {
				return new \WP_Error( 'ingot-invalid-config', __( sprintf( 'Groups require the field %s', $key ), 'ingot'  ), $data );
			}

		}

		if ( false == self::validate_type( $data ) ) {
			return new \WP_Error( 'ingot-invalid-group-type', __( 'Invalid group type', 'ingot'  ), $data );
		}

		if( 'click' == $data[ 'type' ] && false == self::validate_click_type( $data ) ) {
			return new \WP_Error( 'ingot-invalid-click-group-type', __( 'Invalid click group type', 'ingot'  ), $data );

		}

		//@todo this validation for price group subtypes
		if( 'price' == $data[ 'type' ] && 1 == 3 ) {
			return new \WP_Error( 'ingot-invalid-click-group-type', __( 'Invalid price group type', 'ingot'  ), $data );

		}

		foreach( self::get_all_fields() as $field ){
			if( ! in_array( $field, array( 'variants', 'meta', 'levers' ) ) ) {
				$data[ $field ] = (string) $data[ $field ];
			}else{
				if( ! is_array( $data[ $field ] ) ) {
					$data[ $field ] = array();
				}

			}
		}

		$data = self::prepare_meta( $data );
		if( is_wp_error( $data ) ) {
			return $data;

		}

		if ( ! empty( 'levers' ) ) {
			foreach ( $data[ 'levers' ] as $i => $lever ) {
				if( ! self::is_lever( $lever ) ) {
					unset( $data[ 'levers' ][ $i ] );
				}

			}
			
		}

		$data[ 'variants' ] == helpers::make_array_values_numeric( $data[ 'variants' ], true );

		return $data;

	}

	/**
	 * Validate and sanitize meta array
	 *
	 * @since 0.4.0
	 *
	 * @param array $data
	 *
	 * @return array|\WP_Error Prepared array or WP_Error if invalid
	 */
	protected static function prepare_meta( $data ) {
		if( ! isset( $data[ 'meta' ] ) || empty( $data[ 'meta' ] || ! is_array( $data[ 'meta']) ) ){
			$data[ 'meta'] = [];
			return $data;
		}

		if ( 'click' == $data[ 'type' ] ) {
			foreach ( [ 'color', 'background_color', 'color_test_text', 'link' ] as $field ) {
				if ( isset( $data[ 'meta' ][ $field ] ) && ! empty( $data[ 'meta' ][ $field ] ) && is_string( $data[ 'meta' ][ $field ] ) ) {

					if ( 'link' == $field ) {
						if ( filter_var( $data[ 'meta' ][ $field ], FILTER_VALIDATE_URL ) ) {
							$data[ 'meta' ][ $field ] = esc_url_raw( $data[ 'meta' ][ $field ] );
						} else {
							return new \WP_Error( 'ingot-invalid-config-click-link', __( 'Click groups must have a valid link.', 'ingot' ) );
						}

					} else {
						$data[ 'meta' ][ $field ] = strip_tags( $data[ 'meta' ][ $field ] );
					}
				}else{
					$data[ 'meta' ][ $field ] = '';
				}

			}
		}

		return $data;

	}

	/**
	 * Fill in needed, but not required keys
	 *
	 * @since 0.4.0
	 *
	 * @access protected
	 *
	 * @param $data
	 *
	 * @return array
	 */
	protected static function fill_in( $data ) {
		foreach( self::needed() as $field ) {
			if( 'created' == $field || 'modified' == $field ) {
				if( ! isset( $data[ $field ] ) ) {
					$data[ $field ] = current_time( 'mysql' );
				}else{
					$data[  $field  ] = self::date_validation( $data[ $field ] );
				}

			}elseif( in_array( $field, ['variants', 'meta', 'levers' ] ) && ( ! isset( $data[  $field ] ) || ! is_array( $data[ $field ] ) ) ) {
				$data[ $field ] = [];
			}else{
				if ( ! isset( $data[ $field ] ) ) {
					$data[ $field ] = '';
				}
			}
		}

		return $data;
	}

	/**
	 * Ensure the that an object of the \MaBandit\Lever class
	 *
	 * @since 0.4.0
	 *
	 * @access protected
	 *
	 * @param object $maybe_lever
	 *
	 * @return bool
	 */
	protected function is_lever( $maybe_lever ) {
		if( is_object( $maybe_lever ) && is_a( $maybe_lever, '\MaBandit\Lever' ) ) {
			return true;
		}

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
			'type'
		);

		return $required;
	}

	/**
	 * Necessary, but not required fields of this object
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected static function needed() {
		$needed = array(
			'name',
			'sub_type',
			'variants',
			'meta',
			'modified',
			'created',
			'levers'
		);

		return $needed;
	}

}
