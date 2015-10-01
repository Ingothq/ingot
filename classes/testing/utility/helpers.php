<?php
/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\utility;


class helpers {


	public static function v( $key, $thing, $default = null ) {
		if ( isset( $thing[ $key ] ) ) {
			return $thing[ $key ];
		}else{
			return $default;
		}

	}

}
