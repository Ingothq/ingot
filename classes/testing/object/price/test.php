<?php
/**
 * Defines the chosen variant and what prices/plugin it represents
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\object\price;


use ingot\testing\crud\variant;
use ingot\testing\utility\price;

class test {

	private $ID;

	private $plugin;

	private $variant;

	private $expires;

	private $price;

	/**
	 * @var \WP_Post
	 */
	private $product;

	private $price_callback;

	public function __construct( $test ) {
		$this->set_properties( $test );
		$this->set_price();
	}

	public function __get( $var ){
		if( property_exists( $this, $var ) ){
			return $this->$var;
		}
	}

	protected function set_price(){
		if ( is_callable( $this->price_callback ) ) {
			$variant = variant::read( $this->variant );
			if ( is_array( $variant ) ) {
				$variation = price::get_price_variation( $variant );
				$base_price = $this->price_callback( $this->product->ID );
				if ( 0 == $base_price || 0 == $variation ) {
					return $base_price;
				}

				$variation   = $variation * 1;
				$this->price = $variation * $base_price;
			}
		}
	}


	private function set_properties( $test ){
		foreach( $test as $prop => $value ){
			if( property_exists( $this, $prop ) ){
				$this->$prop = $value;
			}

		}

	}

}
