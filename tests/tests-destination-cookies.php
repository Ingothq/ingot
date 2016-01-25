<?php

/**
 * Tests for destination cookie setup
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */
class test_cookies extends \WP_UnitTestCase {

	public function setUp(){
		parent::setUp();
		\ingot\testing\crud\group::delete( 'all' );
	}

	/**
	 * Test that we can query for these tests properly
	 *
	 * @since 1.1.0
	 *
	 * @group group
	 * @group destination
	 * @group cookie
	 * @group destination_cookie
	 *
	 * @covers \ingot\testing\tests\click\destination\init::get_destination_tests()
	 */
	public function testQuery() {
		ingot_test_data_price::edd_tests();
		ingot_tests_data::make_groups();
		$x = 0;
		foreach ( \ingot\testing\tests\click\destination\types::destination_types() as $type ) {
			if( 'hook' == $type ) {
				continue;
			}

			$data = ingot_test_desitnation::create( $type );

			$this->assertTrue( is_numeric( $data[ 'group_ID' ] ) );
			$the_groups[] = $data[ 'group_ID' ];
			$x++;

		}


		$groups = \ingot\testing\tests\click\destination\init::get_destination_tests();
		$this->assertSame( $x, count( $groups ) );

		$this->assertEquals( $the_groups , $groups );

	}

	/**
	 *
	 *
	 * @since 1.1.0
	 *
	 * @group group
	 * @group destination
	 * @group cookie
	 * @group destination_cookie
	 *
	 * @covers \ingot\testing\tests\click\destination\init::setup_cookies()
	 */
	public function testCookieInitial(){

		$data = ingot_test_desitnation::create( 'page' );
		$group_1 = $data[ 'group_ID' ];
		$variants_1 = $data[ 'variants' ];
		$data = ingot_test_desitnation::create( 'page' );
		$group_2 = $data[ 'group_ID' ];
		$variants_2 = $data[ 'variants' ];
		$variants = \ingot\testing\tests\click\destination\init::setup_cookies();

		$this->assertEquals( 2, count( $variants ) );

		$this->assertArrayHasKey( $group_1, $variants );
		$this->assertTrue( in_array( $variants[ $group_1 ], $variants_1 ) );

		$this->assertArrayHasKey( $group_2, $variants );
		$this->assertTrue( in_array( $variants[ $group_2 ], $variants_2 ) );

	}

	/**
	 *
	 *
	 * @since 1.1.0
	 *
	 * @group group
	 * @group destination
	 * @group cookie
	 * @group destination_cookie
	 *
	 * @covers \ingot\testing\tests\click\destination\init::get_test()
	 * @covers \ingot\testing\tests\click\destination\init::set_tracking()
	 * @covers \ingot\testing\tests\click\destination\cookie::get_cookie()
	 */
	public function testCookieTracking(){

		$data = ingot_test_desitnation::create( 'page' );
		$group_id = $data[ 'group_ID' ];
		$this->assertTrue( is_numeric( $group_id ) );
		$group = \ingot\testing\crud\group::read( $group_id );
		$this->assertInternalType( 'array', $group );
		$bandit  = new \ingot\testing\bandit\content( $group_id );
		$variant_id = $bandit->choose();
		$this->assertTrue( is_numeric( $variant_id ) );
		\ingot\testing\tests\click\destination\init::set_tracking();
		\ingot\testing\tests\click\destination\init::get_test( $group_id );
		$obj = new \ingot\testing\object\group( $group_id );
		$levers = $obj->get_levers();
		$this->assertInternalType( 'array', $levers );

		$cookie_name = \ingot\testing\tests\click\destination\cookie::cookie_key( $group_id );
		$variant_id = \ingot\testing\tests\click\destination\init::get_test( $group_id );
		$this->assertTrue( is_numeric( $variant_id ) );
		$_COOKIE[ $cookie_name ] = $variant_id;
		$this->assertEquals( $variant_id, \ingot\testing\tests\click\destination\cookie::get_cookie( $group_id ) );
		$obj = new \ingot\testing\object\group( $group_id );
		$_levers = $obj->get_levers();
		for ( $i = 0; $i <= 10; $i++  ) {
			\ingot\testing\tests\click\destination\init::set_tracking();
		}

		$obj = new \ingot\testing\object\group( $group_id );
		$levers = $obj->get_levers();
		$this->assertEquals( $levers, $_levers );
		$this->assertInternalType( 'array', $levers );
		$this->assertArrayHasKey( $group_id, $levers );
		$this->assertArrayHasKey( $variant_id, $levers[ $group_id ] );
		$lever = $levers[ $variant_id ];
		$this->assertInternalType( 'object', $lever );
		$this->assertEquals( 0, $lever->getNumerator() );
		$this->assertEquals( 1, $lever->getDenominator() );

	}

	/**
	 * Test getting group IDs for destination cookies

	 * @since 1.1.0
	 *
	 * @group destination
	 * @group cookie
	 * @group destination_cookie
	 * @group array_filter
	 *
	 * @covers \ingot\testing\utility\array_filters::filter_results()
	 * @covers \ingot\testing\utility\array_filters::match()
	 * @covers \ingot\testing\utility\array_filters::prepare()
	 */
	public function testGetCookies(){
		$_COOKIE = [
			'ingot_destination_9' => 12,
			'ingot_destination_42' => 7,
			'hi chris',
			'ingot_97' => 94,
			'ingot_destination' => 88,
			'hats_8765' => [ 'batman', 'robin']
		];
		$results = \ingot\testing\tests\click\destination\cookie::get_all_cookies();
		$this->assertEquals( [
			'9',
			'42'
		], $results );
	}
}
