<?php
/**
 * Generic CRUD using options storage
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\crud;


abstract class options_crud extends crud {

	/**
	 * Get an item
	 *
	 * @param int $id Item ID
	 *
	 * @return array Item config array.
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
		$item = get_option( self::key_name( $id ), array() );
		$item = self::fill_in( $item );
		if( ! empty( $item ) && ! isset( $item[ 'ID' ] ) ) {
			$item[ 'ID' ] = $id;
		}

		/**
		 * Runs before an object is returned from DB
		 *
		 * @since 0.0.6
		 *
		 * @param array $item Data to be returned
		 * @param string $what Object name
		 */
		$item = apply_filters( 'ingot_crud_read', $item, static::what() );
		return $item;

	}

	/**
	 * Delete an item or all items
	 *
	 * @since 0.0.1
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
		if ( is_numeric( $id ) ) {
			return delete_option( self::key_name( $id ) );
		}

		if ( 'all' == $id ){
			self::delete_all();
		}

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
	protected static function save( $data,$id = null, $bypass_cap = false  ) {
		$data = self::validate_config( $data );
		if ( ! is_array( $data ) ) {
			return new \WP_Error( 'ingot-invalid-config' );
		}

		$allowed = array_merge( static::required(), static::needed() );
		foreach( $data as $k => $v ) {
			if ( is_numeric( $k ) || ! in_array( $k, $allowed ) ) {
				unset( $data[ $k ] );
			}
		}


		if ( ! $bypass_cap ) {
			$cap = apply_filters( 'ingot_save_cap', 'manage_options', static::what(), $id );
		} else {
			$cap = true;
		}

		if ( $cap ) {
			if ( ! $id ) {
				$id = self::increment_id();
			}
			$key = self::key_name( $id );

			if ( ! isset( $data[ 'ID' ] ) || $data[ 'ID' ] != $id ) {
				$data[ 'ID' ] = $id;

			}

			$saved = update_option( $key, $data  );
			if ( $saved ) {
				do_action( 'ingot_config_saved', $id, $data, static::what() );
				return $id;

			}else{
				return new \WP_Error( 'ingot-can-not-save-config' );

			}
		}else{
			return new \WP_Error( 'ingot-save-config-not-allowed' );

		}

	}

	protected static function get_all( $limit, $page = 1) {
		if ( 1 == $page ) {
			$offset = 0;
		}else{
			$offset = (int) $limit * (int) $page;
		}

		global $wpdb;
		$what = static::what();
		$like = "%ingot_{$what}_%";
		$sql = sprintf( 'SELECT * FROM `%1s` WHERE `option_name` LIKE "%2s" ORDER BY `option_id` DESC LIMIT %d OFFSET %d', $wpdb->options, $like, $limit, $offset );

		$results = $wpdb->get_results( $sql, ARRAY_A );
		$all = array();
		if ( ! empty( $results ) ) {
			foreach( $results as $result ) {
				$all[ $result[ 'option_id' ] ] = maybe_unserialize( $result[ 'option_value' ] );
			}
		}
		return $all;
	}

	/**
	 * Delete all items
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @return array|null|object
	 */
	protected function delete_all() {
		global $wpdb;
		$what = static::what();
		$like = "%ingot_{$what}_%";
		$sql = sprintf( 'DELETE FROM `%s` WHERE `option_name` LIKE "%s"', $wpdb->options, $like  );

		$results = $wpdb->get_results( $sql );
		return $results;
	}

	/**
	 * Get an incriemnted ID and increase counter
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @return int
	 */
	protected static function increment_id() {

		$key = 'ingot_id_increment_' . static::what();
		$id = get_option( $key, 1 );
		update_option( $key, $id + 1 );
		return $id;
	}


	/**
	 * Create an option key name
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @param int $id Item ID
	 *
	 * @return string
	 */
	private static function key_name( $id ) {
		$what = static::what();
		return "ingot_{$what}_{$id}";
	}




}
