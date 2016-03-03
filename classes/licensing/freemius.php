<?php
/**
 * Plan/license handling for Freemius
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\licensing;


class freemius extends license {

	/**
	 * Setup plan object
	 *
	 * @since 1.1.1
	 */
	protected function set_plan(){
		$plan = ingot_fs()->get_plan();
		$this->plan = new plan( $plan->name  );
	}

	/**
	 * Save plan details
	 *
	 *  @since 1.1.1
	 *
	 * @param array $plan
	 */
	public function save_plan( array $plan ){

	}


}
