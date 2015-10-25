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
	 * @covers \ingot\testing\crud\price_group::read();
	 * @covers \ingot\testing\crud\price_group::fill_in()
	 */
	public function testCreateMinimal() {
		$params = array(
			'type' => 'price',
			'plugin' => 'edd'
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
			'plugin' => 'edd'
		);

		$created = \ingot\testing\crud\price_group::create( $params );
		$this->assertTrue( is_wp_error( $created ) );

		$params = array(
			'type' => 'price',
			'plugin' => 'salad'
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
			'test_order' => array( rand(), rand(), rand(), rand() ),
			'initial' => '42',
			'threshold' => '84',
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


}
