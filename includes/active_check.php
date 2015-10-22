<?php
/**
 * Checks for if an eCommerce plugin is active
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */


function ingot_is_edd_active() {
	return class_exists( 'Easy_Digital_Downloads' );
}

function ingot_is_woo_active() {
	return class_exists( 'WooCommerce' );
}
