<?php
/**
 * Base class for custom table crud
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\crud;


use ingot\testing\utility\helpers;

abstract class table_crud extends crud {

	protected static function what() {
		return static::$what;
	}

	/**
	 * Get one item from table
	 *
	 * @since 0.0.7
	 *
	 * @param int $id
	 *
	 * @return array|mixed|null|object|void
	 */
	public static function read( $id ) {
		/**
		 * Runs before an object is read.
		 *
		 * @since 0.0.6
		 *
		 * @param int $id Item ID
		 * @param string $what Object name
		 */
		do_action( 'ingot_crud_pre_read', $id, static::what() );

		global $wpdb;

		$table = static::get_table_name();
		$sql = $wpdb->prepare( "SELECT * FROM $table WHERE `ID` = %d", $id );
		$results = $wpdb->get_results( $sql, ARRAY_A );
		if( is_array( $results ) && ! empty( $results ) && isset( $results[0])) {
			$results = $results[0];
		}else{
			$results = false;
		}

		/**
		 * Runs before an object is returned from DB
		 *
		 * @since 0.0.6
		 *
		 * @param array $item Data to be returned
		 * @param string $what Object name
		 */
		$results = apply_filters( 'ingot_crud_read', $results, static::what() );



		return $results;

	}

	/**
	 * Delete an item or all items
	 *
	 * @since 0.0.7
	 *
	 * @param int|string $id Item id or "all" to delete all
	 *
	 * @return bool
	 */
	public static function delete( $id ) {
		/**
		 * Runs before an object is deleted.
		 *
		 * @since 0.0.6

		 * @param int $id Item ID
		 * @param string $what Object name
		 */
		do_action( 'ingot_crud_pre_delete', $id, static::what() );

		if( 'all' == $id ) {
			return self::delete_all();
		}

		global $wpdb;
		$deleted = $wpdb->delete( self::get_table_name(), array( 'ID' => $id ), array( '%d' ) );
		if( is_numeric( $deleted ) ){
			return true;

		}else{
			return false;

		}

	}

	/**
	 * Delete all rows from table
	 *
	 * @since 0.0.7
	 *
	 * @return bool
	 */
	protected static function delete_all() {
		global $wpdb;
		$table = static::get_table_name();
		$deleted = $wpdb->query( "Truncate table $table" );

		if( is_numeric( $deleted ) ){
			return true;

		}else{
			return false;

		}
	}

	/**
	 * Get table name
	 *
	 * @since 0.0.7
	 *
	 * @return string
	 */
	public static function get_table_name() {
		global $wpdb;
		return $wpdb->prefix . 'ingot_' . static::what();
	}


	/**
	 * Generic save for read/update
	 *
	 * @since 0.0.4
	 *
	 * @param array $data Item con
	 * @param int $id Optional. Item ID. Not used or needed if using to create.
	 * @param bool|false $bypass_cap
	 *
	 * @return int|bool||WP_Error Item ID if created,or false if not created, or error if not allowed to create.
	 */
	protected static function save( $data, $id = null, $bypass_cap = false  ) {

		$data = self::prepare_data( $data );
		if( is_wp_error( $data ) ) {
			return $data;
		}

		$table_name = static::get_table_name();

		foreach( $data as $key => $datum ) {
			if( is_array( $data[ $key ] ) ) {
				$data[ $key ] = helpers::sanitize( $data[ $key ] );
				$data[ $key ] = serialize( $datum );
			}

		}

		if( self::can( $id, $bypass_cap ) ) {
			unset( $data[ 'ID' ] );

			global $wpdb;
			if( $id ) {
				$wpdb->update(
					$table_name,
					$data,
					array( 'ID' => $id )

				);
			}else{
				$wpdb->insert(
					$table_name,
					$data
				);

			}

			$id =  $wpdb->insert_id;

			return $id;

		}else{
			return false;

		}


	}

	/**
	 * Get fields with sprintf pattern
	 *
	 * @todo make this less of a hack that's going to break shit when other objects become tables
	 *
	 * @since 0.0.7
	 *
	 * @return array
	 */
	protected static function get_fields_with_format() {
		$_fields = self::get_all_fields();
		$fields = array();
		foreach( $_fields as $field ) {
			if( in_array( $field, array( 'created', 'modified', 'test_type', 'plugin', 'group_name', 'sequences', 'test_order' ) ) ) {
				$fields[ $field ] = '%s';
			}else{
				$fields[ $field ] = '%d';
			}

		}

		return $fields;

	}





}
