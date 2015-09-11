<?php
/**
 * Loads the plugin if dependencies are met.
 *
 * @package   Ingot
 * @author    Josh Pollock
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Polloc
 */


if ( file_exists( INGOT_PATH . 'vendor/autoload.php' ) ){
	//autoload dependencies
	require_once( INGOT_PATH . 'vendor/autoload.php' );

	// initialize plugin
	ingot\core::get_instance();

}else{
	return new WP_Error( 'ingot--no-dependencies', __( 'Dependencies for Ingot could not be found.', 'ingot' ) );
}


