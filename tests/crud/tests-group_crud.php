<?php

/**
 * Class test_group_crud
 *
 * Test group CRUD
 */
class test_group_crud extends \WP_UnitTestCase {


	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
		\ingot\testing\crud\group::delete( 'all' );
	}

	/**
	 * Test create
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\group::create()
	 */
	public function testCreateMinimal() {
		$params = array(
			'type' => 'click',
		);

		$created = \ingot\testing\crud\group::create( $params );
		$this->assertTrue( is_int( $created ) );
		$this->assertFalse(  is_wp_error( $created ) );
		$this->assertTrue( is_numeric( $created ) );


	}

	/**
	 * Test read
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\group::create()
	 * @covers \ingot\testing\crud\read::create()
	 */
	public function testRead() {
		$params = array(
			'type' => 'click',
			'click_type' => 'link',
			'name' => 'hats',
			'selector' => '.hats',
			'link' => 'https://hats.com',
			'current_sequence' => 42
		);

		$created = \ingot\testing\crud\group::create( $params );

		$this->assertTrue( is_int( $created ) );
		$group = \ingot\testing\crud\group::read( $created );
		$this->assertTrue( is_array( $group ) );

		$params = array(
			'type' => 'click',
			'name' => 'bats',
			'selector' => '.bats',
			'link' => 'https://bats.com',

		);

		$created_2 = \ingot\testing\crud\group::create( $params );
		$group = \ingot\testing\crud\group::read( $created_2 );
		$this->assertTrue( is_array( $group ) );
		$this->assertEquals( $group[ 'selector' ], '.bats' );
		$this->assertEquals( $group[ 'link' ], 'https://bats.com' );

		$group = \ingot\testing\crud\group::read( $created );
		$this->assertTrue( is_array( $group ) );
		$this->assertEquals( $group[ 'selector' ], '.hats' );
		$this->assertEquals( $group[ 'link' ], 'https://hats.com' );

	}

	/**
	 * Test that optional fields are filled in.
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\group::create()
	 * @covers \ingot\testing\crud\crud::fill_in()
	 * @covers \ingot\testing\crud\crud::validate_config()
	 */
	public function testCreateFillIn() {
		$params = array(
			'type' => 'click',
			'name' => 'bats'
		);

		$created = \ingot\testing\crud\group::create( $params );

		$group = \ingot\testing\crud\group::read( $created );

		$this->assertEquals( 'link', $group[ 'click_type' ] );
		foreach( \ingot\testing\crud\group::get_required_fields() as $field ) {
			$this->assertArrayHasKey( $field, $group );
		}

		foreach( \ingot\testing\crud\group::get_needed_fields() as $field ) {
			$this->assertArrayHasKey( $field, $group );
		}
	}

	/**
	 * Test that a missing required field will make an error
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\group::create()
	 * @covers \ingot\testing\crud\crud::validate_config()
	 */
	public function testCreateRequired() {
		$params = array(
			rand() => rand()
		);

		$created = \ingot\testing\crud\group::create( $params );

		$this->assertInstanceOf( "\WP_Error", $created );

	}

	/**
	 * Test that a group is of the right type doesn't false trip type check
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\group::create()
	 * @covers \ingot\testing\crud\crud::validate_config()
	 * @covers \ingot\testing\crud\crud::validate_type
	 */
	public function testValidType() {
		$params = array(
			'type' => 'click',
			'name' => 'bats'
		);

		$created = \ingot\testing\crud\group::create( $params );

		$this->assertFalse(  is_wp_error( $created ) );
		$this->assertTrue( is_numeric( $created ) );

		$params = array(
			'type' => 'price',
			'name' => 'bats'
		);

		$created = \ingot\testing\crud\group::create( $params );

		$this->assertFalse(  is_wp_error( $created ) );
		$this->assertTrue( is_numeric( $created ) );
	}

	/**
	 * Test that invalid type groups return an error
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\group::create()
	 * @covers \ingot\testing\crud\crud::validate_config()
	 * @covers \ingot\testing\crud\crud::validate_type
	 */
	public function testInvalidType() {
		$params = array(
			'type' => 'josh',
			'name' => 'bats'
		);

		$created = \ingot\testing\crud\group::create( $params );
		$this->assertInstanceOf( "\WP_Error", $created );
	}

	/**
	 * Test that a click group of the right type does not false trip click type check
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\group::create()
	 * @covers \ingot\testing\crud\crud::validate_config()
	 * @covers \ingot\testing\crud\crud::validate_type
	 */
	public function testValidClickType() {
		$params = array(
			'type' => 'click',
			'click_type' => 'link'
		);

		$created = \ingot\testing\crud\group::create( $params );

		$this->assertFalse(  is_wp_error( $created ) );
		$this->assertTrue( is_numeric( $created ) );

		$params = array(
			'type' => 'click',
			'click_type' => 'text'
		);

		$created = \ingot\testing\crud\group::create( $params );

		$this->assertFalse(  is_wp_error( $created ) );
		$this->assertTrue( is_numeric( $created ) );

		$params = array(
			'type' => 'click',
			'click_type' => 'button'
		);

		$created = \ingot\testing\crud\group::create( $params );

		$this->assertFalse(  is_wp_error( $created ) );
		$this->assertTrue( is_numeric( $created ) );
	}

	/**
	 * Test that a group with the wrong click type is not allowed
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\group::create()
	 * @covers \ingot\testing\crud\crud::validate_config()
	 * @covers \ingot\testing\crud\crud::validate_type
	 */
	public function testInvalidClickType() {
		$params = array(
			'type' => 'click',
			'click_type' => 'josh'
		);

		$created = \ingot\testing\crud\group::create( $params );
		$this->assertInstanceOf( "\WP_Error", $created );
	}

	/**
	 * Test that we can delete a group
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\group::delete()
	 */
	public function testDelete() {
		$params = array(
			'type' => 'click',
			'name' => 'bats'
		);

		$created = \ingot\testing\crud\group::create( $params );
		$this->assertNotEmpty( \ingot\testing\crud\group::read( $created ) );
		$deleted = \ingot\testing\crud\group::delete( $created );
		$this->assertFalse( \ingot\testing\crud\group::read( $created ) );
	}

	/**
	 * Test that we can update a group
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\group::update()
	 */
	public function testUpdate() {
		$params = array(
			'type' => 'click',
			'name' => 'hats',
			'selector' => '.hats',
			'link' => 'https://hats.com'
		);

		$created = \ingot\testing\crud\group::create( $params );
		$group = \ingot\testing\crud\group::read( $created );
		$group[ 'selector' ] = '.changed';
		\ingot\testing\crud\group::update( $group, $created );
		$group = \ingot\testing\crud\group::read( $created );

		foreach( \ingot\testing\crud\group::get_required_fields() as $field ) {
			$this->assertArrayHasKey( $field, $group );
		}

		foreach( \ingot\testing\crud\group::get_needed_fields() as $field ) {
			$this->assertArrayHasKey( $field, $group );
		}

		$this->assertEquals( $group[ 'selector'], '.changed' );
		$this->assertEquals( $group[ 'name' ],  'hats' );
	}

	/**
	 * Test that we can delete a group
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\group::delete()
	 * @covers \ingot\testing\crud|options_crud::delete_all()
	 */
	public function testDeleteAll() {
		for ( $i=0; $i <= 7; $i++ ) {
			$params = array(
				'name' => $i,
				'type' => 'click',
			);
			$created[ $i ] = \ingot\testing\crud\group::create( $params );
		}


		\ingot\testing\crud\group::delete( 'all' );

		$items = \ingot\testing\crud\group::get_items( array() );
		$this->assertEquals( count( $items ), 0 );

	}

	/**
	 * Test limiting of get_all() query
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\group::get_items()
	 * @covers \ingot\testing\crud|options_crud::get_all()
	 */
	public function testGetItemsLimit() {
		\ingot\testing\crud\group::delete( 'all' );
		for ( $i=1; $i <= 7; $i++ ) {
			$params = array(
				'name' => $i,
				'type' => 'click',
			);

			$created[ $i ] = \ingot\testing\crud\group::create( $params );
		}

		$params = array(
			'limit' => 5,
			'page' => 1,
		);

		$items = \ingot\testing\crud\group::get_items( $params );
		$this->assertEquals( count( $items ), 5 );


	}

	/**
	 * Test pagination of get_all() query
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\group::get_items()
	 * @covers \ingot\testing\crud|options_crud::get_all()
	 */
	public function testGetItemsPagination() {
		\ingot\testing\crud\group::delete( 'all' );
		for ( $i=1; $i <= 11; $i++ ) {
			$params = array(
				'name' => $i,
				'type' => 'click',
			);

			$created[ $i ] = \ingot\testing\crud\group::create( $params );
		}

		$params = array(
			'limit' => 5,
			'page' => 2,
		);

		$items = \ingot\testing\crud\group::get_items( $params );
		$this->assertEquals( count( $items ), 5 );

		$params = array(
			'limit' => 5,
			'page' => 3,
		);

		$items = \ingot\testing\crud\group::get_items( $params );
		$this->assertEquals( count( $items ), 2 );


	}

	/**
	 * Test that when we ask for too high of a page, we get nothing back
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\group::get_items()
	 * @covers \ingot\testing\crud|options_crud::get_all()
	 */
	public function testGetItemsPaginationTooHigh() {
		\ingot\testing\crud\group::delete( 'all' );
		for ( $i=1; $i <= 11; $i++ ) {
			$params = array(
				'name' => $i,
				'type' => 'click',
			);

			$created[ $i ] = \ingot\testing\crud\group::create( $params );
		}

		$params = array(
			'limit' => 5,
			'page' => 4,
		);

		$items = \ingot\testing\crud\group::get_items( $params );
		$this->assertEmpty( $items );



	}

	/**
	 * Test a get_all() query done by ID
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\group::get_items()
	 * @covers \ingot\testing\crud|options_crud::select_by_ids()
	 * @covers \ingot\testing\crud|options_crud::format_results_from_sql_query()
	 */
	public function testGetItemsByID() {
		\ingot\testing\crud\group::delete( 'all' );
		for ( $i=1; $i <= 11; $i++ ) {
			$params = array(
				'name' => $i,
				'type' => 'click',
			);

			$c[ $i ] = \ingot\testing\crud\group::create( $params );
		}

		$params = array(
			'ids' => array( $c[ 1 ], $c[3], $c[7] ),
		);

		$items = \ingot\testing\crud\group::get_items( $params );
		$this->assertEquals( count( $items ), 3 );
		$this->assertEquals( $items[0][ 'ID'], $c[1] );
		$this->assertEquals( $items[1][ 'ID'], $c[3] );
		$this->assertEquals( $items[2][ 'ID'], $c[7] );


	}

	/**
	 * Test that the option name is correct
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud|options_crud::key_name()
	 */
	public function testKeyName() {
		$params = array(
			'type' => 'click',
			'name' => 'hats',
			'selector' => '.hats',
			'link' => 'https://hats.com'
		);

		$created = \ingot\testing\crud\group::create( $params );
		$key = 'ingot_group_' . $created;
		$this->assertEquals( get_option( $key ), \ingot\testing\crud\group::read( $created ) );
	}



}
