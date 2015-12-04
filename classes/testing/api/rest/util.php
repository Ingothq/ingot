<?php
/**
 * Utility functions for the Ingot REST API
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\api\rest;


class util {

	/**
	 * Get Ingot API namespace with current API version
	 *
	 * @since 0.2.0
	 *
	 * @return string
	 */
	public static function get_namespace() {
		$version   = '1';
		$namespace = 'ingot/v' . $version;

		return $namespace;
	}

	/**
	 * Get URL for Ingot API or an API route
	 *
	 * Note URL is returned unescaped. Don't forget to late escape.
	 *
	 * @since 0.2.0
	 *
	 * @param null|string $route Optional. If null base URL is returned. Optionally provide a route name.
	 *
	 * @return string
	 */
	public static function get_url( $route = null ) {

		if( $route ) {
			$route = self::get_route( $route );
		}else{
			$route = self::get_namespace();
		}

		$url = trailingslashit( rest_url( $route ) );

		return $url;

	}

	/**
	 * Get a route with namespace
	 *
	 * @since 0.0.2
	 *
	 * @param string $route Route name
	 *
	 * @return string
	 */
	public static function get_route( $route ) {
		return trailingslashit( self::get_namespace() ) . trailingslashit( str_replace( '_', '-', $route ) );
	}

	/**
	 * Verify sessions nonce
	 *
	 * @since 0.3.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public static function verify_session_nonce( $request ) {
		$nonce = $request->get_param( 'ingot_session_nonce' );

		if ( is_string( $nonce ) ) {
			return ingot_verify_session_nonce( $nonce );
		}

	}


}
