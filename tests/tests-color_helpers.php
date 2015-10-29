<?php

/**
 * Covers color utilities
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class tests_color_helpers extends \WP_UnitTestCase {

	/**
	 * Test that a valid 3 digit hex passes
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\helpers::prepare_color()
	 * @covers \ingot\testing\utility\sanitize_hex_color::prepare_color()
	 * @covers \ingot\testing\utility\maybe_hash_hex_color::prepare_color()
	 */
	public function testValidWithHash3Digits() {
		$color = '#fff';
		$processed = \ingot\testing\utility\helpers::prepare_color( $color, true );
		$this->assertSame( $color, $processed );
	}

	/**
	 * Test that a valid 6 digit hex passes
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\helpers::prepare_color()
	 * @covers \ingot\testing\utility\sanitize_hex_color::prepare_color()
	 * @covers \ingot\testing\utility\maybe_hash_hex_color::prepare_color()
	 */
	public function testValidWithHash6Digits() {
		$color = '#fdfdfd';
		$processed = \ingot\testing\utility\helpers::prepare_color( $color, true );
		$this->assertSame( $color, $processed );
	}

	/**
	 * Test that a valid 3 digit hex passes and removes hash when asked to.
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\helpers::prepare_color()
	 * @covers \ingot\testing\utility\sanitize_hex_color::prepare_color()
	 * @covers \ingot\testing\utility\maybe_hash_hex_color::prepare_color()
	 */
	public function testValidRemoveHash3Digits() {

		$processed = \ingot\testing\utility\helpers::prepare_color( '#fff', false );
		$this->assertSame( 'fff', $processed );

	}


	/**
	 * Test that a valid 6 digit hex passes and removes hash when asked to.
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\helpers::prepare_color()
	 * @covers \ingot\testing\utility\sanitize_hex_color::prepare_color()
	 * @covers \ingot\testing\utility\sanitize_hex_color_no_hash::prepare_color()
	 */
	public function testValidRemoveHash6Digits() {

		$processed = \ingot\testing\utility\helpers::prepare_color( '#f00fff', false );
		$this->assertSame( 'f00fff', $processed );

	}

	/**
	 * Test that an INVALID hex returns default and still gives us a hash when asked for.
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\helpers::prepare_color()
	 * @covers \ingot\testing\utility\sanitize_hex_color::prepare_color()
	 * @covers \ingot\testing\utility\maybe_hash_hex_color::prepare_color()
	 */
	public function testInvalidAddHash() {
		$default = \ingot\testing\utility\defaults::color();
		$default = '#' . $default;
		$processed = \ingot\testing\utility\helpers::prepare_color( '#fdogscatsfflllll', true );
		$this->assertSame( $default, $processed );

	}

	/**
	 * Test that an INVALID hex returns default and still gives removes a hash when asked for.
	 *
	 * @since 0.1.1
	 *
	 * @covers \ingot\testing\utility\helpers::prepare_color()
	 * @covers \ingot\testing\utility\sanitize_hex_color::prepare_color()
	 * @covers \ingot\testing\utility\sanitize_hex_color_no_hash::prepare_color()
	 */
	public function testInvalidRemoveHash() {
		$default = \ingot\testing\utility\defaults::color();
		$processed = \ingot\testing\utility\helpers::prepare_color( '#fdogscatsfflllll', false );
		$this->assertSame( $default, $processed );

	}

}
