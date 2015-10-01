<?php
/**
 Plugin Name: INGOT?
 */

define( 'INGOT_VER', '0.0.5' );
define( 'INGOT_URL', plugin_dir_url( __FILE__ ) );
include_once( dirname(__FILE__ ) . '/ingot_bootstrap.php' );
add_action( 'plugins_loaded', array( 'ingot_bootstrap', 'maybe_load' ) );
