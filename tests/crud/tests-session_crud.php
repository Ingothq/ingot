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
		if( ! isset( $_SERVER ) ) {
			$_SERVER = array();
		}
	}
	public function tearDown() {
		parent::tearDown();
		unset( $_SERVER[ 'REQUEST_URI' ] );

	}



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

		$this->assertFalse( $session[ 'used' ] );
	}

	public function testPageSlug() {

		$_SERVER[ 'REQUEST_URI' ] = '/pants';
		$id = \ingot\testing\crud\session::create( array() );

		$session = \ingot\testing\crud\session::read( $id );
		$this->assertSame( '/pants', $session[ 'slug' ] );
	}

	public function testNotUsedYet() {

		$id = \ingot\testing\crud\session::create( array() );

		$this->assertFalse( \ingot\testing\crud\session::is_used( $id ) );


	}

	public function testMarkUsed() {

		$id = \ingot\testing\crud\session::create( array() );
		\ingot\testing\crud\session::mark_used( $id );
		$session = \ingot\testing\crud\session::read( $id );
		$this->assertSame( 0, $session[ 'used' ] );

	}

	public function testReadMarkedUsed(){

		$id = \ingot\testing\crud\session::create( array() );
		$session = \ingot\testing\crud\session::mark_used( $id );
		$this->assertSame( 1, $session[ 'used' ] );
	}


}
