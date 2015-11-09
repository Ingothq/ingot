<?php
/**
 * API For Settings
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\api\rest;


class settings extends route {


	/**
	 * Marks what object this is for.
	 *
	 * @since 0.2.0
	 *
	 * @var string
	 */
	protected $what = 'settings';

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @since 0.2.0
	 */
	public function register_routes() {
		$namespace = $this->make_namespace();
		$base = $this->what;
		register_rest_route( $namespace, '/' . $base, array(
			array(
				'methods'         => \WP_REST_Server::READABLE,
				'callback'        => array( $this, 'read' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			),
			array(
				'methods'         => \WP_REST_Server::EDITABLE,
				'callback'        => array( $this, 'update' ),
				'permission_callback' => array( $this, 'permissions_check' ),
				'args'            => $this->args( false )
			),
		) );
	}

	/**
	 * Get all settings
	 *
	 * @since 0.2.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function read( $request ) {
		$settings = array();
		foreach( array_keys( $this->args() ) as $setting ) {
			$settings[ $setting ] = \ingot\testing\crud\settings::read( $setting );
		}

		return rest_ensure_response( $settings );
	}

	/**
	 * Update all settings
	 *
	 * @since 0.2.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function update( $request ){
		$params = $request->get_params();


		foreach( array_keys( $this->args() ) as $setting ) {
			if( isset( $params[ $setting ] ) && ! empty( $params[ $setting ] ) ) {
				$saved = \ingot\testing\crud\settings::write( $setting, $params[ $setting ] );
				if( is_wp_error( $saved  ) ) {
					return rest_ensure_response( $saved, 500 );
				}
				$settings[ $setting ] = $params[ $setting ];
			}else{
				$settings[ $setting ] = \ingot\testing\crud\settings::read( $setting );
			}

		}


		return rest_ensure_response( $settings );

	}

	/**
	 *
	 * @since 0.2.0
	 *
	 * @param bool $required
	 *
	 * @return array
	 */
	public function args( $required = true ) {
		return array(
			'click_tracking' => array(
				'type'              => 'boolean',
				'default'           => true,
			),
			'anon_tracking'  => array(
				'type'              => 'boolean',
				'default'           => true,
			),
			'license_code' => array(
				'type'              => 'string',
				'default'           => '',

			),

		);
	}

	/**
	 * Permissions check
	 *
	 * @since 0.2.0
	 *
	 * @return bool
	 */
	public function permissions_check() {
		return current_user_can( 'manage_options' );
	}




}
