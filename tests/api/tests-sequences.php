<?php

/**
 * Test sequence route
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class tests_sequences extends ingot_rest_test_case {


	/**
	 * Route name
	 *
	 * @since 0.2.0
	 *
	 * @var string
	 */
	protected $route_name = 'sequence';


	public function testGetByClickGroupID() {
		wp_set_current_user( 1 );
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
		$s1_id = $group[ 'current_sequence' ];
		$s2_id = \ingot\testing\tests\sequence_progression::make_next_sequence( $group_id, $test_1, $group );


		$request = new \WP_REST_Request( 'GET', $this->namespaced_route );
		$request->set_query_params( array(
			'group_ID' => $group_id,
		) );
		$response = $this->server->dispatch( $request );
		$response = rest_ensure_response( $response );
		$this->assertEquals( 200, $response->get_status() );
		$data = (array) $response->get_data();
		$this->assertNotEmpty( $data );
		$ids = wp_list_pluck( $data, 'ID' );
		$this->assertTrue( in_array( $s1_id, $ids ) );
		$this->assertTrue( in_array( $s2_id, $ids ) );

	}
	
}
