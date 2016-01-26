<?php
/**
 * Test array_filter utility class
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */
class tests_array_filters extends \WP_UnitTestCase{
	/**
	 * Test flattener
	 *
	 * @since 1.1.0
	 *
	 * @group utility
	 * @group array_filter
	 *
	 * @covers \ingot\testing\utility\array_filters::flatten()
	 */
	public function testFlatten(){
		$array = [
			'ingot_destination' => 88,
			'hats_8765' => [ 'batman', 'robin']
		];
		$this->assertEquals( ['ingot_destination' => 88], \ingot\testing\utility\array_filters::flatten( $array ) );

		$array = [
			'ingot_destination' => 88,
			'ingot_destination_12',
			'hats_8765' => [ 'batman', 'robin']
		];
		$this->assertEquals( ['ingot_destination' => 88, 'ingot_destination_12'], \ingot\testing\utility\array_filters::flatten( $array ) );

		$array = [
			new \stdClass(),
			'hats_8765' => [ 'batman', 'robin']
		];
		$this->assertEquals( [], \ingot\testing\utility\array_filters::flatten( $array ) );
	}

	/**
	 * Test the basic filter
	 *
	 * @since 1.1.0
	 *
	 * @group utility
	 * @group array_filter
	 *
	 * @covers \ingot\testing\utility\array_filters::filter()
	 * @covers \ingot\testing\utility\array_filters::match()
	 * @covers \ingot\testing\utility\array_filters::prepare()
	 */
	public function testFilter(){
		$data = [ 'ingot_destination_9', 'ingot_destination_42', 'hi chris', 'ingot_', 'ingot_destination' ];
		$results = \ingot\testing\utility\array_filters::filter( $data, 'ingot_destination_' );
		$this->assertEquals( [
			'ingot_destination_9',
			'ingot_destination_42'
		], $results );
	}

	/**
	 * Test filter by array keys
	 *
	 * @since 1.1.0
	 *
	 * @group utility
	 * @group array_filter
	 *
	 * @covers \ingot\testing\utility\array_filters::filter_keys()
	 * @covers \ingot\testing\utility\array_filters::match()
	 * @covers \ingot\testing\utility\array_filters::prepare()
	 */
	public function testKeyFilter(){
		$data = [
			'ingot_destination_9' => 12,
			'ingot_destination_42' => 7,
			'hi chris',
			'ingot_' => 94,
			'ingot_destination' => 88,
		];
		$results = \ingot\testing\utility\array_filters::filter_keys( $data, 'ingot_destination_' );
		$this->assertEquals( [
			'ingot_destination_9',
			'ingot_destination_42'
		], $results );
	}

	/**
	 * Test getting results from key substr in matching keys
	 *
	 *
	 * @since 1.1.0
	 *
	 * @group utility
	 * @group array_filter
	 *
	 * @covers \ingot\testing\utility\array_filters::filter_results()
	 * @covers \ingot\testing\utility\array_filters::match()
	 * @covers \ingot\testing\utility\array_filters::prepare()
	 */
	public function testResultsFilter(){
		$data = [
			'ingot_destination_9' => 12,
			'ingot_destination_42' => 7,
			'hi chris',
			'ingot_97' => 94,
			'ingot_destination' => 88,
			'hats_8765' => [ 'batman', 'robin']
		];
		$results = \ingot\testing\utility\array_filters::filter_results( $data, 'ingot_destination_' );
		$this->assertEquals( [
			'9',
			'42'
		], $results );
	}

	/**
	 * Test getting results from matching values

	 * @since 1.1.0
	 *
	 * @group utility
	 * @group array_filter
	 *
	 * @covers \ingot\testing\utility\array_filters::filter_values()
	 * @covers \ingot\testing\utility\array_filters::match()
	 * @covers \ingot\testing\utility\array_filters::prepare()
	 */
	public function testResultsValues(){
		$data = [
			'ingot_destination_19' => 12,
			'ingot_destination_12142' => 17,
			'ingot_destination_4899898',
			'hi chris',
			'ingot_97' => 94,
			'ingot_destination' => 88,
			'hats_8765' => [ 'batman', 'robin']
		];
		$results = \ingot\testing\utility\array_filters::filter_values( $data, 'ingot_destination_' );
		$this->assertEquals( [
			12,
			17
		], $results );
	}

}
