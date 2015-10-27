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
				echo $this->make_group_page( $id );
			}else{
				echo $this->new_group_page();
			}
		}else{
			status_header( 500 );
		}
		status_header( 200 );
		exit;
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
		$group = price_group::read( $id );
		$price_tests = $this->get_tests_by_group( $group );
		ob_start();
		$group = price_group::read( $id );
		$back_link = $this->price_group_admin_page_link();
		$stats_link = $this->stats_page_link( $id );

		if( ! is_array( $group ) ) {
			ob_flush();
			status_header( 500 );
			wp_die(  __( 'Invalid Group', 'ingot' ) );
		}



		include_once ( $this->partials_dir_path() . 'price-test-group.php' );
		$out = ob_get_clean();
		return $out;
	}


	/**
	 * Get all tests in a group
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @param $group
	 *
	 * @return string
	 */
	protected  function get_tests_by_group( $group ){
		$tests = price_test::get_items( array( 'ids' => $group[ 'test_order' ] ) );

		$out = '';
		if( ! empty( $tests ) ){

			foreach( $tests as $test ){
				ob_start();
				$id = $test[ 'ID' ];
				echo '<div id="' . esc_attr( $id ) . '" class="price-test">';
				echo '<p><pre>' . esc_attr( $id ) . '</pre></p>';
				include INGOT_UI_PARTIALS_DIR . 'price-test-a-b.php' ;
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
		$groups_inner_html = $this->group_list_html( $page_number );
		ob_start();
		$groups_inner_html = $groups_inner_html;
		$settings_form = $this->get_settings_form();
		$next_button = false;
		$prev_button = false;
		$main_page_link = $this->main_page_link();
		$new_link = $this->price_group_edit_link( 0 );
		include_once ( $this->partials_dir_path() . 'price-test-group-list.php' );
		$html = ob_get_clean();
		return $html;
	}

	protected function group_list_html( $page ) {
		$groups = $this->get_groups( $page );
		if ( ! empty( $groups ) ) {
			$out = '';
			foreach ( $groups as $group ) {
				$out .= $this->group_input( $group );
			}

			return $out;
		}

	}

	/**
	 * Get HTML for group input group
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @return string
	 */
	protected function group_input( $group ) {
		ob_start();
		$id = $group[ 'ID' ];
		$edit_link = $this->price_group_edit_link( (int) $id );
		$stats_link = $this->stats_page_link( $id );
		include_once( INGOT_UI_PARTIALS_DIR . 'price-test-group-preview.php' );
		$html = ob_get_clean();
		return $html;
	}

	protected function get_groups( $page ) {
		return price_group::get_items( array(
			'limit' => 10,
			'page' => $page,
			'get_current' => false
		));
	}

}
