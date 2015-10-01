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
	 * Get a collection of items
	 *
	 * @since 0.0.5
	 *
	 * @param array $params {
	 *  $ids array Optional. Array of ids to get. DOESN'T WORK YET
	 *  $limit int Optional. Limit results, default is -1 which gets all.
	 *  $page int Optional. Page of results, used with $limit. Default is 1
	 * }
	 *
	 * @return array
	 */
	public static function get_items( $params ) {
		$limit = $page = 1;
		$args = wp_parse_args(
			$params,
			array(
				'ids' => array(),
				'limit' => -1,
				'page' => 1,
			)
		);

		if ( -1 == $args[ 'limit' ] ) {
			//@todo better hack?
			$args[ 'limit' ] = 90000001;
		}

		extract( $args );
		return self::get_all( $limit, $page );
	}


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
			'type',

		);

		return $required;
	}

	/**
	 * Neccasary, but not required fields of this object
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
			'created',
			'modified',
		);

		return $needed;
	}

}
