<?php

/**
 * Test defaults class
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class tests_defaults extends \WP_UnitTestCase{

	/**
	 * Test default threshold
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\defaults::threshold()
	 */
	public function testThreshold() {
		$this->assertEquals( 20, \ingot\testing\utility\defaults::threshold() );
	}

	/**
	 * Test default initial
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\defaults::initial()
	 */
	public function testInitial() {
		$this->assertEquals( 100, \ingot\testing\utility\defaults::initial() );
	}

	/**
	 * Test default threshold filter
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\defaults::threshold()
	 * @covers ingot_default_threshold
	 */
	public function testThresholdFilter() {
		add_filter( 'ingot_default_threshold', function() {
			return 42;
		});
		$this->assertEquals( 42, \ingot\testing\utility\defaults::threshold() );
	}

	/**
	 * Test default initial filter
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\defaults::initial()
	 * @covers ingot_default_initial
	 */
	public function testInitialFilter() {
		add_filter( 'ingot_default_initial', function() {
			return 9;
		});
		$this->assertEquals( 9, \ingot\testing\utility\defaults::initial() );
	}

	/**
	 * Test that threshold is filled in right by the defaults for a click group
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\defaults::threshold()
	 * @covers \ingot\testing\crud\group::fill_in()
	 */
	public function testFillInClickThreshold() {
		$params = array(
			'type' => 'click',
		);

		$created = \ingot\testing\crud\group::create( $params );
		$group = \ingot\testing\crud\group::read( $created );
		$this->assertArrayHasKey( 'threshold', $group );
		$this->assertEquals( 20, $group[ 'threshold'] );
	}
	/**
	 * Test that threshold is filled in right by the defaults for a price group
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\defaults::threshold()
	 * @covers \ingot\testing\crud\price_group::fill_in()
	 */
	public function testFillInPriceThreshold() {
		$params = array(
			'type' => 'price',
			'plugin' => 'edd',
			'product_ID' => rand(),
		);

		$created = \ingot\testing\crud\price_group::create( $params );
		$group = \ingot\testing\crud\price_group::read( $created );
		$this->assertArrayHasKey( 'threshold', $group );
		$this->assertEquals( 20, $group[ 'threshold'] );
	}

	/**
	 * Test that threshold is filled in right by the defaults for a price group when using the filter
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\defaults::threshold()
	 * @covers \ingot\testing\crud\group::fill_in()
	 * @covers ingot_default_threshold
	 */
	public function testFillInPriceThresholdFilter() {
		$params = array(
			'type' => 'price',
			'plugin' => 'edd',
			'product_ID' => rand(),
		);

		add_filter( 'ingot_default_threshold', function() {
			return 42;
		});

		$created = \ingot\testing\crud\price_group::create( $params );
		$group = \ingot\testing\crud\price_group::read( $created );
		$this->assertArrayHasKey( 'threshold', $group );
		$this->assertEquals( 42, $group[ 'threshold'] );
	}

	/**
	 * Test that threshold is filled in right by the defaults for a click group.
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\defaults::threshold()
	 * @covers \ingot\testing\crud\group::fill_in()
	 */
	public function testFillInInitialThreshold() {
		$params = array(
			'type' => 'click',
		);

		$created = \ingot\testing\crud\group::create( $params );
		$group = \ingot\testing\crud\group::read( $created );
		$this->assertArrayHasKey( 'initial', $group );
		$this->assertEquals( 100, $group[ 'initial'] );
	}

	/**
	 * Test that initial is filled in right by the defaults for a click group.
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\defaults::threshold()
	 * @covers \ingot\testing\crud\price_group::fill_in()
	 */
	public function testFillInPriceInitial() {
		$params = array(
			'type' => 'price',
			'plugin' => 'edd',
			'product_ID' => rand(),
		);

		$created = \ingot\testing\crud\price_group::create( $params );
		$group = \ingot\testing\crud\price_group::read( $created );
		$this->assertArrayHasKey( 'initial', $group );
		$this->assertEquals( 100, $group[ 'initial'] );
	}

	/**
	 * Test that intial is filled in right by the defaults for a price group when using the filter
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\defaults::initial()
	 * @covers \ingot\testing\crud\price_group::fill_in()
	 * @covers ingot_default_initial
	 */
	public function testFillInPriceInitialFilter() {
		$params = array(
			'type' => 'price',
			'plugin' => 'edd',
			'product_ID' => rand(),
		);

		add_filter( 'ingot_default_initial', function() {
			return 42;
		});

		$created = \ingot\testing\crud\price_group::create( $params );
		$group = \ingot\testing\crud\price_group::read( $created );
		$this->assertArrayHasKey( 'initial', $group );
		$this->assertEquals( 42, $group[ 'initial'] );

	}
}
