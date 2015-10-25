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

}
