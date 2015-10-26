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


use ingot\testing\crud\price_group;
use ingot\testing\crud\price_test;
use ingot\testing\utility\helpers;
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

	/**
	 * Get the price test a/b field via ajax
	 *
	 * @uses "wp_ajax_get_price_ab_field"
	 *
	 * @since 0.0.9
	 */
	public static function get_price_ab_field() {
		if( isset( $_GET[ '_nonce' ], $_GET[ 'plugin' ] ) && wp_verify_nonce( $_GET[ '_nonce' ] ) ){
			echo include_once( INGOT_UI_PARTIALS_DIR . 'price-test-a-b.php' );
			status_header( 200 );
			die();
		}
		wp_send_json_error();
	}

	/**
	 * Get price tests by group
	 *
	 * @uses "wp_ajax_get_price_tests_by_group"
	 *
	 * @since 0.0.9
	 */
	public static function get_price_tests_by_group( ){
		if( isset( $_GET[ '_nonce' ], $_GET[ 'group_id' ] ) && wp_verify_nonce( $_GET[ '_nonce' ] ) ){
			$options = self::price_test_in_group( helpers::v( 'group_id', $_GET, 0, 'absint' ) );
			if( ! empty( $options ) ){
				status_header( 200 );
				wp_send_json_success( $options );
				die();
			}
		}
		status_header( 500 );
		die();

	}

	protected static function price_test_in_group( $group_id ){
		$group = price_group::read( $group_id );
		$tests = price_test::get_items( array( 'ids' => $group[ 'test_order' ] ) );
		$options = array();
		if( ! empty( $tests ) ){
			foreach( $tests as $test ){
				$options[ $test[ 'ID' ] ] = $test[ 'group_name' ];
			}
		}

		return $options;
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
