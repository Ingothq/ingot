<?php

/**
 * Class test_sequence_crud
 *
 * Test sequence CRUD
 */
class test_sequence_crud extends \WP_UnitTestCase {


	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
		\ingot\testing\crud\test::delete( 'all' );
		\ingot\testing\crud\sequence::delete( 'all' );
	}

	/**
	 * Test create
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\sequence::create()
	 */
	public function testCreateMinimal() {
		$params = array(
			'text' => rand(),
			'name' => rand(),
		);
		$test_1 = \ingot\testing\crud\test::create( $params );
		$params = array(
			'text' => rand(),
			'name' => rand(),
		);
		$test_2 = \ingot\testing\crud\test::create( $params );

		$params = array(
			'test_type' => 'click',
			'a_id' => $test_1,
			'b_id' => $test_2
		);

		$created = \ingot\testing\crud\sequence::create( $params );
		$this->assertFalse(  is_wp_error( $created ) );
		$this->assertTrue( is_numeric( $created ) );


	}

	/**
	 * Test read
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\sequence::create()
	 * @covers \ingot\testing\crud\read::create()
	 */
	public function testRead() {
		$params = array(
			'text' => rand(),
			'name' => rand(),
		);
		$test_1 = \ingot\testing\crud\test::create( $params );
		$params = array(
			'text' => rand(),
			'name' => rand(),
		);
		$test_2 = \ingot\testing\crud\test::create( $params );

		$params = array(
			'test_type' => 'click',
			'a_id' => $test_1,
			'b_id' => $test_2
		);

		$created = \ingot\testing\crud\sequence::create( $params );
		$this->assertFalse(  is_wp_error( $created ) );
		$sequence = \ingot\testing\crud\sequence::read( $created );
		$this->assertTrue( is_array( $sequence ) );
		$params = array(
			'text' => rand(),
			'name' => rand(),
		);
		$test_3 = \ingot\testing\crud\test::create( $params );
		$params = array(
			'text' => rand(),
			'name' => rand(),
		);
		$test_4 = \ingot\testing\crud\test::create( $params );

		$params = array(
			'test_type' => 'click',
			'a_id' => $test_3,
			'b_id' => $test_4
		);

		$created_2 = \ingot\testing\crud\sequence::create( $params );
		$this->assertFalse(  is_wp_error( $created_2 ) );
		$sequence = \ingot\testing\crud\sequence::read( $created_2 );
		$this->assertTrue( is_array( $sequence ) );
		$this->assertEquals( $sequence[ 'a_id' ], $test_3);
		$this->assertEquals( $sequence[ 'b_id' ], $test_4 );

		$sequence = \ingot\testing\crud\sequence::read( $created );
		$this->assertTrue( is_array( $sequence ) );
		$this->assertEquals( $sequence[ 'a_id' ], $test_1);
		$this->assertEquals( $sequence[ 'b_id' ], $test_2 );

	}

	/**
	 * Test that optional fields are filled in.
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\sequence::create()
	 * @covers \ingot\testing\crud\crud::fill_in()
	 * @covers \ingot\testing\crud\crud::validate_config()
	 */
	public function testCreateFillIn() {
		$params = array(
			'text' => rand(),
			'name' => rand(),
		);
		$test_1 = \ingot\testing\crud\test::create( $params );
		$params = array(
			'text' => rand(),
			'name' => rand(),
		);
		$test_2 = \ingot\testing\crud\test::create( $params );

		$params = array(
			'test_type' => 'click',
			'a_id' => $test_1,
			'b_id' => $test_2,
			'group_ID' => 12
		);

		$created = \ingot\testing\crud\sequence::create( $params );
		$sequence = \ingot\testing\crud\sequence::read( $created );

		foreach( \ingot\testing\crud\sequence::get_required_fields() as $field ) {
			$this->assertArrayHasKey( $field, $sequence );
		}

		foreach( \ingot\testing\crud\sequence::get_needed_fields() as $field ) {
			$this->assertArrayHasKey( $field, $sequence );
		}

		$this->assertEquals( 0, $sequence[ 'a_win' ] );
		$this->assertEquals( 0, $sequence[ 'a_total' ] );
		$this->assertEquals( 0, $sequence[ 'b_win' ] );
		$this->assertEquals( 0, $sequence[ 'b_total' ] );
	}

	/**
	 * Test that a missing required field will make an error
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\sequence::create()
	 * @covers \ingot\testing\crud\crud::validate_config()
	 */
	public function testCreateRequired() {
		$params = array(
			rand() => rand(),
			rand() => rand()
		);

		$created = \ingot\testing\crud\sequence::create( $params );

		$this->assertInstanceOf( "\WP_Error", $created );

	}



	/**
	 * Test that we can delete a sequence
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\sequence::delete()
	 */
	public function testDelete() {
		$params = array(
			'text' => rand(),
			'name' => rand(),
		);
		$test_1 = \ingot\testing\crud\test::create( $params );
		$params = array(
			'text' => rand(),
			'name' => rand(),
		);
		$test_2 = \ingot\testing\crud\test::create( $params );

		$params = array(
			'test_type' => 'click',
			'a_id' => $test_1,
			'b_id' => $test_2
		);

		$created = \ingot\testing\crud\sequence::create( $params );
		$this->assertNotEmpty( \ingot\testing\crud\sequence::read( $created ) );
		$deleted = \ingot\testing\crud\sequence::delete( $created );
		$this->assertFalse( \ingot\testing\crud\sequence::read( $created ) );
	}

	/**
	 * Test that we can update a sequence
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\sequence::update()
	 */
	public function testUpdate() {
		$params = array(
			'text' => rand(),
			'name' => rand(),
		);
		$test_1 = \ingot\testing\crud\test::create( $params );
		$params = array(
			'text' => rand(),
			'name' => rand(),
		);
		$test_2 = \ingot\testing\crud\test::create( $params );

		$params = array(
			'test_type' => 'click',
			'a_id' => $test_1,
			'b_id' => $test_2,
			'group_ID' => 12

		);

		$created = \ingot\testing\crud\sequence::create( $params );
		$sequence = \ingot\testing\crud\sequence::read( $created );
		$sequence[ 'test_type' ] = 'price';
		\ingot\testing\crud\sequence::update( $sequence, $created );
		$sequence = \ingot\testing\crud\sequence::read( $created );

		foreach( \ingot\testing\crud\sequence::get_required_fields() as $field ) {
			$this->assertArrayHasKey( $field, $sequence );
		}

		foreach( \ingot\testing\crud\sequence::get_needed_fields() as $field ) {
			$this->assertArrayHasKey( $field, $sequence );
		}

		$this->assertEquals( $sequence[ 'test_type'], 'price' );
	}

	/**
	 * Test that we can delete a sequence
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\sequence::delete()
	 * @covers \ingot\testing\crud|options_crud::delete_all()
	 */
	public function testDeleteAll() {
		for ( $i=0; $i <= 7; $i++ ) {

			$params = array(
				'text' => rand(),
				'name' => rand(),
			);
			$test_1 = \ingot\testing\crud\test::create( $params );
			$params = array(
				'text' => rand(),
				'name' => rand(),
			);
			$test_2 = \ingot\testing\crud\test::create( $params );

			$params = array(
				'name' => $i,
				'test_type' => 'click',
				'a_id' => $test_1,
				'b_id' => $test_2
			);


			$created[ $i ] = \ingot\testing\crud\sequence::create( $params );
			$this->assertTrue( is_numeric( $created[ $i ] ) );
		}


		\ingot\testing\crud\sequence::delete( 'all' );

		$items = \ingot\testing\crud\sequence::get_items( array() );
		$this->assertEquals( count( $items ), 0 );

	}

	/**
	 * Test limiting of get_all() query
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\sequence::get_items()
	 * @covers \ingot\testing\crud|options_crud::get_all()
	 */
	public function testGetItemsLimit() {
		\ingot\testing\crud\test::delete( 'all' );
		\ingot\testing\crud\sequence::delete( 'all' );
		for ( $i=1; $i <= 7; $i++ ) {
			$params = array(
				'text' => rand(),
				'name' => rand(),
			);
			$test_1 = \ingot\testing\crud\test::create( $params );
			$params = array(
				'text' => rand(),
				'name' => rand(),
			);
			$test_2 = \ingot\testing\crud\test::create( $params );

			$params = array(
				'name' => $i,
				'test_type' => 'click',
				'a_id' => $test_1,
				'b_id' => $test_2
			);


			$created[ $i ] = \ingot\testing\crud\sequence::create( $params );
			$this->assertTrue( is_numeric( $created[ $i ] ) );
		}

		$params = array(
			'limit' => 5,
			'page' => 1,
		);

		$items = \ingot\testing\crud\sequence::get_items( $params );
		$this->assertEquals( count( $items ), 5 );


	}

	/**
	 * Test pagination of get_all() query
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\sequence::get_items()
	 * @covers \ingot\testing\crud|options_crud::get_all()
	 */
	public function testGetItemsPagination() {
		\ingot\testing\crud\test::delete( 'all' );
		\ingot\testing\crud\sequence::delete( 'all' );
		for ( $i=1; $i <= 11; $i++ ) {
			$params = array(
				'text' => rand(),
				'name' => rand(),
			);
			$test_1 = \ingot\testing\crud\test::create( $params );
			$params = array(
				'text' => rand(),
				'name' => rand(),
			);
			$test_2 = \ingot\testing\crud\test::create( $params );

			$params = array(
				'name' => $i,
				'test_type' => 'click',
				'a_id' => $test_1,
				'b_id' => $test_2
			);


			$created[ $i ] = \ingot\testing\crud\sequence::create( $params );
			$this->assertTrue( is_numeric( $created[ $i ] ) );
		}

		$params = array(
			'limit' => 5,
			'page' => 2,
		);

		$items = \ingot\testing\crud\sequence::get_items( $params );
		$this->assertEquals( count( $items ), 5 );

		$params = array(
			'limit' => 5,
			'page' => 3,
		);

		$items = \ingot\testing\crud\sequence::get_items( $params );
		$this->assertEquals( count( $items ), 2 );


	}

	/**
	 * Test that when we ask for too high of a page, we get nothing back
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\sequence::get_items()
	 * @covers \ingot\testing\crud|options_crud::get_all()
	 */
	public function testGetItemsPaginationTooHigh() {
		\ingot\testing\crud\test::delete( 'all' );
		\ingot\testing\crud\sequence::delete( 'all' );
		for ( $i=1; $i <= 7; $i++ ) {
			$params = array(
				'text' => rand(),
				'name' => rand(),
			);
			$test_1 = \ingot\testing\crud\test::create( $params );
			$params = array(
				'text' => rand(),
				'name' => rand(),
			);
			$test_2 = \ingot\testing\crud\test::create( $params );

			$params = array(
				'name' => $i,
				'test_type' => 'click',
				'a_id' => $test_1,
				'b_id' => $test_2
			);


			$created[ $i ] = \ingot\testing\crud\sequence::create( $params );
			$this->assertTrue( is_numeric( $created[ $i ] ) );
		}

		$params = array(
			'limit' => 5,
			'page' => 4,
		);

		$items = \ingot\testing\crud\sequence::get_items( $params );
		$this->assertEmpty( $items );



	}

	/**
	 * Test a get_all() query done by ID
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\sequence::get_items()
	 * @covers \ingot\testing\crud|options_crud::select_by_ids()
	 * @covers \ingot\testing\crud|options_crud::format_results_from_sql_query()
	 */
	public function testGetItemsByID() {
		\ingot\testing\crud\test::delete( 'all' );
		\ingot\testing\crud\sequence::delete( 'all' );
		for ( $i=1; $i <= 7; $i++ ) {
			$params = array(
				'text' => rand(),
				'name' => rand(),
			);
			$test_1 = \ingot\testing\crud\test::create( $params );
			$params = array(
				'text' => rand(),
				'name' => rand(),
			);
			$test_2 = \ingot\testing\crud\test::create( $params );

			$params = array(
				'name' => $i,
				'test_type' => 'click',
				'a_id' => $test_1,
				'b_id' => $test_2
			);


			$created[ $i ] = \ingot\testing\crud\sequence::create( $params );
			$this->assertTrue( is_numeric( $created[ $i ] ) );
		}

		$params = array(
			'ids' => array( $created[ 1 ], $created[3], $created[7] ),
		);

		$items = \ingot\testing\crud\sequence::get_items( $params );
		$this->assertEquals( count( $items ), 3 );
		$this->assertEquals( $items[0][ 'ID'], $created[1] );
		$this->assertEquals( $items[1][ 'ID'], $created[3] );
		$this->assertEquals( $items[2][ 'ID'], $created[7] );


	}

	public function testGetAllByGroup() {
		\ingot\testing\crud\test::delete( 'all' );
		\ingot\testing\crud\sequence::delete( 'all' );
		for ( $g = 1; $g <= 3 ; $g++  ) {
			for ( $i = 1; $i <= 7; $i ++ ) {
				$params = array(
					'text' => rand(),
					'name' => rand(),
				);
				$test_1 = \ingot\testing\crud\test::create( $params );
				$params = array(
					'text' => rand(),
					'name' => rand(),
				);
				$test_2 = \ingot\testing\crud\test::create( $params );

				$params = array(
					'name'      => $i,
					'test_type' => 'click',
					'a_id'      => $test_1,
					'b_id'      => $test_2,
					'group_ID'  => $g
				);


				$created[ $g ][ $i ] = \ingot\testing\crud\sequence::create( $params );
				$this->assertTrue( is_numeric( $created[ $g ][ $i ] ) );
			}

		}

		$params = array(
			'group_ID' => 2,
		);
		$items = \ingot\testing\crud\sequence::get_items( $params );

		$ids_should_be = array_keys( $created[ 2 ] );
		$ids = wp_list_pluck( $items, 'ID' );
		foreach( $created[ 2 ] as $id ) {
			$this->assertArrayHasKey( $id, array_flip( $ids ) );
		}


	}

	/**
	 * Test that the invalid args make error
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud|options_crud::prepare_data()
	 * @covers \ingot\testing\crud|options_crud::fill_in()
	 */
	public function testInvalidArgs() {
		$params = array(
			'text' => rand(),
			'name' => rand(),
		);
		$test_1 = \ingot\testing\crud\test::create( $params );

		$params = array(
			'text' => rand(),
			'name' => rand(),
		);
		$test_2 = \ingot\testing\crud\test::create( $params );

		$params = array(
			'name' => rand(),
			'test_type' => 'click',
			'a_id' => array( rand( ) ),
			'b_id' => $test_1
		);

		$created = \ingot\testing\crud\sequence::create( $params );
		$this->assertInstanceOf( "\WP_Error", $created );

		$params = array(
			'name' => rand(),
			'test_type' => 'click',
			'a_id' => $test_1,
			'b_id' => new stdClass()
		);
		$created = \ingot\testing\crud\sequence::create( $params );
		$this->assertInstanceOf( "\WP_Error", $created );


		$params = array(
			'name' => rand(),
			'test_type' => 'Iron Maiden',
			'a_id' => $test_1,
		);

		$created = \ingot\testing\crud\sequence::create( $params );
		$this->assertInstanceOf( "\WP_Error", $created );


	}

	/**
	 * Test complete method
	 *
	 * @since 0.0.7
	 *
	 * @covers \ingot\testing\crud\sequence::complete()
	 */
	public function testComplete() {
		$params = array(
			'text' => rand(),
			'name' => rand(),
		);
		$test_1 = \ingot\testing\crud\test::create( $params );

		$params = array(
			'text' => rand(),
			'name' => rand(),
		);
		$test_2 = \ingot\testing\crud\test::create( $params );
		$params = array(
			'test_type' => 'click',
			'a_id'      => $test_1,
			'b_id'      => $test_2,
			'group_ID'  => 404
		);


		$created = \ingot\testing\crud\sequence::create( $params );
		$this->assertTrue( is_numeric( $created ) );
		$completed = \ingot\testing\crud\sequence::complete( $created );
		$sequence = \ingot\testing\crud\sequence::read( $created );
		$this->assertFalse( is_wp_error( $sequence ) );
		$this->assertEquals( 1, $sequence[ 'completed' ] );

	}




}
