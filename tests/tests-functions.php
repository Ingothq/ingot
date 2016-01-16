<?php

/**
 * Tests for function
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class tests_functions extends  \WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$_SERVER[ 'HTTP_USER_AGENT' ] = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36';

	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * Test our browser detection
	 *
	 *
	 * @covers ingot_get_browser()
	 */
	public function testGetBrowser() {
		$this->assertEquals( 'chrome', ingot_get_browser() );

	}

	/**
	 * Test UTM array preperation
	 *
	 * @since 0.0.7
	 *
	 * @covers ingot_get_utm();
	 */
	public function testUTM(){
		$_GET[ 'utm_referrer' ] = 'twitter';
		$_GET[ 'utm_campaign' ] = 'a112357';
		$_GET[ 'utm_medium' ] = 'social';

		$utm = ingot_get_utm();
		$this->assertArrayHasKey( 'referrer', $utm );
		$this->assertArrayHasKey( 'campaign', $utm );
		$this->assertArrayHasKey( 'medium', $utm );

		$this->assertEquals( 'twitter', $utm[ 'referrer' ] );
		$this->assertEquals( 'a112357', $utm[ 'campaign' ] );
		$this->assertEquals( 'social', $utm[ 'medium' ] );

	}

	/**
	 * Test that a valid Refferer in UTM tracks right
	 *
	 * @since 0.0.7
	 *
	 * @covers ingot_get_refferer()
	 */
	public function testReferalTrackingByUTMValid() {
		$_GET[ 'utm_referrer' ] = 'twitter';
		$this->assertEquals( 'twitter', ingot_get_refferer() );

	}

	/**
	 * Test that a valid Refferer in $_SERVER['HTTP_REFERER'] tracks right
	 *
	 * @since 0.0.7
	 *
	 * @group functions
	 *
	 * @covers ingot_get_refferer()
	 */
	public function testReferalTrackingByServerVarValid() {
		$_SERVER['HTTP_REFERER'] = 'http://twitter.com/xyz?x=hats&b=food';
		$this->assertEquals( 'twitter', ingot_get_refferer() );

	}

	/**
	 * Test that an invalid Refferer in UTM doesn't track.
	 *
	 * @since 0.0.7
	 *
	 * @group functions
	 *
	 * @covers ingot_get_refferer()
	 */
	public function testReferalTrackingByUTMInvalid() {
		$_SERVER['HTTP_REFERER'] = 'http://airplanes.com';
		$_GET[ 'utm_referrer' ] = 'myspace';
		$this->assertFalse( ingot_get_refferer() );

	}

	/**
	 * Test that an invalid Refferer in $_SERVER['HTTP_REFERER'] tracks right
	 *
	 * @since 0.0.7
	 *
	 * @group functions
	 *
	 * @covers ingot_get_refferer()
	 */
	public function testReferalTrackingByServerVarInvalid() {
		$_SERVER['HTTP_REFERER'] = 'http://airplanes.com';
		$this->assertFalse( ingot_get_refferer() );

	}

	/**
	 * Test that when UTM and server var reffers are valid, UTM is used.
	 *
	 * @since 0.0.7
	 *
	 * @group functions
	 *
	 * @covers ingot_get_refferer()
	 */
	public function testReferrerWithBoth(){
		$_SERVER['HTTP_REFERER'] = 'http://facebook.com/xyz?x=hats&b=food';
		$_GET[ 'utm_referrer' ] = 'twitter';
		$this->assertEquals( 'twitter', ingot_get_refferer() );

	}

}


