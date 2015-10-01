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
		//$api_class =  "\\ingot\\testing\\api\\temp";
		//add_action( 'wp_ajax_ingot_click_test', array( $api_class, 'record_click_text' ) );
		//add_action( 'wp_ajax_nopriv_ingot_click_test', array( $api_class, 'record_click_text' ) );
		add_action( 'wp_enqueue_scripts', function() {
			wp_enqueue_script( 'ingot', plugin_dir_url( __FILE__ ) . '/assets/js/ingot.js', array( 'jquery'), INGOT_VER, true );
			wp_localize_script( 'ingot', 'INGOT', ingot::js_vars() );
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

		$updated = sequence::save( $sequence, $sequence_id, true );
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

	public static function js_vars() {
		$vars = array(
			'api_url' => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
			'nonce' => wp_create_nonce( 'ingot_nonce' )
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

	}




}
