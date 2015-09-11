<?php
/**
 * Boots Up This Plugin
 *
 * @package   Ingot
 * @author    Josh Pollock
 * @license   GPL-2.0+
 * @link      
 * @copyright 2015 Josh Pollock
 */
namespace ingot;
use ingot\testing\ingot;

/**
 * Main plugin class.
 *
 * @package Ingot
 * @author  Josh Pollock
 */
class core {

	/**
	 * The slug for this plugin
	 *
	 * @since 0.0.4
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'ingot';

	/**
	 * Holds class instance
	 *
	 * @since 0.0.4
	 *
	 * @var      object|\ingot\core
	 */
	protected static $instance = null;

	/**
	 * Holds the option screen prefix
	 *
	 * @since 0.0.4
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since 0.0.4
	 *
	 * @access private
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_stylescripts' ) );


		//load settings class in admin
		if ( is_admin() ) {
			new settings();
		}

		//make the ingot testing go.
		new ingot();

	}


	/**
	 * Return an instance of this class.
	 *
	 * @since 0.0.4
	 *
	 * @return    object|\ingot\core    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 0.0.4
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( $this->plugin_slug, false, basename( INGOT_PATH ) . '/languages');

	}


	
	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since 0.0.4
	 *
	 * @return    null
	 */
	public function enqueue_admin_stylescripts() {

		$screen = get_current_screen();

		if( !is_object( $screen ) ){
			return;

		}

		
		if( false !== strpos( $screen->base, 'ingot' ) ){
			wp_enqueue_style( 'ingot-core-style', INGOT_URL . 'assets/css/styles.css' );
			wp_enqueue_style( 'ingot-baldrick-modals', INGOT_URL . 'assets/css/modals.css' );
			wp_enqueue_script( 'ingot-wp-baldrick', INGOT_URL . 'assets/js/wp-baldrick-full.js', array( 'jquery' ) , false, true );
			wp_enqueue_script( 'jquery-ui-autocomplete' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'ingot-core-script', INGOT_URL . 'assets/js/scripts.js', array( 'ingot-wp-baldrick' ) , false );
			wp_enqueue_script( 'ingot-admin', INGOT_URL . 'assets/js/ingot-admin.js', array( 'jquery' ) , false );
		
		}


	}



}















