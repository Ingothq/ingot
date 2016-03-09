<?php
/**
 * Set up destination tracking
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\testing\tests\click\destination;

use ingot\testing\bandit\content;
use ingot\testing\crud\group;
use ingot\testing\crud\variant;
use ingot\testing\utility\destination;
use ingot\testing\utility\helpers;

class init {

	protected static $group_ids;

	/**
	 * Holds an array of tests that have been queried for using self::get_test()
	 *
	 * Handles situation where variant has been selected, but is not yet in cookie.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected static $tests = [];

	/**
	 * Get all destination tests
	 *
	 * @since 1.1.0
	 *
	 * @param bool $ids Optional. If true, the ID of found groups is returned. If false, full group configs are returned
	 *
	 * @return array|null|object
	 */
	public static function get_destination_tests( $ids = true ){
		if( $ids && ! empty( self::$group_ids ) ) {
			return self::$group_ids;
		}

		global $wpdb;
		if( $ids ) {
			$select = '`ID`';
		}else{
			$select = '*';
		}

		$tablename = group::get_table_name();
		$sql = sprintf( "SELECT %s FROM `%s` WHERE `type` = 'click' AND `sub_type` = 'destination'", $select, $tablename );
		$results = $wpdb->get_results( $sql, ARRAY_A );
		if( ! empty( $results ) ){
			if ( $ids ) {
				self::$group_ids = $results;
				$results = wp_list_pluck( $results, 'ID' );
			} else {
				$results = group::bulk_results( $results );
			}

		}

		return $results;
	}


	/**
	 * Setup the cookies for destination tests
	 *
	 * @since 1.1.0
	 *
	 * @return array Array of variant IDs put in cookies, keyed by group ID.
	 */
	public static function setup_cookies( $groups = [] ){
		$variants = [];
		if ( ! empty( $groups ) ) {
			$groups = self::get_destination_tests();
		}
		if( ! empty( $groups ) ){
			foreach( $groups as $group_id  ){
				$variants[ $group_id ] = self::get_test( $group_id );
			}

		}

		return $variants;

	}

	/**
	 * Holds tagline, if in use
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected static $tagline;

	/**
	 * Setup tracking hooks
	 *
	 * @since 1.1.0
	 *
	 * @return array Groups being tracked
	 */
	public static function set_tracking(){
		$groups = self::get_destination_tests();
		if ( ! empty( $groups ) ) {
			self::clear_invalid( $groups );
		}
		if( ! empty( $groups ) ){
			foreach( $groups as $group_id ){
				$group = group::read( $group_id );
				if( group::valid( $group ) ){
					if( destination::is_tagline( $group ) ){
						if( ! self::$tagline ) {
							$variant_id = self::get_test( $group_id );
							$variant = variant::read( $variant_id );
							if ( variant::valid( $variant ) ) {
								self::$tagline = $variant[ 'content' ];
							}

						}

						if( is_string( self::$tagline ) ) {

							add_filter( 'bloginfo', function( $output, $show ){
								if ( 'description' == $show ) {
									return self::$tagline;
								}

								return $output;
							}, 24, 2 );
						}

					}

				}
			}

			if ( ! empty( $groups ) ) {
				 \ingot\testing\tests\click\destination\hooks::get_instance( $groups );
			}

			return $groups;

		}


	}

	/**
	 * @TODO REMOVE?
	 *
	 * @since 1.1.0
	 *
	 * @param $destination
	 * @param $group
	 *
	 * @return mixed|void
	 */
	public static function get_hook( $destination, $group ){
		switch( $destination ) {
			case 'hook' :
				$hook = helpers::v( 'hook', $group[ 'meta' ], false );
				break;
			case 'cart_edd' :
				$hook = 'edd_post_add_to_cart';
				break;
			case 'sale_edd' :
				$hook = 'edd_complete_purchase';
				break;
			case 'cart_woo' :
				$hook = 'woocommerce_add_to_cart';
				break;
			case 'sale_woo' :
				$hook = 'woocommerce_payment_complete_order_status';
				break;
			default :
				$hook = null;
				break;
		}

		return apply_filters( 'ingot_destination_tracking_hook', $hook, $destination, $group );

	}

	/**
	 * Get variant ID, by group ID.
	 *
	 * If no variant chosen, makes selection
	 *
	 * @since 1.1.0
	 *
	 * @param int $group_id Group ID to get variant for
	 *
	 * @return int
	 */
	public static function get_test( $group_id ) {
		if ( ! isset( self::$tests[ $group_id ]) ) {
			if ( ! cookie::get_cookie( $group_id ) ) {
				$group = group::read( $group_id );
				if( group::valid( $group ) && ! empty( $group[ 'variants' ] ) ){
					$bandit  = new content( $group_id );
					$variant = $bandit->choose();
					if( is_a( $variant, 'MaBandit\Lever' ) ){
						/** @var \MaBandit\Lever $variant */
						$variant = $variant->getValue();
					}

					if ( is_numeric( $variant ) ) {
						cookie::set_cookie( $group_id, $variant );
					}
				}else{
					return false;
				}

			} else {
				$variant = cookie::get_cookie( $group_id );
			}

			self::$tests[ $group_id ] = $variant;
		}

		return self::$tests[ $group_id ];

	}

	/**
	 * Clear out invalid destination cookies
	 *
	 * @since 1.1.0
	 *
	 * @param $groups
	 */
	public static function clear_invalid( array $groups ){
		$cookies = cookie::get_all_cookies();

		$clear = [];
		if( empty( $groups ) ){
			$clear = $groups;
		}elseif( ! empty( $cookies ) && ! empty( $groups ) ){
			foreach( $cookies as $group_id ){
				if( ! in_array( $group_id, $groups ) || ! group::exists( $group_id ) ){
					cookie::clear_cookie( $group_id );
				}

			}

		}

		if( ! empty( $clear ) ){
			foreach( $clear as $group_id ){
				cookie::clear_cookie( $group_id );
			}

		}

	}

	/**
	 * Get current tests
	 *
	 * @since 1.1.1
	 *
	 * @return array group_ID => variant_ID
	 */
	public static function get_tests(){
		if( empty( self::$group_ids ) ){
			self::get_destination_tests( true );
		}
		//make sure self::$tests is set right
		foreach( self::$group_ids as $group_id ){
			if( ! isset( self::$tests[ $group_id[ 'ID' ] ] ) ) {
				self::get_test( $group_id[ 'ID' ] );
			}
		}

		return self::$tests;
	}

}
