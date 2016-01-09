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
	 * @group price
	 * @group helpers
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
			$this->assertTrue( ingot\testing\utility\price::valid_percentage( $number ) );
		}
	}

	/**
	 * Ensure invalid floats are not allowed
	 *
	 * @since 0.2.0
	 *
	 * @group price
	 * @group helpers
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
			$this->assertFalse( ingot\testing\utility\price::valid_percentage( $number ) );
		}
	}

	/**
	 * Test checking validity of price sub_type
	 *
	 * @since 1.1.0
	 *
	 * @group price
	 * @group helpers
	 *
	 * @covers ingot_acceptable_plugin_for_price_test()
	 * @covers ingot_accepted_plugins_for_price_tests()
	 * @covers \ingot\testing\types::allowed_price_types()
	 */
	public function testAllowedSubTypes(){
		foreach( ['woo', 'edd' ] as $plugin ){
			$this->assertTrue( in_array( $plugin, \ingot\testing\types::allowed_price_types() ) );
			$this->assertTrue( ingot_acceptable_plugin_for_price_test( $plugin ) );
			$this->assertTrue( in_array( $plugin, ingot_accepted_plugins_for_price_tests() ) );
		}

		$plugin = 'batman';
		$this->assertFalse( in_array( $plugin, \ingot\testing\types::allowed_price_types() ) );
		$this->assertFalse( ingot_acceptable_plugin_for_price_test( $plugin ) );
		$this->assertFalse( in_array( $plugin, ingot_accepted_plugins_for_price_tests() ) );


	}


	/**
	 * Test checking validity of price sub_type with filter
	 *
	 * @since 1.1.0
	 *
	 * @group price
	 * @group helpers
	 *
	 * @covers ingot_acceptable_plugin_for_price_test()
	 * @covers ingot_accepted_plugins_for_price_tests()
	 * @covers \ingot\testing\types::allowed_price_types()
	 */
	public function testAllowedSubTypesFilter(){
		add_filter( 'ingot_accepted_plugins_for_price_tests', function( $plugins ){
			$plugins[ 'roy2020' ] = 'Roy!';
			return $plugins;
		});

		$this->assertTrue( in_array( 'roy2020', \ingot\testing\types::allowed_price_types() ) );
		$this->assertTrue( ingot_acceptable_plugin_for_price_test( 'roy2020' ) );
		$this->assertTrue( in_array( 'roy2020', ingot_accepted_plugins_for_price_tests() ) );
		foreach( ['woo', 'edd' ] as $plugin ){
			$this->assertTrue( in_array( $plugin, \ingot\testing\types::allowed_price_types() ) );
			$this->assertTrue( ingot_acceptable_plugin_for_price_test( $plugin ) );
			$this->assertTrue( in_array( $plugin, ingot_accepted_plugins_for_price_tests() ) );
		}
	}


}
