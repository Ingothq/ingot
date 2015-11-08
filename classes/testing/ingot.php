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


use ingot\testing\api\rest\price_test;
use ingot\testing\api\rest\price_test_group;
use ingot\testing\api\rest\test;
use ingot\testing\api\rest\test_group;
use ingot\testing\api\rest\util;
use ingot\testing\crud\group;
use ingot\testing\crud\price_group;
use ingot\testing\crud\sequence;
use ingot\testing\crud\settings;
use ingot\testing\crud\tracking;
use ingot\testing\tests\click\click;
use ingot\testing\tests\flow;
use ingot\testing\utility\helpers;

class ingot {

	/**
	 * Class instance
	 *
	 * @since 0.2.0
	 *
	 * @var ingot
	 */
	private static $instance;

	/**
	 * Get class instance
	 *
	 * @since 0.2.0
	 *
	 * @return ingot
	 */
	public static function instance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor for class
	 *
	 * @access protected
	 *
	 * @since 0.0.5
	 */
	private function __construct() {
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

		add_action( 'pre_update_option', array( $this, 'presave_settings' ), 10, 2  );
	}

	/**
	 * Load the REST API
	 *
	 * @uses "rest_api_init"
	 */
	public static function boot_rest_api() {
		if ( ! did_action( 'ingot_rest_api_booted' ) ) {
			$test_group = new test_group();
			$test_group->register_routes();
			$test = new test();
			$test->register_routes();
			$sequence = new \ingot\testing\api\rest\sequence();
			$sequence->register_routes();
			$price_test_group = new price_test_group();
			$price_test_group->register_routes();
			$price_test = new price_test();
			$price_test->register_routes();

			/**
			 * Runs after the Ingot REST API is booted up
			 *
			 * @since 0.2.0
			 *
			 * @param string $namespace Namespace of API
			 */
			do_action( 'ingot_rest_api_booted', util::get_namespace() );
		}

	}

	/**
	 * Sanatize Ingot settings
	 *
	 * @since 0.0.8
	 *
	 * @uses "pre_update_option"
	 */
	public function presave_settings( $value, $option ){
		if( in_array( $option, settings::get_key_names()) ) {
			$value = settings::sanatize_setting( $option, $value );
		}

		return $value;

	}

	/**
	 * Data to be localize as INGOT_VARS
	 *
	 * @return array
	 */
	static public function js_vars() {
		$vars = array(
			'api_url' => esc_url_raw( util::get_url() ),
			'nonce' => wp_create_nonce( 'wp_rest' )
		);

		return $vars;
	}

	public  function read_hook( $item, $what ) {
		if ( 'group' == $what ){
			if( empty( $item[ 'sequences'] ) ){
				remove_filter( 'ingot_crud_read', array( $this, 'read_hook' ) );
				\ingot\testing\tests\sequence_progression::make_initial_sequence( $item[ 'ID' ] );
				$group = group::read( $item[ 'ID' ] );
				return $group;


			}
		}elseif( 'tracking' == $what ) {
			foreach( array(
				'meta',
				'UTM'
			) as $key ) {
				$item[ $key ] = maybe_unserialize( $item[ $key ] );
			}

			return $item;
		}elseif( 'price_group' == $what ) {
			foreach( array(
				'sequences',
				'test_order'
			) as $key ) {
				$item[ $key ] = maybe_unserialize( $item[ $key ] );
			}
			return $item;
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
		if( 'group' == $what || 'price_group' == $what ){
			$price = false;
			if( 'price_group' == $what ){
				$price = true;
			}

			\ingot\testing\tests\sequence_progression::make_initial_sequence( $id, $price );

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
