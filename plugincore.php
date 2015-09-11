<?php
/**
 * @package   Ingot
 * @author    Josh Pollock
 * @license   GPL-2.0+
 * @link      
 * @copyright 2015 Josh Polloc
 *
 * @wordpress-plugin
 * Plugin Name: Ingot
 * Plugin URI:  http://CalderaWP.com
 * Description: I am a desciption
 * Version:     0.0.4
 * Author:      Josh Pollock
 * Author URI:  http://Josh Pollock
 * Text Domain: ingot
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('INGOT_PATH',  plugin_dir_path( __FILE__ ) );
define('INGOT_CORE',  __FILE__ );
define('INGOT_URL',  plugin_dir_url( __FILE__ ) );
define('INGOT_VER',  '0.0.4' );



// Load instance
add_action( 'plugins_loaded', 'ingot_bootstrap' );
function ingot_bootstrap(){




	if ( ! version_compare( PHP_VERSION, '5.3.0', '>=' ) ) {
		if ( is_admin() ) {
			//BIG nope nope nope!
			$message = __( sprintf( 'Ingot requires PHP version %1s or later. We strongly recommend PHP 5.5 or later for security and performance reasons. Current version is %2s.', '5.3.0', PHP_VERSION ), 'ingot' );
			echo caldera_warnings_dismissible_notice( $message, true, 'activate_plugins' );
		}

	}else{
		//bootstrap plugin
		require_once( INGOT_PATH . 'bootstrap.php' );

	}

}
