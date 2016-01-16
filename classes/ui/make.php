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


use ingot\ui\admin\app\load;


class make {

	/**
	 * Make UI go
	 *
	 * @since 0.0.6
	 */
	public function __construct() {
		//admin app
		new load();

		//shortcode inserter
		$this->shortcode_hooks();

		//post editor scripts


	}

	/**
	 * Hooks for adding the shortcode button
	 *
	 * @since 1.1.0
	 */
	public function shortcode_hooks() {
		add_action( 'media_buttons', array( 'ingot\ui\admin\post\shortcode_inserter', 'button' ), 11 );
		add_action( 'admin_footer', array( 'ingot\ui\admin\post\shortcode_inserter', 'modal' ) );
		add_action( 'admin_enqueue_scripts', array( 'ingot\ui\admin', 'post_editor_scripts' ) );
	}

}
