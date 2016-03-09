<?php
/**
 * Handle current license
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\licensing;


use ingot\testing\utility\helpers;

class license {

	/**
	 * Class instance
	 *
	 * @since 1.1.0
	 *
	 * @var license
	 */
	protected static $instance;

	/**
	 * Plan object
	 *
	 * @since 1.1.0
	 *
	 * @var plan
	 */
	protected $plan;

	/**
	 * Key for the license details to be saved in
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	private $storage_key = '_ingot_plan_details';

	/**
	 * Constructor for class
	 *
	 * Sets up plan
	 *
	 * @since 1.1.0
	 */
	protected function __construct(){
		$this->set_plan();
		if( ! $this->plan->is_full() ){
			new pre_save( $this->plan );
		}
	}

	/**
	 * Get class instance
	 *
	 * @since 1.1.0
	 *
	 * @return \ingot\licensing\license
	 */
	public static function get_instance(){
		if ( null === static::$instance) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Get the plan object
	 *
	 * @since 1.1.0
	 *
	 * @return \ingot\licensing\plan
	 */
	public function get_plan(){
		return $this->plan;
	}

	/**
	 * Setup plan object
	 *
	 * @since 1.1.0
	 */
	protected function set_plan(){
		$details = get_option( $this->storage_key, [] );
		$this->plan = new plan( helpers::v( 'plan', $details ) );
	}

	/**
	 * Save plan details
	 *
	 *  @since 1.1.0
	 *
	 * @param array $plan
	 */
	public function save_plan( array $plan ){
		update_option( $this->storage_key, $plan );
		$this->set_plan();
	}


}
