<?php
/**
 * Sequence CRUD
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */


namespace ingot\testing\crud;


class sequence extends options_crud {

	/**
	 * Name of this object
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected static $what = 'sequence';

	protected static function what() {
		return 'sequence';
	}

	/**
	 * Get a collection of items
	 *
	 * @since 0.0.5
	 *
	 * @param array $params {
	 *  $ids array Optional. Array of ids to get.
	 *  $current bool Optional. Used with $ids, if true, will return the first non-complete sequence. Default is false.
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
				'current' => false,
				'limit' => -1,
				'page' => 1,
			)
		);

		$find_current = $params[ 'current' ];
		if ( ! empty( $params[ 'ids' ] ) ) {
			$all = array();
			foreach( $params[ 'ids' ] as $id ) {
				$sequence = self::read( $id );
				if ( $find_current ){
					if( ! $sequence[ 'completed' ] ) {
						return $sequence;

					}
					continue;
				}
				$all[] = $sequence;
			}

			return $all;
		}else{
			if ( -1 == $args[ 'limit' ] ) {
				//@todo better hack?
				$args[ 'limit' ] = 90000001;
			}

			extract( $args );
			return self::get_all( $limit, $page );
		}
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
			'a_id',
			'b_id',
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
			'a_win',
			'b_win',
			'a_total',
			'b_total',
			'initial',
			'completed',
			'threshold',
			'created',
			'modified',
			'group',
		);

		return $needed;
	}

}
