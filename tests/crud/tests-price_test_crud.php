<?php

/**
 * Validate price test CRUD
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class tests_price_test_crud extends \WP_UnitTestCase {


	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
		\ingot\testing\crud\price_test::delete( 'all' );
	}


	/**
	 * Test create with minimal params set
	 *
	 * @since 0.0.9
	 *
	 * @covers \ingot\testing\crud\price_test::create()
	 * @covers \ingot\testing\crud\price_test::fill_in()
	 */
	public function testCreateMinimal() {
		$params = array(
			'product_ID' => 100,
			'default'    => -0.9
		);

		$created = \ingot\testing\crud\price_test::create( $params );
		$this->assertFalse( is_wp_error( $created ) );
		$this->assertTrue( is_numeric( $created ) );


	}

	/**
	 * Test create with minimal params set and use variable pricing
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\crud\price_test::create()
	 * @covers \ingot\testing\crud\price_test::fill_in()
	 */
	public function testCreateWithVariable() {
		$params = array(
			'product_ID' => 100,
			'default'    => 0.9,
			'variable'   => array(
				0 => 0.1,
				1 => -0.5,
			)
		);

		$created = \ingot\testing\crud\price_test::create( $params );
		$this->assertFalse( is_wp_error( $created ) );
		$this->assertTrue( is_numeric( $created ) );


	}

	/**
	 * Test creating with invalid variable pricing options
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\crud\price_test::create()
	 * @covers \ingot\testing\crud\price_test::fill_in()
	 */
	public function testCreateWithVariableInvalid() {
		$params = array(
			'product_ID' => 100,
			'default'    => 0.9,
			'variable'   => array(
				0 => 0.1,
				1 => -1000,
			)
		);

		$created = \ingot\testing\crud\price_test::create( $params );
		$this->assertFalse( is_wp_error( $created ) );
		$this->assertTrue( is_numeric( $created ) );

		$params = array(
			'product_ID' => 100,
			'default'    => 0.9,
			'variable'   => array(
				0 => 1000,
				1 => -.5,
			)
		);

		$created = \ingot\testing\crud\price_test::create( $params );
		$this->assertFalse( is_wp_error( $created ) );

		$params = array(
			'product_ID' => 100,
			'default'    => 0.9,
			'variable'   => array(
				'hats'
			)
		);

		$created = \ingot\testing\crud\price_test::create( $params );
		$this->assertFalse( is_wp_error( $created ) );


		$params = array(
			'product_ID' => 100,
			'default'    => 0.9,
			'variable'   => array(
				'hats' => 0.1
			)
		);

		$created = \ingot\testing\crud\price_test::create( $params );
		$this->assertFalse( is_wp_error( $created ) );





	}

	/**
	 * Test that invalid configurations will generate an error
	 *
	 * @since 0.0.9
	 *
	 * @covers \ingot\testing\crud\price_test::create()
	 * @covers \ingot\testing\crud\price_test::validate_config()
	 */
	public function testCreateMinimalInvalidDefault() {
		$params = array(
			'product_ID' => 100,
			'default'    => array()

		);

		$created = \ingot\testing\crud\price_test::create( $params );
		$this->assertTrue( is_wp_error( $created ) );

		$params = array(
			'product_ID' => 100,
			'default'    => 'hats'

		);

		$created = \ingot\testing\crud\price_test::create( $params );
		$this->assertTrue( is_wp_error( $created ) );

		$params = array(
			'product_ID' => 100,
			'default'    => 99.9,

		);

		$created = \ingot\testing\crud\price_test::create( $params );
		$this->assertTrue( is_wp_error( $created ) );

		$params = array(
			'product_ID' => 100,
			'default'    => new stdClass()

		);

		$created = \ingot\testing\crud\price_test::create( $params );
		$this->assertTrue( is_wp_error( $created ) );

	}

	/**
	 * Test we can read a test we create
	 *
	 * @since 0.0.9
	 *
	 * @covers \ingot\testing\crud\price_test::create()
	 * @covers \ingot\testing\crud\price_test::read()
	 */
	public function testRead() {
		$now    = time();
		$params = array(
			'product_ID' => 100,
			'default'    => 0.9,
			'name'       => rand(),
			'created'    => $now,
			'modified'   => $now

		);

		$created = \ingot\testing\crud\price_test::create( $params );
		$this->assertFalse( is_wp_error( $created ) );
		$this->assertTrue( is_numeric( $created ) );

		$test = \ingot\testing\crud\price_test::read( $created );
		$this->assertTrue( is_array( $test ) );

		foreach ( $params as $key => $value ) {
			$this->assertArrayHasKey( $key, $test );
			$this->assertEquals( $value, $test[ $key ] );
		}

	}

	/**
	 * Test we can update a test we create
	 *
	 * @since 0.0.9
	 *
	 * @covers \ingot\testing\crud\price_test::update()
	 */
	public function testUpdate() {
		$now    = time();
		$params = array(
			'product_ID' => 100,
			'default'    => 0.1,
			'name'       => rand(),
			'created'    => $now,
			'modified'   => $now

		);

		$created = \ingot\testing\crud\price_test::create( $params );
		$this->assertFalse( is_wp_error( $created ) );
		$this->assertTrue( is_numeric( $created ) );

		$test = \ingot\testing\crud\price_test::read( $created );
		$this->assertTrue( is_array( $test ) );

		$params['default'] = 0.01;
		$params['name']         = 'DRAGONS!';
		$updated                = \ingot\testing\crud\price_test::update( $params, $created );
		$this->assertEquals( $updated, $created );

		$test = \ingot\testing\crud\price_test::read( $updated );
		$this->assertTrue( is_array( $test ) );

		foreach ( $params as $key => $value ) {
			$this->assertArrayHasKey( $key, $test );
			$this->assertEquals( $value, $test[ $key ] );
		}

	}

	/**
	 * Test we can delete a test we create
	 *
	 * @since 0.0.9
	 *
	 * @covers \ingot\testing\crud\price_test::delete()
	 */
	public function testDelete() {
		$params = array(
			'product_ID' => 100,
			'default'    => 0.9

		);

		$test_1 = \ingot\testing\crud\price_test::create( $params );
		$this->assertFalse( is_wp_error( $test_1 ) );
		$this->assertTrue( is_numeric( $test_1 ) );

		$params = array(
			'product_ID' => 199,
			'default'    => 0.4
		);

		$test_2 = \ingot\testing\crud\price_test::create( $params );
		$this->assertFalse( is_wp_error( $test_2 ) );
		$this->assertTrue( is_numeric( $test_2 ) );

		\ingot\testing\crud\price_test::delete( $test_2 );
		$this->assertFalse( is_array( \ingot\testing\crud\price_test::read( $test_2 ) ) );

		$this->assertTrue( is_array( \ingot\testing\crud\price_test::read( $test_1 ) ) );


	}


}
