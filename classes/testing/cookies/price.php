<?php
/**
 * Setsups price cookies
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\cookies;

use ingot\testing\crud\group;
use ingot\testing\types;
use ingot\testing\utility\helpers;

class price {

	/**
	 * The price cookie
	 *
	 * @since 0.2.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	private $price_cookie;

	/**
	 * @var array
	 */
	private $tests;


	/**
	 * Construct object
	 *
	 * @since 0.0.9
	 *
	 * @param array $current_sequences Current sequences
	 * @param array $price_cookie Price cookie portion of our cookie
	 * @param bool $reset Optional. Whether to rest or not, default is false
	 */
	public function __construct( $current_sequences, $price_cookie, $reset = true ){
		$this->set_price_cookie( $price_cookie, $reset );

	}

	/**
	 * Get price cookie
	 *
	 * @since 0.0.9
	 *
	 * @return array Price cookie portion of our cookie
	 */
	public function get_price_cookie() {
		return $this->price_cookie;
	}


	protected function get_tests(){
		$key = md5( __FUNCTION__ );
		if( WP_DEBUG || ! is_array( $tests = get_transient( $key ) ) && ! empty( $tests ) ){
			$tests = group::get_items( [ 'type' => 'price' ] );
			if ( is_array( $tests ) ) {
				$this->tests = $tests;
				set_transient( $key, $tests, HOUR_IN_SECONDS );
			}
		}

		return $tests;

	}

	protected function setup_price_cookie(){
		foreach( $this->tests as $test ) {
			if( $this->needed_to_add( $test ) ){
				$this->add_test( $test );
			}
		}
	}

	protected function needed_to_add( $test ){
		if( ! empty( $this->price_cookie )  ) {
			if( ! isset( $this->price_cookie[ $test[ 'sub_type' ] ], $this->price_cookie[ $test[ 'sub_type' ] ][ $test[ 'ID' ] ] ) ){
				return true;

			}else{
				$expires = $this->price_cookie[ $test[ 'sub_type' ] ][ $test[ 'ID' ] ][ 'expires' ];
				if( $expires < $this->expires() ) {
					return true;

				}

			}
		}
	}

	protected function add_test( $test ){
		$varinat = '';//need a price test bandit class

		return [
			'plugin' => $test[ 'sub_type' ],
			'ID' => $test[ 'ID' ],
			'variant' => $variant,
			'expries' =>$this->expires(),

		];
	}
	/**
	 * Get expiration time for tests
	 *
	 * @todo filter/option/etc?
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @return int
	 */
	protected function expires() {
		return time() + ( 10 * DAY_IN_SECONDS );

	}

	/**
	 * @param $price_cookie
	 * @param $reset
	 */
	private function set_price_cookie( $price_cookie, $reset ) {
		if ( false == $reset ) {
			$this->price_cookie = $price_cookie;
		} else {
			$this->price_cookie = [ ];
		}

		foreach ( types::allowed_price_types() as $type ) {
			if ( ! isset( $this->price_cookie[ $type ] ) ) {
				$this->price_cookie[ $type ] = [ ];
			}
		}
	}

}
