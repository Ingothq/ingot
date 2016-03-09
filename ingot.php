<?php
/**
Plugin Name: Ingot
Version: 1.1.1-b-2
Plugin URI:  http://IngotHQ.com
Description: A/B testing made easy for WordPress
Author:      Ingot LLC
Author URI:  http://IngotHQ.com
Text Domain: ingot
Domain Path: /languages
 */

/**
 * Copyright 2015-2015 Ingot LLC
 *
 * Licensed under the terms of the GNU General Public License version 2 or later
 */

define( 'INGOT_VER', '1.1.1-b-2' );
define( 'INGOT_URL', plugin_dir_url( __FILE__ ) );
define( 'INGOT_DIR', dirname( __FILE__ ) );
define( 'INGOT_UI_PARTIALS_DIR', dirname( __FILE__ ) . '/classes/ui/admin/partials/' );
define( 'INGOT_ROOT', basename( dirname( __FILE__ ) ) );


/**
 * Actions to boot up plugin
 */
add_action( 'plugins_loaded', 'ingot_maybe_load', 0 );
add_action( 'ingot_loaded',  'ingot_edd_sl_init', 1 );
/**
 * Load plugin if possible
 *
 * @since 0.0.0
 */
function ingot_maybe_load() {
	$fail = false;
	if ( ! version_compare( PHP_VERSION, '5.5.0', '>=' ) ) {
		$fail = true;
		if ( is_admin() ) {
			include_once( dirname( __FILE__ ) . '/vendor/calderawp/dismissible-notice/src/functions.php' );
			$message = esc_html__( sprintf( 'Ingot requires PHP version 5.5.0 or later. Current version is %s.', PHP_VERSION ), 'ingot' );
			echo caldera_warnings_dismissible_notice( $message, true, 'activate_plugins' );
		}
		
	}
	global $wp_version;
	if ( ! version_compare( $wp_version, '4.4', '>=' ) ) {
		$fail = true;
		if ( is_admin() ) {
			include_once( dirname( __FILE__ ) . '/vendor/calderawp/dismissible-notice/src/functions.php' );
			$message = esc_html__( sprintf( 'Ingot requires WordPress version 4.4 or later. Current version is %s.', $wp_version ), 'ingot' );
			echo caldera_warnings_dismissible_notice( $message, true, 'activate_plugins' );
		}
		
	}
	if( false == $fail ){

		/**
		 * Runs before Ingot is loaded
		 *
		 * NOTE: Only runs if version checks pass
		 *
		 * @since 1.1.1
		 */
		do_action( 'ingot_before' );

		include_once( dirname(__FILE__ ) . '/ingot_bootstrap.php' );
		add_action( 'plugins_loaded', array( 'ingot_bootstrap', 'maybe_load' ) );
		add_action( 'ingot_loaded', 'ingot_fs' );
		add_action( 'ingot_loaded', function(){
			\ingot\licensing\count::cta();
		});
	}

}
/**
 * EDD Licensing
 *
 * @since 0.3.0
 */
function ingot_edd_sl_init(){
	define( 'INGOT_SL_STORE_URL', 'http://ingothq.com' );
	define( 'INGOT_SL_ITEM_NAME', 'Ingot Plugin: The Automatic A/B Tester' );
	add_action( 'admin_init', 'ingot_sl_plugin_updater', 0 );
	add_action( 'admin_init', 'ingot_sl_register_option' );
	if ( is_admin() && ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
		include( dirname( __FILE__ ) . '/includes/EDD_SL_Plugin_Updater.php' );
	}
}

/**
 * Load Freemius
 *
 * @return \Freemius
 */
function ingot_fs() {
	global $ingot_fs;

	if ( ! isset( $ingot_fs ) ) {

		require_once dirname(__FILE__) . '/vendor/freemius/wordpress-sdk/start.php';
		$args = array(
			'id'                => '210',
			'slug'              => 'ingot',
			'public_key'        => 'pk_e6a19a3508bdb9bdc91a7182c8e0c',
			'is_live'           => false,
			'is_premium'        => true,
			'has_addons'        => true,
			'has_paid_plans'    => true,
			'is_org_compliant'  => false,
			'menu'              => array(
				'slug'       => 'ingot-admin-app',
				'support'    => false,
				'first-path' => 'admin.php?ingot-admin-app',
			),

		);

		if( defined( 'INGOT_FS_SECRET' ) ){
			$args[ 'secret_key' ] = INGOT_FS_SECRET;
		}

		$ingot_fs = fs_dynamic_init( $args );


	}

	return $ingot_fs;

}




/**
 * Load translations
 *
 * @since 1.1.0
 */
add_action( 'plugins_loaded', 'ingot_load_textdomain' );

/**
 * Load plugin textdomain.
 *
 * @since 1.1.0
 */
function ingot_load_textdomain() {
	load_plugin_textdomain( 'ingot', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}

