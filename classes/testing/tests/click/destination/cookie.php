<?php
/**
 * Cookies for destination tests
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\testing\tests\click\destination;


class cookie {

	/**
	 * Set a cookie for a destination test
	 *
	 * @todo use main cookie class?
	 *
	 * @since 1.1.0
	 *
	 * @param int $group_id ID of group
	 * @param int $variant_id ID of chosen variant
	 */
	public static function set_cookie( $group_id, $variant_id ){
		$name = self::cookie_key( $group_id );
		if ( ! headers_sent() && ! isset( $_COOKIE[ $name ] )) {
			$expires = time() + ingot_cookie_time();

			$set = setcookie( $name, (string) $variant_id, $expires, COOKIEPATH, COOKIE_DOMAIN, false );
			do_action( 'ingot_destination_cookie_set', $set, $name, $group_id, $variant_id, $expires  );
		}

	}

	/**
	 * Clear destination test cookie
	 *
	 * @since 1.1.0
	 *
	 * @param int $group_id ID of group
	 */
	public static function clear_cookie( $group_id ){
		$key = self::cookie_key( $group_id );
		if( isset( $_COOKIE[ $key ] ) ){
			unset( $_COOKIE[ $key ]);
			if ( ! headers_sent() ) {
				setcookie( $key, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, false );
			}

		}

	}

	/**
	 * Set a cookie for a destination test
	 *
	 * NOTE: Generally should not be used, and ingot\testing\tests\click\destination\init::get_test() should be used instead since that can get tests added in this session.
	 *
	 * @todo use main cookie class?
	 *
	 * @since 1.1.0
	 *
	 * @param int $group_id ID of group
	 *
	 * @return int Variant ID
	 */
	public static function get_cookie( $group_id ) {
		$key = self::cookie_key( $group_id );
		if ( isset( $_COOKIE[ $key ] ) ) {
			return absint( $_COOKIE[ $key ] );
		}

	}


	/**
	 * Get the index name to use in cookie
	 *
	 * @since 1.1.0
	 *
	 * @param int $group_id ID of group
	 *
	 * @return string
	 */
	public static function cookie_key( $group_id ){
		return 'ingot_destination_' . $group_id;
	}

	/**
	 * Get all destination cookies
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public static function get_all_cookies(){
		if ( isset( $_COOKIE ) && is_array( $_COOKIE) ) {
			return \ingot\testing\utility\array_filters::filter_results( $_COOKIE, 'ingot_destination_' );
		} else {
			return [];
		}

	}



}
