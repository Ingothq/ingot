<?php
/**
 * Tests for EDD price tests
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class _tests_edd_price_tests extends \WP_UnitTestCase {

	/**
	 * Test that EDD sample data generation is valid
	 *
	 * @since 1.1.0
	 *
	 * @group price
	 * @group edd_price
	 */
	public function testDataGeneration(){
		$group_data  = ingot_test_data_price::edd_tests( 10 );
		$group = \ingot\testing\crud\group::read( $group_data[ 'group_ID'  ] );
		$this->assertTrue( \ingot\testing\crud\group::valid( $group  ) );
		foreach( $group_data[ 'variants' ] as $variant_id ){
			$this->assertTrue( is_numeric( $variant_id ) );
			$variant = \ingot\testing\crud\variant::read( $variant_id );
			$this->assertTrue( \ingot\testing\crud\variant::valid( $variant ) );
			$this->assertEquals( $variant[ 'content' ], $group[ 'meta'][ 'product_ID' ] );
		}

	}


	public function _testPrice(){
		$download = ingot_test_data_price::edd_create_simple_download( 10 );

		$price = edd_get_download_price( $download->ID );
	}

	public function _testVariablePrice() {
		$download = ingot_test_data_price::edd_create_variable_download( 10 );
		$prices = edd_get_variable_prices( $download->ID );
	}
}
