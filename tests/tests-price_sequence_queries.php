<?php

/**
 * Check that we can run queries for price test sequences properly
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class tests_price_sequence_queries extends \WP_UnitTestCase{

	public function tearDown(){
		parent::tearDown();
		\ingot\testing\crud\price_group::delete( 'all' );
		\ingot\testing\crud\price_test::delete( 'all' );
		\ingot\testing\crud\sequence::delete( 'all' );
	}

	/**
	 * Test proper deactivate of price test groups
	 *
	 * @since 0.0.9
	 *
	 * @cover \ingot\testing\crud\price_group::deactivate
	 */
	public function testDeactive() {
		$group = $this->make_group( 42 );
		$group_id = $group[ 'group' ];
		$group = \ingot\testing\crud\price_group::read( $group_id );
		$sequence_id =  $group[ 'current_sequence' ];
		\ingot\testing\crud\price_group::deactivate( $group_id );
		$this->assertNotSame( 0, $group[ 'current_sequence' ] );

		$sequence = \ingot\testing\crud\sequence::read( $sequence_id );
		$this->assertEquals( 1, (int) $sequence[ 'completed' ] );

		$group = \ingot\testing\crud\price_group::read( $group_id );
		$this->assertSame( '0', $group[ 'current_sequence' ] );

	}

	/**
	 * Test getting active items properly
	 *
	 * @since 0.0.9
	 *
	 * @covers \ingot\testing\crud\sequence::get_items()
	 * @covers \ingot\testing\crud\sequence::get_for_price_tests()
	 * @covers 	\ingot\testing\crud\price_group::get_items()
	 */
	public function testGetActive() {

		//make and deactivate a few first
		for ( $i = 0; $i <= rand( 2, 5 ); $i ++ ) {
			$group = $this->make_group( $i );
			\ingot\testing\crud\price_group::deactivate( $group[ 'group' ] );
		}

		for ( $i = 0; $i <= 4; $i ++ ) {
			$id = $this->make_group( $i );
			$groups[ $i ] = $id;
		}

		$inactive_group_data = $this->make_group( 99 );
		$inactive_group_id = $inactive_group_data[ 'group' ];
		$inactive_group = \ingot\testing\crud\price_group::read( $inactive_group_id );
		$inactive_sequence_id = $inactive_group[ 'current_sequence' ];

		\ingot\testing\crud\price_group::deactivate( $inactive_group_id );
		$inactive_sequence = \ingot\testing\crud\sequence::read( $inactive_sequence_id );
		$this->assertEquals( 1, (int) $inactive_sequence[ 'completed'] );

		//make and deactivate a few more
		for ( $i = 0; $i <= rand( 2, 5 ); $i ++ ) {
			$group = $this->make_group( $i );
			\ingot\testing\crud\price_group::deactivate( $group[ 'group' ] );
		}

		$the_sequences = $the_groups = array();
		foreach( $groups as $i => $group ){
			$group_id = $group[ 'group' ];
			$_group = \ingot\testing\crud\price_group::read( $group_id );
			$this->assertTrue( is_array( $_group ) );
			$the_groups[ $_group[ 'ID' ] ] = $_group;
			$_sequence = \ingot\testing\crud\sequence::read( $_group[ 'current_sequence' ] );
			$this->assertTrue( is_array( $_sequence  ) );
			$the_sequences[ $_sequence[ 'ID'] ] = $_sequence;
		}

		$args = array(
			'price_test' => true,
			'current' => true,
			'limit' => -1
		);

		$active_sequences = \ingot\testing\crud\sequence::get_items( $args );
		$this->assertTrue( is_array( $active_sequences ) );
		$this->assertSame( count( $active_sequences ), count( $the_sequences ) );
		$active_sequences_ids = wp_list_pluck( $active_sequences, 'ID' );

		foreach( $active_sequences_ids as $id ) {
			$this->assertArrayHasKey( $id, $the_sequences );
		}

		$this->assertArrayNotHasKey( $inactive_sequence_id, $active_sequences_ids );

	}

	public function testGetTestsByGroup(){
		for ( $i = 0; $i <= 4; $i ++ ) {
			$id = $this->make_group( $i );
			$groups[ $i ] = $id;
		}

		$x = rand( 0, 4 );
		$group_id = $groups[ $x ][ 'group' ];
		$group = \ingot\testing\crud\price_group::read( $group_id );
		$expected_test_ids = $group[ 'test_order' ];
		$this->assertTrue( is_array( $expected_test_ids  ) );
		$this->assertFalse( empty( $expected_test_ids  ) );

		$tests_from_db = \ingot\testing\crud\price_test::get_items( array( 'ids' => $group[ 'test_order' ] ) );
		$this->assertTrue( is_array( $tests_from_db  ) );
		$this->assertFalse( empty( $tests_from_db  ) );

		$this->assertSame( count( $expected_test_ids ), count( $tests_from_db ) );
		var_dump( $expected_test_ids );
		var_dump( $tests_from_db );
		foreach( $tests_from_db as $test ) {
			$this->assertArrayHasKey( 'ID', $test );
			$this->assertTrue( in_array( $test[ 'ID' ],  $expected_test_ids ) );

		}



	}

	/**
	 * Make test group
	 *
	 * @since 0.0.9
	 *
	 * @param int $product_id
	 *
	 * @return array
	 */
	protected function make_group( $product_id ) {
		$params     = array(
			'product_ID' => $product_id,
			'default'    => array(
				'a' => rand( -0.9, 0.9 ),
				'b' => rand( -0.9, 0.9 )
			)

		);

		for ( $i = 0; $i <= 1; $i ++ ) {
			$id = \ingot\testing\crud\price_test::create( $params );
			$tests[ $i ] = $id;
			$params[ 'default' ][ 'a' ] = rand( -0.9, 0.9 );
			$params[ 'default' ][ 'b' ] = rand( -0.9, 0.9 );
		}

		$params = array(
			'type'       => 'price',
			'plugin'     => 'edd',
			'group_name' => rand(),
			'test_order' => $tests,
			'initial'    => '42',
			'threshold'  => '84',
			'product_ID' => $product_id

		);

		$group_id = \ingot\testing\crud\price_group::create( $params );

		return array(
			'group' => $group_id,
			'tests' => $tests
		);

	}

}
