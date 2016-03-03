<?php
/**
 * Base class for license related filters
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\licensing;


abstract class filters {

	/**
	 * Current plan object
	 *
	 * @since 1.1.0
	 *
	 * @var \ingot\licensing\plan
	 */
	protected $plan;

	/**
	 * Constructor
	 *
	 * @since 1.1.0
	 *
	 * @param \ingot\licensing\plan $plan
	 */
	public function __construct( plan $plan  ){
		$this->plan = $plan;
		$this->add_hooks();
	}

	/**
	 * Use to add hooks in sub classes
	 *
	 * @since 1.1.0
	 */
	protected abstract function add_hooks();

}
