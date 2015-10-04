<?php
/**
 Plugin Name: Ingot
Version: 0.0.6
 */


define( 'INGOT_VER', '0.0.6' );
define( 'INGOT_URL', plugin_dir_url( __FILE__ ) );
include_once( dirname(__FILE__ ) . '/ingot_bootstrap.php' );
add_action( 'plugins_loaded', array( 'ingot_bootstrap', 'maybe_load' ) );
