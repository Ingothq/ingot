<?php
/**
 * Generic getters for tests, groups of either type
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\utility;


use ingot\testing\crud\group;
use ingot\testing\crud\price_group;
use ingot\testing\crud\price_test;
use ingot\testing\crud\test;

class get {

	/**
	 * Generic getter for groups of both types
	 *
	 * @since 0.2.0
	 *
	 * @param int $id Group ID
	 * @param string $what Optional. Which type of group. Defaults to click.
	 *
	 * @return array|bool
	 */
	public static function group( $id, $what = 'click' ) {
		if( self::is_price( $what ) )  {
			return price_group::read( $id );

		}

		return group::read( $id );

	}

	/**
	 * Generic getter for tests of both types
	 *
	 * @param int $id Test ID
	 * @param string $what Optional. Which kind of test. Defaults to click
	 *
	 * @return array|bool
	 */
	public static function test( $id, $what = 'click' ){
		if( self::is_price( $what ) ) {
			return price_test::read( $id );

		}

		return test::read( $id );

	}

	public static function current_sequence( $group, $what = 'click' ) {
		if( ! is_array( $group ) ) {
			$group = group::read( $group );
		}

		return helpers::v( 'current_sequence', $group, array() );
	}

	protected static function is_price( $what ){
		if( 'price_test' == $what || 'price' == $what ) {
			return true;

		}

	}
	
	


}
