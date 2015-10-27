<?php
/**
 * Setup a price test for Easy Digital Downloads
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\tests\price\plugins;


use ingot\testing\tests\price\price;

class edd  extends price{

	/**
	 * Setup the variable and prices properties
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 */
	protected function set_price() {
		if( edd_has_variable_prices( $this->product->ID ) ) {
			$this->variable = true;
			$this->prices = edd_get_variable_prices( $this->product->ID );
		}else{
			$this->variable = false;
			$this->prices = edd_get_download_price( $this->product->ID );
		}
	}

	/**
	 * Hooks for variable price products
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 */
	protected function variable_price_hooks() {
		add_filter( 'edd_get_variable_prices', array( $this, 'filter_variable_prices' ), 98, 2 );
	}

	/**
	 * Hooks for non-variable price products
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 */
	protected function non_variable_price_hooks() {
		add_filter( 'edd_get_download_price', array( $this, 'filter_price' ), 98, 2 );
	}

	/**
	 * Sanatize prics display
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 * 
	 * @param float|string $price
	 *
	 * @return string
	 */
	protected function sanatize_price( $price ) {
		return edd_sanitize_amount( $price );
	}

}
