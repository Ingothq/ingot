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


use ingot\testing\tests\flow;
use ingot\testing\tests\sequence_progression;
use ingot\testing\types;
use ingot\testing\utility\defaults;
use ingot\testing\utility\helpers;

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
	 * @return ID|bool||WP_Error Item ID,or false if not created, or error if not allowed.
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

		/**
		 * Modify data before saving (read or update)
		 *
		 * @since 0.0.6
		 *
		 * @param array $data Data to be saved.
		 * @param int $id Item ID
		 * @param string $what Object name
		 */
		$data = apply_filters( 'ingot_crud_update', $data, $id, static::what() );

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
		/**
		 * Runs before an object is read.
		 *
		 * @since 0.0.6
		 *
		 * @param int $id Item ID
		 * @param string $what Object name
		 */
		do_action( 'ingot_crud_pre_read', $id, static::what() );
		_doing_it_wrong( __FUNCTION__, __( 'Must ovveride', 'ingot' ), '0.0.5' );

		/**
		 * Runs before an object is returned from DB
		 *
		 * @since 0.0.6
		 *
		 * @param array $item Data to be returned
		 * @param string $what Object name
		 */
		$item = apply_filters( 'ingot_crud_read', array(), static::what() );
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
	protected static function save( $data, $id = null, $bypass_cap = false  ) {
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
				return new \WP_Error( 'ingot-invalid-config', __( sprintf( '%s require the field %s', static::what(), $key )  ) );
			}

		}

		$data = static::fill_in( $data );

		if ( 'group' == static::what() ) {
			if ( false == self::validate_type( $data ) || false == self::validate_click_type( $data ) ) {
				return false;
			}

		}elseif( 'sequence' == static::what() ) {
			if ( false == self::validate_type( $data, 'test_type' )  ) {
				return false;
			}

		}

		foreach ( static::get_all_fields() as  $key  ) {
			if ( 'order' == $key || 'sequences' == $key || 'UTM' == $key || 'meta' == $key ) {
				if ( ! is_array( $data[ $key ] ) ) {
					$data[ $key ] = array();
				}
			}elseif( 'completed' == $key ) {
				if(  ! in_array( $data[ 'completed' ], array( false, true, 1, 0, 'false', 'true', 'FALSE', 'TRUE', '1', '0' ) ) ) {
					$data[ 'completed' ] = 0;
				}
			} elseif ( is_int( $data[ $key ] ) || is_string( $data[ $key ] ) ) {
				continue;
			} elseif ( 'group' == static::what() && empty( $data[ 'current_sequence' ] ) ) {
				$data[ 'current_sequence' ] = 0;
		} else {
				return new \WP_Error( $key . '-invalid', __( 'Invalid data type', 'ingot' ), array( $key => $data[ $key ] ) );
			}
		}

		if ( 'tracking' != static::what() ) {
			$data['modified'] = time();
		}

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
	 * @param string $key Optional. Array key to test in. Default is "types"
	 *
	 * @return bool True if valid, false if not
	 */
	protected static function validate_type( $data, $key = 'type' ) {
		if( ! in_array(  $data[ $key ], types::allowed_types() ) ) {
			return false;
		}


		return true;

	}

	/**
	 * Validate item click type
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @param array $data Item config
	 *
	 * @return bool True if valid, false if not
	 */
	protected static function validate_click_type( $data ) {
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

		//not in love with any of this hack
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
				} elseif( 'click_type' == $key ){
					$data[ $key ] = 'link';
				}elseif( 'IP' == $key ){
					$data[ $key ] = ingot_get_ip();
				}elseif( 'user_agent' == $key ) {
					$data[ $key ] = ingot_get_user_agent();
				}elseif( 'browser' == $key ){
					$data[ $key ] = ingot_get_browser( false );
				}elseif( 'time' == $key ) {
					$data[ $key ] =  time();
				}elseif( 'meta' == $data ) {
					$data[ $key ] = array();
				}elseif( 'referrer' == $data ) {
					$data[ $key ] = ingot_get_refferer();
				}elseif( 'threshold' == $key ) {
					$data[ $key ] =  defaults::threshold();
				}elseif( 'initial' == $key ) {
					$data[ $key ] =  defaults::initial();
				}else{
					$data[ $key ] = 0;
				}
			}

		}


		//this date validation shit is serious fucking mess
		if ( 'tracking' != static::what() ) {
			foreach ( array( 'created', 'modified' ) as $key ) {
				if ( ! isset( $data[ $key ] ) || 0 == $data[ $key ] || ( is_string( $data[ $key ] ) && false === strtotime( $data[ $key ] ) ) ) {
					$data[ $key ] = time();
				}

				if ( ! is_numeric( $data[ $key ] ) ) {
					$data[ $key ] = time();
				}

			}
		}

		if( 'sequence' == static::what() ) {
			$data[ 'created' ] = date("Y-m-d H:i:s", $data[ 'created' ] );
			$data[ 'modified' ] = date("Y-m-d H:i:s", $data[ 'modified' ] );
		}

		if( 'tracking' == static::what() ) {

			if ( ! empty( $data[ 'meta' ] ) ) {
				if ( ! is_array( maybe_unserialize( $data['meta'] ) ) ) {
					$data['meta'] = array();
				}

			}

			if( is_numeric( $data[ 'time' ] ) ) {
				$data[ 'time' ] = date("Y-m-d H:i:s", $data[ 'time' ] );
			}

			if( 0 == strtotime( $data[ 'time' ] ) ) {
				$data[ 'time' ] = date("Y-m-d H:i:s" );
			}

		}


		return $data;

	}

	/**
	 * Calculate a offset for a paginated query
	 *
	 * @since 0.0.7
	 *
	 * @param int $limit Total results to show.
	 * @param int $page Page of results to get
	 *
	 * @return int
	 */
	protected static function calculate_offset( $limit, $page ) {
		if ( 1 == $page ) {
			$offset = 0;

			return $offset;

		} else {
			$offset = ( (int) $limit * ( (int) $page - 1 ) ) - 1;

			return absint( $offset );

		}

	}


	/**
	 * Get the required fields
	 *
	 * @since 0.0.6
	 *
	 * @return array
	 */
	public static function get_required_fields() {
		return static::required();

	}

	/**
	 * Get the needed, but not required fields
	 *
	 * @since 0.0.6
	 *
	 * @return array
	 */
	public static function get_needed_fields() {
		return static::needed();

	}

	/**
	 * Get all the fields
	 *
	 * @since 0.0.6
	 *
	 * @return array
	 */
	public static function get_all_fields() {
		return array_merge( static::get_required_fields(), static::get_needed_fields() );

	}

	/**
	 * Prepare data for save/update
	 *
	 * @since 0.0.7
	 *
	 * @param $data
	 *
	 * @return array|\WP_Error Data as array or WP_Error if invalid
	 */
	protected static function prepare_data( $data ) {
		$data = static::validate_config( $data );
		if ( ! is_array( $data ) ) {
			if( is_wp_error( $data ) ) {
				return $data;
			}

			$class = get_called_class();
			$class = explode( "\\", $class );
			$class =  array_pop( $class );

			return new \WP_Error( 'ingot-invalid-config', '', array( $class ) );

		}

		$allowed = array_merge( static::required(), static::needed() );
		foreach ( $data as $k => $v ) {
			if ( is_numeric( $k ) || ! in_array( $k, $allowed ) ) {
				unset( $data[ $k ] );
			}
		}


		return $data;

	}

	/**
	 * Check if current user can (if cap check is needed
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @param int $id Item ID
	 * @param bool $bypass_cap Whether to bypass check
	 *
	 * @return bool True if user can, false if not
	 */
	protected static function can( $id, $bypass_cap ) {
		if ( ! $bypass_cap ) {
			/**
			 * Sets the capability required to preform update
			 *
			 * @since 0.0.5
			 *
			 * @param string $cap The capability
			 */
			$cap = apply_filters( 'ingot_save_cap', 'manage_options', static::what(), $id );
			$can = current_user_can( $cap );

		} else {
			$can = true;


		}

		/**
		 * Filter if user can
		 *
		 * @since 0.0.7
		 */
		return apply_filters( 'ingot_user_can', $can, $id, static::what() );
	}

	/**
	 * Get total number of items
	 *
	 * @since 0.2.0
	 *
	 * @return int
	 */
	public static function total(){
		_doing_it_wrong( __FUNCTION__, __( 'Must ovveride', 'ingot' ), '0.2.0' );
		return 0;

	}

}
