<?php
/**
 * Utility functions for Ingot
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\utility;


class helpers {

	/**
	 * Get key/property from array or object with fallback.
	 *
	 * This method wishes it was pods_v()
	 *
	 * @since 0.0.3
	 *
	 * @param string $key Key/index/property to search for.
	 * @param array|object $thing Object or array to search for index/key/property in.
	 * @param null|mixed $default Default if not set.
	 * @param bool|array|string Optional. If true, santize method of this class is used to sanatized input. If callaballe output is passed through that, else no extra sanitization.
	 *
	 *
	 * @return mixed
	 */
	public static function v( $key, $thing, $default = null, $sanitize = false ) {
		$return = $default;
		if ( ! empty( $thing ) ) {
			if ( is_array( $thing ) ) {
				if ( isset( $thing[ $key ] ) ) {
					$return = $thing[ $key ];

				}
			}

			if ( is_object( $thing ) ) {
				if ( isset( $thing->$key ) ) {
					$return = $thing->$key;


				}

			}

		}

		if( true == $sanitize ){
			return self::sanitize( $return );
		}elseif( is_callable( $sanitize ) ) {
			if ( ! empty( $return ) ) {
				call_user_func( $sanitize, $return );
			}
		}else{
			return $return;

		}

	}

	/**
	 * Get key/property from array or object with fallback and recursive sanitzation.
	 *
	 * This method wishes it was pods_v_sanitized()
	 *
	 * @since 0.0.3
	 *
	 * @param string $key Key/index/property to search for.
	 * @param array|object $thing Object or array to search for index/key/property in.
	 * @param null|mixed $default Default if not set.
	 *
	 * @return mixed
	 */
	public static function v_sanitized( $input, $thing, $default ){
		return self::v( $input, $thing, $default, true );

	}

	/**
	 * Filter input and return sanitized output
	 *
	 * Pretty much copypasta of pods_sanitize()
	 *
	 * @since 0.0.9
	 *
	 * @param mixed $input The string, array, or object to sanitize
	 * @param array $params Optional Additional options
	 *
	 * @return array|mixed|object|string|void
	 */
	public static function sanitize( $input, $params = array() ) {
		if ( '' === $input || is_int( $input ) || is_float( $input ) || empty( $input ) ) {
			return $input;
		}

		$output = array();

		$defaults = array(
			'nested' => false,
			'type' => null // %s %d %f etc
		);

		if ( !is_array( $params ) ) {
			$defaults[ 'type' ] = $params;

			$params = $defaults;
		}
		else {
			$params = array_merge( $defaults, (array) $params );
		}

		if ( is_object( $input ) ) {
			$input = get_object_vars( $input );

			$n_params = $params;
			$n_params[ 'nested' ] = true;

			foreach ( $input as $key => $val ) {
				$output[ self::sanitize( $key ) ] = self::sanitize( $val, $n_params );
			}

			$output = (object) $output;
		}
		elseif ( is_array( $input ) ) {
			$n_params = $params;
			$n_params[ 'nested' ] = true;

			foreach ( $input as $key => $val ) {
				$output[ self::sanitize( $key ) ] = self::sanitize( $val, $n_params );
			}
		}
		elseif ( !empty( $params[ 'type' ] ) && false !== strpos( $params[ 'type' ], '%' ) ) {
			/**
			 * @var $wpdb wpdb
			 */
			global $wpdb;

			$output = $wpdb->prepare( $params[ 'type' ], $output );
		}
		// @todo Switch this full over to esc_sql once we get sanitization sane again in PodsAPI so we *don't* have to unsanitize in various places
		elseif ( function_exists( 'wp_slash' ) ) {
			$output = wp_slash( $input );
		}
		else {
			$output = esc_sql( $input );
		}

		return $output;
	}



}
