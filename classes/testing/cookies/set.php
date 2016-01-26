<?php
/**
 * Sets up cookies and price testing
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\testing\cookies;


use ingot\testing\tests\price\plugins\edd;
use ingot\testing\tests\price\plugins\woo;

class set {

	/**
	 * Run all parts of cookie/price tests setup
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public static function run(){

		if( false == ingot_is_front_end() ) {
			return false;
		}

		$all_cookies = $cookies = array();


		if ( INGOT_DEV_MODE ) {
			if ( isset( $_COOKIE ) && is_array( $_COOKIE ) ) {
				$all_cookies = $_COOKIE;
			}


			$cookies = init::create( $all_cookies );
			self::setup_cookies( $cookies );
		}

		/**
		 * Fires after Ingot Cookies Are Set
		 *
		 * Note: will fire if they were set empty
		 * Should happen at init:25
		 *
		 * @since 0.0.9
		 *
		 * @param \ingot\testing\cookies\init $cookies Cookies object
		 */
		do_action( 'ingot_cookies_set', $cookies );

		return true;

	}


	/**
	 * Set the actual cookies
	 *
	 * @since 1.1.0
	 *
	 * @param \ingot\testing\cookies\init $cookies Cookies init class
	 */
	public static function setup_cookies( $cookies ){
		if( ! empty( $cookies->get_ingot_cookie( false ) ) ){
			$cookie_time = ingot_cookie_time();
			$cookie_name = $cookies->get_cookie_name();
			setcookie( $cookie_name, $cookies->get_ingot_cookie(true), time() + $cookie_time, COOKIEPATH, COOKIE_DOMAIN, false );

		}

	}

	/**
	 * Run the price testing setup
	 *
	 * @since 1.1.0
	 *
	 * @param array $ingot_cookies Cookies as an array
	 *
	 * @return array Array of setup class objects
	 */
	public static function price_testing( $ingot_cookies ){
		$objects = [];
		if ( ingot_is_edd_active() && isset( $ingot_cookies[ 'price' ][ 'edd' ] ) && ! empty( $ingot_cookies[ 'price' ][ 'edd' ] )  ) {
			$objects[ 'edd' ] = new edd( $ingot_cookies[ 'price' ][ 'edd' ] );

		}

		if ( ingot_is_woo_active() && isset( $ingot_cookies[ 'price' ][ 'woo' ] ) && ! empty( $ingot_cookies[ 'price' ][ 'woo' ] )  ) {
			$object[ 'woo' ] = new woo( $ingot_cookies[ 'price' ][ 'woo' ] );

		}

		/**
		 * Runs after price tests are initialized with the object for each of those classes.
		 *
		 * NOTE: May be empty array if no price testing is possible.
		 *
		 * @since 1.1.0
		 *
		 * @param array $ingot_cookies Cookies as an array
		 */
		return apply_filters( 'ingot_price_test_objects', $objects );

	}

}
