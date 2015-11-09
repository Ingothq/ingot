<?php

/**
 * Tests for settings CRUD
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class tests_settings_crud extends \WP_UnitTestCase {

	public function tearDown() {
		parent::tearDown();
		foreach( array(
			'click_tracking',
			'anon_tracking',
			'license_code'
		) as $setting ) {
			$saved = \ingot\testing\crud\settings::write( $setting, 0 );
		}
	}

	/**
	 * Test save click tracking
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\crud\settings::write()
	 */
	public function testSaveClickTracking() {
		$saved = \ingot\testing\crud\settings::write( 'click_tracking', true );
		$this->assertTrue( $saved );
	}

	/**
	 * Test save anon tracking
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\crud\settings::write()
	 */
	public function testSaveAnonTracking() {
		$saved = \ingot\testing\crud\settings::write( 'anon_tracking', true );
		$this->assertTrue( $saved );
	}


	/**
	 * Test save license
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\crud\settings::write()
	 */
	public function testSaveLicense() {
		$saved = \ingot\testing\crud\settings::write( 'license_code', rand() );
		$this->assertTrue( $saved );
	}

	/**
	 * Test reading click tracking
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\crud\settings::read()
	 */
	public function testReadClickTracking() {
		$saved = \ingot\testing\crud\settings::write( 'click_tracking', true );
		$this->assertSame( 1, \ingot\testing\crud\settings::read( 'click_tracking' ) );
		$saved = \ingot\testing\crud\settings::write( 'click_tracking', false );
		$this->assertSame( 0, \ingot\testing\crud\settings::read( 'click_tracking' ) );
	}

	/**
	 * Test reading anon tracking
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\crud\settings::read()
	 */
	public function testReadAnonTracking() {
		$saved = \ingot\testing\crud\settings::write( 'anon_tracking', true );
		$this->assertSame( 1, \ingot\testing\crud\settings::read( 'anon_tracking' ) );
		$saved = \ingot\testing\crud\settings::write( 'anon_tracking', false );
		$this->assertSame( 0, \ingot\testing\crud\settings::read( 'anon_tracking' ) );
	}

	/**
	 * Test reading licensing
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\crud\settings::read()
	 */
	public function testReadLicense() {
		$code = (string) rand();
		$saved = \ingot\testing\crud\settings::write( 'license_code', $code );
		$this->assertSame( $code, \ingot\testing\crud\settings::read( 'license_code' ) );
		$code = (string) rand();
		$saved = \ingot\testing\crud\settings::write( 'license_code', $code );
		$this->assertSame( $code, \ingot\testing\crud\settings::read( 'license_code' ) );

	}


	/**
	 * Test that saving invalid setting click tracking does not work
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\crud\settings::write()
	 */
	public function testSaveClickTrackingInvalid() {
		$saved = \ingot\testing\crud\settings::write( 'click_tracking', rand( 7, 900 ) );
		$this->assertSame( 0, \ingot\testing\crud\settings::read( 'click_tracking' ) );
	}

	/**
	 * Test that saving invalid setting anon tracking does not work
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\crud\settings::write()
	 */
	public function testSaveAnonTrackingInvalid() {
		$saved = \ingot\testing\crud\settings::write( 'anon_tracking', rand( 7, 900 ) );
		$this->assertSame( 0, \ingot\testing\crud\settings::read( 'anon_tracking' ) );
	}

	/**
	 * Test that saving invalid license code does not work
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\crud\settings::write()
	 */
	public function testSaveLicenseCodeInvalid() {
		$saved = \ingot\testing\crud\settings::write( 'license_code', array() );
		$this->assertSame( '', \ingot\testing\crud\settings::read( 'license_code' ) );

		$saved = \ingot\testing\crud\settings::write( 'license_code', serialize( array( 'bat', 'man' ) ) );
		$this->assertSame( '', \ingot\testing\crud\settings::read( 'license_code' ) );

		$saved = \ingot\testing\crud\settings::write( 'license_code', wp_json_encode( array( 'bat', 'man' ) ) );
		$this->assertSame( '', \ingot\testing\crud\settings::read( 'license_code' ) );

	}

	/**
	 * Test that we can not save an invalid setting
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\crud\settings::write()
	 */
	public function testSaveInvalidSetting() {
		$saved = \ingot\testing\crud\settings::write( 'batman', 'batcave' );
		$this->assertFalse( $saved );
		$this->assertFalse( get_option( 'ingot_settings_batman', false ) );
	}

	/**
	 * Test that we can not get an invalid setting, even if its prefixed with ingot_settings
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\crud\settings::write()
	 */
	public function testReadInvalidSetting() {
		update_option( 'ingot_settings_cat', 'dog' );
		$read = \ingot\testing\crud\settings::read( 'cat' );
		$this->assertFalse( $read );

	}


}
