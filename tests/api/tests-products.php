<?php
/**
 * Unit tests for /products
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */
class products_api extends ingot_rest_test_case {

	/**
	 * In subclass, add route here
	 *
	 * @var string
	 */
	protected $route_name = 'products';

	/**
	 * Test GET /products for EDD
	 *
	 * @since 1.1.0
	 *
	 * @group products_api
	 * @group edd
	 * @group price
	 *
	 * @covers ingot\testing\api\rest/products::get_items()
	 */
	public function testGetEDDItems(){
		wp_set_current_user( 1 );
		$product_1 = ingot_test_data_price::edd_create_simple_download(10);
		$product_2 = ingot_test_data_price::edd_create_simple_download(10);
		$not_edd = get_post( wp_insert_post( [
			'post_type' => 'page',
			'post_title' => 'hats'
		]));

		$request = new \WP_REST_Request( 'GET', $this->namespaced_route );
		$request->set_query_params( array(
			'plugin' => 'edd'
		) );
		$response = $this->server->dispatch( $request );
		$response = rest_ensure_response( $response );
		$this->assertEquals( 200, $response->get_status() );
		$data = (array) $response->get_data();
		$this->assertSame( 2, count( $data ) );

		$ids = [ $product_1->ID, $product_2->ID ];
		$titles = [ $product_1->post_title, $product_2->post_title ];
		foreach( $data as $product ){
			$this->assertArrayHasKey( 'value', $product );
			$this->assertArrayHasKey(  'label', $product );
			$this->assertTrue( in_array( $product[ 'value' ], $ids ) );
			$this->assertTrue( in_array( $product[ 'label' ], $titles ) );
		}

	}

	/**
	 * Test GET /products/plugins
	 *
	 * @since 1.1.0
	 *
	 * @group products_api
	 * @group edd
	 * @group woo
	 * @group price
	 *
	 * @covers ingot\testing\api\rest/products::get_plugins()
	 */
	public function testGetPlugins(){
		wp_set_current_user( 1 );

		$request = new \WP_REST_Request( 'GET', $this->namespaced_route  . '/plugins' );
		$request->set_query_params( array(
			'plugin' => 'edd'
		) );
		$response = $this->server->dispatch( $request );
		$response = rest_ensure_response( $response );
		$this->assertEquals( 200, $response->get_status() );
		$data = (array) $response->get_data();
		$this->assertSame( 1, count( $data ) );
		foreach( $data as $plugin ) {
			$this->assertArrayHasKey( 'value', $plugin );
			$this->assertArrayHasKey( 'label', $plugin );
		}

	}
}
