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


	public function __construct( $test, $a_or_b ) {
		$this->a_or_b = $a_or_b;
		$this->test = $test;
		$this->product = get_post( $this->test[ 'product_ID' ] );
		if ( is_object( $this->product ) ) {
			$this->set_price();
			$this->add_hooks();
		}
	}

	public function filter_price( $price, $id ) {
		if( $id == $this->product->ID ) {
			$price = $this->apply_price( $price );
		}

		return $price;
	}

	public function filter_variable_prices( $prices, $id ) {
		if( $id == $this->product->ID ) {
			$prices = $this->apply_variable_price( $prices );
		}

		return $prices;


	}

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

	protected function apply_price( $price, $index = 'default' ) {
		$p = $this->test[ 'test' ][ $index ][ $this->a_or_b ];

		$price = $this->change_price( $price, $p );

		return $price;

	}



	/**
	 * @param $price
	 * @param $p
	 *
	 * @return string
	 */
	protected function change_price( $price, $p ) {
		$price = ( (float) $price * (float) $p ) + (float ) $price;

		return $this->sanatize_price( $price );
	}


	/**
	 * @param $price
	 *
	 * @return string
	 */
	protected function sanatize_price( $price ) {
		return ingot_sanitize_amount( $price );
	}

	protected function add_hooks() {
		$this->display_hooks();
	}

	protected function display_hooks() {
		if ( $this->variable ) {
			$this->variable_price_hooks();
		} else {
			$this->non_variable_price_hooks();
		}
	}



	protected function tracking_hooks() {

	}


	protected function set_price() {
		_doing_it_wrong( __METHOD__, __( 'Must override in subclass', 'ingot' ), '0.0.8' );
	}

	protected function variable_price_hooks() {
		_doing_it_wrong( __METHOD__, __( 'Must override in subclass', 'ingot' ), '0.0.8' );
	}

	protected function non_variable_price_hooks() {
		_doing_it_wrong( __METHOD__, __( 'Must override in subclass', 'ingot' ), '0.0.8' );
	}

}
