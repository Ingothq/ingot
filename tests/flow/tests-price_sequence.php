<?php

/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class tests_price_sequence extends \WP_UnitTestCase {

	public function tearDown(){
		\ingot\testing\crud\price_group::delete( 'all' );
		\ingot\testing\crud\price_test::delete( 'all' );
	}


	public function testGetCurrentSequences(){
		$product_id = 42;
		$params = array(
			'product_ID' => $product_id,
			'default' => array(
				'a' => 0.1,
				'b' => 0.9
			)

		);

		for ( $i = 0; $i <= 3 ; $i++ ) {
			$id = \ingot\testing\crud\price_test::create( $params );
			$this->assertTrue( is_numeric( $id ) );
			$tests[ $i ] = $id;
		}

		$params = array(
			'type' => 'price',
			'plugin' => 'edd',
			'group_name' => rand(),
			'test_order' =>  $tests,
			'initial' => '42',
			'threshold' => '84',
			'product_ID' => $product_id

		);

		$created = \ingot\testing\crud\price_group::create( $params );
		$this->assertFalse(  is_wp_error( $created ) );
		$group = \ingot\testing\crud\price_group::read( $created );
		$sequence_id = $group[ 'current_sequence' ];
		$this->assertTrue( is_numeric( $sequence_id ) );
		$sequence = \ingot\testing\crud\sequence::read( $sequence_id );
		$this->assertTrue( is_array( $sequence ) );
		$this->assertEquals( $tests[0], $sequence[ 'a_id' ] );
		$this->assertEquals( $tests[1], $sequence[ 'b_id' ] );

	}

	/**
	 * Test that we can increase total for a price group sequence
	 *
	 * @since 0.0.9
	 *
	 * @covers \ingot\testing\tests\flow::increase_total()
	 */
	public function testIncreasePriceTotal() {
		$product_id = 42;
		$params = array(
			'product_ID' => $product_id,
			'default' => array(
				'a' => 0.1,
				'b' => 0.9
			)

		);

		for ( $i = 0; $i <= 1 ; $i++ ) {
			$id = \ingot\testing\crud\price_test::create( $params );
			$this->assertTrue( is_numeric( $id ) );
			$tests[ $i ] = $id;
		}

		$params = array(
			'type' => 'price',
			'plugin' => 'edd',
			'group_name' => rand(),
			'test_order' =>  $tests,
			'initial' => '42',
			'threshold' => '84',
			'product_ID' => $product_id

		);

		$group_id = \ingot\testing\crud\price_group::create( $params );
		$this->assertTrue( is_numeric( $group_id ) );
		$group = \ingot\testing\crud\price_group::read( $group_id );
		$sequence_id = $group[ 'current_sequence' ];
		$this->assertTrue( is_numeric( $sequence_id ) );

		\ingot\testing\tests\flow::increase_total( $tests[0], $sequence_id );
		$sequence = \ingot\testing\crud\sequence::read( $sequence_id );
		$this->assertEquals( 1, $sequence[ 'a_total' ] );
		$this->assertEquals( 0, $sequence[ 'b_total' ] );

		\ingot\testing\tests\flow::increase_total( $tests[0], $sequence_id );
		$sequence = \ingot\testing\crud\sequence::read( $sequence_id );
		$this->assertEquals( 2, $sequence[ 'a_total' ] );
		$this->assertEquals( 0, $sequence[ 'b_total' ] );

		\ingot\testing\tests\flow::increase_total( $tests[1], $sequence_id );
		$sequence = \ingot\testing\crud\sequence::read( $sequence_id );
		$this->assertEquals( 2, $sequence[ 'a_total' ] );
		$this->assertEquals( 1, $sequence[ 'b_total' ] );

	}

	/**
	 * Test that we can increase victory for a price group sequence
	 *
	 * @since 0.0.9
	 *
	 * @covers \ingot\testing\tests\flow::increase_victory()
	 * @covers ingot\testing\tests\click:make_initial_sequence()
	 */
	public function testIncreasePriceVictory() {
		$product_id = 42;
		$params = array(
			'product_ID' => $product_id,
			'default' => array(
				'a' => 0.1,
				'b' => 0.9
			)

		);

		for ( $i = 0; $i <= 1 ; $i++ ) {
			$id = \ingot\testing\crud\price_test::create( $params );
			$this->assertTrue( is_numeric( $id ) );
			$tests[ $i ] = $id;
		}

		$params = array(
			'type' => 'price',
			'plugin' => 'edd',
			'group_name' => rand(),
			'test_order' =>  $tests,
			'initial' => '42',
			'threshold' => '84',
			'product_ID' => $product_id

		);

		$group_id = \ingot\testing\crud\price_group::create( $params );
		$this->assertTrue( is_numeric( $group_id ) );
		$group = \ingot\testing\crud\price_group::read( $group_id );
		$sequence_id = $group[ 'current_sequence' ];
		$this->assertTrue( is_numeric( $sequence_id ) );

		\ingot\testing\tests\flow::increase_victory( $tests[0], $sequence_id );
		$sequence = \ingot\testing\crud\sequence::read( $sequence_id );
		$this->assertEquals( 1, $sequence[ 'a_win' ] );
		$this->assertEquals( 0, $sequence[ 'b_win' ] );

		\ingot\testing\tests\flow::increase_victory( $tests[0], $sequence_id );
		$sequence = \ingot\testing\crud\sequence::read( $sequence_id );
		$this->assertEquals( 2, $sequence[ 'a_win' ] );
		$this->assertEquals( 0, $sequence[ 'b_win' ] );

		\ingot\testing\tests\flow::increase_victory( $tests[1], $sequence_id );
		$sequence = \ingot\testing\crud\sequence::read( $sequence_id );
		$this->assertEquals( 2, $sequence[ 'a_win' ] );
		$this->assertEquals( 1, $sequence[ 'b_win' ] );

	}

	/**
	 * Test that enough wins will create a new sequence
	 *
	 * @since 0.0.9
	 *
	 * @covers \ingot\testing\tests\flow::increase_victory()
	 * @covers ingot\testing\tests\click:make_next_sequence()
	 */
	public function testCreateNextSequence() {
		$product_id = 42;
		$params = array(
			'product_ID' => $product_id,
			'default' => array(
				'a' => 0.1,
				'b' => 0.9
			)

		);

		for ( $i = 0; $i <= 2 ; $i++ ) {
			$id = \ingot\testing\crud\price_test::create( $params );
			$this->assertTrue( is_numeric( $id ) );
			$tests[ $i ] = $id;
		}

		$params = array(
			'type' => 'price',
			'plugin' => 'edd',
			'group_name' => rand(),
			'test_order' =>  $tests,
			'initial' => 0,
			'threshold' => 2,
			'product_ID' => $product_id

		);

		$group_id = \ingot\testing\crud\price_group::create( $params );
		$this->assertFalse(  is_wp_error( $group_id ) );
		$group = \ingot\testing\crud\price_group::read( $group_id );
		$sequence_id = $group[ 'current_sequence' ];
		$this->assertTrue( is_numeric( $sequence_id ) );
		$sequence = \ingot\testing\crud\sequence::read( $sequence_id );
		$this->assertTrue( is_array( $sequence ) );
		$this->assertEquals( $tests[0], $sequence[ 'a_id' ] );
		$this->assertEquals( $tests[1], $sequence[ 'b_id' ] );

		\ingot\testing\tests\flow::increase_victory( $tests[0], $sequence_id );
		\ingot\testing\tests\flow::increase_victory( $tests[0], $sequence_id );
		$sequence = \ingot\testing\crud\sequence::read( $sequence_id );
		$this->assertEquals( 1, $sequence[ 'completed' ] );

		$group = \ingot\testing\crud\price_group::read( $group_id );

		$new_sequence_id = $group[ 'current_sequence' ];
		$new_sequence = \ingot\testing\crud\sequence::read( $new_sequence_id );
		$this->assertTrue( is_array( $new_sequence ) );
		$this->assertArrayHasKey( 'a_id', $new_sequence );
		$this->assertArrayHasKey( 'b_id', $new_sequence );
		$this->assertTrue( in_array( $tests[0], array( $new_sequence[ 'a_id' ], $new_sequence[ 'b_id' ] ) ) );
		$this->assertTrue( in_array( $tests[2], array( $new_sequence[ 'a_id' ], $new_sequence[ 'b_id' ] ) ) );

	}



}
