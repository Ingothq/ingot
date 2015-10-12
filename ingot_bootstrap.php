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
			add_filter( 'ingot_force_update_table', '__return_true' );
			if (  ! did_action( 'ingot_loaded') ) {
				include_once( $autoloader );

				self::maybe_add_sequence_table();
				self::maybe_add_tracking_table();
				if( ! self::table_exists( \ingot\testing\crud\sequence::get_table_name() ) || ! self::table_exists( \ingot\testing\crud\tracking::get_table_name() ) ) {
					if( is_admin() ) {
						printf( '<div class="error"><p>%s</p></div>', __( 'Ingot Not Loaded', 'ingot' ) );
					}

					return;
				}


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
	public static function maybe_add_sequence_table( $drop_first = false ) {
		global $wpdb;

		$table_name = \ingot\testing\crud\sequence::get_table_name();

		if( $drop_first  ) {
			if( self::table_exists( $table_name )  ) {
				$wpdb->query( "DROP TABLE $table_name" );
			}

		}

		if(  self::table_exists( $table_name ) ) {
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

	/**
	 * If needed, add the custom table for sequences
	 *
	 * @since 0.0.7
	 *
	 * @param bool $drop_first Optional. If true, DROP TABLE statemnt is made first. Default is false
	 */
	public static function maybe_add_tracking_table( $drop_first = false ) {
		global $wpdb;

		$table_name = \ingot\testing\crud\tracking::get_table_name();

		if( $drop_first  ) {
			if( self::table_exists( $table_name )  ) {
				$wpdb->query( "DROP TABLE $table_name" );
			}

		}

		if(  self::table_exists( $table_name ) ) {
			return;
		}

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
		  ID bigint(9) NOT NULL AUTO_INCREMENT,
		  group_ID mediumint(9) NOT NULL,
		  sequence_ID mediumint(9) NOT NULL,
		  test_ID mediumint(9) NOT NULL,
		  IP VARCHAR(255) NOT NULL,
		  UTM LONGTEXT NOT NULL,
		  referrer VARCHAR(255) NOT NULL,
		  browser VARCHAR(255) NOT NULL,
		  meta LONGTEXT NOT NULL,
		  user_agent LONGTEXT NOT NULL,
		  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  UNIQUE KEY ID (ID)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

	}

	/**
	 * Check if table exists
	 *
	 * @since 0.0.7
	 *
	 * @param string $table_name
	 *
	 * @return bool
	 */
	protected static function table_exists( $table_name ) {
		global $wpdb;
		if( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name  ) {
			return true;

		}else{
			return false;

		}

	}

}