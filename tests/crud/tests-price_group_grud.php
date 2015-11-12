<?php

/**
 * Tests for price group CRUD
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class test_price_group_crud extends \WP_UnitTestCase {


	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
		\ingot\testing\crud\price_group::delete( 'all' );
	}

	/**
	 * Test create with minimal params set
	 *
	 * @since 0.0.9
	 *
	 * @covers \ingot\testing\crud\price_group::read();
	 * @covers \ingot\testing\crud\price_group::fill_in()
	 */
	public function testCreateMinimal() {
		$params = array(
			'type' => 'price',
			'plugin' => 'edd',
			'product_ID' => 1009
		);

		$created = \ingot\testing\crud\price_group::create( $params );
		$this->assertFalse(  is_wp_error( $created ) );
		$this->assertTrue( is_numeric( $created ) );

	}

	/**
	 * Test that we can't create with invalid arguments
	 *
	 * @since 0.0.9
	 *
	 * @covers \ingot\testing\crud\price_group::validate_config();
	 * @covers ingot_accepted_plugins_for_price_tests()
	 */
	public function testCreateInvalid() {
		$params = array(
			'type' => 'hats',
			'plugin' => 'edd',
			'product_ID' => 1009
		);

		$created = \ingot\testing\crud\price_group::create( $params );
		$this->assertTrue( is_wp_error( $created ) );

		$params = array(
			'type' => 'price',
			'plugin' => 'salad',
			'product_ID' => 1009
		);

		$created = \ingot\testing\crud\price_group::create( $params );
		$this->assertTrue( is_wp_error( $created ) );

		$params = array(
			'type' => 'price',
			'plugin' => 'edd',
		);

		$created = \ingot\testing\crud\price_group::create( $params );
		$this->assertTrue( is_wp_error( $created ) );



	}

	/**
	 * Test reading
	 *
	 * @since 0.0.9
	 *
	 * @covers \ingot\testing\crud\price_group::read()
	 */
	public function testRead() {
		$params = array(
			'type' => 'price',
			'plugin' => 'edd',
			'group_name' => rand(),
			'sequences' => array( rand(), rand(), rand() ),
			'test_order' => array(),
			'initial' => '42',
			'threshold' => '84',
			'product_ID' => 1009

		);

		$created = \ingot\testing\crud\price_group::create( $params );
		$this->assertFalse(  is_wp_error( $created ) );
		$this->assertTrue( is_numeric( $created ) );

		$group = \ingot\testing\crud\price_group::read( $created );
		$this->assertTrue( is_array( $group ) );

		foreach( $params as $param => $value ) {
			$this->assertArrayHasKey( $param,  $group );
			$this->assertEquals( $value, $group[ $param ] );
		}

	}

	/**
	 * Test creating a group with real price tests
	 *
	 * @since 0.0.9
	 *
	 * @covers \ingot\testing\crud\price_group::read();
	 */
	public function testCreateWithRealPriceTest() {


		$product_id = 4242;

		$params = array(
			'product_ID' => $product_id,
			'default' => 0.9
		);

		$test_1 = \ingot\testing\crud\price_test::create( $params );
		$this->assertFalse( is_wp_error( $test_1 ) );
		$this->assertTrue( is_numeric( $test_1 ) );
		$test_2 = \ingot\testing\crud\price_test::create( $params );
		$this->assertFalse( is_wp_error( $test_2 ) );
		$this->assertTrue( is_numeric( $test_2 ) );


		$params = array(
			'type' => 'price',
			'plugin' => 'edd',
			'group_name' => rand(),
			'sequences' => array(),
			'test_order' => array( $test_1, $test_2 ),
			'initial' => '42',
			'threshold' => '84',
			'product_ID' => $product_id

		);

		$created = \ingot\testing\crud\price_group::create( $params );
		$this->assertFalse(  is_wp_error( $created ) );
		$this->assertTrue( is_numeric( $created ) );

		$group = \ingot\testing\crud\price_group::read( $created );
		$this->assertEquals( array( $test_1, $test_2 ), $group[ 'test_order' ] );
	}

	/**
	 * Test creating a group with real price tests with a different product ID
	 *
	 * @since 0.0.9
	 *
	 * @covers \ingot\testing\crud\price_group::read()
	 * @covers \ingot\testing\crud\price_group::validate_config()
	 */
	public function testCreateWithInvalidTests() {


		$product_id = 4242;

		$params = array(
			'product_ID' => $product_id,
			'default' => 0.1,

		);

		$test_1 = \ingot\testing\crud\price_test::create( $params );
		$this->assertFalse( is_wp_error( $test_1 ) );
		$this->assertTrue( is_numeric( $test_1 ) );
		$test_2 = \ingot\testing\crud\price_test::create( $params );
		$this->assertFalse( is_wp_error( $test_2 ) );
		$this->assertTrue( is_numeric( $test_2 ) );

		$product_id = 43;

		$params = array(
			'type' => 'price',
			'plugin' => 'edd',
			'group_name' => rand(),
			'sequences' => array(),
			'test_order' => array( $test_1, $test_2 ),
			'initial' => '42',
			'threshold' => '84',
			'product_ID' => $product_id

		);

		$created = \ingot\testing\crud\price_group::create( $params );
		$this->assertTrue(  is_wp_error( $created ) );


	}

	/**
	 * Test we can update a test group
	 *
	 * @since 0.0.9
	 *
	 * @covers \ingot\testing\crud\price_group::update()
	 */
	public function testUpdate() {
		$params = array(
			'type' => 'price',
			'plugin' => 'edd',
			'product_ID' => 1009

		);

		$created = \ingot\testing\crud\price_group::create( $params );
		$params[ 'plugin' ] = 'woo';
		$updated = \ingot\testing\crud\price_group::update( $params, $created );
		$this->assertEquals( $created, $updated );
		$group = \ingot\testing\crud\price_group::read( $updated );
		foreach( $params as $key => $value ){
			$this->assertArrayHasKey( $key, $group );
			$this->assertEquals( $value, $group[ $key ] );
		}

	}

	/**
	 * Test we can delete a test group
	 *
	 * @since 0.0.9
	 *
	 * @covers \ingot\testing\crud\price_group::delete()
	 */
	public function testDelete() {
		$params = array(
			'type' => 'price',
			'plugin' => 'edd',
			'product_ID' => 1009

		);

		$group_1 = \ingot\testing\crud\price_group::create( $params );
		$this->assertFalse(  is_wp_error( $group_1 ) );
		$this->assertTrue( is_numeric( $group_1 ) );
		$params[ 'plugin' ] = 'woo';
		$group_2 = \ingot\testing\crud\price_group::create( $params );
		$this->assertFalse(  is_wp_error( $group_2 ) );
		$this->assertTrue( is_numeric( $group_2 ) );

		\ingot\testing\crud\price_group::delete( $group_2 );
		$this->assertTrue( is_array( \ingot\testing\crud\price_group::read( $group_1 ) ) );
		$this->assertFalse( is_array( \ingot\testing\crud\price_group::read( $group_2 ) ) );

	}



}
