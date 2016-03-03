<?php
/**
 * Filters for handling logic of plans
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\licensing;


class type_filters extends filters {

	/**
	 * Add hooks for this class
	 *
	 * @since 1.1.0
	 */
	protected function add_hooks(){
		if( $this->plan->is_nugget() ){
			add_filter( 'ingot_allowed_types', [ $this, 'allowed_types' ] );
			add_filter( 'ingot_allowed_click_types', [ $this, 'allowed_click_types' ] );
		}

		if( $this->plan->is_ecommerce() ){
			add_filter( 'ingot_allowed_click_types', [ $this, 'limit_destination_types' ] );
			add_filter( 'ingot_accepted_plugins_for_price_tests', [ $this, 'price_test_plugins' ] );
		}

	}

	/**
	 * Don't allow price tests in nugget plan
	 *
	 * @uses "ingot_allowed_types" filter
	 *
	 * @param $types
	 *
	 * @return array
	 */
	public function allowed_types( $types ){
		return array_diff( $types, [ 'price' ] );
	}

	/**
	 * Don't allow price tests in nugget plan
	 *
	 * @uses "ingot_allowed_click_types" filter
	 *
	 * @param array $types
	 *
	 * @return mixed
	 */
	public function allowed_click_types( $types ){

		unset( $types[ 'destination' ] );
		return $types;

	}

	/**
	 * Limit destination types by type of ecommerce plan
	 *
	 * @since 1.1.0
	 *
	 * @uses "ingot_allowed_click_types" filter
	 *
	 * @param array $types
	 *
	 * @return array
	 */
	public function limit_destination_types( $types ){
		switch( $this->plan->get_plan_slug() ){
			case ( 'edd' ) :
				unset( $types[ 'cart_woo' ] );
				unset( $types[ 'sale_woo' ] );
				break;
			case 'woo' :
				unset( $types[ 'cart_edd' ] );
				unset( $types[ 'sale_edd' ] );
				break;
		}

		return $types;

	}

	/**
	 * Limit plugins for price testing by plan
	 *
	 * @since 1.1.0
	 *
	 * @uses "ingot_accepted_plugins_for_price_tests" filter
	 *
	 * @param array $types
	 *
	 * @return array
	 */
	public function  price_test_plugins( $types ){
		switch( $this->plan->get_plan_slug() ){
			case ( 'edd' ) :
				unset( $types[ 'woo' ] );
				break;
			case 'woo' :
				unset( $types[ 'edd' ] );
				break;
		}

		return $types;
	}


}
