<?php
/**
 *Load admin screens
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */


namespace ingot\ui;


use ingot\ui\admin\screens;

class make {

	/**
	 * An instance of the screens class
	 *
	 * @since 0.0.6
	 *
	 * @var \ingot\ui\admin\screens
	 */
	private $screens;

	/**
	 * @since 0.0.6
	 */
	public function __construct() {
		if ( is_admin() ) {
			$this->screens = new screens();
			$this->make_screens();
			add_action( 'init', array( $this, 'add_admin_nonce' ) );
		}
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
	}

	/**
	 * Add admin menu
	 *
	 * @access protected
	 *
	 * @since 0.0.6
	 */
	protected function make_screens() {
		add_action( 'admin_menu', array( $this->screens, 'add_menu'  ) );
	}

	/**
	 * Ensure we have a nonce set in the admin page URL
	 *
	 * @uses "init"
	 *
	 * @since 0.0.9
	 */
	public function add_admin_nonce() {
		if( isset( $_GET[ 'page' ] ) && 'ingot' == $_GET[ 'page' ] && ! isset( $_GET[ '_nonce' ] ) ) {
			wp_safe_redirect( add_query_arg( '_nonce', wp_create_nonce(), ingot_current_url() ) );
			exit;
		}

	}

	/**
	 * Register admin scripts
	 *
	 * @since 0.0.6
	 */
	public function register_scripts() {

		wp_register_style( 'ingot-click-test', trailingslashit( INGOT_URL ) . 'assets/front-end/css/ingot-click-tests.css' );
		wp_register_script( 'ingot-click-test', trailingslashit( INGOT_URL ) . 'assets/front-ends/js/ingot-click-tests.js', array( 'jquery' ) );
		wp_localize_script( 'ingot-click-test', 'INGOT', array(
				'api_url' => rest_url( 'ingot/v1'),
				'nonce' => wp_create_nonce( 'wp_rest' ),

			)
		);
	}

}
