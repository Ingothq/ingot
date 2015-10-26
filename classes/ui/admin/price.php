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


use ingot\testing\crud\price_group;
use ingot\testing\crud\price_test;
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
			$id = helpers::v_sanitized( 'group_id', $_GET, 0 );
			if ( 0 < $id ) {
				return $this->make_group_page( $id );
			}else{
				return $this->new_group_page();
			}
		}else{
			status_header( 500 );
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
		ob_start();
		$group = price_group::read( $id );
		$back_link = $this->price_group_admin_page_link();
		$stats_link = $this->stats_page_link( $id );
		$group = price_group::read( $id );
		if( ! is_array( $group ) ) {
			ob_flush();
			status_header( 500 );
			wp_die(  __( 'Invalid Group', 'ingot' ) );
		}

		$price_tests = $this->get_tests( $group );

		include_once ( $this->partials_dir_path() . 'price-test-group.php' );
		$out = ob_get_clean();
		return $out;
	}

	protected  function get_tests( $group ){
		$tests = price_test::get_items( array( 'ids' => $group[ 'test_order' ] ) );

		$out = '';
		if( ! empty( $tests ) ){

			foreach( $tests as $test ){
				ob_start();
				$id = $test[ 'ID' ];
				echo '<div id="' . esc_attr( $id ). '" class="price-test">';
				include_once( INGOT_UI_PARTIALS_DIR . 'price-test-a-b.php' );
				echo '</div>';
				echo '<!--/' . $id . '-->';
				$out .= ob_get_clean();
			}
		}

		return $out;



	}

	protected function new_group_page(){
		ob_start();
		$back_link = $this->price_group_admin_page_link();
		include_once ( $this->partials_dir_path() . 'price-test-group-new.php' );
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
		include_once ( $this->partials_dir_path() . 'price-test-group-list.php' );
		return ob_get_clean();
	}

}
