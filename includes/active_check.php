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

/**
 * Generic checker for ecommerce active check plugins in this file
 *
 * @since 0.0.9
 *
 * @param string $plugin
 *
 * @return bool
 */
function ingot_check_ecommerce_active( $plugin ){
	$func = "ingot_is_{$plugin}_active";
	if ( function_exists( $func ) ) {
		$active = call_user_func( $func );

		return $active;
	}

	return false;
}

/**
 * Check if Easy Digital Downloads is active
 *
 * @since 0.0.9
 *
 * @return bool
 */
function ingot_is_edd_active() {
	return class_exists( 'Easy_Digital_Downloads' );
}

/**
 * Check if WooCommerce is active
 *
 * @since 0.0.9
 *
 * @return bool
 */
function ingot_is_woo_active() {
	return class_exists( 'WooCommerce' );
}

/**
 * Check if Give is active
 *
 * @since 1.1.0
 *
 * @return bool
 */
function ingot_is_give_active(){
	return class_exists( 'Give' );
}
