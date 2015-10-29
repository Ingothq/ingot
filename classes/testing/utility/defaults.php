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

}
