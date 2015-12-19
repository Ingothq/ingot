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


class group extends table_crud {


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
		return $results;

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
			'created'
		);

		return $needed;
	}

}
