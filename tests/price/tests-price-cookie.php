<?php
/**
 * Price cookie tests
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class tests_price_cookie extends \WP_UnitTestCase {

	public function setUp(){
		parent::setUp();
		\ingot\testing\crud\group::delete( 'all' );
		\ingot\testing\crud\variant::delete( 'all' );

	}

	/**
	 * Test contents of price cookie
	 *
	 * @since 1.1.0
	 *
	 * @group cookie
	 * @group price_cookie
	 * @group price
	 *
	 * @covers \ingot\testing\cookies\price()
	 */
	public function testCookieSetup(){
return;
		$group_1  = ingot_test_data_price::edd_tests( 10 );

		$group_2  = ingot_test_data_price::edd_tests( 15 );
		$cookie_class = new \ingot\testing\cookies\price( [] );
		$price_cookie = $cookie_class->get_cookie();

		$this->assertArrayHasKey( 'edd', $price_cookie );

		$this->assertFalse( empty( $price_cookie[ 'edd' ] ) );
		$this->assertInternalType( 'array', $price_cookie[ 'edd' ] );
		$product_1 = \ingot\testing\utility\price::get_product_ID( $group_1[ 'group_ID' ] );
		$product_2 = \ingot\testing\utility\price::get_product_ID( $group_2[ 'group_ID' ] );
		$this->assertArrayHasKey( $product_1, $price_cookie[ 'edd' ] );
		$this->assertArrayHasKey( $product_2, $price_cookie[ 'edd' ] );

		$this->assertSame( 2, count( $price_cookie[ 'edd' ] ) );

		foreach ( $price_cookie[ 'edd' ] as $content ) {
			$content = \ingot\testing\utility\price::inflate_price_test( $content );
			$this->assertInternalType( 'object', $content );
			foreach (
				[
					'plugin',
					'ID',
					'variant',
					'expires',
					'price',
					'product'
				] as $key
			) {
				$this->assertObjectHasAttribute( $key, $content );

			}

			$this->assertInternalType( 'object', $content->product );

		}

	}

	/**
	 * Test that price tests in cookie makes 1 time ran only.
	 *
	 * @since 1.1.0
	 *
	 * @group cookie
	 * @group price_cookie
	 * @group price
	 *
	 *
	 * @covers
	 */
	public function testEnsureRate(){
		$data  = ingot_test_data_price::edd_tests( 10 );
		for( $i= 0; $i <= 10; $i++ ) {
			wp_remote_get( home_url() );
		}
		$obj = new \ingot\testing\object\group( $data[ 'group_ID' ] );
		$stats_obj = new \ingot\testing\object\group_stats( $obj->get_levers() );

		$stats = $stats_obj->get_stats();
		$this->assertArrayHasKey( 'group', $stats );
		$this->assertInternalType( 'object', $stats[ 'group' ] );

		/** @var \ingot\testing\object\stats $group_stats */
		$group_stats = $stats[ 'group' ];
		$this->assertSame( 1, $group_stats->total );
		$this->assertSame( 0, $group_stats->conversions );

	}

}
