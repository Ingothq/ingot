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
	 * Construct object
	 *
	 * @since 0.0.9
	 *
	 * @param array $current_sequences Current sequences
	 * @param array $price_cookie Price cookie portion of our cookie
	 * @param bool $reset Optional. Whether to rest or not, default is false
	 */
	public function __construct( $current_sequences, $price_cookie, $reset = true ){
		if ( false == $reset  ) {
			$this->price_cookie = $price_cookie;
		}else{
			$this->price_cookie = [];
		}

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

}
