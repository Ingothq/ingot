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
	 * Set and check our cookies
	 *
	 * @since 0.0.9
	 *
	 * @uses "init"
	 *
	 * @param array $cookies cookies super var
	 */
	public function __construct( $cookies ) {
		$this->set_cookie( $cookies );
		$this->get_ingot_cookie();
		$this->setup_cookies();
		$this->refresh_cache();

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
	 * Refresh the non-peristant cache used to share this data within a session
	 *
	 * @since 0.0.9
	 */
	public function refresh_cache() {
		foreach ( $this->cookie_parts as $part  ) {
			cache::instance()->update( $part, $this->cookie[ $part ] );
		}

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

		$price = new price( $this->cookie[ 'price' ], INGOT_DEV_MODE );
		$this->cookie[ 'price' ] = $price->get_price_cookie();
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
