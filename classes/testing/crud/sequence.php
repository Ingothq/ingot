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


use ingot\testing\utility\helpers;

class sequence extends table_crud {

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
	 *  $group_id int ID of group to get all
	 *  $ids array Optional. Array of ids to get.
	 *  $current bool Optional. Used with $ids or $group_id, if true, will return the first non-completed sequence for that group_id or set of ids. Default is false.
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
				'group_id' => null,
				'ids' => array(),
				'current' => false,
				'limit' => -1,
				'page' => 1,
			)
		);

		global $wpdb;
		$table_name = self::get_table_name();
		if( helpers::v( 'group_id', $args, null ) ){
			$sql = sprintf( 'SELECT * FROM %s WHERE `group_id` = %d',$table_name, helpers::v( 'group_id', $params )  );
		}elseif( ! empty( helpers::v( 'ids', $args, array() ) ) ){
			$in = implode( ',', helpers::v( 'group_id', $params ) );
			$sql = sprintf( 'SELECT * FROM %s WHERE `group_id` = IN( %s)',$table_name, $in );
		}else{
			$sql = sprintf( 'SELECT * FROM %s', $table_name );
		}

		if( helpers::v( 'current', $args, false ) ) {
			$sql .= ' AND `completed` != 1';
		}

		$sql .= sprintf( ' LIMIT %d OFFSET %d', $args[ 'limit' ], self::calculate_offset( $args[ 'limit' ], $args[ 'page' ] )  );

		$results = $wpdb->get_results( $wpdb->prepare( $sql ), ARRAY_A );

		return $results;

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
			'test_type',
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
			'group_ID',
		);

		return $needed;
	}

}
