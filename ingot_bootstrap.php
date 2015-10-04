<?php
/**
 * Load up the Ingot
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class ingot_bootstrap {

	/**
	 * Loads ingot if not already loaded.
	 *
	 * @since 0.0.5
	 */
	public static function maybe_load() {
		if ( did_action( 'ingot_loaded' ) ) {
			return;
		}

		if ( ! defined( 'INGOT_DEV_MODE' ) ){
			/**
			 * Puts Ingot into dev mode
			 *
			 * Don't use on a live site -- makes API totally open
			 *
			 * @since 0.0.5
			 */
			define( 'INGOT_DEV_MODE', false );
		}

		$load = true;
		if ( ! version_compare( PHP_VERSION, '5.5.0', '>=' ) ) {
			$load = false;
		}

		$autoloader = dirname( __FILE__ ) . '/vendor/autoload.php';
		if ( ! file_exists( $autoloader ) ) {
			$load = false;
		}

		if ( $load ) {
			include_once( $autoloader );
			new ingot\testing\ingot();
			new ingot\ui\make();
			self::maybe_load_api();

			/**
			 * Runs when Ingot has loaded.
			 *
			 * @since 0.0.5
			 *
			 */
			do_action( 'ingot_loaded' );
		}


	}

	protected static function maybe_load_api() {
		if( ! defined( 'REST_API_VERSION' ) ) {
			include_once( INGOT_DIR . '/wp-api/plugin.php' );
		}

	}

}
