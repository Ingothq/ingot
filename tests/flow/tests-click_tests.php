<?php

/**
 * Test, assuming functionality of CRUD is correct, that test flow is correct.
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class test_click_tests extends \WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
		\ingot\testing\crud\test::delete( 'all' );
		\ingot\testing\crud\sequence::delete( 'all' );
		\ingot\testing\crud\group::delete( 'all' );
	}

	/**
	 * Test that initial sequence is created for a group
	 *
	 * @since 0.0.7
	 *
	 * @covers ingot\testing\tests\click\click::make_initial_sequence
	 */
	public function testInitialSequence() {
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
			'text' => rand(),
			'name' => rand(),
		);
		$test_3 = \ingot\testing\crud\test::create( $params );


		$params = array(
			'type' => 'click',
			'name' => 'hats',
			'selector' => '.hats',
			'link' => 'https://hats.com',
			'order' => array( $test_1, $test_2, $test_3 )
		);

		$group_id = \ingot\testing\crud\group::create( $params );
		$group = \ingot\testing\crud\group::read( $group_id );

		$sequence = \ingot\testing\crud\sequence::read( $group[ 'sequences' ][0] );
		$this->assertFalse( empty( $sequence ) );
		$this->assertTrue( is_array( $sequence ) );
		$this->assertFalse( empty( $group[ 'sequences' ] )  );
		$this->assertArrayHasKey( 0, $group[ 'sequences' ] );
		$this->assertTrue( is_numeric( $group[ 'sequences' ][0] )   );
		$this->assertFalse( empty( $group[ 'order' ] )  );

		$this->assertEquals( $group[ 'current_sequence' ], $group[ 'sequences' ][0] );
		$this->assertEquals( $group[ 'order' ][0], $test_1 );
		$this->assertEquals( $group[ 'order' ][1], $test_2 );
		$this->assertEquals( $group[ 'order' ][2], $test_3 );

		$this->assertTrue( is_array( $sequence ) );
		$this->assertEquals( $sequence[ 'a_id' ], $test_1);
		$this->assertEquals( $sequence[ 'b_id' ], $test_2 );

	}

	/**
	 * Test that the is A utility function works properly
	 *
	 * @since 0.0.7
	 *
	 * @covers ingot\testing\tests\click\click::is_a
	 */
	public function testIsA() {
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
			'type' => 'click',
			'selector' => '.hats',
			'link' => 'https://hats.com',
			'order' => array( $test_1, $test_2 )
		);
		$group_id = \ingot\testing\crud\group::create( $params );

		$group = \ingot\testing\crud\group::read( $group_id );
		$sequence = \ingot\testing\crud\sequence::read( $group[ 'sequences' ][0] );

		$this->assertTrue( \ingot\testing\tests\click\click::is_a( $test_1, $sequence ) );
		$this->assertFalse( \ingot\testing\tests\click\click::is_a( $test_2, $sequence ) );

	}

	/**
	 * Test that the is A utility function returns a WP_Error when test is not a part of the squence
	 *
	 * @since 0.0.7
	 *
	 * @covers ingot\testing\tests\click\click::is_a
	 */
	public function testInvalidIsA() {
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
			'type' => 'click',
			'selector' => '.hats',
			'link' => 'https://hats.com',
			'order' => array( $test_1, $test_2, $test_3 )
		);
		$group_id = \ingot\testing\crud\group::create( $params );

		$group = \ingot\testing\crud\group::read( $group_id );
		$sequence = \ingot\testing\crud\sequence::read( $group[ 'sequences' ][0] );

		$this->assertInstanceOf( "\WP_Error", \ingot\testing\tests\click\click::is_a( $test_3, $sequence ) );
		$this->assertInstanceOf( "\WP_Error", \ingot\testing\tests\click\click::is_a( $test_4, $sequence ) );


	}

	/**
	 * Test that we can increase totals or tests within a sequence properly.
	 *
	 * @since 0.0.7
	 *
	 * @covers ingot\testing\tests\click\click::increase_total
	 */
	public function testIncreaseTotal() {
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
			'type' => 'click',
			'selector' => '.hats',
			'link' => 'https://hats.com',
			'order' => array( $test_1, $test_2 )
		);

		$group_id = \ingot\testing\crud\group::create( $params );

		$group = \ingot\testing\crud\group::read( $group_id );
		$sequence = \ingot\testing\crud\sequence::read( $group[ 'sequences' ][0] );

		\ingot\testing\tests\click\click::increase_total( $test_1, $sequence[ 'ID' ] );

		$sequence = \ingot\testing\crud\sequence::read( $sequence[ 'ID' ] );
		$this->assertEquals( 1, $sequence[ 'a_total' ] );
		$this->assertEquals( 0, $sequence[ 'b_total' ] );

		\ingot\testing\tests\click\click::increase_total( $test_2, $sequence[ 'ID' ] );

		$sequence = \ingot\testing\crud\sequence::read( $sequence[ 'ID' ] );
		$this->assertEquals( 1, $sequence[ 'a_total' ] );
		$this->assertEquals( 1, $sequence[ 'b_total' ] );

		\ingot\testing\tests\click\click::increase_total( $test_2, $sequence[ 'ID' ] );
		\ingot\testing\tests\click\click::increase_total( $test_2, $sequence[ 'ID' ] );

		$sequence = \ingot\testing\crud\sequence::read( $sequence[ 'ID' ] );
		$this->assertEquals( 1, $sequence[ 'a_total' ] );
		$this->assertEquals( 3, $sequence[ 'b_total' ] );

		$times = rand(0, 11 );
		for ( $i=1; $i <= $times; $i++ ) {
			\ingot\testing\tests\click\click::increase_total( $test_1, $sequence['ID'] );
		}

		$times++;
		$sequence = \ingot\testing\crud\sequence::read( $sequence[ 'ID' ] );
		$this->assertEquals( $times, $sequence[ 'a_total' ] );
		$this->assertEquals( 3, $sequence[ 'b_total' ] );

	}

	/**
	 * Test that we can increase wins for tests within a sequence properly.
	 *
	 * @since 0.0.7
	 *
	 * @covers ingot\testing\tests\click\click::increase_victory
	 */
	public function testIncreaseVictory() {
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
			'type' => 'click',
			'selector' => '.hats',
			'link' => 'https://hats.com',
			'order' => array( $test_1, $test_2 ),
			'threshold' => 500
		);

		$group_id = \ingot\testing\crud\group::create( $params );

		$group = \ingot\testing\crud\group::read( $group_id );
		$sequence = \ingot\testing\crud\sequence::read( $group[ 'sequences' ][0] );

		\ingot\testing\tests\click\click::increase_victory( $test_1, $group[ 'current_sequence' ] );

		$sequence = \ingot\testing\crud\sequence::read( $group[ 'current_sequence' ] );
		$this->assertEquals( $sequence[ 'a_id' ], $test_1);
		$this->assertEquals( $sequence[ 'b_id' ], $test_2 );
		$this->assertEquals( 1, $sequence[ 'a_win' ] );
		$this->assertEquals( 0, $sequence[ 'b_win' ] );

		\ingot\testing\tests\click\click::increase_victory( $test_2, $group[ 'current_sequence' ] );

		$sequence = \ingot\testing\crud\sequence::read( $group[ 'current_sequence' ] );
		$this->assertEquals( 1, $sequence[ 'a_win' ] );
		$this->assertEquals( 1, $sequence[ 'b_win' ] );

		\ingot\testing\tests\click\click::increase_victory( $test_2, $group[ 'current_sequence' ] );
		\ingot\testing\tests\click\click::increase_victory( $test_2, $group[ 'current_sequence' ] );

		$sequence = \ingot\testing\crud\sequence::read( $sequence[ 'ID' ] );
		$this->assertEquals( 1, $sequence[ 'a_win' ] );
		$this->assertEquals( 3, $sequence[ 'b_win' ] );

		$times = rand(0, 11 );
		for ( $i=1; $i <= $times; $i++ ) {
			\ingot\testing\tests\click\click::increase_victory( $test_1, $group[ 'current_sequence' ] );
		}

		$times++;
		$sequence = \ingot\testing\crud\sequence::read( $group[ 'current_sequence' ] );
		$this->assertEquals( $times, $sequence[ 'a_win' ] );
		$this->assertEquals( 3, $sequence[ 'b_win' ] );

	}

	/**
	 * Test that we can create the next sequence arbitrarily
	 *
	 * @since 0.0.7
	 *
	 * @covers ingot\testing\tests\click\click::make_next_sequence
	 */
	public function testMakeNextSequence() {
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
			'type' => 'click',
			'selector' => '.hats',
			'link' => 'https://hats.com',
			'order' => array( $test_1, $test_2, $test_3 )
		);
		$group_id = \ingot\testing\crud\group::create( $params );

		$group = \ingot\testing\crud\group::read( $group_id );
		$sequence = \ingot\testing\crud\sequence::read( $group[ 'sequences' ][0] );
		$new_sequence_id = \ingot\testing\tests\click\click::make_next_sequence( $group_id, $test_1 );
		$group = \ingot\testing\crud\group::read( $group_id );
		$this->assertArrayHasKey( 1, $group[ 'sequences'] );
		$this->assertEquals( $new_sequence_id, $group[ 'sequences' ][1] );

		$new_sequence = \ingot\testing\crud\sequence::read( $new_sequence_id );
		$old_sequence = \ingot\testing\crud\sequence::read( $group[ 'sequences' ][0] );
		$this->assertFalse( empty( $new_sequence ) );
		$this->assertEquals( $new_sequence[ 'a_id' ], $test_1 );
		$this->assertEquals( $new_sequence[ 'b_id' ], $test_3 );
		$this->assertEquals( 1, $old_sequence[ 'completed' ] );


	}

	/**
	 * Test that we can create the next sequence in context
	 *
	 * @since 0.0.7
	 *
	 * @covers ingot\testing\tests\click\click::make_next_sequence
	 */
	public function testMakeNextInContext() {
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
			'type' => 'click',
			'selector' => '.hats',
			'link' => 'https://hats.com',
			'order' => array( $test_1, $test_2, $test_3 ),
			'threshold' => rand( 15, 27 )
		);
		$group_id = \ingot\testing\crud\group::create( $params );
		$group = \ingot\testing\crud\group::read( $group_id );
		$sequence_id = $group[ 'sequences' ][0];
		$sequence = \ingot\testing\crud\sequence::read( $sequence_id );

		for( $i = 0; $i <= 37; $i++ ){

			\ingot\testing\tests\click\click::increase_victory( $test_2, $sequence_id );
			$sequence = \ingot\testing\crud\sequence::read( $sequence_id );

			if ( $i + 1 < $group[ 'threshold' ] ) {
				$this->assertEquals( $i + 1,  $sequence[ 'b_win' ] );
				$this->assertEquals( 0, (int) $sequence['completed'] );
			}else{
				$this->assertEquals( 1, (int) $sequence[ 'completed' ] );
				break;
			}
		}


		$group = \ingot\testing\crud\group::read( $group_id );
		$new_sequence_id = $group[ 'current_sequence'];
		$this->assertNotEquals( (int) $sequence_id, $group[ 'current_sequence'] );

		$new_sequence = \ingot\testing\crud\sequence::read( $new_sequence_id );
		$this->assertEquals( (int) $new_sequence[ 'a_id' ], (int) $test_2 );
		$this->assertEquals( (int) $new_sequence[ 'a_id' ], (int) $test_2 );
		$this->assertEquals( (int) $new_sequence[ 'b_id' ], (int) $test_3 );

	}




}
