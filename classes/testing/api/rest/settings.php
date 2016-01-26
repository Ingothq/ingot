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
				'args'            => $this->args( false )
			),
			array(
				'methods'         => \WP_REST_Server::EDITABLE,
				'callback'        => array( $this, 'update' ),
				'permission_callback' => array( $this, 'permissions_check' ),
				'args'            => $this->args( false )
			),
		) );
		register_rest_route( $namespace, '/' . $base . '/page-search', array(
			array(
				'methods'         => \WP_REST_Server::READABLE,
				'callback'        => array( $this, 'page_search' ),
				'permission_callback' => array( $this, 'permissions_check' ),
				'args'            => array(
					'search' => array(
						'default' => '',
						'sanitize_callback' => 'sanitize_text_field'
					)
				)
			)
		));
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
		return $this->response( $request );
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
			if( isset( $params[ $setting ] ) ) {
				$saved = \ingot\testing\crud\settings::write( $setting, $params[ $setting ] );
				if( is_wp_error( $saved  ) ) {
					return rest_ensure_response( $saved, 500 );
				}

				$settings[ $setting ] = $params[ $setting ];
			}else{
				$settings[ $setting ] = \ingot\testing\crud\settings::read( $setting );
			}

		}


		return $this->response( $request, $settings );

	}

	/**
	 * Create a response
	 *
	 * @since 0.2.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @param array $settings Optional. Current settings. If not used current settings will be queried
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	protected function response( $request, $settings = array() ){
		if( empty( $settings ) ){
			foreach( array_keys( $this->args() ) as $setting ) {
				$settings[ $setting ] = \ingot\testing\crud\settings::read( $setting );
			}
		}

		if( 'admin' == $request->get_param( 'context' ) ){
			$valid = false;
			if( ingot_sl_check_license( false ) ) {
				$valid = true;
			}

			$settings[ 'license_valid' ] = (int) $valid;

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
			'cache_mode' => array(
				'type' => 'string',
				'default' => false,
			),
			'context' => array(
				'type' => 'string',
				'default' => 'admin'
			)

		);
	}

	/**
	 * Page search
	 *
	 * @since 1.1.0
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @param array $settings Optional. Current settings. If not used current settings will be queried
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function page_search( $request ){
		$posts = [];
		if( ! empty( $request->get_param( 'search' ) ) ){
			$query = new \WP_Query( [
				's' => $request->get_param( 'search' ),
				'post_type' => 'page'
			]);
			if( $query->have_posts() ) {
				$_posts = array_combine( wp_list_pluck( $query->posts, 'ID' ), wp_list_pluck( $query->posts, 'post_title' ) );
				foreach( $_posts as $id => $title  ) {
					$posts[ $id ] = [
						'id' => $id,
						'title' => $title
					];
				}
			}

		}

		return ingot_rest_response( $posts );
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
