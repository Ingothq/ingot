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
	public static function setup_cookies(){
		$variants = [];
		$groups = self::get_destination_tests();
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
	 */
	public static function set_tracking(){
		$groups = self::get_destination_tests();
		if( ! empty( $groups ) ){
			foreach( $groups as $group_id ){
				$group = group::read( $group_id );
				if( group::valid( $group ) ){
					if( destination::is_tagline( $group ) ){
						if( ! self::$tagline ) {
							$variant_id = self::get_test( $group_id );
							$variant = $variant = variant::read( $variant_id );
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

			new \ingot\testing\tests\click\destination\hooks( $groups );

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

}
