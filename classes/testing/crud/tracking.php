<?php
/**
 * CRUD for tracking table
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\crud;


class tracking extends table_crud {

	/**
	 * Name of this object
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected static $what = 'tracking';

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
			'test_ID',
		);

		return $required;
	}

	/**
	 * Neccasary, but not required fields of this object
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected static function needed() {
		$needed = array(
			'group_ID',
			'sequence_ID',
			'test_ID',
			'IP',
			'UTM',
			'browser',
			'meta',
			'user_agent',
			'time',
		);

		return $needed;
	}

}
