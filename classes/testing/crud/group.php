<?php
/**
 * Group CRUD
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\crud;


class group extends options_crud {


	/**
	 * Name of this object
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected static $what = 'group';

	protected static function what() {
		return 'group';
	}

	/**
	 * Required fields of this object
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected static function required() {
		$required = array(
			'type'
		);

		return $required;
	}

	/**
	 * Necessary, but not required fields of this object
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected static function needed() {
		$needed = array(
			'name',
			'sequences',
			'order',
			'initial',
			'selector',
			'threshold',
			'click_type',
			'link',
			'meta',
			'created',
			'modified',
			'current_sequence'
		);

		return $needed;
	}

}
