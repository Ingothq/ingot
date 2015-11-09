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
	 * @param bool $with_labels Optional. If true, labels are included. If false, the default, only types are returned
	 *
	 * @return array $types The allowed click test types
	 */
	public static function allowed_click_types( $with_labels = false ) {
		$types = array(
			'link' => __( 'Link', 'ingot' ),
			'text' => __( 'Text', 'ingot' ),
			'button' => __( 'Button', 'ingot' ),
			'button_color' => __( 'Button Color', 'ingot' ),
		);

		//@todo figure out how to make content blocks work
		unset( $types[ 'text' ] );

		/**
		 * Allowed test types
		 *
		 * Types are keys, labels are values
		 *
		 * @since 0.0.7
		 *
		 * @param array $types The allowed click test types
		 */
		$types = apply_filters( 'ingot_allowed_click_types', $types );

		if( false == $with_labels ) {
			return array_keys( $types );

		}else{
			return $types;

		}

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
