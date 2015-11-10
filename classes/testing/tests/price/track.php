<?php
/**
 * Track sales of products in a price test
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\tests\price;


use ingot\testing\cookies\cache;
use ingot\testing\tests\flow;
use ingot\testing\utility\price;

class track {

	/**
	 * Track an EDD sale
	 *
	 * @since 0.0.0
	 *
	 * @uses "edd_complete_purchase"
	 *
	 * @param $payment_id
	 */
	public static function track_edd_sale( $payment_id ) {
		$payment_meta = edd_get_payment_meta( $payment_id );
		$products = self::get_edd_downlaods( $payment_meta );
		self::check_for_winners( $products, 'edd' );
	}

	/**
	 * Check products in a sale for any we are testing
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @param array $products
	 * @param string $plugin
	 */
	protected static function check_for_winners( $products, $plugin ) {

		$winners = array();

		$testing = price::current();

		if ( ! empty( $testing ) && isset( $testing[ $plugin ])) {
			foreach ( $products as $product ) {
				if ( isset( $testing[ $plugin ][ $product ] ) ) {
					$winners[] = $testing[ $plugin ][ $product ];
				}
			}

			self::record_victories( $winners );
		}

	}

	/**
	 * Track winning tests
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @param array $winners
	 */
	protected static function record_victories( $winners ){
		if( ! empty( $winners ) ) {
			foreach( $winners as $winner ){
				flow::increase_victory( $winner[ 'test_ID' ], $winner[ 'sequence_ID' ] );
			}
		}
	}

	/**
	 * Get products IDs from EDD payment meta
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @param array $payment_meta
	 *
	 * @return array
	 */
	protected static function get_edd_downlaods( $payment_meta ) {
		$downloads = array();
		if( is_array( $payment_meta ) && isset( $payment_meta[ 'downloads' ] ) && ! empty( $payment_meta[ 'downloads' ] ) ) {
			$downloads = wp_list_pluck( $payment_meta[ 'downloads' ], 'id' );
		}

		return $downloads;
	}

}
