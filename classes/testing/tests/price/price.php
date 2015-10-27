<?php
/**
 * Base class for setting up a price test
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\tests\price;


use ingot\testing\crud\price_test;
use ingot\testing\utility\helpers;

abstract class price {

	/**
	 * @var array
	 */
	protected $test;

	/**
	 * @var \WP_Post
	 */
	protected $product;

	/**
	 * @var array
	 */
	protected $prices;

	/**
	 * @var bool
	 */
	protected $variable;

	/**
	 * @var string
	 */
	protected $a_or_b;

	/**
	 * Set up object
	 *
	 * @since 0.0.9
	 *
	 * @param int|array $test Test ID or config. (Currently must be ID)
	 * @param string $a_or_b a|b A or B as string not bool.
	 */
	public function __construct( $test, $a_or_b ) {
		$this->a_or_b = $a_or_b;
		$this->set_test( $test );
		if( is_array( $this->test ) ){
			$this->product = get_post( $this->test['product_ID'] );
			if ( is_object( $this->product ) ) {
				$this->set_price();
				$this->add_hooks();
			}
		}
	}

	/**
	 * Callback for the price hook for non-variable prices
	 *
	 * @since 0.0.9
	 *
	 * @param $price
	 * @param $id
	 *
	 * @return string
	 */
	public function filter_price( $price, $id ) {
		if( $id == $this->product->ID ) {
			$price = $this->apply_price( $price );
		}

		return $price;
	}

	/**
	 * Callback for the price hook for variable price products
	 *
	 * @since 0.0.9
	 *
	 * @param $prices
	 * @param $id
	 *
	 * @return mixed
	 */
	public function filter_variable_prices( $prices, $id ) {
		if( $id == $this->product->ID ) {
			$prices = $this->apply_variable_price( $prices );
		}

		return $prices;


	}

	/**
	 * Change variable prices based on test
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @param $prices
	 *
	 * @return mixed
	 */
	protected function apply_variable_price( $prices ) {

		foreach( $prices as $i => $price ) {
			if ( ! isset( $this->test[ 'test' ][ $i ]) ) {
				$prices[ $i ][ 'amount'] = $this->apply_price( $price );
			} else {
				$prices[ $i ][ 'amount'] = $this->apply_price( $price, $i  );
			}
		}


		return $prices;

	}

	/**
	 * Change a price based on test
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @param $price
	 * @param string $index
	 *
	 * @return string
	 */
	protected function apply_price( $price, $index = 'default' ) {
		$p = $this->test[ 'test' ][ $index ][ $this->a_or_b ];

		$price = $this->change_price( $price, $p );

		return $price;

	}



	/**
	 * Change price based on percentage
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @param $price
	 * @param float $p Percentage as float
	 *
	 * @return string
	 */
	protected function change_price( $price, $p ) {
		$price = ( (float) $price * (float) $p ) + (float ) $price;

		return $this->sanatize_price( $price );
	}


	/**
	 * Sanatize price for output
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @param $price
	 *
	 * @return string
	 */
	protected function sanatize_price( $price ) {
		return ingot_sanitize_amount( $price );
	}

	/**
	 * Add our hooks
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @since 0.0.9
	 *
	 */
	protected function add_hooks() {
		if ( $this->variable ) {
			$this->variable_price_hooks();
		} else {
			$this->non_variable_price_hooks();
		}
	}

	/**
	 * Set the test property of this class
	 *
	 * @since 0.0.9
	 *
	 * @access private
	 *
	 * @param int|array $test
	 */
	private function set_test( $test ) {
		if( is_numeric( $test ) ){
			$this->test = price_test::read( $test );
		}elseif( is_array( $test ) ){
			//@todo allow this once validation is in place
			//$this->test = $test;
		}
	}


	/**
	 * Use in subclass to setup hooks for variably priced products
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 */
	protected function variable_price_hooks() {
		_doing_it_wrong( __METHOD__, __( 'Must override in subclass', 'ingot' ), '0.0.9' );
	}

	/**
	 * Use in subclass to setup hooks for nonvariable products
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 */
	protected function non_variable_price_hooks() {
		_doing_it_wrong( __METHOD__, __( 'Must override in subclass', 'ingot' ), '0.0.9' );
	}

	/**
	 * Use in subclass to set the variable and prices properties
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 */
	protected function set_price() {
		_doing_it_wrong( __METHOD__, __( 'Must override in subclass', 'ingot' ), '0.0.9' );
	}

}
