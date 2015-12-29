<?php
/**
 * Sets up price cookies
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

class price extends cookie {



	/**
	 * Hold all tests we need to track
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	private $tests = [];


	/**
	 * Construct object
	 *
	 * @since 1.1.0
	 *
	 * @param array $cookie Current contents of this part of cookie
	 * @param bool $reset Optional. Whether to rest or not, default is false
	 */
	public function __construct(  $cookie, $reset = true ){
		parent::__construct( $cookie, $reset );
		$this->set_tests();
		if( ! empty( $this->tests ) ){
			$this->setup_cookie();
		}

	}


	/**
	 * Set tests property with all test we need to track
	 *
	 * @since 1.1.0
	 *
	 * @access private
	 *
	 * @return array|mixed
	 */
	private function set_tests(){
		$key = md5( __FUNCTION__ );
		if( WP_DEBUG || ! is_array( $tests = get_transient( $key ) ) && ! empty( $tests ) ){
			$tests = group::get_items( [ 'type' => 'price' ] );
			if ( is_array( $tests ) ) {
				$this->tests = $tests;
				set_transient( $key, $tests, HOUR_IN_SECONDS );
			}
		}

	}

	/**
	 * Setup the cookie contents
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 */
	protected function setup_cookie(){
		foreach( $this->tests as $test ) {
			if( $this->needed_to_add( $test ) ){
				$this->add_test( $test );
			}
		}
	}

	/**
	 * Test if we need to add test to cookie
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 *
	 * @param array $test
	 *
	 * @return bool
	 */
	protected function needed_to_add( $test ){
		if( ! empty( $this->cookie )  ) {
			if( ! isset( $this->cookie[ $test[ 'sub_type' ] ], $this->cookie[ $test[ 'sub_type' ] ][ $test[ 'ID' ] ] ) ){
				return true;

			}else{
				$expires = $this->cookie[ $test[ 'sub_type' ] ][ $test[ 'ID' ] ][ 'expires' ];
				if( $expires < $this->expires() ) {
					return true;

				}

			}
		}
	}

	/**
	 * Add test to cookie
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 *
	 * @param array $test
	 *
	 * @return array
	 */
	protected function add_test( $test ){
		$bandit = new \ingot\testing\bandit\price( $test );
		$variant = $bandit->choose();

		return [
			'plugin' => $test[ 'sub_type' ],
			'ID' => $test[ 'ID' ],
			'variant' => $variant,
			'expries' =>$this->expires(),

		];
	}


	/**
	 * Set cookie property for class
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @param array $cookie Current contents of this part of cookie
	 * @param bool $reset Optional. Whether to rest or not, default is false
	 */
	protected function set_cookie( $cookie, $reset ) {
		if ( false == $reset ) {
			$this->cookie = $cookie;
		} else {
			$this->cookie = [ ];
		}

		foreach ( types::allowed_price_types() as $type ) {
			if ( ! isset( $this->cookie[ $type ] ) ) {
				$this->cookie[ $type ] = [ ];
			}
		}
	}

}
