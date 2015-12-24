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

namespace ingot;


class permissions {

	public static function get_for( $what, $context = 'create' ) {
		if ( in_array( $what, array( 'groups', 'variants', 'tracking', 'session', 'products' ) ) ) {
			return apply_filters( 'ingot_permissions', 'manage_options', $what, $context );
		}
	}

}
