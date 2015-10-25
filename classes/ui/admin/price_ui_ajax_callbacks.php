<?php
/**
 * Helper class for getting product IDs via AJAX
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui\admin;


use ingot\ui\admin;
use ingot\ui\util;

class price_ui_ajax_callbacks {

	/**
	 * @uses "wp_ajax_get_all_products" action
	 */
	public static function get_all_products() {
		if( isset( $_GET[ '_nonce' ], $_GET[ 'plugin' ] ) && wp_verify_nonce( $_GET[ '_nonce' ] ) ){
			if( in_array( $_GET[ 'plugin' ], ingot_accepted_plugins_for_price_tests() ) ){
				if( 'edd' == $_GET[ 'plugin' ] ){
					$products = self::get_all_edd();
				}elseif( 'woo' == $_GET[ 'plugin' ] ){
					$products = self::get_all_woo();
				}else{
					wp_send_json_error();
				}

				status_header( 200 );
				echo wp_json_encode( $products );
				die();

			}
		}

		wp_send_json_error();


	}

	public static function get_all_edd() {
		$post_type = 'download';

		return self::get_posts( $post_type );


	}

	public static function get_all_woo() {
		$post_type = 'product';

		return self::get_posts( $post_type );


	}

	/**
	 * @param $post_type
	 *
	 * @return array
	 */
	protected static function get_posts( $post_type ) {
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
