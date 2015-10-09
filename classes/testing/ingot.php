<?php
/**
 * Main class to make Ingot go.
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing;


use ingot\testing\api\rest\test;
use ingot\testing\api\rest\test_group;
use ingot\testing\crud\group;
use ingot\testing\crud\sequence;
use ingot\testing\tests\click\click;


class ingot {

	/**
	 * Constructor for class
	 *
	 * @since 0.0.5
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Add hooks
	 *
	 * @since 0.0.5
	 */
	public function hooks(){
		add_action( 'rest_api_init', array( __CLASS__ , 'boot_rest_api' ) );
		add_action( 'wp_enqueue_scripts', function() {
			$version = INGOT_VER;
			if( WP_DEBUG ) {
				$version = rand();
			}

			wp_enqueue_script( 'ingot', INGOT_URL . '/assets/front-end/js/ingot-click-test.js', array( 'jquery'), $version, true );
			wp_localize_script( 'ingot', 'INGOT_VARS', ingot::js_vars() );
		});

		add_action( 'ingot_crud_created', array( $this, 'create_hook' ), 10, 2 );
		add_action( 'ingot_crud_updated', array( $this, 'update_hook'), 10, 2 );
		add_filter( 'ingot_crud_read', array( $this, 'read_hook' ), 10, 2 );

	}

	/**
	 * Load the REST API
	 *
	 * @uses "rest_api_init"
	 */
	public static function boot_rest_api() {
		$test_group = new test_group();
		$test_group->register_routes();
		$test = new test();
		$test->register_routes();
	}

	/**
	 * Increase total of times test ran in a sequence
	 *
	 * @since 0.0.5
	 *
	 * @param int $test_id      Test ID to increase total times run.
	 * @param int $sequence_id  Sequence ID this is a part of
	 *
	 * @return bool|int
	 */
	public static function increase_total( $test_id, $sequence_id ) {
		$sequence = sequence::read( $sequence_id );
		$is_a = self::is_a( $test_id, $sequence );

		switch( $is_a ) {
			case true === $is_a  :
				$sequence[ 'a_total' ] = $sequence[ 'a_total' ] + 1;
				break;
			case false === $is_a :
				$sequence[ 'b_total' ] = $sequence[ 'b_total' ] + 1;
				break;
			case is_wp_error( $is_a ) :
				return $is_a;
			break;
		}

		$updated =  sequence::update( $sequence, $sequence_id, true );

		return $updated;

	}

	/**
	 * Increase times a victory
	 *
	 * @param $test_id
	 * @param $sequence_id
	 *
	 * @return bool|int
	 */
	public static function increase_victory( $test_id, $sequence_id ) {
		$sequence = sequence::read( $sequence_id );
		$is_a = self::is_a( $test_id, $sequence );
		switch( $is_a  ) {
			case true === $is_a  :
				$sequence[ 'a_win' ] = $sequence[ 'a_win' ] + 1;
				break;
			case false === $is_a :
				$sequence[ 'b_win' ] = $sequence[ 'b_win' ] + 1;
				break;
		}

		$updated = sequence::update( $sequence, $sequence_id, true );
		return $updated;

	}

	public static function is_a( $test_id, $sequence ) {
		if ( $test_id == $sequence[ 'a_id' ] ){
			return true;

		}elseif ( $test_id == $sequence[ 'b_id' ] ) {
			return false;

		}else{
			return new \WP_Error( 'ingot-test-squence-mismatch' );
		}

	}



	public static function choose_a( $a_chance ) {
		$val = rand( 1, 100 );
		if ( $val <= $a_chance ) {
			return true;

		}
	}

	/**
	 * Data to be localize as INGOT_VARS
	 *
	 * @return array
	 */
	static public function js_vars() {
		$vars = array(
			'api_url' => esc_url_raw( self::ingot_api_url() ),
			'nonce' => wp_create_nonce( 'wp_rest' )
		);

		return $vars;
	}

	public  function read_hook( $item, $what ) {
		if ( 'group' == $what ){
			if( empty( $item[ 'sequences'] ) ){
				remove_filter( 'ingot_crud_read', array( $this, 'read_hook' ) );
				click::make_initial_sequence( $item[ 'ID' ] );
				$group = group::read( $item[ 'ID' ] );
				return $group;


			}
		}

		return $item;
	}
	/**
	 * Routes post create hook
	 *
	 * @uses "ingot_crud_created"
	 *
	 * @since 0.0.5
	 *
	 * @param int $id Item ID
	 * @param string $what Item type
	 */
	public  function create_hook( $id, $what){
		if( 'group' == $what ){
			click::make_initial_sequence( $id );

		}
	}

	/**
	 * Routes post update hook
	 *
	 * @uses "ingot_crud_updated"
	 *
	 * @since 0.0.5
	 *
	 * @param int $id Item ID
	 * @param string $what Item type
	 */
	public  function update_hook( $id, $what){
		if( 'sequence' == $what ) {

		}
	}

	/**
	 * Get the URL for the INGOT REST API
	 *
	 * @since 0.0.6
	 *
	 * @return string
	 */
	static public function ingot_api_url() {
		return trailingslashit( rest_url( 'ingot/v1' ) );
	}




}
