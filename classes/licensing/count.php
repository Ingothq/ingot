<?php
/**
 * Count various types of tests
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\licensing;


use ingot\testing\crud\group;
use ingot\testing\types;

class count {

	/**
	 * Construct an OR clause for getting number of CTA test groups
	 *
	 * @since 1.1.1
	 *
	 * @return string
	 */
	protected static function cta_type_or(){
		$types = types::allowed_click_types();
		$or = [];
		foreach( $types as $type ){
			if( 'destination' != $type ){
				$or[] =  $type;
			}
		}

		return '`sub_type` = "' . implode( '" OR `sub_type` = "', $or ) . '"';
	}

	/**
	 * Run a query and get number of rows
	 *
	 * @since 1.1.0
	 *
	 * @param string $sql SQL for count
	 *
	 * @return int
	 */
	protected static function count_query( $sql ){
		global $wpdb;
		$wpdb->get_results(  $sql );
		return (int) $wpdb->num_rows;
	}

	/**
	 * Get number of CTA test groups
	 *
	 * @since 1.1.1
	 *
	 * @return int
	 */
	public static function cta(){

		$table_name = group::get_table_name();
		$or = self::cta_type_or();
		$sql = sprintf( 'SELECT COUNT(`ID`) FROM `%s` WHERE `type` = "click" AND %s', $table_name, $or );
		return self::count_query( $sql );

	}

	/**
	 * Get number of destination test groups
	 *
	 * @since 1.1.1
	 *
	 * @return int
	 */
	public static function destination(){
		$table_name = group::get_table_name();
		$sql = sprintf( 'SELECT COUNT(`ID`) FROM `%s` WHERE `type` = "click" AND `sub_type` = "destination"', $table_name );
		return self::count_query( $sql );
	}



}
