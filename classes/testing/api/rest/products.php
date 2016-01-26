<?php
/**
 * Get products created by an ecommerce plugin
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\api\rest;


use ingot\testing\types;
use ingot\testing\utility\price;

class products extends route {

	/**
	 * Marks what object this is for.
	 *
	 * @since 0.2.1
	 *
	 * @var string
	 */
	protected $what = 'products';

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @since 0.2.0
	 */
	public function register_routes(){
		$namespace = util::get_namespace();
		register_rest_route( $namespace, '/products', array(
			'methods'         => \WP_REST_Server::READABLE,
			'callback'        => array( $this, 'get_items' ),
			'permission_callback' => array( $this, 'get_items_permissions_check' ),
			'args'            => array(
				'plugin' => array(
					'default' => 'edd',
					'sanitize_callback'  => array( $this, 'strip_tags' ),
				)
			)
		));
		register_rest_route( $namespace, '/products/price/(?P<id>[\d]+)', array(
			'methods'         => \WP_REST_Server::READABLE,
			'callback'        => array( $this, 'get_price' ),
			'permission_callback' => array( $this, 'get_items_permissions_check' ),
			'args'            => array(
				'plugin' => array(
					'default' => 'edd',
					'sanitize_callback'  => array( $this, 'strip_tags' ),
				)
			)
		));
		register_rest_route( $namespace, '/products/plugins', array(
			'methods'         => \WP_REST_Server::READABLE,
			'callback'        => array( $this, 'get_plugins' ),
			'permission_callback' => array( $this, 'get_items_permissions_check' ),
			'args'            => array(
				'context' => array(
					'default' => 'select-options'
				)
			)

		));
	}

	/**
	 * Get all products from an ecommerce plugin
	 *
	 * @since 0.2.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_items( $request ) {
		$plugin = $request->get_param( 'plugin' );
		if( ! in_array( $plugin, ingot_accepted_plugins_for_price_tests() ) ) {
			return new \WP_Error( 'ingot-invalid-plugin' );
		}

		if( 'woo' == $plugin ) {
			$products =  $this->get_all_woo();
		}elseif( 'edd' == $plugin ) {
			$products = $this->get_all_edd();
		}else{
			$products = array();
		}

		return rest_ensure_response( $products );

	}

	/**
	 * Get plugins we can use for price tests
	 *
	 * @since 0.2.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_plugins( $request ) {
		if( 'list' == $request->get_param( 'context' ) ){
			$plugins = ingot_ecommerce_plugins_list();
			return ingot_rest_response( $plugins );
		}

		$plugins = [];
		$allowed = ingot_accepted_plugins_for_price_tests( true );
		if( ! empty( $allowed ) ) {
			foreach( $allowed as $value => $label ) {
				if( ingot_check_ecommerce_active( $value ) ) {
					$plugins[] = array(
						'value' => $value,
						'label' => $label
					);
				}
			}
			return ingot_rest_response( $plugins );
		}else{
			return ingot_rest_response( '', 404 );
		}
	}

	/**
	 * Get price of a product
	 *
	 * @since 1.1.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_price( $request ) {
		$price = price::get_price( $request->get_param( 'plugin' ), $request->get_url_params()[ 'id' ] );
		return ingot_rest_response( [ 'price' => $price ] );
	}

	/**
	 * Get all EDD products
	 *
	 * @since 0.2.0
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_all_edd() {
		$post_type = 'download';

		return $this->get_posts( $post_type );


	}

	/**
	 * Get all WooCommerce products
	 *
	 * @since 0.2.0
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_all_woo() {
		$post_type = 'product';

		return $this->get_posts( $post_type );


	}

	/**
	 * Get all posts of a given post type
	 *
	 * @since 0.2.0
	 *
	 * @access protected
	 *
	 * @param $post_type
	 *
	 * @return array
	 */
	protected function get_posts( $post_type ) {
		if( WP_DEBUG ) {
			$cache_key = rand();
		}else{
			$cache_key = md5( __CLASS__ . __METHOD__ . $post_type );
		}

		$maybe_cached = get_transient( $cache_key );
		if( is_array( $maybe_cached ) ) {
			return $maybe_cached;

		}

		$args = array(
			'numberposts' => - 1,
			'post_type'   => $post_type
		);

		$products = array();

		$query = new \WP_Query( $args );
		if ( $query->have_posts() ) {
			foreach ( $query->posts as $post ) {
				$products[] = array(
					'value' => $post->ID,
					'label' => $post->post_title
				);
			}

			set_transient( $cache_key, $products, 599 );
		}

		return $products;

	}

}
