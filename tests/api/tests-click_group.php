<?php

/**
 * Generic tests for our routes and endpoints
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class test_click_group extends ingot_rest_test_case {

	/**
	 * Route name
	 *
	 * @since 0.2.0
	 *
	 * @var string
	 */
	protected $route_name = 'test-group';

	/**
	 * Namespaced route name
	 *
	 * @var string
	 */
	protected $namespaced_route;

	/**
	 * Test that we can pass valid params when creating
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\api\rest\test_group::create_item()
	 */
	public function testCreateValidCheckRequest() {
		$request = new \WP_REST_Request( 'POST', $this->namespaced_route );
		$request->set_query_params( array(
			'type' => 'click',
			'click_type' => 'link',
		) );

		$params = (array) $request->get_params();
		$this->assertArrayHasKey( 'type', $params );
		$this->assertEquals( $params[ 'type' ], 'click' );

		$this->assertArrayHasKey( 'click_type', $params );
		$this->assertEquals( $params[ 'click_type' ], 'link' );



	}

	/**
	 * Test that we get the right response code for a valid request when is permitted
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\api\rest\test_group::create_item()
	 */
	public function testCreateValidCheckResponseCode() {
		wp_set_current_user( 1 );
		$request = new \WP_REST_Request( 'POST', $this->namespaced_route );
		$request->set_query_params( array(
			'type' => 'click',
			'click_type' => 'link',
		) );

		$response = $this->server->dispatch( $request );
		//test for correct response code
		$response = rest_ensure_response( $response );
		$this->assertEquals( 200, $response->get_status() );

		$data = (array) $response->get_data();

		$this->assertArrayHasKey( 'ID', $data );
		$this->assertTrue( is_numeric( $data[ 'ID' ] ) );
		$this->assertArrayHasKey( 'type', $data );
		$this->assertEquals( $data[ 'type' ], 'click' );

		$this->assertArrayHasKey( 'click_type', $data );
		$this->assertEquals( $data[ 'click_type' ], 'link' );


	}

	/**
	 * Test that we get a 403 response code for an otherwise valid request when NOT permitted.
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\api\rest\test_group::create_item()
	 */
	public function testCreateUnauthorizedCheckResponseCode() {
		$id = wp_create_user( rand(), md5( rand() ) );
		wp_set_current_user( $id );
		$request = new \WP_REST_Request( 'POST', $this->namespaced_route );
		$request->set_query_params( array(
			'type' => 'click',
			'click_type' => 'link',
		) );

		$response = $this->server->dispatch( $request );
		$response = rest_ensure_response( $response );
		$this->assertEquals( 403, $response->get_status() );



	}

	/**
	 * Test that we get the right response for a valid request when creating is permitted
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\api\rest\test_group::create_item()
	 */
	public function testCreateValidCheckResponse() {
		wp_set_current_user( 1 );
		$request = new \WP_REST_Request( 'POST', $this->namespaced_route );
		$request->set_query_params( array(
			'type' => 'click',
			'click_type' => 'button',
			'text' => 'foo fighter'
		) );

		$response = $this->server->dispatch( $request );

		$data = (array) $response->get_data();

		$id =  $data[ 'ID' ];
		$group = \ingot\testing\crud\group::read( $id );
		$this->assertTrue( is_array( $group ) );
		$this->assertSame( 'click', $group[ 'type' ] );
		$this->assertSame( 'button', $group[ 'click_type' ] );
		$this->assertSame( 'foo fighter', $group[ 'text' ] );


	}

	/**
	 * Test that with invalid click 500 error happens
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\api\rest\test_group::register_routes()
	 * @covers \ingot\testing\api\rest\test_group::validate_click_type()
	 */
	public function testInvalidClickType() {
		wp_set_current_user( 1 );
		$request = new \WP_REST_Request( 'POST', $this->namespaced_route );
		$request->set_query_params( array(
			'type' => 'click',
			'click_type' => 'grohl',
		) );

		$response = $this->server->dispatch( $request );
		$response = rest_ensure_response( $response );
		$this->assertEquals( 500, $response->get_status() );
	}

	/**
	 * Test that with invalid type 500 error happens
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\api\rest\test_group::register_routes()
	 * @covers \ingot\testing\api\rest\test_group::validate_click_type()
	 */
	public function testInvalidType() {
		wp_set_current_user( 1 );
		$request = new \WP_REST_Request( 'POST', $this->namespaced_route );
		$request->set_query_params( array(
			'type' => 'boomerang',
		) );

		$response = $this->server->dispatch( $request );
		$response = rest_ensure_response( $response );
		$this->assertEquals( 500, $response->get_status() );
	}

	/**
	 * Test that we can create tests and have them added properly to group/sequence
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\api\rest\test_group::create_item()
	 */
	public function testCreateWithTests() {
		wp_set_current_user( 1 );
		$request = new \WP_REST_Request( 'POST', $this->namespaced_route );
		$request->set_query_params( array(
			'type' => 'click',
			'click_type' => 'link',
			'tests' => array(
				array(
					'text' => 'test1',
				),
				array(
					'text' => 'test2',

				),
				array(
					'text' => 'test3',
				)
			)
		) );

		$response = $this->server->dispatch( $request );
		$response = rest_ensure_response( $response );
		$this->assertEquals( 200, $response->get_status() );

		$data = (array) $response->get_data();

		$this->assertArrayHasKey( 'ID', $data );
		$group = \ingot\testing\crud\group::read( $data[ 'ID' ] );

		$order = $group[ 'order' ];
		$this->assertTrue( is_array( $order ) );
		foreach( $order as $test_id ) {
			$this->assertTrue( is_array( \ingot\testing\crud\test::read( $test_id ) ) );
		}

		$sequence = \ingot\testing\crud\sequence::read( $group[ 'current_sequence' ] );
		$this->assertTrue( is_array( $sequence ) );
		$this->assertTrue( in_array( $sequence[ 'a_id' ], $order ) );
		$this->assertTrue( in_array( $sequence[ 'b_id' ], $order ) );

	}

	/**
	 * Test updating an item
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\api\rest\test_group::update()
	 */
	public function testUpdate() {
		wp_set_current_user( 1 );

		for( $i = 0; $i <= rand( 3, 5 ); $i++ ) {
			$params = array(
				'text' => rand(),
				'name' => rand(),
			);
			\ingot\testing\crud\test::create( $params );
			$params = array(
				'type' => 'click',
			);

			\ingot\testing\crud\group::create( $params );
		}

		$params = array(
			'text' => rand(),
			'name' => rand(),
		);
		$test_1 = \ingot\testing\crud\test::create( $params );

		for( $i = 0; $i <= rand( 5, 8 ); $i++ ) {
			$params = array(
				'text' => rand(),
				'name' => rand(),
			);
			\ingot\testing\crud\test::create( $params );
		}

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
			'order' => array( $test_1, $test_2, $test_3 ),
			'link' => 'http://bats.com',
			'name' => 'BATCAVE'
		);

		$group_id = \ingot\testing\crud\group::create( $params );
		$group = \ingot\testing\crud\group::read( $group_id );
		$order = $group[ 'order' ];

		$request = new \WP_REST_Request( 'POST', $this->namespaced_route . '/' . $group_id );
		$request->set_query_params( array(
			'type' => 'click',
			'click_type' =>  'button',
			'link' => 'http://farts.com',
		) );

		$response = $this->server->dispatch( $request );
		$response = rest_ensure_response( $response );
		$this->assertEquals( 200, $response->get_status() );

		$data = (array) $response->get_data();
		$this->assertEquals( $data[ 'ID' ], $group_id );
		$group = \ingot\testing\crud\group::read( $group_id );
		$this->assertEquals( 'BATCAVE', $group[ 'name' ] );
		$this->assertEquals( 'http://farts.com', $group[ 'link' ] );
		$this->assertEquals( $order, $group[ 'order' ] );


	}

	/**
	 * Test deleting items
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\api\rest\test_group::delete_item()
	 */
	public function testDelete() {
		wp_set_current_user( 1 );
		$params = array(
			'type' => 'click',
		);

		$group_1_id = \ingot\testing\crud\group::create( $params );

		$params = array(
			'type' => 'click',
		);

		$group_2_id = \ingot\testing\crud\group::create( $params );

		$request = new \WP_REST_Request( 'DELETE', $this->namespaced_route . '/' . $group_2_id );

		$response = $this->server->dispatch( $request );
		$response = rest_ensure_response( $response );
		$this->assertEquals( 200, $response->get_status() );

		$this->assertFalse( \ingot\testing\crud\group::read( $group_2_id ) );
		$this->assertTrue( is_array( \ingot\testing\crud\group::read( $group_1_id ) ) );

		$request = new \WP_REST_Request( 'DELETE', $this->namespaced_route . '/' . $group_1_id );

		$response = $this->server->dispatch( $request );
		$response = rest_ensure_response( $response );
		$this->assertEquals( 200, $response->get_status() );

		$this->assertFalse( \ingot\testing\crud\group::read( $group_1_id ) );


	}

	/**
	 * Test that our extra route works
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\api\rest\test_group::register_more_routes()
	 */
	public function testTestsByGroupRouteExists() {
		$routes = $this->server->get_routes();
		$expected = $this->namespaced_route  . '/(?P<id>[\d]+)/tests';
		$this->assertArrayHasKey( $expected, $routes, get_class( $this ) );
	}

	/**
	 * Test getting tests by group ID
	 *
	 * @since 0.2.0
	 *
	 * @covers \ingot\testing\api\rest\test_group::get_tests_by_group()
	 */
	public function testGetTestsByGroup() {
		for( $i = 0; $i <= rand( 3, 5 ); $i++ ) {
			$params = array(
				'text' => rand(),
				'name' => rand(),
			);
			\ingot\testing\crud\test::create( $params );
			$params = array(
				'type' => 'click',
			);

			\ingot\testing\crud\group::create( $params );
		}

		$params = array(
			'text' => rand(),
			'name' => rand(),
		);
		$test_1 = \ingot\testing\crud\test::create( $params );

		for( $i = 0; $i <= rand( 5, 8 ); $i++ ) {
			$params = array(
				'text' => rand(),
				'name' => rand(),
			);
			\ingot\testing\crud\test::create( $params );
		}

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
			'click_type' => 'link',
			'order' => array( $test_1, $test_2, $test_3 ),

		);
		$group_id = \ingot\testing\crud\group::create( $params );
		$group = \ingot\testing\crud\group::read( $group_id );
		$order = $group[ 'order' ];
		wp_set_current_user( 1 );


		$request = new \WP_REST_Request( 'GET', $this->namespaced_route  . '/' . (int) $group_id . '/tests'  );
		$response = $this->server->dispatch( $request );
		$response = rest_ensure_response( $response );
		$this->assertEquals( 200, $response->get_status() );
		$data = (array) $response->get_data();
		$this->assertFalse( empty( $data ) );
		$ids = wp_list_pluck( $data, 'ID' );
		$this->assertEquals( array_values( $ids ), $order );


	}


}
