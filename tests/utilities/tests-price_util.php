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

	/**
	 * Check price detail util
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\utility\price::price_detail()
	 */
	function testPriceDetails(){
		return;
		$product_id = 9;
		$params     = array(
			'product_ID' => $product_id,
			'default'    => rand( -0.9, 0.9 )
		);

		for ( $i = 0; $i <= 3; $i ++ ) {
			$id = \ingot\testing\crud\price_test::create( $params );
			$tests[ $i ] = $id;
			$params[ 'default' ] = rand( -0.9, 0.9 );

		}

		$params = array(
			'type'       => 'price',
			'plugin'     => 'edd',
			'group_name' => rand(),
			'test_order' => $tests,
			'product_ID' => $product_id

		);

		$group_id = \ingot\testing\crud\price_group::create( $params );
		$group = \ingot\testing\crud\price_group::read( $group_id );
		$sequence_id =  $group[ 'current_sequence' ];

		$test = \ingot\testing\crud\price_test::read( $tests[ rand( 0, 3 )  ] );

		$details = \ingot\testing\utility\price::price_detail( $test, 'a', $sequence_id, $group_id );

		$this->assertEquals( $details[ 'plugin' ], $test[ 'plugin' ] );
		$this->assertEquals( $details[ 'product_ID' ], $test[ 'product_ID' ] );
		$this->assertEquals( $details[ 'test_ID' ], $test[ 'ID' ] );
		$this->assertEquals( $details[ 'sequence_ID' ], $sequence_id );
		$this->assertEquals( $details[ 'group_ID' ], $group_id );
	}

}
