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
		$this->assertEquals( 500, \ingot\testing\utility\defaults::threshold() );
	}

	/**
	 * Test default initial
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\defaults::initial()
	 */
	public function testInitial() {
		$this->assertEquals( 1000, \ingot\testing\utility\defaults::initial() );
	}

	/**
	 * Test default button color
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\defaults::button_color()
	 */
	public function testButtonColor() {
		$this->assertEquals( '#2e3842', \ingot\testing\utility\defaults::button_color() );
	}


	/**
	 * Test default button filter
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\defaults::button_color()
	 * @covers ingot_default_button_color
	 */
	public function testButtonColorFilter() {
		add_filter( 'ingot_default_button_color', function() {
			return 'fff';
		});
		$this->assertEquals( '#fff', \ingot\testing\utility\defaults::button_color() );
	}

	/**
	 * Test default button filter returns trimmed
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\defaults::button_color()
	 * @covers ingot_default_button_color
	 */
	public function testButtonColorFilterTrim() {
		add_filter( 'ingot_default_button_color', function() {
			return '#fff';
		});
		//$this->assertEquals( 'fff', \ingot\testing\utility\defaults::button_color() );
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




}
