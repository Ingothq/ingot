<?php
/**
 * Utility functions for destination content types
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\testing\utility;

use ingot\testing\tests\click\destination\cookie;
use ingot\testing\tests\click\destination\types;

class destination {


	/**
	 * Check if a destination tests should set tagline
	 *
	 * @param $group
	 *
	 * @param array $group Group config
	 *
	 * @return bool True if is valid group of the destination subtype and is a tagline test
	 */
	public static function is_tagline( array $group ){
		if ( self::is_destination( $group ) ) {
			return helpers::v( 'is_tagline', $group[ 'meta' ], false );
		}

	}

	/**
	 * Get destination type
	 *
	 * @since 1.1.0
	 *
	 * @param array $group Group config
	 *
	 * @return string|bool Destination type or false if $group is not valid group config
	 */
	public static function get_destination( array $group ){
		if ( self::is_destination( $group ) ) {
			return helpers::v( 'destination', $group[ 'meta' ], false );
		}

	}


	/**
	 * Check if a destination tests is a page ID type of destination test.
	 *
	 * @since 1.1.0
	 *
	 * @param array $group Group config
	 *
	 * @return bool True if is valid group of the destination subtype of the page ID type of destination test.
	 */
	public static function get_page_id( array $group ){
		if ( self::is_destination( $group ) ) {
			return helpers::v( 'page', $group[ 'meta' ], 0 );
		}

	}

	/**
	 * Register a conversion for a destination test
	 *
	 * @since 1.1.0
	 *
	 * @param int $group_id
	 */
	public static function conversion( $group_id ){
		$variant_id = cookie::get_cookie( $group_id );
		if( is_numeric( $variant_id ) ){
			ingot_register_conversion( $variant_id );
		}

	}

	/**
	 * Check if is a destination test
	 *
	 * @since 1.1.0
	 *
	 * @param array $group Group config
	 *
	 * @return bool True if is valid group of the destination subtype
	 */
	public static function is_destination( array $group ) {
		if ( 'destination' == \ingot\testing\utility\group::sub_type( $group ) ) {
			return true;
		}
	}

	/**
	 * Check if is a hook test
	 *
	 * @since 1.1.0
	 *
	 * @param array $group Group config
	 *
	 * @return bool True if is valid group of the destination subtype
	 */
	public static function is_hook( array $group ) {
		if ( 'hook' == \ingot\testing\utility\group::sub_type( $group ) ) {
			return true;
		}

	}

	/**
	 * Prepare the meta in group config array
	 *
	 * @since 1.1.0
	 *
	 * @param array $group Group config
	 *
	 * @return array|\WP_Error
	 */
	public static function prepare_meta( array $group ){
		if( ! isset( $group[ 'meta' ] ) || ! is_array( $group[ 'meta' ] ) ){
			$group[ 'meta' ] = [];
		}

		$meta = $group[ 'meta' ];

		if( ! isset( $meta[ 'destination' ] ) || ! types::allowed_destination_type( $meta[ 'destination' ] ) ){
			return new \WP_Error( 'ingot-invalid-destination-type', __( 'Invalid destination', 'ingot' ),
				[
					'destination' => helpers::v( 'destination', $meta, false ),
					'meta' => $meta
				]);
		}

		if ( 'page' == $meta[ 'destination' ] ) {

			if ( ! isset( $meta[ 'page' ] ) ) {
				return new \WP_Error( 'ingot-invalid-destination-page', __( 'Page destination types need a page ID', 'ingot' ) );
			} else {
				$meta[ 'page' ] = absint( $meta[ 'page' ] );
			}
		}

		if ( 'hook' == $meta[ 'destination' ] ) {

			if ( ! isset( $meta[ 'hook' ] ) ) {
				return new \WP_Error( 'ingot-invalid-destination-hook', __( 'Hook destination types need a hook.', 'ingot' ) );
			} else {
				$meta[ 'hook' ] = trim( $meta[ 'hook' ] );
			}
		}

		if( ! isset( $meta[ 'is_tagline' ] ) ){
			$meta[ 'is_tagline' ] = false;
		}

		$meta[ 'is_tagline' ] = (bool) $meta[ 'is_tagline' ];



		$group[ 'meta' ] = $meta;

		return $group;

	}

	/**
	 * Hooks to use for tracking
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public static function hooks(){
		$hooks = [
			'template_redirect' => null
		];

		/**
		 * Change or add hooks for destination conversion tracking
		 *
		 * @since 1.1.0
		 *
		 * @param array $hooks hook_name => callback If callback is null, then it must exist in the `ingot\testing\tests\click\destination\hooks` class or else bad things...
		 */
		return apply_filters( 'ingot_destination_hooks', $hooks );

	}

}
