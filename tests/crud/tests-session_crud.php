<?php

/**
 * Tests for session CRUD
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class tests_session_crud extends \WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

	}
	public function tearDown() {
		parent::tearDown();

	}


	/**
	 * Test creating a session
	 *
	 * @since 0.3.0
	 *
	 * @group session
	 * @covers \ingot\testing\crud\session::create()
	 * @covers \ingot\testing\crud\session::read()
	 */
	public function testCreate() {
		$id = \ingot\testing\crud\session::create( array() );
		$this->assertTrue( is_numeric( $id ) );
		$session = \ingot\testing\crud\session::read( $id );
		$this->assertTrue( is_array( $session ) );
		$fields = [
			'ID',
			'created',
			'ingot_ID',
			'IP',
			'uID',
			'slug',
			'used',
			'click_url',
			'click_test_ID'
		];

		foreach( $fields as $field ) {
			$this->assertArrayHasKey( $field, $session );
		}

		$this->assertFalse( (bool) $session[ 'used' ] );
	}

	/**
	 * Test creating a session
	 *
	 * @since 0.3.0
	 *
	 * @group session
	 * @covers \ingot\testing\crud\session::get_slug()
	 */
	public function testPageSlug() {
		$_SERVER[ 'REQUEST_URI' ] = '/pants';
		$id = \ingot\testing\crud\session::create( array() );

		$session = \ingot\testing\crud\session::read( $id );
		$this->assertSame( '/pants', $session[ 'slug' ] );
	}

	/**
	 * Test the is_used function
	 *
	 * @since 0.3.0
	 *
	 * @group session
	 * @covers \ingot\testing\crud\session::is_used()
	 */
	public function testNotUsedYet() {

		$id = \ingot\testing\crud\session::create( array() );
		$this->assertTrue( is_numeric( $id ) );

		$this->assertFalse( \ingot\testing\crud\session::is_used( $id ) );


	}

	/**
	 * Test that we can mark a session as used
	 *
	 * @since 0.3.0
	 *
	 * @group session
	 * @covers \ingot\testing\crud\session::mark_used()
	 */
	public function testMarkUsed() {

		$id = \ingot\testing\crud\session::create( array() );

		$this->assertTrue( is_numeric( $id ) );
		\ingot\testing\crud\session::mark_used( $id );
		$session = \ingot\testing\crud\session::read( $id );
		$this->assertSame( '1', $session[ 'used' ] );

	}

}
