<?php
/**
 * Price test utilities
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\utility;


use ingot\testing\cookies\init;
use ingot\testing\crud\group;
use ingot\testing\crud\price_test;
use ingot\testing\crud\variant;
use ingot\testing\types;

class price {

	/**
	 * Ensure a number is a float to represent a percentage.
	 *
	 * Must be between -.9 and .9
	 *
	 * @since 0.2.0
	 *
	 * @param float $float
	 *
	 * @return bool
	 */
	public static function valid_percentage( $float ){
		if ( is_numeric( $float ) ) {
			if ( - 1 < $float && 1 > $float ) {
				return true;

			}
		}

		return false;

	}

	/**
	 * Get product associated with a price group
	 *
	 * @since 1.1.0
	 *
	 * @param $group
	 *
	 * @return array|null|\WP_Post
	 */
	public static function get_product( $group ){
		if( ! is_numeric( $group ) ){
			$group = group::read( $group );
		}

		if( group::valid( $group ) && 'price' == $group[ 'type' ] && isset( $group[ 'meta' ][ 'product_ID' ] ) ){
			$post = get_post( $group[ 'meta' ][ 'product_ID' ] );
			if( is_object( $post ) ) {
				return $post;
			}

		}

	}

	/**
	 * Get price variation or variation as a float for a variant
	 *
	 * @since 1.1.0
	 *
	 * @param int|array $variant Variation or variations ID
	 *
	 * @return float|void
	 */
	public static function get_price_variation( $variant ){
		if( is_numeric( $variant ) ){
			$variant = variant::read( $variant );
		}

		if( variant::valid( $variant ) && 'price' == $variant[ 'type' ] ){
			return $variant[ 'meta' ][ 'price' ];
		}


	}

	/**
	 * Get callback function for finding price of a product by plugin
	 *
	 * @since 1.1.0
	 *
	 * @param $plugin
	 *
	 * @return string
	 */
	public static function get_price_callback( $plugin ){
		/**
		 * Set the function to get the price for a product.
		 *
		 * Function must accept product ID as first argument.
		 *
		 * @since 1.1.0
		 *
		 * @param null|string|array $callback Name of callback. Defaults to null, which uses internal logic
		 * @param string $plugin Slug of plugin edd|woo
		 */
		$callback = apply_filters( 'ingot_get_price_callback', null, $plugin );
		if ( is_null( $callback ) ) {
			switch ( $plugin ) {
				case 'edd' :
					$callback = 'edd_get_download_price';
					break;
				default :
					$callback = '__return_false';
					break;
			}

		}

		return $callback;
	}

	/**
	 * Get callback function for finding a product
	 *
	 * @since 1.1.0
	 *
	 * @param string $plugin Slug of plugin edd|woo
	 *
	 * @return string
	 */
	public static function get_product_function( $plugin ){
		/**
		 * Set the function to get the product.
		 *
		 * Function must accept product ID as first argument.
		 *
		 * @since 1.1.0
		 *
		 * @param null|string|array $callback Name of callback. Defaults to null, which uses internal logic
		 * * @param string $plugin Slug of plugin edd|woo
		 */
		$callback = apply_filters( 'ingot_get_price_callback', null, $plugin );
		if (  is_null( $callback ) ) {
			switch ( $plugin ) {
				case 'edd' :
					$callback = 'edd_get_download';
					break;
				default:
					$callback = 'get_post';
					break;
			}
		}

		return $callback;
	}

	/**
	 * Get price test object from the price cookie
	 *
	 * @since 1.1.0
	 *
	 * @param string $plugin Slug of plugin edd|woo
	 * @param int $id  Product ID
	 *
	 * @return \ingot\testing\object\price\test
	 */
	public static function get_price_test_from_cookie( $plugin, $id ){

		if ( in_array( $plugin, types::allowed_price_types() ) ) {
			$cookie = init::get_instance()->get_ingot_cookie( false )[ 'price' ];
			if ( isset( $cookie[ $plugin ][ $id ] ) ) {
				return $cookie[ $plugin ][ $id ];
			}

		}

	}



}
