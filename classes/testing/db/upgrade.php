<?php
/**
 * Upgrader for Ingot DB
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\testing\db;


use ingot\testing\crud\group;

class upgrade {

	/**
	 * Current version
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Construct and go
	 *
	 * @since 1.1.0
	 *
	 * @param string $version Current version
	 */
	public function __construct( $version ){
		$this->version = $version;
	}

	/**
	 * Run the upgrade process if needed
	 *
	 * @since 1.1.0
	 */
	public function run(){
		$this->dump_prebeta();
		if( $this->before( '1.1.0' ) ){
			$this->add_wp_id_column();
		}

	}

	/**
	 * Check if specified version is before current version or equal
	 *
	 * @since 1.1.0
	 *
	 * @param $version
	 *
	 * @return bool
	 */
	public function before( $version ){
		if( $version === $this->version ) {
			return true;
		}
		if ( version_compare( $this->version, $version, '>' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Add wp_ID column to the groups table
	 *
	 * @since 1.1.0
	 */
	public function add_wp_id_column(){
		global $wpdb;
		$table_name = group::get_table_name();
		$wpdb->query( sprintf( "ALTER TABLE %s ADD COLUMN wp_ID VARCHAR(255) NOT NULL", $table_name ) );
	}

	/**
	 * Dump pre-beta tables
	 *
	 * @access protected
	 *
	 * @since 1.0.1
	 */
	protected function dump_prebeta(){
		global $wpdb;
		$table_name = $wpdb->prefix . '_ingot_sequence';
		if( delta::table_exists( $table_name ) ) {
			ingot_destroy();
		}

	}

}
