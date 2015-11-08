<?php

/**
 * Test click test route
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class tests_click_tests extends ingot_rest_test_case {


	/**
	 * Route name
	 *
	 * @since 0.2.0
	 *
	 * @var string
	 */
	protected $route_name = 'test';

	/**
	 * Test click nonce
	 *
	 * @since 0.2.0
	 *
	 * @covers ingot\ui\verify_click_nonce()
	 * @covers ingot\ui\click_nonce_action()
	 * @covers ingot\ui\click_nonce()
	 */
	public function testClickNonce() {
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
			'order' => array( $test_1, $test_2 ),

		);
		$group_id = \ingot\testing\crud\group::create( $params );
		$group = \ingot\testing\crud\group::read( $group_id );
		$sequence_id =  $group[ 'current_sequence' ];
		$nonce = \ingot\ui\util::click_nonce( $test_1, $sequence_id, $group_id );

		$verify = \ingot\ui\util::verify_click_nonce(
			$nonce,
			$test_1,
			$sequence_id,
			$group_id
		);
		$this->assertTrue( $verify );

		$dont_verify = \ingot\ui\util::verify_click_nonce(
			$nonce,
			$test_3,
			$sequence_id,
			$group_id
		);
		$this->assertFalse( $dont_verify );

	}

	/**
	 * Test that we can increase victory properly
	 *
	 * @since 0.2.0
	 *
	 * @covers ingot\testing\api\rest\register_click()
	 * @covers ingot\testing\api\rest\verify_test_nonce()
	 */
	public function testClick() {
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
		$sequence_id =  $group[ 'current_sequence' ];
		$nonce = \ingot\ui\util::click_nonce( $test_1, $sequence_id, $group_id );

		$request = new \WP_REST_Request( 'POST', $this->namespaced_route . '/' . $test_1 . '/click'   );
		$request->set_query_params( array(
			'id' => $test_1,
			'sequence' => $sequence_id,
			'click_nonce' => $nonce
		) );

		$response = $this->server->dispatch( $request );
		$response = rest_ensure_response( $response );
		$this->assertEquals( 200, $response->get_status() );

		$sequence = \ingot\testing\crud\sequence::read( $sequence_id );
		$this->assertEquals( 1, $sequence[ 'a_win' ]  );
		$this->assertEquals( 0, $sequence[ 'b_win' ]  );

	}

}
