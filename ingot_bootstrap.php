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
			self::maybe_add_sequence_table();
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

	/**
	 * If not installed as a plugin include our bundled REST API
	 *
	 * @since 0.0.6
	 */
	protected static function maybe_load_api() {
		if( ! defined( 'REST_API_VERSION' ) ) {
			include_once( INGOT_DIR . '/wp-api/plugin.php' );
		}

	}

	/**
	 * If needed, add the custom table for sequences
	 *
	 * @since 0.0.7
	 */
	protected static function maybe_add_sequence_table( $drop_first = false ) {
		global $wpdb;


		$table_name = \ingot\testing\crud\sequence::get_table_name();

		if( apply_filters( 'ingot_force_update_table', $drop_first = true ) ) {
			$wpdb->query( "DROP TABLE $table_name" );

		}elseif( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name) {
				return;
		}

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
		  ID mediumint(9) NOT NULL AUTO_INCREMENT,
		  a_id mediumint(9) NOT NULL,
		  b_id mediumint(9) NOT NULL,
	   	  test_type VARCHAR( 255 ) NOT NULL,
		  a_win mediumint(9) NOT NULL,
		  b_win mediumint(9) NOT NULL,
		  a_total mediumint(9) NOT NULL,
		  b_total mediumint(9) NOT NULL,
		  initial mediumint(9) NOT NULL,
		  threshold mediumint(9) NOT NULL,
		  created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  modified  datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  completed  tinyint(1) NOT NULL,
		  group_ID mediumint(9) NOT NULL,

		  UNIQUE KEY id (id)
		) $charset_collate;";



		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

	}

}
