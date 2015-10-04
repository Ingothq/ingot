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
	 * @param string $key
	 * @param array|object $thing
	 * @param null|mixes $default
	 *
	 * @return mixed
	 */
	public static function v( $key, $thing, $default = null ) {
		if ( ! empty( $thing ) ) {
			if ( is_array( $thing ) ) {
				if ( isset( $thing[ $key ] ) ) {
					return $thing[ $key ];

				} else {
					return $default;

				}
			}

			if ( is_object( $thing ) ) {
				if( isset( $thing->$key ) ) {
					return $thing->$key;

				}else{
					return $default;

				}

			}
		}else{
			return $default;

		}

	}

}
