<?php
/**
 * Ingot Options.
 *
 * @package   Ingot
 * @author    Josh Pollock
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot;

/**
 * Options class.
 *
 * @package Ingot
 * @author  Josh Pollock
 */
class options {

	/**
	 * The name of the option we use for this plugin
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	public static $option_name = 'ingot';

	/**
	 * Create a new ingot.
	 *
	 * @since 0.0.1
	 *
	 * @param string $name Name for ingot.
	 * @param string $slug Slug for ingot.
	 *
	 * @return array|void|bool Config array for new ingot if it exists. Void if not. False if not allowed
	 */
	public static function create( $name, $slug ) {
		$can = self::can();
		if ( $can ) {
			$name     = sanitize_text_field( $name );
			$slug     = sanitize_title_with_dashes( $slug );
			$item_id  = self::create_unique_id();
			$registry = self::get_registry();

			if ( ! isset( $registry[ $item_id ] ) ) {
				$new = array(
					'id'   => $item_id,
					'name' => $name,
					'slug' => $slug,
				);

				$registry[ $item_id ] = $new;

				self::update( $new, $registry );

				return $new;

			}
		} else {
			return $can;

		}

	}

	/**
	 * Get an individual item by its ID.
	 *
	 * @since 0.0.1
	 *
	 * @param string $id ingot ID
	 *
	 * @return bool|array ingot config or false if not found.
	 */
	public static function get_single( $id ) {
		$option_name = self::item_option_name( $id );
		$ingot = get_option( $option_name, false );

		// try slug based lookup
		if ( false === $ingot ){
			$registry = self::get_registry();
			foreach ( $registry as $single_id => $single ) {
				if ( $single['slug'] === $id ) {
					$option_name = self::item_option_name( $single_id );
					$ingot = get_option( $option_name, false );
					break;
				}

			}

		}

		/**
		 * Filter a ingot config before returning
		 *
		 * @since 0.0.1
		 *
		 * @param array $ingot The config to be returned
		 * @param string $option_name The name of the option it was stored in.
		 */
		return apply_filters( 'ingot_get_single', $ingot, $option_name );


	}

	/**
	 * Get the registry of ingot.
	 *
	 * @since 0.0.1
	 *
	 * @return mixed|bool Array of ingots or false if not allowed.
	 */

	public static function get_registry() {
		$registry = get_option( self::registry_name(), array() );

		/**
		 * Filter the registry before returning
		 *
		 * @since 0.0.1
		 */

		return apply_filters( 'ingot_get_registry', $registry );


	}

	/**
	 * Update both a single item and the registry
	 *
	 * @since 0.0.1
	 *
	 * @param array $config Single item config.
	 * @param array|bool. Optional. If false, current registry will be used, if is array, that reg
	 */
	public static function update( $config, $update_registry = false ) {
		if ( ! is_array( $update_registry ) ) {
			$update_registry = self::get_registry();
		}

		if( isset( $config['id'] ) && !empty( $update_registry[ $config['id'] ] ) ){
			$update = array(
				'id'	=>	$config['id'],
				'name'	=>	$config['name'],
				'slug'	=>	$config['slug'],

			);

			// add search form to registery
			if( ! empty( $config['search_form'] ) ){
				$updated_registery['search_form'] = $config['search_form'];
			}

			$update_registry[ $config[ 'id' ] ] = $update;

		}

		$saved_registry = self::save_registry( $update_registry );

		$saved_single = self::save_single( $config['id'], $config );

		if ( false === $saved_registry || false == $saved_single ) {
			return false;

		}else{
			return true;

		}

	}

	/**
	 * Delete an item and clear it from the registry
	 *
	 * @since 0.0.1
	 *
	 * @param string $id Item ID
	 *
	 * @return bool True on success.
	 */
	public static function delete( $id ) {
		$deleted = delete_option( self::item_option_name( $id ) );
		if ( $deleted ) {
			$registry = self::get_registry();
			if ( isset( $registry[ $id ] ) ) {
				unset( $registry[ $id ] );
				return self::save_registry( $registry );

			}

		}

	}

	/**
	 * Save the registry of items.
	 *
	 * @since 0.0.1
	 *
	 * @param array $registry The registry
	 *
	 * @return bool
	 */
	protected static function save_registry( $registry ) {
		return update_option( self::registry_name(), $registry );

	}

	/**
	 * Save an individual item.
	 *
	 * @param string $id ingot ID
	 * @param array $config ingot config
	 *
	 * @return bool
	 */
	protected static function save_single( $id, $config ) {
		return update_option( self::item_option_name( $id ), $config );

	}



	/**
	 * Get the name to use for an individual item option.
	 *
	 * @since 0.0.1
	 *
	 * @access protected
	 *
	 * @param string $id
	 *
	 * @return string
	 */
	protected static function item_option_name( $id ) {
		$name = self::$option_name . '_' . $id;
		if ( 50 < strlen( $name ) ) {
			$name = md5( $name );
		}

		return $name;

	}

	/**
	 * Get the name used for the registry option
	 *
	 * @since 0.0.1
	 *
	 * @access protected
	 *
	 * @return string
	 */
	protected static function registry_name() {
		$registry_name = '_' . self::$option_name . '_registry';
		return $registry_name;

	}

	/**
	 * Create unique ID
	 *
	 * @since 0.0.1
	 *
	 * @access protected
	 *
	 * @return string
	 */
	protected static function create_unique_id() {
		$slug_parts = explode( '_', 'ingot' );
		$slug = '';

		foreach ( $slug_parts as $slug_part ) {
			$slug .= substr( $slug_part, 0,1 );
		}

		$slug = strtoupper( $slug );

		$item_id = uniqid( $slug ) . rand( 100, 999 );

		return $item_id;

	}

	/**
	 * Generic capability check to use before reading/writing
	 *
	 * @since 0.0.1
	 *
	 * @param string $cap Optional. Capability to check. Defaults to 'manage_options'
	 *
	 * @return bool
	 */
	public static function can( $cap = 'manage_options' ) {
		return current_user_can( $cap );

	}

}
