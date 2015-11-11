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


class products extends route {


	public function register_routes(){
		$namespace = util::get_namespace();
		register_rest_route( $namespace, '/products', array(
			'methods'         => \WP_REST_Server::READABLE,
			'callback'        => array( $this, 'get_items' ),
			'permission_callback' => array( $this, 'get_items_permissions_check' ),
			'args'            => array(
				'plugin' => array(
					'default' => 'edd',
					'sanitize_callback'  => 'strip_tags',
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

		rest_ensure_response( $products );

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

		$posts = array();

		$query = new \WP_Query( $args );
		if ( $query->have_posts() ) {
			foreach ( $query->posts as $post ) {
				$posts[ $post->ID ] = $post->post_title;
			}

			set_transient( $cache_key, $posts, 599 );
		}

		return $posts;

	}

}
