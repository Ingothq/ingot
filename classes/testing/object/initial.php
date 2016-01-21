<?php
/**
 * Find number of tests to run at random, initially for a group
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\testing\object;



use ingot\testing\object\stats\sessions;
use ingot\testing\utility\defaults;

class initial {

	/**
	 * Variant levers
	 *
	 * @access private
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	private $levers;

	/**
	 * Total number of iterations for this group
	 *
	 * @access private
	 *
	 * @since 1.1.0
	 *
	 * @var int
	 */
	private $total;

	/**
	 * Calculated minimum iterations
	 *
	 * @access private
	 *
	 * @since 1.1.0
	 *
	 * @var
	 */
	private $initial;

	/**
	 * Create and setup object
	 *
	 * @since 1.1.0
	 *
	 * @param \ingot\testing\object\group|int $group Group object or ID
	 */
	public function __construct( $group ){
		$group = $this->set_levers( $group );
		if ( INGOT_DEV_MODE || ! $this->set_from_cache( $group->get_ID() ) ) {
			$this->set_total( $group->get_group_config() );
			$this->set_initial( $group );
			$this->set_cache( $group->get_ID() );
		}


	}

	/**
	 * Attempt to set the initial and total properties from cache
	 *
	 * @access protected
	 *
	 * @since 1.1.0
	 *
	 * @param int $id Group ID
	 *
	 * @return bool True if properties were set from cache, false if not.
	 */
	protected function set_from_cache( $id ){
		$total_key = md5( __CLASS__ . "total_{$id}" );
		$total = get_transient( $total_key  );
		if( is_numeric( $total ) ) {
			$initial_key = md5( __CLASS__ . "initial_{$id}" );
			$initial = get_transient( $initial_key );
			if( is_numeric( $initial ) ){
				$this->initial = $initial;
				$this->total = $total;
				return true;

			}

		}

		return false;
	}

	/**
	 * Cache total and initial
	 *
	 * @access protected
	 *
	 * @since 1.1.0
	 *
	 * @param int $id Group ID
	 *
	 */
	protected function set_cache( $id ){
		set_transient( md5( __CLASS__ . "total_{$id}" ), $this->total, DAY_IN_SECONDS );
		set_transient( md5( __CLASS__ . "initial_{$id}" ), $this->initial, DAY_IN_SECONDS );
	}

	/**
	 * Has group completed more iterations then the minimum?
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public function is_passed_initial(){
		if( 0 == $this->total || (int) $this->total < (int) $this->initial ){
			return false;
		}else{
			return true;
		}

	}

	/**
	 * Calculate correct initial value
	 *
	 * @access protected
	 *
	 * @since 1.1.0
	 *
	 * @param \ingot\testing\object\group $group
	 */
	protected function set_initial( \ingot\testing\object\group $group ){
		$average = sessions::get_instance()->get_average( $this->use_unique( $group->get_group_config() ) );
		if( ! is_numeric( $average ) || 0 == $average || $average < defaults::threshold() ){
			$this->initial = defaults::initial();
		}else{
			$this->initial = $average;
		}

	}

	/**
	 * Based on group type, should unique sessions or total sessions be used for calculating averages
	 *
	 * @access protected
	 *
	 * @since 1.1.0
	 *
	 * @param array $group Group config array
	 *
	 * @return bool
	 */
	protected function use_unique( $group ){
		if( 'price' == \ingot\testing\utility\group::type( $group ) || 'destination' == \ingot\testing\utility\group::sub_type( $group ) ){
			return true;
		}


		return false;
	}

	/**
	 * Set the total property for this object
	 *
	 * @access protected
	 *
	 * @since 1.1.0
	 *
	 * @param array $group Group config
	 *
	 */
	private function set_total( array $group ){
		$this->total = 0;
		if( ! empty( $this->levers ) ){
			$this->calculate_total( $group );
		}
	}

	/**
	 * Calculate total number of times this group has been tested
	 *
	 * @access protected
	 *
	 * @since 1.1.0
	 *
	 * @param array $group Group config
	 *
	 */
	protected function calculate_total( array $group ){
		$this->total = \ingot\testing\utility\group::get_total( $group );
	}

	/**
	 * Set levers property of this object
	 *
	 * @access private
	 *
	 * @since 1.1.0
	 *
	 * @param \ingot\testing\object\group|int $group Group object or ID
	 *
	 * @return \ingot\testing\object\group
	 */
	private function set_levers( $group ){
		if( is_numeric( $group ) ){
			$group = new \ingot\testing\object\group( $group );
		}elseif( is_array( $group ) ){
			$group = new \ingot\testing\object\group( $group );
		}

		if( is_object( $group ) ){
			$this->levers = $group->get_levers();
		}else{
			$this->levers = [];
		}

		return $group;

	}

}
