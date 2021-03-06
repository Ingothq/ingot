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

	/**
	 * Test get price of EDD product
	 *
	 * @since 1.1.0
	 *
	 * @group price
	 * @group helper
	 * @group edd
	 *
	 * @covers \ingot\testing\utility\price::get_price()
	 */
	public function testGetPriceEDD(){
		$product_1 = ingot_test_data_price::edd_create_simple_download( 10.51 );
		$product_2 = ingot_test_data_price::edd_create_simple_download( 5.21 );
		$this->assertSame( ingot_sanitize_amount( 10.51 ), \ingot\testing\utility\price::get_price( 'edd', $product_1->ID ) );
		$this->assertSame( ingot_sanitize_amount( 5.21 ), \ingot\testing\utility\price::get_price( 'edd', $product_2->ID ) );

	}

	/**
	 * Test applying utility method for price variation with negative variation
	 *
	 * @since 1.1.0
	 *
	 * @group price
	 * @group helper
	 *
	 * @covers \ingot\testing\utility\price::apply_variation()
	 */
	public function testPriceVariationNegative(){
		$base_price = 10;

		$this->assertSame( ingot_sanitize_amount( 5 ), ingot_sanitize_amount( \ingot\testing\utility\price::apply_variation( -0.5, $base_price ) ) );

		$this->assertSame( ingot_sanitize_amount( 8 ), ingot_sanitize_amount( \ingot\testing\utility\price::apply_variation( -0.2, $base_price ) ) );

		$this->assertSame( ingot_sanitize_amount( 7.50 ), ingot_sanitize_amount( \ingot\testing\utility\price::apply_variation( -0.25, $base_price ) ) );

		$this->assertSame( ingot_sanitize_amount( 2.50 ), ingot_sanitize_amount( \ingot\testing\utility\price::apply_variation( -0.75, $base_price ) ) );

	}

	/**
	 *
	 * Test applying utility method for price variation with positive variation
	 *
	 * @since 1.1.0
	 *
	 * @group price
	 * @group helper
	 *
	 * @covers \ingot\testing\utility\price::apply_variation()
	 */
	public function testPriceVarPositive(){
		$base_price = 10;
		$this->assertSame( ingot_sanitize_amount( 15 ), ingot_sanitize_amount( \ingot\testing\utility\price::apply_variation( 0.5, $base_price ) ) );

		$this->assertSame( ingot_sanitize_amount( 12 ), ingot_sanitize_amount( \ingot\testing\utility\price::apply_variation( 0.2, $base_price ) ) );

		$this->assertSame( ingot_sanitize_amount( 18.50 ), ingot_sanitize_amount( \ingot\testing\utility\price::apply_variation( 0.85, $base_price ) ) );

	}

	/**
	 * Test that we can't create a group for a product that is already being tested
	 *
	 * @since 1.1.0
	 *
	 * @group price
	 * @group helper
	 * @covers 	\ingot\testing\utility\price::product_test_exists()
	 */
	public function testPriceTestExists(){

		$group = \ingot\testing\crud\group::create( [
			'name'     => 'd',
			'type'     => 'price',
			'sub_type' => 'edd',
			'meta'     => [
				'product_ID' => 169,
			],
			'wp_ID' => 169
		], true );

		$this->assertTrue( is_numeric( $group ) );

		$existing = \ingot\testing\utility\price::product_test_exists( 169 );

		$this->assertEquals( $existing, $group );

	}

}
