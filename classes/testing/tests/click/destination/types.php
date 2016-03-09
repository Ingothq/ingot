<?php
/**
 * Defines and tests possible destiantion tests
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\testing\tests\click\destination;


class types {

	/**
	 * Get possible destination test types
	 *
	 * @param bool $with_labels Optional. If true, name/description returned. If false, the default only "slugs"
	 *
	 * @return array
	 */
	public static function destination_types( $with_labels = false, $api_format = false ){
		$types = self::get_internal_types();

		/**
		 * Allowed test types
		 *
		 * Types are keys, labels are values
		 *
		 * @since 0.0.7
		 *
		 * @param array $types The allowed click test types
		 */
		$types = apply_filters( 'ingot_allowed_destination_types', $types );

		if( $api_format ) {
			$_types = [];
			foreach( $types as $value => $type ){
				$_types[ $value ] = array_merge( $type, [ 'value' => $value ] );

			}

			return $_types;
		}

		if( false == $with_labels ){
			$types = array_keys( $types );
		}

		return $types;

	}

	/**
	 * Is an allowed destination type?
	 *
	 * @since 1.1.0
	 *
	 * @param string $type The type to test
	 *
	 * @return bool
	 */
	public static function allowed_destination_type( $type ) {
		return in_array( $type, self::destination_types( false ) );
	}

	/**
	 * Get internal destination test types
	 *
	 * @since 1.1.0
	 *
	 * @acces protected
	 *
	 * @return array
	 */
	protected static function get_internal_types() {
		$types = [
			'page'     => [
				'name'        => __( 'Page', 'ingot' ),
				'description' => __( 'Conversion is registered when user reaches a page.', 'ingot' ),
			],
			'hook'     => [
				'name'        => __( 'Hook', 'ingot' ),
				'description' => __( 'Conversion is registered when a hook is fired -- for developers.', 'ingot' )
			]
		];

		return $types;

	}

}
