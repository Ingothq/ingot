<?php
/**
 * Setup WooCommerce Price Testing
 *
 * @package   Ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\testing\tests\price\plugins;
use ingot\testing\tests\price\price;

class woo extends price {

	/**
	 * Hooks for variable price products
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 */
	protected function variable_price_hooks() {
		//@TODO make sure this is right
		add_filter( 'woocommerce_get_price', array( $this, 'prepare_filter_variable_prices' ), 98, 2 );
	}

	/**
	 * Hooks for non-variable price products
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 */
	protected function non_variable_price_hooks() {
		add_filter( 'woocommerce_get_price', array( $this, 'prepare_filter_price' ), 98, 2 );
	}

	/**
	 * Prepare args for parent filter_price method
	 *
	 * @since 1.1.0
	 *
	 * @param string $price Formatted price
	 * @param \WC_Product $object Product object
	 *
	 * @return string
	 */
	protected function prepare_filter_price( $price, $object ){

		return parent::filter_price( $price, $object->id );

	}

	/**
	 * Prepare args for parent filter_variable_prices method
	 *
	 * @since 1.1.0
	 *
	 * @param string $price Formatted price
	 * @param \WC_Product $object Product object
	 *
	 * @return string
	 */
	protected function prepare_filter_variable_prices( $price, $object ){

		return parent::filter_variable_prices( $price, $object->id );

	}

	/**
	 * Hook for tracking purchases
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 */
	protected function purchase_hook(){
		add_filter( 'woocommerce_payment_successful_result', array( $this, 'track_woo_sale' ), 50, 2 );
	}

	/**
	 * Track Woo sales
	 *
	 * @since 1.0.0
	 *
	 * @param array $result
	 * @param int $order_id
	 *
	 * @return array
	 */
	public function track_woo_sale( $result, $order_id ){
		$order = new \WC_Order( $order_id );
		$items = $order->get_items();
		if( ! empty( $items ) ) {
			$this->check_for_winners( array_keys( $items ) );
		}

		return $result;

	}

}
