<?php
/**
 * Admin screen for price tests
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui\admin;


use ingot\testing\utility\helpers;
use ingot\ui\admin;

class price extends admin {

	/**
	 * Create a group page
	 *
	 * @since 0.0.9
	 *
	 * @return string
	 */
	public function group_page() {
		if( $this->check_nonce() ) {
			$id = helpers::v_sanitized( 'price_id', $_GET, 0 );
			return $this->make_group_page( $id );
		}
	}

	/**
	 * Create a list page
	 *
	 * @return string
	 */
	public function list_page() {
		if( $this->check_nonce() ) {
			return $this->make_list_page( helpers::v_sanitized( 'page_number', $_GET, 1 ) );
		}
	}

	/**
	 * Make the group page
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @param int $id ID of group. Use 0 for a new group.
	 *
	 * @return string
	 */
	protected function make_group_page( $id ) {
		ob_get_clean();
		

		include_once ( $this->partials_dir_path() . 'price-test.php' );
		return ob_get_clean();

	}

	/**
	 * Make the group list page
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @param int $page_number Page number
	 *
	 * @return string
	 */
	protected function make_list_page( $page_number ) {
		ob_get_clean();
		$settings_form = $this->get_settings_form();
		$next_button = false;
		$prev_button = false;
		$main_page_link = $this->main_page_link();
		$new_link = $this->price_group_edit_link( 0 );
		$groups_inner_html = false;
		include_once ( $this->partials_dir_path() . 'price-test-list.php' );
		return ob_get_clean();
	}

}
