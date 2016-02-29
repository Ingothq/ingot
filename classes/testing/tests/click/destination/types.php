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

		if( ! ingot_is_woo_active() ) {
			unset( $types[ 'cart_woo' ] );
			unset( $types[ 'sale_woo' ] );
		}

		if( ! ingot_is_edd_active() ) {
			unset( $types[ 'cart_edd' ] );
			unset( $types[ 'sale_edd' ] );
		}

		if( ! ingot_is_give_active() ) {
			unset( $types[ 'givewp' ] );
		}

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
			'cart_edd' => [
				'name'        => __( 'Add To Cart -- Easy Digital Downloads', 'ingot' ),
				'description' => __( 'Conversion is registered when an item is added to the Easy Digital Downloads cart.', 'ingot' ),
			],
			'sale_edd' => [
				'name'        => __( 'Purchase -- Easy Digital Downloads', 'ingot' ),
				'description' => __( 'Conversion is registered when an Easy Digital Downloads sale is completed.', 'ingot' ),
			],
			'cart_woo' => [
				'name'        => __( 'Add To Cart -- WooCommerce', 'ingot' ),
				'description' => __( 'Conversion is registered when an item is added to the WooCommerce cart.', 'ingot' ),
			],
			'sale_woo' => [
				'name'        => __( 'Purchase -- WooCommerce', 'ingot' ),
				'description' => __( 'Conversion is registered when a WooCommerce sale is completed.', 'ingot' ),
			],
			'hook'     => [
				'name'        => __( 'Hook', 'ingot' ),
				'description' => __( 'Conversion is registered when a hook is fired -- for developers.', 'ingot' )
			],
			'givewp'   => [
				'name'        => __( 'Give', 'ingot' ),
				'description' => __( 'Conversion is registered when a donation is made.', 'ingot' )
			]
		];

		return $types;

	}

}
