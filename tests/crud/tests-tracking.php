<?php

/**
 * Test for tracking table CRUD
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class tests_tracking extends \WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$_SERVER[ 'HTTP_USER_AGENT' ] = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36';

	}

	public function tearDown() {
		parent::tearDown();

		\ingot\testing\crud\tracking::delete( 'all' );
		\ingot\testing\crud\sequence::delete( 'all' );
	}

	/**
	 * Test that table name is right
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\tracking::get_table_name()
	 */
	public function testTableName() {
		$tablename = \ingot\testing\crud\tracking::get_table_name();
		global $wpdb;
		$this->assertEquals( $wpdb->prefix . 'ingot_tracking', $tablename );
	}

	/**
	 * Test that ceaating with only the required field works
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\tracking::create()
	 * @covers \ingot\testing\crud\tracking::fill_in()
	 */
	public function testCreateMinimal() {
		$t_id = rand();
		$params = array(
			'test_ID' => $t_id
		);
		$id = \ingot\testing\crud\tracking::create( $params );
		$this->assertTrue( is_numeric( $id ) );

	}

	/**
	 * Test read of a minimially created item.
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\tracking::create()
	 * @covers \ingot\testing\crud\tracking::read()
	 */
	public function testRead() {

		$t_id = rand(1, 1029 );
		$params = array(
			'test_ID' => $t_id
		);
		$id =\ingot\testing\crud\tracking::create( $params );
		$this->assertTrue( is_numeric( $id ) );

		$tracking = \ingot\testing\crud\tracking::read( $id );

		$this->assertTrue( is_array( $tracking ) );
		$this->assertEquals( $t_id, $tracking[ 'test_ID' ] );

	}

	/**
	 * Test that optional fields are filled in.
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\tracking::create()
	 * @covers \ingot\testing\crud\crud::fill_in()
	 * @covers \ingot\testing\crud\crud::validate_config()
	 */
	public function testCreateFillIn() {

		$params = array(
			'test_ID' => 42
		);

		$created = \ingot\testing\crud\tracking::create( $params );
		$this->assertTrue( is_numeric( $created ) );
		$tracking = \ingot\testing\crud\tracking::read( $created );

		foreach( \ingot\testing\crud\tracking::get_required_fields() as $field ) {
			$this->assertArrayHasKey( $field, $tracking );
		}

		foreach( \ingot\testing\crud\tracking::get_needed_fields() as $field ) {
			$this->assertArrayHasKey( $field, $tracking );
		}

		$this->assertEquals( '127.0.0.1', ingot_get_ip() );

	}

	/**
	 * Test create with all data filled in.
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\tracking::create()
	 * @covers \ingot\testing\crud\tracking::read()
	 */
	public function testCreateWithData() {
		$params = array(
			'test_ID' => 2,
			'group_ID' => 3,
			'sequence_ID' => 7,
			'IP' => '2.3.5.8.13',
			'UTM' => array( 'a' => 'batman', 'c' => 'robin' ),
			'browser' => 'firefox',
			'user_agent' => 'windows and stuff',
			'time' => time(),
			'meta' => array( 'bees' => 'knees')
		);

		$created = \ingot\testing\crud\tracking::create( $params );
		$tracking = \ingot\testing\crud\tracking::read( $created );
		$params[ 'ID' ] = $created;
		foreach( $params as $key => $value ) {
			if( 'time' == $key ) {
				$this->assertEquals( date("Y-m-d H:i:s", $value ), $tracking[ $key ] );
			}else{
				$this->assertEquals( $value, $tracking[ $key ] );
			}

		}

	}




}
