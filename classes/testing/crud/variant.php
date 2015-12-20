<?php
/**
 * CRUD for variants
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\crud;


class variant extends crud {

	public static $what = 'variant';

	public static function get_items( $param ){}

	public static function needed(){}

	public static function required(){}

	public static function validate_config( $data ){}

	protected static function fill_in( $data ){}

}
