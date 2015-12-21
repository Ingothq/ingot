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


use ingot\testing\api\rest\groups;
use ingot\testing\api\rest\price_test;
use ingot\testing\api\rest\price_test_group;
use ingot\testing\api\rest\products;
use ingot\testing\api\rest\test;
use ingot\testing\api\rest\test_group;
use ingot\testing\api\rest\util;
use ingot\testing\api\rest\variant;
use ingot\testing\crud\group;
use ingot\testing\crud\price_group;
use ingot\testing\crud\sequence;
use ingot\testing\crud\session;
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

		add_action( 'pre_update_option', array( $this, 'presave_settings' ), 10, 2  );

		add_action( 'parse_request', array( $this, 'init_session' ), 50 );
	}

	/**
	 * Load the REST API
	 *
	 * @uses "rest_api_init"
	 */
	public static function boot_rest_api() {
		if ( ! did_action( 'ingot_rest_api_booted' ) ) {
			$group = new groups();
			$group->register_routes();
			$variant = new variant();
			$variant->register_routes();
			$settings = new \ingot\testing\api\rest\settings();
			$settings->register_routes();
			$products = new products();
			$products->register_routes();
			$session = new \ingot\testing\api\rest\session();
			$session->register_routes();

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
		$keys = settings::get_key_names();
		if( in_array( $option, $keys ) ) {
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
			'nonce' => wp_create_nonce( 'wp_rest' ),
			'session_nonce' => wp_create_nonce( 'ingot_session' ),
			'session' => \ingot\testing\object\session::instance()->get_session_info()
		);

		return $vars;
	}

	/**
	 * Inititialize Ingot session
	 *
	 * @uses "parse_request"
	 *
	 * @since 0.3.0
	 */
	public function init_session(){
		$id = null;
		if( isset( $_GET[ 'ingot_session_ID' ] ) && ingot_verify_session_nonce( helpers::v( 'ingot_session_nonce', $_GET, '' ) ) ) {
			$id = helpers::v( 'ingot_session_ID', $_GET, null );
		}

		$session = \ingot\testing\object\session::instance( $id );
		$session_data = $session->get_session_info();
		do_action( 'ingot_session_initialized', $session_data );
	}



}


