<?php
/**
 * Abstract class for test/sequence/test group CRUD
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\crud;


abstract class crud {

	/**
	 * Create an item
	 *
	 * @since 0.0.4
	 *
	 * @param array $config Item con
	 * @param bool|false $bypass_cap
	 *
	 * @return array|bool||WP_Error Item config array,or false if not found, or error if not allowed.
	 */
	public static function create( $config, $bypass_cap = false ){
		do_action( 'ingot_crud_pre_update', $config, self::what() );
		$config = apply_filters( 'ingot_crud_create', $config, self::what() );
		return self::save( $config, null, $bypass_cap );
	}

	/**
	 * Update an item
	 *
	 * @since 0.0.4
	 *
	 * @param array $config Item con
	 * @param int $id Optional. Item ID. Not used or needed if using to create.
	 * @param bool|false $bypass_cap
	 *
	 * @return array|bool||WP_Error Item config array,or false if not found, or error if not allowed.
	 */
	public static function update( $config, $id = null, $bypass_cap = false ) {
		do_action( 'ingot_crud_pre_update',$config, $id, self::what() );
		$config = apply_filters( 'ingot_crud_update', $config, self::what() );
		return self::save( $config, $id, $bypass_cap );
	}


	/**
	 * Get an item
	 *
	 * @param int $id Item ID
	 *
	 * @return array Item config array.
	 */
	public static function read( $id ) {
		do_action( 'ingot_crud_pre_read', $id, self::what() );
		$item = get_option( self::key_name( $id ), array() );
		$item = self::fill_in( $item );
		if( ! empty( $item ) && ! isset( $item[ 'ID' ] ) ) {
			$item[ 'ID' ] = $id;
		}

		$item = apply_filters( 'ingot_crud_read', $item, self::what() );
		return $item;
	}

	/**
	 * Delete an item or all items
	 *
	 * @since 0.0.1
	 *
	 * @param int|string $id Item id or "all" to delete all
	 */
	public static function delete( $id ) {
		do_action( 'ingot_crud_pre_delete', $id, self::what() );
		if ( is_numeric( $id ) ) {
			delete_option( self::key_name( $id ) );
		}

		if ( 'all' == $id ){
			self::delete_all();
		}

	}



	/**
	 * Get the type of object we are CRUDing
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @return string
	 */
	protected static function what() {
		return static::$what;
	}

	/**
	 * Get array of non-required, yet needed fields
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected static function needed() {
		_doing_it_wrong( __METHOD__ , __( 'Must ovveride in subclass', 'ingot' ), INGOT_VER );
		return array();
	}

	/**
	 * Get array of required fields
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected static function required() {
		_doing_it_wrong( __METHOD__ , __( 'Must ovveride in subclass', 'ingot' ), INGOT_VER );
		return array();
	}



	/**
	 * Generic save for read/update
	 *
	 * @todo make protected
	 *
	 * @since 0.0.4
	 *
	 * @param array $config Item con
	 * @param int $id Optional. Item ID. Not used or needed if using to create.
	 * @param bool|false $bypass_cap
	 *
	 * @return array|bool||WP_Error Item config array,or false if not found, or error if not allowed.
	 */
	public static function save( $config,$id = null, $bypass_cap = false  ) {
		$config = self::validate_config( $config );
		if ( ! is_array( $config ) ) {
			return new \WP_Error( 'ingot-invalid-config' );
		}

		if ( ! $bypass_cap ) {
			$cap = apply_filters( 'ingot_save_cap', 'manage_options', self::what(), $id );
		} else {
			$cap = true;
		}

		if ( $cap ) {
			if ( ! $id ) {
				$id = self::increment_id();
			}
			$key = self::key_name( $id );

			if ( ! isset( $config[ 'ID' ] ) || $config[ 'ID' ] != $id ) {
				$config[ 'ID' ] = $id;

			}

			$saved = update_option( $key, $config  );
			if ( $saved ) {
				return $id;

			}else{
				return new \WP_Error( 'ingot-can-not-save-config' );

			}
		}else{
			return new \WP_Error( 'ingot-save-config-not-allowed' );

		}

	}


	/**
	 * Get an item
	 *
	 * @todo remove
	 *
	 * @since 0.0.4
	 *
	 * @param int $id Item ID
	 *
	 * @return array Item config array.
	 */
	public static function get( $id ) {
		return self::read( $id );
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
		$what = self::what();
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

		$key = 'ingot_id_increment_' . self::what();
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
		$what = self::what();
		return "ingot_{$what}_{$id}";
	}

	/**
	 * Validate item config
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @param array $config Item config
	 *
	 * @return bool|array Item config array if valid, false if not.
	 */
	protected static function validate_config( $config ) {
		$required = static::required();
		foreach( $required as $key ) {
			if ( ! isset( $config[ $key ] ) ) {
				return false;
			}

		}

		$config = self::fill_in( $config );

		$config[ 'modified' ] = time();

		return $config;
	}

	/**
	 * Fill in needed, but not required keys
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @param $config
	 *
	 * @return array
	 */
	protected static function fill_in( $config ) {
		$needed = static::needed() ;
		foreach ( $needed as $key ) {
			if ( ! isset( $config[ $key ] ) ) {
				if ( 'created' == $key ) {
					$config[ $key ] = time();
				}elseif( 'name' == $key ){
					$config[ $key ] = sprintf( '%s - %s', self::what(), $config[ 'ID' ] );
				} elseif( 'order' == $key || 'sequences' == $key ) {
					$config[ $key ] = array();
				} else{
					$config[ $key ] = 0;
				}
			}

		}

		return $config;

	}

}
