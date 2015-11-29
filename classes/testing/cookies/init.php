<?php
/**
 * Setup ingot cookies
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\cookies;


use ingot\testing\crud\price_test;
use ingot\testing\tests\flow;
use ingot\testing\utility\price;

class init {

	/**
	 * Index we use in cookie super global
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $cookie_name = 'ingot_cookie';


	/**
	 * Index of our cookie array
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $cookie_parts = array(
		'price',
		'click',
		'meta'
	);

	/**
	 * The ingot cookie
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $cookie = array();

	/**
	 * Set and check our cookies
	 *
	 * @since 0.0.9
	 *
	 * @uses "init"
	 *
	 * @param array $cookies cookies super var
	 * @param bool $rebuild Optional. Trigger a rebuild.
	 */
	public function __construct( $cookies, $rebuild = true ) {
		if( $rebuild ) {
			$this->rebuild( $cookies );
		}

	}

	/**
	 * Rebuild the cookies
	 *
	 * @since 0.2.0
	 *
	 * @param array $cookies cookies super var
	 */
	public function rebuild( $cookies ) {
		$this->set_cookie( $cookies );
		$this->get_ingot_cookie();
		$this->setup_cookies();
	}

	/**
	 * Get what we should set in the cookie
	 *
	 * @since 0.0.9
	 *
	 * @param bool $encode Optional. If true, the default, data is returned as a JSON encoded string. If false, as array.
	 *
	 * @return false|string
	 */
	public function get_ingot_cookie( $encode = true ) {
		if ( $encode ) {
			return wp_json_encode( $this->cookie );
		} else {
			return  $this->cookie;
		}
	}

	/**
	 * Get name of index we use in cookie super global
	 *
	 * @since 0.0.9
	 *
	 * @return string
	 */
	public function get_cookie_name(){
		return $this->cookie_name;
	}



	/**
	 * Set the cookie property of this class
	 *
	 * @since 0.0.9
	 *
	 * @access private
	 *
	 * @param array $cookies Array to pull from. Should be cookies super global.
	 */
	protected function set_cookie( $cookies ){
		if( isset( $cookies[ $this->cookie_name ] ) ) {
			$this->cookie = json_decode( $cookies[ $this->cookie_name ] );
		}else{
			$this->cookie = array();
		}

		foreach( $this->cookie_parts as $part ) {
			if( ! isset( $this->cookie[ $part ] ) ){
				$this->cookie[ $part ] = array();
			}

		}

	}

	/**
	 * Setup our cookies
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 */
	protected function setup_cookies() {
		$this->setup_price_cookie();
		//$this->setup_click_cookie();
		//$this->setup_meta_cookie();
	}

	/**
	 * Setup our price cookies
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 */
	protected function setup_price_cookie() {

		$price = new \ingot\testing\cookies\price( $this->collect_sequence(), $this->cookie[ 'price' ] );
		$this->cookie[ 'price' ] = $price->get_price_cookie();
	}

	/**
	 * Collect the tests we need
	 *
	 * @since 0.2.0
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected function collect_sequence() {

		$args = array(
			'price_test' => true,
			'current' => true,
			'limit' => -1
		);

		$active_sequences = \ingot\testing\crud\sequence::get_items( $args );

		return $active_sequences;


	}

	/**
	 * Setup our click cookies
	 *
	 * @todo this
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 */
	protected function setup_click_cookie() {
		//do this later
	}

	/**
	 * Setup our meta cookies
	 *
	 * @todo this
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 */
	protected function setup_meta_cookie(){
		//so this later
	}

}