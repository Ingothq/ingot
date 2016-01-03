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


use ingot\testing\crud\group;
use ingot\testing\crud\variant;
use ingot\testing\ingot;
use ingot\testing\utility\price;

class test {

	/**
	 * Variant ID
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 *
	 * @var int
	 */
	private $ID;

	/**
	 * Plugin slug
	 *
	 * @since 1.1.0
	 *
	 * @access private
	 *
	 * @var string
	 */
	private $plugin;

	/**
	 * Variant config
	 *
	 * @since 1.1.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $variant;

	/**
	 * Time unix time in seconds when this test expires
	 *
	 * @since 1.1.0
	 *
	 * @access private
	 *
	 * @var int
	 */
	private $expires;

	/**
	 * Price with test applied
	 *
	 * @since 1.1.0
	 *
	 * @access private
	 *
	 * @var string
	 */
	private $price;

	/**
	 * Product object
	 *
	 * @since 1.1.0
	 *
	 * @access private
	 *
	 * @var \WP_Post
	 */
	private $product;

	/**
	 * Callback function for getting base price
	 *
	 * @since 1.1.0
	 *
	 * @access private
	 *
	 * @var string|array
	 */
	private $price_callback;

	/**
	 * Test bandit object
	 *
	 * @since 1.1.0
	 *
	 * @access private
	 *
	 * @var \ingot\testing\bandit\price
	 */
	private $bandit;

	/**
	 * Create object with test details
	 *
	 * @param int $id Group or variant ID
	 * @param int $expires Expiration date from now, in seconds
	 *
	 * @throws \Exception If test invalid
	 */
	public function __construct( $id, $expires = null ) {
		if( ! $expires ){
			$expires = ingot_cookie_time() + time();
		}

		$this->expires = $expires;
		$this->setup( $id );
		$this->set_price();
	}

	protected function setup( $id ){
		if( variant::valid( $this->variant = variant::read( $id ) ) ){
			$group = group::read( $this->variant[ 'group_ID' ] );
			$this->ID = $this->variant[ 'ID' ];
		}elseif( group::valid( $group = group::read( $id ) ) ){
			$this->set_bandit( $group[ 'group_ID' ] );
			$this->ID = $this->bandit->choose();
			if( ! variant::valid( $this->variant = variant::read( $this->ID  ) ) ){
				return;
			}

		}

		$this->product = get_post( price::get_product_ID( $group ) );


	}
	/**
	 * Record a victory for this test
	 *
	 * return int
	 */
	public function record_victory(){
		$group_id = $this->variant[ 'group_ID' ];
		$this->set_bandit( $group_id );

		$this->bandit->record_victory( $this->ID );
		ingot::instance()->get_current_session();

	}

	public function get_price(){
		return $this->price;
	}

	/**
	 * Get any of the declared properties
	 *
	 * @since 1.1.0
	 *
	 * @param string $prop Name of property
	 *
	 * @return mixed
	 */
	public function __get( $prop ){
		if( property_exists( $this, $prop ) ){
			if( 'product' == $prop ) {
				$this->product_to_object();
			}

			return $this->$prop;

		}

	}

	/**
	 * Set price
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 *
	 * @return int|void
	 */
	protected function set_price(){
		$this->product_to_object();
		if ( is_callable( $this->price_callback ) ) {
			$variant = variant::read( $this->ID );
			if ( is_array( $variant ) ) {
				$variation = price::get_price_variation( $variant );
				$base_price = call_user_func( $this->price_callback, $this->product->ID );
				if ( 0 == $base_price || 0 == $variation ) {
					return $base_price;
				}

				$variation   = $variation * 1;
				$this->price = $variation * $base_price;
			}

		}

	}

	/**
	 * Verify test is legit
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 *
	 * @param array $test Test details to make object from
	 *
	 * @return array
	 * @throws \Exception
	 */
	protected function verify_test( $test ){
		$required = array_keys( get_object_vars( $this ) );
		foreach( $required as $key ){
			if( 'price' == $key ){
				continue;
			}

			if( ! isset( $test[ $key ] ) ){
				throw new \Exception( __( 'invalid-price-test', 'ingot' ) );
			}

		}

		return $test;
	}


	/**
	 * Set the test bandit object
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 *
	 * @param $group_id
	 */
	protected function set_bandit( $group_id ) {
		if ( is_null( $this->bandit ) ) {
			$this->bandit = new \ingot\testing\bandit\price( $group_id );
		}

	}

	/**
	 * If product property is numeric, make into an object
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 */
	protected function product_to_object() {
		if ( is_numeric( $this->product ) ) {
			$this->product = price::get_product_function( $this->plugin );
		}

	}


}
