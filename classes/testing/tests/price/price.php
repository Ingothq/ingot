<?php
/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\tests\price;


use ingot\testing\utility\helpers;

abstract class price {


	protected $plugin;

	protected $post_type;

	protected $product;

	protected $prices;

	protected $product_id;

	protected $price_test;

	public function __construct( $price_test ) {
		$this->price_test = $price_test;
		$this->set_product_id();
		if( ! is_null( $this->product_id ) ) {
			$this->get_product();
			$this->set_prices();
		}

		if( ! is_null( $this->prices ) ) {
			$this->add_filters();
		}

	}


	protected function get_product( ) {
		$product = get_post( $this->product_id );
		if( $this->post_type != $product->post_type ) {
			return false;
		}
		$this->product = $product;
	}

	protected function set_prices() {
		if( $this->is_variable_price() ) {
			$this->prices = $this->get_variable_prices();
		}else{
			$this->prices = $this->get_base_price();
		}
	}

	protected function add_filters() {
		_doing_it_wrong( __METHOD__, __( 'Must override in subclass', 'ingot' ), '0.0.8' );
	}


	protected function get_variable_prices() {
		_doing_it_wrong( __METHOD__, __( 'Must override in subclass', 'ingot' ), '0.0.8' );
	}

	protected function is_variable_price() {
		_doing_it_wrong( __METHOD__, __( 'Must override in subclass', 'ingot' ), '0.0.8' );
	}

	protected function get_base_price(){
		_doing_it_wrong( __METHOD__, __( 'Must override in subclass', 'ingot' ), '0.0.8' );
	}

	private function set_product_id() {
		$this->product_id = helpers::v( 'product_ID', $this->price_test );
	}

}
