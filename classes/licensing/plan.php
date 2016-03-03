<?php
/**
 * Plan object
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\licensing;


class plan {

	/**
	 * Possible plan types
	 *
	 * @since 1.1.1
	 *
	 * @var array
	 */
	protected $plans = [
		'nugget',
		'woo',
		'edd',
		'full'
	];

	/**
	 * Slug for plan
	 *
	 * @since 1.1.1
	 *
	 * @var string
	 */
	protected $plan_slug;

	/**
	 * Constructor
	 *
	 * @since 1.1.1
	 *
	 * @param string $plan_slug
	 */
	public function __construct( $plan_slug ){
		$this->set_plan_slug( $plan_slug );
	}

	/**
	 * Get plan slug
	 *
	 * @since 1.1.1
	 *
	 * @return string
	 */
	public function get_plan_slug(){
		return $this->plan_slug;
	}

	/**
	 * Is plan the full plan
	 *
	 * @since 1.10
	 *
	 * @return bool
	 */
	public function is_full(){
		if( 'full' == $this->plan_slug ){
			return true;
		}

		return false;
	}

	/**
	 * Is plan the nugget plan
	 *
	 * @since 1.10
	 *
	 * @return bool
	 */
	public function is_nugget(){
		return 'nugget' == $this->plan_slug;
	}

	/**
	 * Is plan an ecommerce plan
	 *
	 * @since 1.10
	 *
	 * @return bool
	 */
	public function is_ecommerce(){
		return in_array( $this->plan_slug, [ 'edd', 'woo' ] );
	}

	/**
	 * Set plan_slug property
	 *
	 * @since 1.1.1
	 *
	 * @param string $plan
	 */
	private function set_plan_slug( $plan ) {
		if ( is_string( $plan ) && ! in_array( $plan, $this->plans ) ) {
			$this->plan_slug = $plan;
		} else {
			$this->plan_slug = 'nugget';
		}
	}
}
