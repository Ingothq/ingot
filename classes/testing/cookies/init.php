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

	protected $cookies;

	/**
	 * Set and check our cookies
	 *
	 * @uses "init"
	 *
	 * @param array $cookies cookies super var
	 */
	public function __construct( $cookies ) {
		$this->cookies = $cookies;

	}

	protected function is_cookie_set() {
		if( isset( $this->cookies[ $this->cookie_name ] ) ){
			return true;
		}
	}

	protected function get_our_cookie() {
		$this->cookie = $this->cookies[ $this->cookie_name ];

	}

	protected function make_cookie_contents() {
		return 'bats';
	}

	protected function create_new_cookie() {
		$length = apply_filters( 'ingot_cookie_length', WEEK_IN_SECONDS );
		setcookie( $this->cookie_name, $this->make_cookie_contents(), $length, COOKIEPATH, COOKIE_DOMAIN, false );
		$this->cookies = $_COOKIE;
	}


	protected function get_url() {

		$url = '.' .  home_url();

		return apply_filters( 'ingot_cookie_domain', $url );

	}

}
