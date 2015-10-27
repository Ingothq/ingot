<?php

/**
 * Class test_test_crud
 *
 * Test CRUD
 */
class test_test_crud extends \WP_UnitTestCase {


	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
		\ingot\testing\crud\test::delete( 'all' );
	}

	/**
	 * Test create
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\test::create()
	 */
	public function testCreateMinimal() {
		$params = array(
			'text' => 'a'
		);

		$created = \ingot\testing\crud\test::create( $params );
		$this->assertFalse(  is_wp_error( $created ) );
		$this->assertTrue( is_numeric( $created ) );


	}

	/**
	 * Test read
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\test::create()
	 * @covers \ingot\testing\crud\read::create()
	 */
	public function testRead() {
		$params = array(
			'text' => 't1',
			'name' => 'n1',
		);

		$created = \ingot\testing\crud\test::create( $params );
		$test = \ingot\testing\crud\test::read( $created );
		$this->assertTrue( is_array( $test ) );

		$params = array(
			'text' => 't2',
			'name' => 'n2',
		);


		$created_2 = \ingot\testing\crud\test::create( $params );
		$test = \ingot\testing\crud\test::read( $created );
		$this->assertTrue( is_array( $test ) );
		$this->assertEquals( $test[ 'text' ], 't1' );
		$this->assertEquals( $test[ 'name' ], 'n1' );

		$test = \ingot\testing\crud\test::read( $created_2 );
		$this->assertTrue( is_array( $test ) );
		$this->assertEquals( $test[ 'text' ], 't2' );
		$this->assertEquals( $test[ 'name' ], 'n2' );

	}

	/**
	 * Test that optional fields are filled in.
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\test::create()
	 * @covers \ingot\testing\crud\crud::fill_in()
	 * @covers \ingot\testing\crud\crud::validate_config()
	 */
	public function testCreateFillIn() {
		$params = array(
			'text' => 't1',
			'name' => 'h1',
		);

		$created = \ingot\testing\crud\test::create( $params );
		$test = \ingot\testing\crud\test::read( $created );

		foreach( \ingot\testing\crud\test::get_required_fields() as $field ) {
			$this->assertArrayHasKey( $field, $test );
		}

		foreach( \ingot\testing\crud\test::get_needed_fields() as $field ) {
			$this->assertArrayHasKey( $field, $test );
		}
	}

	/**
	 * Test that a missing required field will make an error
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\test::create()
	 * @covers \ingot\testing\crud\crud::validate_config()
	 */
	public function testCreateRequired() {
		$params = array(
			rand() => rand()
		);

		$created = \ingot\testing\crud\test::create( $params );

		$this->assertInstanceOf( "\WP_Error", $created );

	}



	/**
	 * Test that we can delete a test
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\test::delete()
	 */
	public function testDelete() {
		$params = array(
			'text' => rand(),
			'name' => rand(),
		);

		$created = \ingot\testing\crud\test::create( $params );
		$this->assertNotEmpty( \ingot\testing\crud\test::read( $created ) );
		$deleted = \ingot\testing\crud\test::delete( $created );
		$this->assertFalse( \ingot\testing\crud\test::read( $created ) );
	}

	/**
	 * Test that we can update a test
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\test::update()
	 */
	public function testUpdate() {
		$params = array(
			'text' => rand(),
			'name' => rand(),
		);

		$created = \ingot\testing\crud\test::create( $params );
		$test = \ingot\testing\crud\test::read( $created );
		$test[ 'name' ] = 'changed';
		\ingot\testing\crud\test::update( $test, $created );
		$test = \ingot\testing\crud\test::read( $created );

		foreach( \ingot\testing\crud\test::get_required_fields() as $field ) {
			$this->assertArrayHasKey( $field, $test );
		}

		foreach( \ingot\testing\crud\test::get_needed_fields() as $field ) {
			$this->assertArrayHasKey( $field, $test );
		}

		$this->assertEquals( $test[ 'name' ], 'changed' );

	}

	/**
	 * Test that we can delete a test
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\test::delete()
	 * @covers \ingot\testing\crud|options_crud::delete_all()
	 */
	public function testDeleteAll() {
		for ( $i=0; $i <= 7; $i++ ) {
			$params = array(
				'name' => $i,
				'text' => rand(),
			);
			$created[ $i ] = \ingot\testing\crud\test::create( $params );
		}


		\ingot\testing\crud\test::delete( 'all' );

		$items = \ingot\testing\crud\test::get_items( array() );
		$this->assertEquals( count( $items ), 0 );

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
			'text' => 'sfsdasf',
			'name' => 'hats',

		);

		$created = \ingot\testing\crud\test::create( $params );
		$this->assertTrue( is_numeric( $created ) );
		$key = 'ingot_test_' . $created;
		$this->assertEquals( get_option( $key ), \ingot\testing\crud\test::read( $created ) );
	}

	/**
	 * Test limiting of get_all() query
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\test::get_items()
	 * @covers \ingot\testing\crud|options_crud::get_all()
	 */
	public function testGetItemsLimit() {
		\ingot\testing\crud\test::delete( 'all' );
		for ( $i=1; $i <= 7; $i++ ) {
			$params = array(
				'name' => $i,
				'text' => rand(),
			);

			$created[ $i ] = \ingot\testing\crud\test::create( $params );
		}

		$params = array(
			'limit' => 5,
			'page' => 1,
		);

		$items = \ingot\testing\crud\test::get_items( $params );
		$this->assertEquals( count( $items ), 5 );


	}

	/**
	 * Test pagination of get_all() query
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\test::get_items()
	 * @covers \ingot\testing\crud|options_crud::get_all()
	 */
	public function testGetItemsPagination() {
		\ingot\testing\crud\test::delete( 'all' );
		for ( $i=1; $i <= 11; $i++ ) {
			$params = array(
				'name' => $i,
				'text' => rand(),
			);

			$created[ $i ] = \ingot\testing\crud\test::create( $params );
		}

		$params = array(
			'limit' => 5,
			'page' => 2,
		);

		$items = \ingot\testing\crud\test::get_items( $params );
		$this->assertEquals( 5, count( $items ) );

		$params = array(
			'limit' => 5,
			'page' => 3,
		);

		$items = \ingot\testing\crud\test::get_items( $params );
		$this->assertEquals( 2, count( $items ) );


	}

	/**
	 * Test that when we ask for too high of a page, we get nothing back
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\test::get_items()
	 * @covers \ingot\testing\crud|options_crud::get_all()
	 */
	public function testGetItemsPaginationTooHigh() {
		\ingot\testing\crud\test::delete( 'all' );
		for ( $i=1; $i <= 11; $i++ ) {
			$params = array(
				'name' => $i,
				'text' => rand(),
			);

			$created[ $i ] = \ingot\testing\crud\test::create( $params );
		}

		$params = array(
			'limit' => 5,
			'page' => 4,
		);

		$items = \ingot\testing\crud\test::get_items( $params );
		$this->assertEmpty( $items );



	}

	/**
	 * Test a get_all() query done by ID
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\test::get_items()
	 * @covers \ingot\testing\crud|options_crud::select_by_ids()
	 * @covers \ingot\testing\crud|options_crud::format_results_from_sql_query()
	 */
	public function testGetItemsByID() {
		\ingot\testing\crud\test::delete( 'all' );
		for ( $i=1; $i <= 11; $i++ ) {
			$params = array(
				'name' => $i,
				'text' => rand(),
			);

			$c[ $i ] = \ingot\testing\crud\test::create( $params );
		}

		$params = array(
			'ids' => array( $c[ 1 ], $c[3], $c[7] ),
		);

		$items = \ingot\testing\crud\test::get_items( $params );
		$this->assertEquals( count( $items ), 3 );
		$this->assertEquals( $items[0][ 'ID'], $c[1] );
		$this->assertEquals( $items[1][ 'ID'], $c[3] );
		$this->assertEquals( $items[2][ 'ID'], $c[7] );


	}


}
