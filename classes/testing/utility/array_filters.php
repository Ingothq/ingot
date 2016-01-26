<?php
/**
 * Array filters searching by beginning of string
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\testing\utility;


class array_filters {

	/**
	 * Holds string we are searching for.
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected static $find;

	/**
	 * Holds matches when seeking results
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected static $matches = [];

	/**
	 * Holds data when needed inside of closure
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected static $data = [];

	/**
	 * Array filter for items that start with a specific string
	 *
	 * @since 1.1.0
	 *
	 * @param array $data Data to search in
	 * @param string $find String to search for
	 *
	 * @return array
	 */
	public static function filter( array $data, $find ){
		$data = self::prepare( $data, $find );
		return array_filter( $data, function( $item ) {
			return self::match( $item, self::$find );
		});
	}

	/**
	 * Array filter for items whose KEYS start with a specific string
	 *
	 * @since 1.1.0
	 *
	 * @param array $data Data to search in
	 * @param string $find String to search for
	 *
	 * @return array
	 */
	public static function filter_keys( array $data, $find ){
		$data = self::prepare( $data, $find );
		$results =  self::filter( array_flip( $data ), $find );
		if( is_array( $results ) && ! empty( $results ) ) {
			return array_values( $results );
		}
	}

	/**
	 * Find items whose KEYS start with a specific string and return string after that string for results
	 *
	 * @since 1.1.0
	 *
	 * @param array $data Data to search in
	 * @param string $find String to search for
	 *
	 * @return array
	 */
	public static function filter_results( array $data, $find ){
		$data = self::prepare( $data, $find );
		array_filter( array_flip( $data ), function( $item ) {
			if( self::match( $item, self::$find ) ) {
				self::$matches[] = substr( $item, strlen( self::$find ) );
			}
		});

		return self::$matches;
	}

	public static function filter_values( array $data, $find ){

		$data = self::prepare( $data, $find );
		$data = self::$data = self::flatten( $data );
		array_filter( array_flip( $data ), function( $item ) {
			if( self::match( $item, self::$find ) && isset( self::$data[ $item ] ) ) {
				self::$matches[] = self::$data[ $item ];
			}

		});

		return self::$matches;
	}

	protected static function match( $item, $find ){
		return is_string( $item ) && 0 === strpos( $item, $find );
	}

	public static function flatten( $array ){
		return array_filter( $array, function( $item ) {
			return is_string( $item ) || is_int( $item );
		});
	}

	protected static function prepare( $data, $find ){
		self::$find = $find;
		self::$matches = [];
		self::$data = [];
		$data  = self::flatten( $data );
		if ( ! empty( $data ) ) {
			self::$data = $data;
			return $data;
		}else{
			return self::$data;
		}
	}

}

