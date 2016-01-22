<?php
/**
 * Utility functions for admin
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui;



class admin {

	/**
	 * Get a partial as a string
	 *
	 * @since 1.1.0
	 *
	 * @param string $file File name
	 * @param array $data Array of data to make available in partial. Will be extract()ed
	 *
	 * @return string
	 */
	public static function get_partial( $file, array $data = [] ){
		if( ! empty( $data ) ){
			extract( $data );
		}
		ob_start();
		include( self::partial_path( $file ) );
		return ob_get_clean();
	}

	/**
	 * Get file path for an admin partial
	 *
	 * @since 1.1.0
	 *
	 * @param string $file File name
	 *
	 * @return string
	 */
	public static function partial_path( $file ){
		return INGOT_DIR . '/classes/ui/admin/partials/' . $file;
	}

	/**
	 * Load scripts for use in post editor
	 *
	 * @uses "admin_enqueue_scripts" hook
	 *
	 * @since 1.1.0
	 */
	public static function post_editor_scripts(){
		wp_enqueue_script( 'ingot-post-editor', trailingslashit( INGOT_URL ) . 'assets/admin/js/ingot-post-editor.min.js', array( 'jquery', 'caldera-modals' ), INGOT_VER, true );
		wp_enqueue_script( 'caldera-modals', trailingslashit( INGOT_URL ) . 'vendor/calderawp/caldera-modals/caldera-modals.js', array( 'jquery'), INGOT_VER, true );
		wp_enqueue_style( 'caldera-modals', trailingslashit( INGOT_URL ) . 'vendor/calderawp/caldera-modals/modals.css' );
	}

	/**
	 * Get URL for the Ingot Admin
	 *
	 * NOTE: Not escaped
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public static function get_admin_url(){
		return admin_url( 'admin.php?page=ingot-admin-app#/' );
	}

	/**
	 * Get HTML for admin URL link
	 *
	 * @since 1.1.0
	 *
	 * @param string $text Text for link
	 * @param bool|string $title Optional. Text for link text attribute. Default is "Go to the Ingot admin screen"
	 *
	 * @return string
	 */
	public static function admin_url_html( $text, $title = false ) {
		if( ! $title ){
			$title = __( 'Go to the Ingot admin screen', 'ingot' );
		}


		return sprintf( '<a href="%s" title="%s" target="_blank">%s</a>', esc_url( self::get_admin_url() ), esc_attr( $title ), esc_html( $text ) );
	}

}
