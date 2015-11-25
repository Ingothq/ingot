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


use ingot\testing\crud\group;
use ingot\testing\crud\price_test;

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
	 * Prepare price test details needed for use in cookies/tracking
	 *
	 * @since 0.2.0
	 *
	 * @param array $price_test Price test config
	 * @param string $a_or_b a|b
	 * @param int $sequence_id Sequence ID
	 * @param int $group_id Group ID
	 *
	 * @return array
	 */
	public static function price_detail( $price_test, $a_or_b, $sequence_id, $group_id ) {
		if( is_numeric( $price_test ) ) {
			$price_test = price_test::read( $price_test );
		}

		if ( is_numeric( $group_id ) ) {
			$group = group::read( $group_id );
		}elseif( is_array( $group_id ) ) {
			$group = $group_id;
			$group_id = helpers::v( 'ID', $group, 0 );
		}else{
			return array();
		}

		if(  ! is_array( $price_test ) || ! is_array( $group ) ) {
			return array();
		}

		$details = array(
			'plugin'      => $group['plugin'],
			'product_ID'  => $price_test['product_ID'],
			'test_ID'     => $price_test['ID'],
			'sequence_ID' => $sequence_id,
			'group_ID'    => $group_id,
			'a_or_b'      => $a_or_b
		);

		return $details;

	}

	/**
	 * Holds current price tests
	 *
	 * @since 0.2.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private static $current;

	/**
	 * Get and optionally set the current price tests
	 *
	 * @since 0.2.0
	 *
	 * @param null|array  $current Optional. IF an array updates current.
	 *
	 * @return array Current price tests
	 */
	public static function current( $current = null ){
		if( is_array( $current ) ) {
			self::$current == $current;
		}

		return self::$current;

	}

}
