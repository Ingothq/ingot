<?php
/**
 * A non-peristant cache to hold data from cookies in memory within a session.
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\cookies;


class cache {

	protected $parts = array(
		'price',
		'click',
		'meta',
		'products'
	);

	protected  $cache;

	/**
	 * Hold class instance
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @var \ingot\testing\cookies\cache
	 */
	private static $instance;


	private function __construct() {
		//prevent creation
	}

	/**
	 * @return \ingot\testing\cookies\cache
	 */
	public static function instance() {
		if( is_null( self::$instance ) ){
			self::$instance = new self();
		}

		return self::$instance;
	}


	public function update( $key, $value ){
		if( in_array( $key, $this->parts ) && is_array( $value ) ){
			$this->cache[ $key ] = $value;
		}
	}

	public function get( $key ){
		if( isset( $this->cache[ $key ] ) ){
			return $this->cache[ $key ];
		}
	}

	public function __get( $key ){
		return $this->get( $key );
	}

	public function __set( $key, $value ){
		$this->update( $key, $value );
	}





}
