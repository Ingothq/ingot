<?php
/**
 * Utility function for groups
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\testing\utility;


class group {

	/**
	 * Get group type
	 *
	 * @since 1.1.0
	 *
	 * @param array $group Group config
	 *
	 * @return string|bool Type or false if $group is not valid group config
	 */
	public static function type( array $group ){
		if( \ingot\testing\crud\group::valid( $group ) ){
			return $group[ 'type' ];
		}

	}

	/**
	 * Get group sub type
	 *
	 * @since 1.1.0
	 *
	 * @param array $group Group config
	 *
	 * @return string|bool Type or false if $group is not valid group config
	 */
	public static function sub_type( array $group ){
		if( \ingot\testing\crud\group::valid( $group ) ){
			return $group[ 'sub_type' ];
		}

	}

}
