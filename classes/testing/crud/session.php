<?php
/**
 * CRUD for tracking sessions
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\crud;


class session extends crud {
	/**
	 * Name of this object
	 *
	 * @since 0.3.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected static $what = 'session';

	/**
	 * Get name of this object
	 *
	 * @since 0.3.0
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
	 * @since 0.3.0
	 *
	 * @access protected
	 *
	 * @param array $data Item config
	 *
	 * @return bool|array Item config array if valid, false if not.
	 */
	protected static function validate_config( $data ) {
		if( ! isset( $data[ 'created' ] ) ) {
			$data[ 'created' ] = current_time( 'mysql' );
		}

		if( ! isset( $data[ 'IP' ] ) )  {
			$data[ 'IP' ] = ingot_get_ip();
		}
		if( ! isset( $data[ 'uID' ] ) ) {
			$data[ 'uID' ] = get_current_user_id();
		}
		if( ! isset( $data[ 'slug' ] ) ){
			$data[ 'slug' ] = self::get_slug();
		}

		if( ! isset( $data[ 'used' ] ) ){
			$data[ 'used' ] = 0;
		}

		if( isset( $data[ 'click_test_ID' ] ) ){
			$data[ 'click_test_ID' ] = absint( $data[ 'click_test_ID' ] );
		}else{
			$data[ 'click_test_ID' ] = 0;
		}

		if( $data[ 'click_test_ID' ] ) {
			$test = test::read( $data[ 'click_test_ID' ] );
			if( ! is_array( $test ) ) {
				$data[ 'click_test_ID' ] = 0;
			}

		}

		if( isset( $data[ 'click_url' ] )) {
			$data[ 'click_url' ] = esc_url_raw( $data[ 'click_url' ] );
		}else{
			$data[ 'click_url' ] = '';
		}

		if( ! isset( $data[ 'ingot_ID' ] ) ){
			$data[ 'ingot_ID' ] = self::find_ingot_id( $data[ 'uID' ],$data[ 'IP' ] );
		}

		$data[ 'used' ] = (bool) $data[ 'used' ];
		$data[ 'used' ] = (int) $data[ 'used' ];

		foreach( self::required() as $key ) {
			if( ! isset( $data[ $key ] ) ){
				return new \WP_Error( 'ingot-session-crud-validation-fail', '', array(
						'fail-key' => $key,
						'data' => $data,
					)
				);
			}
		}

		$allowed = array_merge( self::required(), self::needed() );
		foreach( $data as $key => $datum ) {
			if( ! in_array( $key, $allowed ) ) {
				unset( $data[ $key ] );
			}elseif( is_numeric( $datum ) ){
				$data[ $key ] = (string) $datum;
			}
		}

		return $data;

	}

	/**
	 * Check if a session is used
	 *
	 * @since 0.3.0
	 *
	 * @param int $id Session ID
	 *
	 * @return bool True if used, false if not
	 */
	public static function is_used( $id ) {
		$session = self::read( $id );
		return (bool) $session[ 'used' ];
	}

	/**
	 * Mark a session as having been used
	 *
	 * @since 0.3.0
	 *
	 * @param int $id Sessi
	 *
	 * @return bool|int
	 */
	public static function mark_used( $id ){
		if( ! self::is_used( $id ) ) {
			$session = self::read( $id );
			$session[ 'used' ] = 1;
			return self::save( $session, $id, true );
		}


	}

	/**
	 * Get "slug" for request if possible.
	 *
	 * @since 0.3.0
	 *
	 * @return string|void
	 */
	protected static function get_slug() {
		if( isset( $_SERVER ) && isset( $_SERVER[ 'REQUEST_URI']) ){
			return $_SERVER[ 'REQUEST_URI' ];
		}

	}

	/**
	 * Find ingot_ID value to tie sessions to same user/IP
	 *
	 * @since 0.3.0
	 *
	 * @param bool|false $uID
	 * @param bool|false $ip
	 *
	 * @return int Returns ingot_ID, possibly a fresh new one, maybe a matched one
	 */
	public static function find_ingot_id( $uID = false, $ip = false ){
		if( ! $uID && 0 != get_current_user_id() ){
			$uID = get_current_user_id();
			$id =  self::lookup_by_uID( $uID );
		}else{
			if( ! $ip ) {
				$ip = ingot_get_ip();
			}

			$id =  self::lookup_by_IP( $ip );


		}

		if( ! $id ) {
			$last_assigned_key = '_ingot_session_ID_last_assigned';
			$last_assigned = get_option( $last_assigned_key, 0 );
			$id = $last_assigned + 1;
			update_option( $last_assigned_key, $id );
		}

		return (int) $id;

	}

	/**
	 * Looks up ingot_ID by user ID
	 *
	 * @since 0.3.0
	 *
	 * @access protected
	 *
	 * @param int $uID User ID
	 *
	 * @return int
	 */
	protected static function lookup_by_uID( $uID ) {

		$lookup_field = 'uID';

		return (int) self::lookup_ingot_id_by( $lookup_field, $uID );

	}

	/**
	 * Looks up ingot_ID by user IP
	 *
	 * @since 0.3.0
	 *
	 * @access protected
	 *
	 * @param string $ip IP address
	 *
	 * @return string|void
	 */
	protected static function lookup_by_IP( $ip ) {

		$lookup_field = 'IP';

		return self::lookup_ingot_id_by( $lookup_field, $ip );

	}

	/**
	 * Necessary, but not required fields of this object
	 *
	 * @since 0.3.0
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected static function needed() {
		$needed = [
			'created'
		];

		return $needed;
	}

	/**
	 * Required fields of this object
	 *
	 * @since 0.3.0
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected static function required() {
		$required = [
			'ingot_ID',
			'IP',
			'uID',
			'slug',
			'used',
			'click_url',
			'click_test_ID'
		];

		return $required;
	}

	/**
	 * Lookup ingot_ID by some other field
	 *
	 * @since 0.3.0
	 *
	 * @access protected
	 *
	 * @param string $lookup_field Field to base look up on
	 * @param string $lookup_by Value to lookup by
	 *
	 * @return string|void
	 */
	protected static function lookup_ingot_id_by( $lookup_field, $lookup_by ) {
		global $wpdb;
		$table_name = static::get_table_name();

		if( 'IP' == $lookup_field ) {
			$lookup_by = '"' .  $lookup_by . '"';
		}

		$sql    = sprintf( 'SELECT MAX(`ingot_ID`) FROM %s WHERE `%s` = %s LIMIT 1', $table_name, $lookup_field, $lookup_by );
		$result = $wpdb->get_results( $sql, ARRAY_N );
		if ( is_array( $result ) && ! empty( $result ) && isset( $result[ 0 ], $result[ 0 ][ 0 ] ) ) {
			return $result[ 0 ][ 0 ];
		}
	}


}
