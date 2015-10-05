<?php
/**
 * Common util functions for testing types
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
namespace ingot\testing;


class types {

	/**
	 * Allowed test types
	 *
	 * @since 0.0.7
	 *
	 * @return array $types The allowed test types
	 */
	public static function allowed_types() {
		$types = array(
			'click',
			'price',
		);

		/**
		 * Allowed test types
		 *
		 * @since 0.0.7
		 *
		 * @param array $types The allowed test types
		 */
		return apply_filters( 'ingot_allowed_types', $types );

	}

	/**
	 * Allowed click test types
	 *
	 * @since 0.0.7
	 *
	 * @return array $types The allowed click test types
	 */
	public static function allowed_click_types() {
		$types = array(
			'link',
			'text',
			'button'
		);

		/**
		 * Allowed test types
		 *
		 * @since 0.0.7
		 *
		 * @param array $types The allowed click test types
		 */
		return apply_filters( 'ingot_allowed_click_types', $types );

	}

	/**
	 * Allowed price test types
	 *
	 * @since 0.0.7
	 *
	 * @return array $types The allowed click test types
	 */
	public static function allowed_price_types() {
		$types = array(

		);

		/**
		 * Allowed test types
		 *
		 * @since 0.0.7
		 *
		 * @param array $types The allowed price test types
		 */
		return apply_filters( 'ingot_allowed_price_types', $types );

	}


}
