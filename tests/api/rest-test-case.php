<?php
/**
 * Test case for REST API tests
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */


abstract class ingot_rest_test_case extends \WP_UnitTestCase {

	/**
	 * In subclass, add route here
	 *
	 * @var string
	 */
	protected $route_name = '';

	/**
	 * Test REST Server
	 *
	 * @var WP_REST_Server
	 */
	protected $server;

	/**
	 * Namespaced route name
	 *
	 * @var string
	 */
	protected $namespaced_route = '';


	public function setUp() {
		parent::setUp();

		/** @var WP_REST_Server $wp_rest_server */
		global $wp_rest_server;
		$this->server = $wp_rest_server = new \WP_REST_Server;
		do_action( 'rest_api_init' );


		$ingot = ingot\testing\ingot::instance();
		$ingot->boot_rest_api();
		$this->setNamespacedRoute();




	}

	public function tearDown() {
		parent::tearDown();

	}

	private function setNamespacedRoute() {
		$this->namespaced_route = '/' . untrailingslashit( \ingot\testing\api\rest\util::get_route( $this->route_name ) );
	}

	/**
	 * Tests designed to detect improperly setup subclass
	 */
	public function testSetUp() {
		$this->assertNotSame( $this->route_name, '' );
		$this->assertNotSame( $this->namespaced_route, __return_null() );
	}


	/**
	 * Test that this route is registered properly
	 *
	 * @since 0.2.0
	 *
	 *
	 */
	public function test_register_route() {
		$routes = $this->server->get_routes();

		$this->assertArrayHasKey( '/' . \ingot\testing\api\rest\util::get_namespace() . '/' . $this->route_name, $routes );
		$this->assertArrayHasKey( $this->namespaced_route, $routes );

	}

	public function test_endpoints() {
		$the_route = '/' . \ingot\testing\api\rest\util::get_namespace() . '/' . $this->route_name;
		$routes = $this->server->get_routes();
		foreach( $routes as $route => $route_config ) {
			if( 0 === strpos( $the_route, $route ) ) {
				$this->assertTrue( is_array( $route_config ) );
				foreach( $route_config as $i => $endpoint ) {
					$this->assertArrayHasKey( 'callback', $endpoint );
					$this->assertArrayHasKey( 0, $endpoint[ 'callback' ] );
					$this->assertArrayHasKey( 1, $endpoint[ 'callback' ] );
					$this->assertTrue( is_callable( array( $endpoint[ 'callback' ][0], $endpoint[ 'callback' ][1] ) ) );

				}

			}
		}

	}


}
