<?php
/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui;


use ingot\ui\admin\screens;

class make {

	private $screens;

	public function __construct() {
		$this->screens = new screens();
		$this->make_screens();
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
	}

	protected function make_screens() {
		add_action( 'admin_menu', array( $this->screens, 'add_menu'  ) );
	}

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
