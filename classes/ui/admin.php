<?php
/**
 * Base class for admin
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui;

use ingot\testing\crud\settings;


abstract class admin {

	/**
	 * Verify admin ajax nonce
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @param bool|true $get
	 *
	 * @return bool|false|int
	 */
	protected function check_nonce( $get = true ) {
		if( $get && isset( $_GET[ '_nonce' ] ) ) {
			$nonce = $_GET[ '_nonce' ];
		}elseif( ! $get && isset( $_POST[ '_nonce' ] ) ) {
			$nonce = $_POST[ '_nonce' ];
		}else{
			return false;
		}

		$verify = wp_verify_nonce( $nonce );
		return $verify;

	}

	/**
	 * Get path to partials dir
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return string
	 */
	protected function partials_dir_path() {
		return dirname( __FILE__ ) . '/admin/partials/';
	}

	/**
	 * Edit link for CLICK groups
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @param bool|false $id
	 *
	 * @return string
	 */
	protected function click_group_edit_link( $id = false ) {
		if ( false === $id || 0 == absint( $id ) ){
			$id = 'new';
		}

		$page = $this->click_group_admin_page_link();

		$link = add_query_arg( 'group', $id, $page );
		return $link;
	}

	/**
	 * Main CLICK admin page link
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @param int $page_number
	 *
	 * @return string
	 */
	protected function click_group_admin_page_link( $page_number = 1 ) {
		$args = array(
			'page_number' => absint( $page_number ),
			'group' => 'list'
		);

		return add_query_arg( $args, $this->main_page_link() );

	}

	/**
	 * Stats page link
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @param $group_id
	 *
	 * @return string
	 */
	protected function stats_page_link( $group_id ) {
		$args = array(
			'group_id' => $group_id,
			'stats' => true
		);

		return add_query_arg( $args, $this->click_group_admin_page_link( ) );

	}

	/**
	 * Link for main Ingot page
	 *
	 * @since 0.0.9
	 *
	 * @return string
	 */
	protected function main_page_link(){
		$args = array(
			'page' => 'ingot',
			'_nonce' => wp_create_nonce(),
		);

		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}

	/**
	 * Get HTML for settings form
	 *
	 * @since 0.0.9
	 *
	 * @return string
	 */
	protected function get_settings_form() {
		$settings_class = new \ingot\ui\admin\settings( settings::get_settings_keys() );
		$settings_form  = $settings_class->get_form();
		return $settings_form;

	}

}
