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

namespace ingot\testing;


class options {

	public static function track_click_details() {

		return apply_filters( 'ingot_track_click_details', get_option( 'ingot_track_click_details', true ) );

	}

}
