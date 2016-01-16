<?php
/**
 * Test setting up cookies via set class
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */
class tests_cookie_setup extends \WP_UnitTestCase{

	public function setUp(){
		parent::setUp();

	}

	/**
	 * Test that we can set the cookie
	 *
	 * @since 1.1.0
	 *
	 * @group cookie
	 * @group price
	 *
	 * @covers \ingot\testing\cookies\set::setup_cookies()
	 */
	public function _testSetCookie(){

	}

	/**
	 * Test price testing setup
	 *
	 * @since 1.1.0
	 *
	 * @group cookie
	 * @group price_cookie
	 * @group price
	 *
	 *
	 * @covers \ingot\testing\cookies\set::price_testing()
	 */
	public function testRunPriceTests(){
return;

		$this->assertFalse( (bool) did_action( 'ingot_loaded' ) );
		$group_1  = ingot_test_data_price::edd_tests( 10 );
		$group_2  = ingot_test_data_price::edd_tests( 15 );
		$product_1 = $group_1[ 'product_ID' ];
		$product_2 = $group_2[ 'product_ID' ];
		$cookies = \ingot\testing\cookies\init::create( [] );
		$this->assertFalse( is_wp_error( $cookies ) );

		$ingot_cookies = $cookies->get_ingot_cookie( false );

		$this->assertInternalType( 'array', $ingot_cookies );
		$this->assertArrayHasKey( 'edd', $ingot_cookies );

		$objects = \ingot\testing\cookies\set::price_testing( $ingot_cookies );

		$this->assertArrayHasKey( 'edd', $objects );
		$this->assertInternalType( 'object', $objects[ 'edd' ] );

		/** @var ingot\testing\tests\price\plugins\edd $edd */
		$edd = $objects[ 'edd' ];
		$products = $edd->get_products();
		$this->assertEquals( 2, count( $products ) );
		$this->assertArrayHasKey( $product_1, $products );
		$this->assertArrayHasKey( $product_2, $products );

	}
}
