<?php

/**
 * Class test_chance
 *
 * Test that chance are calcuated properly
 */
class test_chance extends  \WP_UnitTestCase {

	/**
	 * Test that when below initial chance is default -- 50
	 *
	 * @covers \ingot\testing\tests\chance
	 * @covers \ingot\testing\tests\chance\less_than_initial()
	 */
	public function testBelowInitial() {
		$sequence =  array(
			'initial' => 50,
			'a_win' => 3,
			'b_win' => 5,
			'a_total' => 3,
			'b_total' => 5
		);

		$chance = new \ingot\testing\tests\chance( $sequence );
		$this->assertEquals( 50, $chance->get_chance() );

	}

	/**
	 * Test that when above initial and A is winning
	 *
	 * @covers \ingot\testing\tests\calcualate_chance()
	 * @covers \ingot\testing\tests\chance
	 * @covers \ingot\testing\tests\chance\less_than_initial()
	 */
	public function testAboveIntialA() {

		$sequence =  array(
			'initial' => 1,
			'a_win' => 4,
			'b_win' => 1,
			'a_total' => 3,
			'b_total' => 5
		);

		$chance = new \ingot\testing\tests\chance( $sequence );
		$this->assertEquals( 75, $chance->get_chance() );


	}

	/**
	 * Test that when above initial and B is winning
	 *
	 * @covers \ingot\testing\tests\calcualate_chance()
	 * @covers \ingot\testing\tests\chance
	 * @covers \ingot\testing\tests\chance\less_than_initial()
	 */
	public function testAboveIntialB() {

		$sequence =  array(
			'initial' => 1,
			'a_win' => 1,
			'b_win' => 4,
			'a_total' => 3,
			'b_total' => 5
		);

		$chance = new \ingot\testing\tests\chance( $sequence );
		$this->assertEquals( 25, $chance->get_chance() );


	}

}
