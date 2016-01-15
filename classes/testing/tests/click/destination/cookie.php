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
		if ( ! headers_sent() ) {
			$expires = ingot_cookie_time();
			setcookie( self::cookie_key( $group_id ), (string) $variant_id, $expires, COOKIEPATH, COOKIE_DOMAIN, false );
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


}
