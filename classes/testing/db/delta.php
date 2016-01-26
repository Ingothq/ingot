<?php
/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\testing\db;


class delta {

	/**
	 * If needed, add the custom table for groups
	 *
	 * @since 1.1.0
	 * @since 0.0.7 in class ingot_boostrap
	 *
	 * @param bool $drop_first Optional. If true, DROP TABLE statement is made first. Default is false
	 */
	public static function maybe_add_group_table( $drop_first = false ) {
		global $wpdb;

		$table_name = \ingot\testing\crud\group::get_table_name();

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
				name VARCHAR(255) NOT NULL,
				type VARCHAR(255) NOT NULL,
				sub_type VARCHAR(255) NOT NULL,
				wp_ID VARCHAR(255) NOT NULL,
				variants LONGTEXT NOT NULL,
				levers LONGTEXT NOT NULL,
				meta LONGTEXT NOT NULL,
				modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				UNIQUE KEY ID (ID)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

	}

	/**
	 * If needed, add the custom table for variants
	 *
	 * @since 1.1.0
	 * @since 0.0.7 in class ingot_boostrap
	 *
	 * @param bool $drop_first Optional. If true, DROP TABLE statement is made first. Default is false
	 */
	public static function maybe_add_variant_table( $drop_first = false ) {
		global $wpdb;

		$table_name = \ingot\testing\crud\variant::get_table_name();

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
				type VARCHAR(255) NOT NULL,
				group_ID mediumint(9) NOT NULL,
				content LONGTEXT NOT NULL,
				meta LONGTEXT NOT NULL,
				modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				UNIQUE KEY ID (ID)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

	}

	/**
	 * If needed, add the custom table for tracking
	 *
	 * @since 1.1.0
	 * @since 0.0.7 in class ingot_boostrap
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

		if( self::table_exists( $table_name ) ) {
			return;
		}

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
		  ID bigint(9) NOT NULL AUTO_INCREMENT,
		  group_ID mediumint(9) NOT NULL,
		  ingot_ID mediumint(9) NOT NULL,
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
	 * If needed, add the custom table for session
	 *
	 * @since 1.1.0
	 * @since 0.3.0 in class ingot_boostrap
	 *
	 * @param bool $drop_first Optional. If true, DROP TABLE statement is made first. Default is false
	 */
	public static function maybe_add_session_table( $drop_first = false ) {
		global $wpdb;

		$table_name = \ingot\testing\crud\session::get_table_name();

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
		  ID BIGINT NOT NULL AUTO_INCREMENT,
		  used tinyint NOT NULL,
		  IP VARCHAR(255) NOT NULL,
		  uID BIGINT NOT NULL,
		  ingot_ID BIGINT NOT NULL,
		  slug VARCHAR(255) NOT NULL,
		  click_url VARCHAR(255) NOT NULL,
		  click_test_ID BIGINT NOT NULL,
		  created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  UNIQUE KEY ID (ID)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

	}

	/**
	 * Check if table exists
	 *
	 * @since 1.1.0
	 * @since 0.0.7 in class ingot_boostrap
	 *
	 * @param string $table_name
	 *
	 * @return bool
	 */
	public static function table_exists( $table_name ) {
		global $wpdb;
		if( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name  ) {
			return true;

		}else{
			return false;

		}

	}

	/**
	 * Add tables if needed
	 *
	 * @since 1.1.0
	 * @since 0.3.0 in class ingot_boostrap
	 */
	public static function add_tables() {
		self::maybe_add_tracking_table();
		self::maybe_add_session_table();
		self::maybe_add_group_table();
		self::maybe_add_variant_table();
		self::check_if_tables_exist();
	}

	/**
	 * Check if all tables exists
	 *
	 * @since 1.1.0
	 * @since 0.3.0 in class ingot_boostrap
	 *
	 * @access protected
	 *
	 * @return bool
	 */
	public static function check_if_tables_exist() {
		if ( ! self::table_exists( \ingot\testing\crud\tracking::get_table_name() )
		     || ! self::table_exists( \ingot\testing\crud\group::get_table_name() )
		     || ! self::table_exists( \ingot\testing\crud\session::get_table_name() )
		     || ! self::table_exists( \ingot\testing\crud\variant::get_table_name() )
		) {
			return false;


		}

		return true;
	}

}
