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
	 * Get default for threshold to consider an average usable
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
		return (int) apply_filters( 'ingot_default_threshold', 500 );

	}

	/**
	 * Get default number of iterations to choose a variant at random.
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
		return (int) apply_filters( 'ingot_default_initial', 1000 );

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
		 * @param string $default The default color
		 */
		return helpers::prepare_color( apply_filters( 'ingot_default_button_color', $default ), true );

	}

	/**
	 * Get default color
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
		 * @param string $default The default color
		 */
		return helpers::prepare_color( apply_filters( 'ingot_default_button_color', '2e3842' ), false );

	}

	/**
	 * Get default text color
	 *
	 * @since 0.2.0
	 *
	 */
	public static function text_color() {
		/**
		 * Set default for button color
		 *
		 * @since 0.2.0
		 *
		 * @param string $default The default color
		 */
		return helpers::prepare_color( apply_filters( 'ingot_default_text_color', 'ffffff' ), false );

	}

}
