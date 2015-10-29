<?php
/**
 * Get default values. Mainly wrappers for filters
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\utility;


class defaults {

	/**
	 * Get default for threshold
	 *
	 * @since 0.1.1
	 *
	 * @return int
	 */
	public static function threshold() {
		/**
		 * Set default for threshold
		 *
		 * @since 0.1.1
		 *
		 * @param int $threshold
		 */
		return (int) apply_filters( 'ingot_default_threshold', 20 );

	}

	/**
	 * Get default for initial
	 *
	 * @since 0.1.1
	 *
	 * @return int
	 */
	public static function initial() {
		/**
		 * Set default for initial
		 *
		 * @since 0.1.1
		 *
		 * @param int $initial
		 */
		return (int) apply_filters( 'ingot_default_initial', 100 );

	}

	/**
	 * Get default for button color
	 *
	 * @since 0.1.1
	 *
	 * @return string
	 */
	public static function button_color( $with_hash = true ) {
		$default = self::color( $with_hash);

		/**
		 * Set default for button color
		 *
		 * @since 0.1.1
		 *
		 * @param int $initial
		 */
		return helpers::prepare_color( apply_filters( 'ingot_default_button_color', $default ), true );

	}

	/**
	 * Get default for color color
	 *
	 * @since 0.1.1
	 *
	 * @return string
	 */
	public static function color() {

		/**
		 * Set default for button color
		 *
		 * @since 0.1.1
		 *
		 * @param int $initial
		 */
		return helpers::prepare_color( apply_filters( 'ingot_default_button_color', '2e3842' ), false );

	}

}
