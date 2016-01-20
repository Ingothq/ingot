<?php
/**
 * Setup hooks for tracking destination test conversions
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\testing\tests\click\destination;


use ingot\testing\tests\click\destination\init;
use ingot\testing\utility\destination;

class hooks {

	/**
	 * Array of group_id => variant_id for the destination tests
	 *
	 * @access protected
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $tracking = [];

	/**
	 * Array of group_id => group config for the destination tests
	 *
	 * @access protected
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $groups = [];

	/**
	 * Have we set $this->groups yet?
	 *
	 * @access protected
	 *
	 * @since 1.1.0
	 *
	 * @var bool
	 */
	protected $groups_set = false;

	/**
	 * Hooks we are tracking
	 *
	 * @access protected
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $hook_tests = [];

	/**
	 *
	 * @since 1.1.0
	 *
	 * @param array $tracking Groups/variants to track
	 */
	public function __construct( $tracking ){
		$this->set_tracking( $tracking );
		if( ! empty( $this->tracking ) ){
			$this->set_groups();
			$this->add_hooks();
		}

	}

	/**
	 * Add hooks for tracking
	 *
	 * @since 1.1.0
	 */
	public function add_hooks(){
		foreach( destination::hooks() as $hook => $callback ){

			if( is_null ( $callback ) && method_exists( $this, $hook ) ){
				$callback = $hook;
			}

			if( ! is_array( $callback ) ){
				add_action( $hook, [ $this, $callback ] );
			}else{
				if ( is_callable( $callback ) ) {
					add_action( $hook, $callback );
				}
			}

			$this->set_hook_tests();

		}

	}

	/**
	 * Track EDD add to cart conversions
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public function edd_post_add_to_cart(){
		return $this->check_if_victory( 'cart_edd' );
	}

	/**
	 * Track EDD sale complete conversions
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public function edd_complete_purchase(){
		return $this->check_if_victory( 'sale_edd' );
	}

	/**
	 * Track WOO add to cart conversions
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public function woocommerce_add_to_cart(){
		return $this->check_if_victory( 'cart_woo' );
	}

	/**
	 * Track WOO sale complete conversions
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public function woocommerce_payment_complete_order_status(){
		return $this->check_if_victory( 'sale_woo' );
	}

	/**
	 * Track got to page conversions
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public function template_redirect() {
		$post = get_post();
		if( is_object( $post ) ) {
			$this->track_by_id( $post->ID );
		}

	}

	/**
	 * Get a group, if possible from groups property of this class
	 *
	 * @access protected
	 *
	 * @since 1.1.0
	 *
	 * @return array|bool
	 */
	protected function get_group( $id ){
		$id = intval( $id );
		$this->set_groups();
		if( ! empty( $this->groups ) && isset( $this->groups[ $id ] ) ){
			return $this->groups[ $id ];
		}

		return false;

	}

	/**
	 * Set groups property of this class
	 *
	 * @access protected
	 *
	 * @since 1.1.0
	 */
	protected function set_groups(){
		if( ! $this->groups_set ) {
			$groups = init::get_destination_tests( false );
			foreach( $groups as $group ){
				$this->groups[ (int) $group[ 'ID' ] ] = $group;
			}
			$this->groups_set = true;
		}

	}

	/**
	 * Check if we can count current context as a conversion, if so register it
	 *
	 * @access protected
	 *
	 * @since 1.1.0
	 *
	 * @param string $type Destination type
	 *
	 * @return bool
	 */
	protected function check_if_victory( $type ) {
		foreach ( $this->tracking as $group_id => $variant_id ) {
			$group = $this->get_group( $group_id );
			if ( $group && $type === destination::get_destination( $group ) ) {
				ingot_register_conversion( $variant_id );
				return true;
			}

		}

	}

	/**
	 * If appropriate, count a conversion based on post ID
	 *
	 * @since 1.1.0
	 *
	 * @param $id
	 */
	public function track_by_id( $id ) {
		$this->set_groups();
		foreach ( $this->groups as $i => $group ) {
			$page = destination::get_page_id( $group );
			if ( 0 != absint( $id ) && $page == $id ) {
				$variant_id = 0;
				if ( isset( $this->tracking[ (int) $group[ 'ID' ] ] ) ) {
					$variant_id = $this->tracking[ (int) $group[ 'ID' ] ];

				}else{
					$variant_id = init::get_test( $group[ 'ID' ] );
				}

				if( 0 != absint( $variant_id ) ){
					ingot_register_conversion( $variant_id );
				}


			}

		}

	}

	/**
	 * Setup tracking for hook tests
	 *
	 * @access protected
	 *
	 * @since 1.1.0
	 */
	protected function set_hook_tests(){
		foreach (  $this->groups as $group  ) {
			if( destination::is_hook( $group ) && isset( $group[ 'meta' ][ 'hook' ] ) ){
				$hook = $group[ 'meta' ][ 'hook' ];
				$variant_id = init::get_test( $group[ 'ID' ] );
				if ( is_numeric( $variant_id ) ) {
					$this->hook_tests[ sanitize_key( $hook ) ] = $variant_id;
					add_action( $hook, [ $this, 'hook_test_cb' ], 55 );
				}
			}

		}

	}

	/**
	 * Generic callback for hook test tracking
	 *
	 * @since 1.1.0
	 */
	public function hook_test_cb(){
		if( ! empty( $this->hook_tests ) && in_array( sanitize_key( current_action() ), $this->hook_tests ) ){
			ingot_register_conversion( $this->hook_tests[ sanitize_key( current_action() ) ] );
		}
		
	}

	/**
	 * Set tracking property of this class
	 *
	 * @access private
	 *
	 * @since 1.1.0
	 *
	 * @param array $tracking Group IDs to track
	 */
	private function set_tracking( array $tracking ) {
		if ( ! empty( $tracking ) ) {
			foreach ( $tracking as $group_id ) {
				$variant_id = init::get_test( $group_id );
				if ( is_numeric( $variant_id ) ) {
					$this->tracking[ (int) $group_id ] = $variant_id;
				}

			}
		}

	}

}
