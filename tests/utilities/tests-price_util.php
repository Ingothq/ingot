<?php

/**
 * Tests for price test utilites
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class tests_price_util extends \WP_UnitTestCase {

	/**
	 * Ensure valid floats are allowed
	 *
	 * @since 0.2.0
	 *
	 * @covers ingot\testing\utility\price::valid_percentage
	 */
	function testFloatTestValid(){
		$numbers = array(
			.5,
			'0.5',
			'0.005',
			0.05,
			-.1,
			'-.001',
			'-.02',
			-.9,
			0,
			'0'
		);
		foreach( $numbers as $number ){
			$this->assertTrue( ingot\testing\utility\price::valid_percentage( $number, $number ) );
		}
	}

	/**
	 * Ensure invalid floats are not allowed
	 *
	 * @since 0.2.0
	 *
	 * @covers ingot\testing\utility\price::valid_percentage
	 */
	function testFloatTestinValid(){
		$numbers = array(
			1.5,
			'1.5',
			'4.005',
			1.05,
			-2,
			'-6.001',
			'-1',
			-999,
			'hats',
			array(),
			new stdClass(),
			(object) array( -0.1, .9 ),
			json_encode( array( -0.1, .9 ) ),
			serialize( array( -.05, .9 ) )
		);
		foreach( $numbers as $number ){
			$this->assertFalse( ingot\testing\utility\price::valid_percentage( $number, $number ) );
		}
	}

}
