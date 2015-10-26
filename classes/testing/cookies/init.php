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

namespace ingot\testing\cookies;


class init {

	protected $cookie_name = 'ingot_cookie';

	protected $cookie;


	protected $cookie_parts = array(
		'price',
		'click',
		'meta'
	);

	/**
	 * Set and check our cookies
	 *
	 * @uses "init"
	 *
	 * @param array $cookies cookies super var
	 */
	public function __construct( $cookies ) {
		$this->set_cookie( $cookies );
		$this->get_ingot_cookie();
		$this->setup_cookies();

	}

	public function get_ingot_cookie() {
		return $this->cookie;
	}

	public function get_cookie_name(){
		return $this->cookie_name;
	}

	public function refresh_cache() {
		foreach ( $this->cookie_parts as $part  ) {
			cache::instance()->update( $part, $this->cookie[ $part ] );
		}

	}

	protected function set_cookie(){
		if( isset( $cookies[ $this->cookie_name ] ) ) {
			$this->cookie = $cookies[ $this->cookie_name ];
		}else{
			$this->cookie = array();
		}

		foreach( $this->cookie_parts as $part ) {
			if( ! isset( $this->cookie[ $part ] ) ){
				$this->cookie[ $part ] = array();
			}

		}

	}

	protected function setup_cookies() {
		$this->setup_price_cookie();
		//$this->setup_click_cookie();
		//$this->setup_meta_cookie();
	}

	protected function setup_price_cookie() {
		$price = new price( $this->cookie[ 'price' ] );
		$this->cookie[ 'price' ] = $price->get_price_cookie();
	}

	protected function setup_click_cookie() {
		//do this later
	}

	protected function setup_meta_cookie(){
		//so this later
	}

}
