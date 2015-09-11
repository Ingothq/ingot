<?php
/**
 * Ingot Setting.
 *
 * @package   Ingot
 * @author    Josh Pollock
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
namespace ingot;

/**
 * Settings class
 * @package Ingot
 * @author  Josh Pollock
 */
class settings extends core{


	/**
	 * Constructor for class
	 *
	 * @since 0.0.4
	 */
	public function __construct(){

		// add admin page
		add_action( 'admin_menu', array( $this, 'add_settings_pages' ), 25 );

		// save config
		add_action( 'wp_ajax_ingot_save_config', array( $this, 'save_config') );

		// exporter
		add_action( 'init', array( $this, 'check_exporter' ) );

				// create new
		add_action( 'wp_ajax_ingot_create_ingot', array( $this, 'create_new_ingot') );

		// delete
		add_action( 'wp_ajax_ingot_delete_ingot', array( $this, 'delete_ingot') );


	}

	/**
	 * builds an export
	 *
	 * @uses "wp_ajax_ingot_check_exporter" hook
	 *
	 * @since 0.0.1
	 */
	public function check_exporter(){

		if( current_user_can( 'manage_options' ) ){

			if( !empty( $_REQUEST['download'] ) && !empty( $_REQUEST['ingot-export'] ) && wp_verify_nonce( $_REQUEST['ingot-export'], 'ingot' ) ){

				$data = options::get_single( $_REQUEST['download'] );

				header( 'Content-Type: application/json' );
				header( 'Content-Disposition: attachment; filename="ingot-export.json"' );
				echo wp_json_encode( $data );
				exit;

			}
			
		}
	}

	/**
	 * Saves a config
	 *
	 * @uses "wp_ajax_ingot_save_config" hook
	 *
	 * @since 0.0.1
	 */
	public function save_config(){

		$can = options::can();
		if ( ! $can ) {
			status_header( 500 );
			wp_die( __( 'Access denied', 'ingot' ) );
		}

		if( empty( $_POST[ 'ingot-setup' ] ) || ! wp_verify_nonce( $_POST[ 'ingot-setup' ], 'ingot' ) ){
			if( empty( $_POST['config'] ) ){
				return;

			}

		}

		if( ! empty( $_POST[ 'ingot-setup' ] ) && empty( $_POST[ 'config' ] ) ){
			$config = stripslashes_deep( $_POST['config'] );

			options::update( $config );


			wp_redirect( '?page=ingot&updated=true' );
			exit;

		}

		if( ! empty( $_POST[ 'config' ] ) ){

			$config = json_decode( stripslashes_deep( $_POST[ 'config' ] ), true );

			if(	wp_verify_nonce( $config['ingot-setup'], 'ingot' ) ){
				$save = options::update( $config );
				wp_send_json_success( $config );

			}

		}

		// nope
		wp_send_json_error( $config );

	}

	/**
	 * Array of "internal" fields not to mess with
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function internal_config_fields() {
		return array( '_wp_http_referer', 'id', '_current_tab' );

	}


	/**
	 * Deletes an item
	 *
	 * @uses 'wp_ajax_ingot_create_ingot' action
	 *
	 * @since 0.0.1
	 */
	public function delete_ingot(){
		$can = options::can();
		if ( ! $can ) {
			status_header( 500 );
			wp_die( __( 'Access denied', 'ingot' ) );
		}

		$deleted = options::delete( strip_tags( $_POST[ 'block' ] ) );

		if ( $deleted ) {
			wp_send_json_success( $_POST );
		}else{
			wp_send_json_error( $_POST );
		}

	}

	/**
	 * Create a new item
	 *
	 * @uses "wp_ajax_ingot_create_ingot"  action
	 *
	 * @since 0.0.1
	 */
	public function create_new_ingot(){

		$can = options::can();
		if ( ! $can ) {
			status_header( 500 );
			wp_die( __( 'Access denied', 'ingot' ) );
		}


		if( !empty( $_POST['import'] ) ){
			$config = json_decode( stripslashes_deep( $_POST[ 'import' ] ), true );

			if( empty( $config['name'] ) || empty( $config['slug'] ) ){
				wp_send_json_error( $_POST );
			}
			$id = null;
			if( !empty( $config['id'] ) ){
				$id = $config['id'];
			}
			options::create( $config[ 'name' ], $config[ 'slug' ], $id );
			options::update( $config );
			wp_send_json_success( $config );
		}

		$new = options::create( $_POST[ 'name' ], $_POST[ 'slug' ] );

		if ( is_array( $new ) ) {
			wp_send_json_success( $new );

		}else {
			wp_send_json_error( $_POST );

		}

	}


	/**
	 * Add options page
	 *
	 * @since 0.0.4
	 *
	 * @uses "admin_menu" hook
	 */
	public function add_settings_pages(){
		// This page will be under "Settings"
		$this->plugin_screen_hook_suffix['ingot'] =  add_menu_page(
			__( 'Ingot', $this->plugin_slug ),
			__( 'Ingot', $this->plugin_slug )
			, 'manage_options', 'ingot',
			array( $this, 'create_admin_page' ),
			'dashicons-smiley'
		);
		$this->plugin_screen_hook_suffix['ingot-ingot'] =  add_submenu_page(
			'ingot',
			__( 'Ingot - Ingot', $this->plugin_slug ),
			__( 'Ingot', $this->plugin_slug ),
			'manage_options',
			'ingot-ingot',
			array( $this, 'create_admin_page' )
		);

		add_action( 'admin_print_styles-' . $this->plugin_screen_hook_suffix['ingot'], array( $this, 'enqueue_admin_stylescripts' ) );

	}

	/**
	 * Options page callback
	 *
	 * @since 0.0.4
	 */
	public function create_admin_page(){
		// Set class property        
		$screen = get_current_screen();
		$base = array_search($screen->id, $this->plugin_screen_hook_suffix);
			
		// include main template
		if( false !== strpos( $base, 'ingot-' ) ){
			
			$file_base = substr( $base, strlen( 'ingot-' ) );
			if( file_exists( INGOT_PATH . 'includes/' . $file_base . '.php' ) ){
				include INGOT_PATH . 'includes/' . $file_base . '.php';
			}

		}else{
			// include main template
			if( empty( $_GET['edit'] ) ){
				include INGOT_PATH . 'includes/admin.php';
			}else{
				include INGOT_PATH . 'includes/edit.php';
			}
		}

		// php based script include
		if( file_exists( INGOT_PATH .'assets/js/inline-scripts.php' ) ){
			echo "<script type=\"text/javascript\">\r\n";
			include INGOT_PATH .'assets/js/inline-scripts.php';
			echo "</script>\r\n";
		}

	}



	
}

