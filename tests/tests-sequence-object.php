<?php

class tests_sequence_object extends \WP_UnitTestCase {
	/**
	 * Test that __get() gives false, not error on invalid property.
	 *
	 * @since 0.0.7
	 *
	 * @covers ingot\testing\object\sequence\__get()
	 * @covers ingot\testing\object\sequence\verify_sequence()
	 * @covers ingot\testing\object\sequence\set_properties()
	 * @covers ingot\testing\object\sequence\__construct()
	 */
	public function testInvalidParam(){
		$params = array(
			'test_type' => 'click',
			'a_id'      => 1,
			'b_id'      => 2,
			'group_ID'  => 404,
			'a_total'     => 10,
			'b_total' => 190,
			'a_win' => 10,
			'b_win' => 4

		);

		$created = \ingot\testing\crud\sequence::create( $params );

		$this->assertTrue( is_numeric( $created ) );
		$sequence = \ingot\testing\crud\sequence::read( $created );
		$sequence = new ingot\testing\object\sequence( $sequence );
		$this->assertFalse( $sequence->fart );
	}

	/**
	 * Test that DB fields can be returned.
	 *
	 * @since 0.0.7
	 *
	 * @covers ingot\testing\object\sequence\__get()
	 * @covers ingot\testing\object\sequence\verify_sequence()
	 * @covers ingot\testing\object\sequence\set_properties()
	 * @covers ingot\testing\object\sequence\__construct()
	 */
	public function testRegularParams(){
		//@todo deal with date issues
		$params = array(
			'test_type' => 'click',
			'a_id'      => 1,
			'b_id'      => 2,
			'group_ID'  => 404,
			'a_total'     => 10,
			'b_total' => 190,
			'a_win' => 10,
			'b_win' => 4,
			//'created' => 17,
			//'modified' => 22
		);

		$created = \ingot\testing\crud\sequence::create( $params );

		$this->assertTrue( is_numeric( $created ) );
		$sequence = \ingot\testing\crud\sequence::read( $created );
		$sequence = new ingot\testing\object\sequence( $sequence );

		foreach( $params as $field => $value ){
			$this->assertEquals( $value, $sequence->$field );
		}
	}

	/**
	 * Ensure that trying to divide by zero returns 0 instead of making an error.
	 *
	 * @since 0.0.7
	 *
	 * @covers ingot\testing\object\sequence\percentage()
	 */
	public function testDivideByZero() {
		$params = array(
			'test_type' => 'click',
			'a_id'      => 1,
			'b_id'      => 2,
			'group_ID'  => 404,
			'a_total'     => 25,
			'b_total' => 0,
			'a_win' => 0
		);


		$created = \ingot\testing\crud\sequence::create( $params );
		$this->assertTrue( is_numeric( $created ) );
		$sequence = \ingot\testing\crud\sequence::read( $created );
		$sequence = new ingot\testing\object\sequence( $sequence );

		$this->assertEquals( 0, $sequence->a_win_percentage );
		$this->assertEquals( 0, $sequence->b_total_percentage );
	}

	/**
	 * Test total is calculated properly.
	 *
	 * @since 0.0.7
	 *
	 * @covers ingot\testing\object\sequence\set_properties()
	 * @covers ingot\testing\object\sequence\set_total()
	 */
	public function testTotal() {
		$params = array(
			'test_type' => 'click',
			'a_id'      => 1,
			'b_id'      => 2,
			'group_ID'  => 404,
			'a_total'     => 25,
			'b_total' => 25
		);


		$created = \ingot\testing\crud\sequence::create( $params );
		$this->assertTrue( is_numeric( $created ) );
		$sequence = \ingot\testing\crud\sequence::read( $created );
		$sequence = new ingot\testing\object\sequence( $sequence );

		$this->assertEquals( 50, $sequence->total );
	}

	/**
	 * Test A win percentage is calculated properly.
	 *
	 * @since 0.0.7
	 *
	 * @covers ingot\testing\object\sequence\b_win_percentage()
	 * @covers ingot\testing\object\sequence\percentage()
	 */
	public function testAWinPercentage() {
		$params = array(
			'test_type' => 'click',
			'a_id'      => 1,
			'b_id'      => 2,
			'group_ID'  => 404,
			'a_total'     => 25,
			'b_total' => 75,
			'a_win' => 10,
			'b_win' => 4

		);

		$created = \ingot\testing\crud\sequence::create( $params );

		$this->assertTrue( is_numeric( $created ) );
		$sequence = \ingot\testing\crud\sequence::read( $created );
		$sequence = new ingot\testing\object\sequence( $sequence );

		$this->assertEquals( 10, $sequence->a_win_percentage );
	}

	/**
	 * Test that B total percentage is calculated properly.
	 *
	 * @since 0.0.7
	 *
	 * @covers ingot\testing\object\sequence\a_total_percentage()
	 * @covers ingot\testing\object\sequence\percentage()
	 */
	public function testATotalPercentage() {
		$params = array(
			'test_type' => 'click',
			'a_id'      => 1,
			'b_id'      => 2,
			'group_ID'  => 404,
			'a_total'     => 100,
			'b_total' => 100,
			'a_win' => 10,
			'b_win' => 25

		);

		$created = \ingot\testing\crud\sequence::create( $params );

		$this->assertTrue( is_numeric( $created ) );
		$sequence = \ingot\testing\crud\sequence::read( $created );
		$sequence = new ingot\testing\object\sequence( $sequence );

		$this->assertEquals( 50, $sequence->a_total_percentage );
	}

	/**
	 * Test that B total percentage is calculated properly.
	 *
	 * @since 0.0.7
	 *
	 * @covers ingot\testing\object\sequence\b_total_percentage()
	 * @covers ingot\testing\object\sequence\percentage()
	 */
	public function testBTotalPercentage() {
		$params = array(
			'test_type' => 'click',
			'a_id'      => 1,
			'b_id'      => 2,
			'group_ID'  => 404,
			'a_total'     => 10,
			'b_total' => 190,
			'a_win' => 10,
			'b_win' => 4

		);

		$created = \ingot\testing\crud\sequence::create( $params );

		$this->assertTrue( is_numeric( $created ) );
		$sequence = \ingot\testing\crud\sequence::read( $created );
		$sequence = new ingot\testing\object\sequence( $sequence );

		$this->assertEquals( 95, $sequence->b_total_percentage );
	}




}
