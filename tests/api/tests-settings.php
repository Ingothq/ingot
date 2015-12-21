<?php

/**
 * Tests settings API Route
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class tests_settings extends ingot_rest_test_case {

	/**
	 * Route name
	 *
	 * @since 0.2.0
	 *
	 * @var string
	 */
	protected $route_name = 'settings';

	public function tearDown() {
		parent::tearDown();
		foreach( array(
			'click_tracking',
			'anon_tracking',
			'license_code'
		) as $setting ) {
			$saved = \ingot\testing\crud\settings::write( $setting, 0 );
		}
	}

	/**
	 *
	 * @group rest
	 * @group settings_rest
	 */
	public function testReadSettings() {
		wp_set_current_user( 1 );
		$expected = array(
			'click_tracking' => 1,
			'anon_tracking' => 1,
			'license_code' => 'batman'
		);

		foreach( $expected as $setting => $value ){
			\ingot\testing\crud\settings::write( $setting, $value );
		}

		$request = new \WP_REST_Request( 'GET', $this->namespaced_route );


		$response = $this->server->dispatch( $request );
		$response = rest_ensure_response( $response );
		$this->assertEquals( 200, $response->get_status() );
		$data = (array) $response->get_data();
		foreach( $expected as $setting => $value ){
			$this->assertArrayHasKey( $setting, $data );
			if ( 'license_code' !== $setting ) {
				$this->assertEquals( $value, $data[ $setting ] );
			}
		}


	}

	public function testUpdate() {
		wp_set_current_user( 1 );
		$expected = array(
			'click_tracking' => 0,
			'anon_tracking' => 0,
			'license_code' => 'batman'
		);

		foreach( $expected as $setting => $value ){
			\ingot\testing\crud\settings::write( $setting, $value );
		}

		$request = new \WP_REST_Request( 'POST', $this->namespaced_route );
		$request->set_query_params( array(
			'click_tracking' => 'true',
			'anon_tracking' => 'true',
			'license_code' => 'allthespidermen'
		) );

		$response = $this->server->dispatch( $request );
		$response = rest_ensure_response( $response );
		$this->assertEquals( 200, $response->get_status() );
		$data = (array) $response->get_data();
		foreach( $expected as $setting => $value ){
			$this->assertArrayHasKey( $setting, $data );
			if( 'license_code' != $setting ) {
				$data[ $setting ] = intval( $data[ $setting ] );
				$this->assertEquals( $value, $data[ $setting ], $setting );
			}

		}


	}


}
