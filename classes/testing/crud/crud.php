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


use ingot\testing\types;

abstract class crud {

	/**
	 * Get a collection of items
	 *
	 * @since 0.0.5
	 *
	 * @param array $params Query params
	 *
	 * @return array
	 */
	public static function get_items( $params ) {
		_doing_it_wrong( __FUNCTION__, __( 'Must ovveride', 'ingot' ), '0.0.5' );
		return array();
	}

	/**
	 * Create an item
	 *
	 * @since 0.0.4
	 *
	 * @param array $data Item data
	 * @param bool|false $bypass_cap
	 *
	 * @return array|bool||WP_Error Item config array,or false if not found, or error if not allowed.
	 */
	public static function create( $data, $bypass_cap = false ){
		/**
		 * Runs before an object is created
		 *
		 * @since 0.0.6
		 *
		 * @param array $data Data to be saved.
		 * @param string $what Object name
		 */
		do_action( 'ingot_crud_pre_create', $data, static::what() );
		$data = apply_filters( 'ingot_crud_create', $data, static::what() );

		$id = static::save( $data, null, $bypass_cap );
		if( is_wp_error( $id ) ) {
			return $id;
		}

		if ( $id ){
			/**
			 * Runs after an object is created
			 *
			 * @since 0.0.6
			 *
			 * @param int $id Item ID
			 * @param string $what Object name
			 */
			do_action( 'ingot_crud_created', $id, static::what() );
		}

		return $id;

	}

	/**
	 * Update an item
	 *
	 * @since 0.0.4
	 *
	 * @param array $data Item config
	 * @param int $id Item ID.
	 * @param bool|false $bypass_cap
	 *
	 * @return array|bool||WP_Error Item config array,or false if not found, or error if not allowed.
	 */
	public static function update( $data, $id , $bypass_cap = false ) {

		/**
		 * Runs before an object is updated.
		 *
		 * @since 0.0.6
		 *
		 * @param array $data Data to be saved.
		 * @param int $id Item ID
		 * @param string $what Object name
		 */
		do_action( 'ingot_crud_pre_update', $data, $id, static::what() );
		$data = apply_filters( 'ingot_crud_update', $data, static::what() );
		$id = static::save( $data, $id, $bypass_cap );
		if ( $id ){
			/**
			 * Runs after an object is updated.
			 *
			 * @since 0.0.6
			 *
			 * @param int $id Item ID
			 * @param string $what Object name
			 */
			do_action( 'ingot_crud_updated', $id, static::what() );
		}

		return $id;
	}


	/**
	 * Get an item
	 *
	 * @param int $id Item ID
	 *
	 * @return array Item config array.
	 */
	public static function read( $id ) {
		_doing_it_wrong( __FUNCTION__, __( 'Must ovveride', 'ingot' ), '0.0.5' );
		return array();

	}

	/**
	 * Delete an item or all items
	 *
	 * @since 0.0.1
	 *
	 * @param int|string $id Item id or "all" to delete all
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

		_doing_it_wrong( __FUNCTION__, __( 'Must ovveride', 'ingot' ), '0.0.5' );

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
		_doing_it_wrong( __METHOD__ , __( 'must ovveride', 'ingot' ), '0.0.4' );
		return '';
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
	 * @since 0.0.4
	 *
	 * @param array $data Item con
	 * @param int $id Optional. Item ID. Not used or needed if using to create.
	 * @param bool|false $bypass_cap
	 *
	 * @return int|bool||WP_Error Item ID if created,or false if not created, or error if not allowed to create.
	 */
	protected static function save( $data,$id = null, $bypass_cap = false  ) {
		_doing_it_wrong( __METHOD__ , __( 'Must ovveride in subclass', 'ingot' ), INGOT_VER );
		return false;

	}


	protected static function get_all( $limit, $page = 1) {
		_doing_it_wrong( __METHOD__ , __( 'Must ovveride in subclass', 'ingot' ), INGOT_VER );
		return array();
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
	protected static function delete_all() {
		_doing_it_wrong( __METHOD__ , __( 'Must ovveride in subclass', 'ingot' ), INGOT_VER );
		return array();
	}


	/**
	 * Validate item config
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @param array $data Item config
	 *
	 * @return bool|array Item config array if valid, false if not.
	 */
	protected static function validate_config( $data ) {
		$required = static::required();
		foreach( $required as $key ) {
			if ( ! isset( $data[ $key ] ) ) {
				return false;
			}

		}

		if ( 'group' == static::what() ) {
			$data = self::fill_in( $data );
		}

		if( false == self::validate_type( $data ) ) {
			return false;
		}

		$data[ 'modified' ] = time();

		return $data;
	}

	/**
	 * Validate item types
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @param array $data Item config
	 *
	 * @return bool True if valid, false if not
	 */
	protected static function validate_type( $data ) {
		if( ! in_array(  $data['type'], types::allowed_types() ) ) {
			return false;
		}


		if( 'click' === $data[ 'type' ] && ! in_array( $data[ 'click_type' ], types::allowed_click_types() ) ) {
			return false;

		}

		return true;
	}

	/**
	 * Fill in needed, but not required keys
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @param $data
	 *
	 * @return array
	 */
	protected static function fill_in( $data ) {
		$needed = static::needed() ;
		foreach ( $needed as $key ) {
			if ( ! isset( $data[ $key ] ) ) {
				if ( 'created' == $key ) {
					$data[ $key ] = time();
				}elseif( 'name' == $key ){
					if ( isset( $data[ 'ID']) ) {
						$data[ $key ] = sprintf( '%s - %s', static::what(), $data['ID'] );
					}else{
						$data[ $key ] = rand();
					}
				} elseif( 'order' == $key || 'sequences' == $key ) {
					$data[ $key ] = array();
				} else{
					$data[ $key ] = 0;
				}
			}

		}

		return $data;

	}

	public static function get_required_fields() {
		return static::required();
	}

	public static function get_needed_fields() {
		return static::needed();
	}

}
