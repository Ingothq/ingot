<?php
/**
 * UI elements and a ton of other shit for admin that needs broken up into other classes.
 *
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui\admin;


use ingot\testing\crud\group;
use ingot\testing\crud\sequence;
use ingot\testing\crud\settings;
use ingot\testing\crud\test;
use ingot\testing\tests\click\click;
use ingot\testing\utility\helpers;
use ingot\ui\admin;

class screens extends admin{

	/**
	 * @var \ingot\ui\admin\click
	 */
	private $click_screen_class;

	/**
	 * @since 0.0.6
	 */
	function __construct() {

		if( ingot_is_admin_ajax() ) {

			add_action( 'wp_ajax_test_field_group', array( $this->get_click_screen_class(), 'get_test_field_group' ) );
			add_action( 'wp_ajax_get_click_page', array( $this->get_click_screen_class(), 'get_click_page' ) );
			new \ingot\ui\admin\settings( settings::get_settings_keys() );
		}else{
			add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
			new \ingot\ui\admin\sequence\viewer();
		}
	}

	/**
	 * @return \ingot\ui\admin\click
	 */
	private function get_click_screen_class() {
		if( is_null( $this->click_screen_class ) ){
			$this->click_screen_class = new admin\click();
		}

		return $this->click_screen_class;
	}

	/**
	 * Add menu page
	 */
	public function add_menu() {

		add_menu_page(
			__( 'Ingot', 'ingot'),
			__( 'Ingot', 'ingot'),
			'manage_options',
			'ingot',
			array( $this, 'ingot_page' ),
			'dashicons-smiley',
			40
		);
	}

	/**
	 * Render the ingot admin page, by context
	 *
	 * @since 0.0.5
	 */
	function ingot_page() {

		if ( isset( $_GET['group' ], $_GET[ 'group' ] ) && ! isset( $_GET[ 'stats' ] )  ) {
			if ( 'list' != $_GET[ 'group' ] ) {
				echo $this->get_click_screen_class()->click_group_page();
			}else{
				echo $this->get_click_screen_class()->main_page();
			}
		}elseif( isset( $_GET[ 'stats' ], $_GET[ 'group_id' ]  ) ){
			$viewer = new admin\sequence\viewer( absint( $_GET[ 'group_id' ] ) );
			$html = $viewer->get_view();
			if( ! empty( $html ) ) {
				echo $html;

			}else{
				echo $this->get_click_screen_class()->main_page();
			}
		}elseif( isset( $_GET[ 'price' ] ) && ! isset( $_GET[ 'price_id' ] ) ){

		}elseif( isset( $_GET[ 'price' ], $_GET[ 'price_id' ] ) ){

		}else{
			echo $this->main_page();
		}

		die();

	}

	/**
	 * Display main ingot page
	 *
	 * @access protected
	 *
	 * @since 0.0.9
	 */
	protected function main_page(){
		$settings_form = $this->get_settings_form();
		$new_click_link = $this->click_group_edit_link();
		$all_click_link = $this->click_group_admin_page_link();
		$new_price_link = false;
		$all_price_link = false;
		ob_start();
		include_once( $this->partials_dir_path() . 'main-page.php' );
		return ob_get_clean();

	}


	/**
	 * Setup admin scripts
	 *
	 * @since 0.0.5
	 */
	public function scripts() {
		if( isset( $_GET[ 'page' ] ) && 'ingot' == $_GET[ 'page' ] ) {
			wp_enqueue_script( 'swal', '//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.0/sweetalert.min.js', array( 'jquery') );
			wp_enqueue_style( 'swal', '//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.0/sweetalert.min.css');
			wp_enqueue_style( 'ingot-click-test-style', INGOT_URL . 'assets/admin/css/click-test-admin.css' );
			wp_enqueue_script( 'ingot-click-test', INGOT_URL . 'assets/admin/js/click-test-admin.js', array( 'jquery', 'swal'), rand() );
			wp_localize_script( 'ingot-click-test', 'INGOT', array(
					'api_url' => rest_url( 'ingot/v1'),
					'test_field' => esc_url_raw( add_query_arg( 'action', 'test_field_group', admin_url( 'admin-ajax.php' ) ) ),
					'admin_ajax' => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
					'nonce' => wp_create_nonce( 'wp_rest' ),
					'test_group_page_title' => __( 'Ingot Test Group: ', 'ingot' ),
					'success' => __( 'Group Saved', 'ingot' ),
					'fail' => __( 'Could Not Save', 'ingot' ),
					'close' => __( 'Close', 'ingot' ),
					'saved' => __( 'Saved Group: ', 'ingot'),
					'cant_remove' => __( 'At this time, you can not remove a test from a group.', 'ingot' ),
					'beta_error_header' => __( 'Beta Limitation Encountered', 'ingot' ),
					'no_stats' => __( 'We do not have a functional stats viewer yet.', 'ingot' ),
					'deleted' => __( 'Test Group Deleted', 'ingot' ),
					'admin_ajax_nonce' => wp_create_nonce(),
					'are_you_sure' => __( 'Are You Sure About That?', 'ingot' ),
					'delete_confirm' => __( 'Deleting all groups is not reverseable or undoable.', 'ingot' ),
					'delete' => __( 'Delete', 'ingot' ),
					'cancel' => __( 'Cancel', 'ignot' ),
					'deleted' => __( 'Deleted', 'ingot' ),
					'canceled' => __( 'Canceled', 'ingot' )



				)
			);
		}



	}


}
