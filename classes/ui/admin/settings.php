<?php
/**
 * Save and read Ingot options
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui\admin;


use ingot\ui\admin;

class settings extends admin {

	/**
	 * Settings keys
	 *
	 * @since 0.0.8
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $settings_keys;

	/**
	 * Constructor the class
	 *
	 * @since 0.0.8
	 *
	 * @param array $settings_keys
	 */
	public function __construct( $settings_keys ) {
		$this->settings_keys = $settings_keys;
		if ( ingot_is_admin_ajax()  ) {
			add_action( 'wp_ajax_ingot_settings', array( $this, 'save' ) );
		}
	}

	/**
	 * Save via AJAX
	 *
	 * @uses "wp_ajax_ingot_settings"
	 *
	 * @since 0.0.8
	 */
	public function save() {
		if ( $this->check_nonce( false ) ) {
			$updated = $this->update();
			if( $updated ) {
				status_header( 200 );
			}else{
				status_header( 500 );
			}

		}else{
			status_header( 403 );
		}

		exit;

	}

	/**
	 * Return HTML for the settings form
	 *
	 * @since 0.0.8
	 *
	 * @return string
	 */
	public function  get_form() {
		return $this->form();
	}


	/**
	 * Create form
	 *
	 * @since 0.0.8
	 *
	 * @access protected
	 *
	 * @return string
	 */
	protected function form() {
		$click_tracking = $anon_tracking = $license_code = false;
		$settings = $this->get();
		extract( $settings );
		ob_start();
		include_once( $this->partials_dir_path() . 'settings-form.php' );
		return  ob_get_clean();

	}

	/**
	 * Get all the settings we need
	 *
	 * @since 0.0.8
	 *
	 * @return array
	 */
	protected function get() {
		$settings = array();
		foreach( $this->settings_keys as $setting ) {
			$settings[ $setting ] = \ingot\testing\crud\settings::read( $setting );
		}

		return $settings;

	}

	/**
	 * Handle updating of options when saving via AJAX
	 *
	 * @since 0.0.8
	 *
	 * @access protected
	 *
	 * @return bool
	 */
	protected function update() {
		$updated = false;
		foreach( $this->settings_keys as $setting ) {
			if( isset( $_POST[ $setting ] ) ) {
				 \ingot\testing\crud\settings::write( $setting, $_POST[ $setting ] );

			}
		}

		return $updated;

	}





}
