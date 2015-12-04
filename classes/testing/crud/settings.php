<?php
/**
 * Crud for Ingot settings
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\crud;


class settings {

	/**
	 * Settings keys
	 *
	 * @since 0.0.8
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected static $settings_keys = array(
		'click_tracking',
		'anon_tracking',
		'license_code',
		'cache_mode'
	);


	/**
	 * Get settings key names <em>without</em> prefix
	 *
	 * @since 0.0.8
	 *
	 * @return array
	 */
	public static function get_settings_keys() {
		return self::$settings_keys;
	}

	/**
	 * Read a value
	 *
	 * @since 0.0.8
	 *
	 * @param string $setting Setting name
	 *
	 * @return mixed|void
	 */
	public static function read( $setting ) {
		if( self::option_key_name( $setting ) ) {
			return get_option( self::option_key_name( $setting ), false );
		}

		return false;

	}

	/**
	 * Save a value
	 *
	 * @since 0.0.8
	 *
	 * @param string $setting Setting name
	 * @param mixed $value Value to be saved
	 *
	 * @return bool
	 */
	public static function write( $setting, $value ) {
		if( self::option_key_name( $setting ) ) {
			if( 'license_code' == $setting && ! empty( $value ) && $value != self::read( $setting ) ) {
				$handled = self::handle_license( $value );
				return $handled;

			}

			return update_option( self::option_key_name( $setting ), $value, false );

		}

		return false;

	}

	/**
	 * Get prefixed key names
	 *
	 * @since 0.0.8
	 *
	 * @return array
	 */
	public static function get_key_names() {
		$keys = array();
		foreach( self::$settings_keys as $key ) {
			$keys[] = self::option_key_name( $key );
		}

		return $keys;

	}

	/**
	 * Handle license update
	 *
	 * @since 0.2.0
	 *
	 * @param $value License code
	 *
	 * @return bool|\WP_Error
	 */
	protected static function handle_license( $value ){
		$current = ingot_sl_get_license();
		if( $value == $current ) {
			return true;

		}else{
			$activated = ingot_sl_activate_license( $value );
			if( 'valid' == $activated ){
				return true;

			}else{
				return false;

			}

		}

	}

	/**
	 * Create a prefixed option key name
	 *
	 * @since 0.0.8
	 *
	 * @access protected
	 *
	 * @param $setting
	 *
	 * @return string
	 */
	protected static function option_key_name( $setting ) {
		if( in_array( $setting, self::$settings_keys ) ) {
			return "ingot_settings_{$setting}";
		}

	}

	/**
	 * Sanatize and validate our settings for this class
	 *
	 * Effectively hooked to "pre_update_option" via ingot\testing\ingot::presave_settings()
	 *
	 * @since 0.0.8
	 *
	 * @param string $setting Setting name
	 * @param mixed $value Value to be saved
	 *
	 * @return int|string
	 */
	public static function sanatize_setting( $setting, $value ) {
		if ( self::option_key_name( 'license_code' ) != $setting ) {
			if ( 'on' == $value || ingot_validate_boolean( $value ) && true == $value ) {
				$value = 1;
			} else {
				$value = 0;
			}

		}else{
			if ( is_string( $value )  ) {
				if( is_array( json_decode( $value, true ) ) ) {
					$value = '';
				}elseif( is_array( maybe_unserialize( $value ) ))	{
					$value = '';
				}else{
					$value = trim( strip_tags( $value ) );
				}
			}else{
				$value = '';
			}
		}

		return $value;
	}

	/**
	 * Check if we are doing detailed click tracking or not
	 *
	 * @since 0.0.8
	 *
	 * @return bool
	 */
	public static function is_click_track_mode() {
		return self::read( 'click_tracking' );

	}



}
