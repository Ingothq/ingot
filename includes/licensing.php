<?php
/**
 * Utility functions for EDD SL Licensing
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */


/**
 * Add EDD SL support
 *
 * @since 0.2.0
 *
 * @uses "admin_init"
 */
function ingot_sl_plugin_updater() {


	$license_key = ingot_sl_get_license();

	$edd_updater = new EDD_SL_Plugin_Updater( INGOT_SL_STORE_URL, __FILE__, array(
			'version' 	=> INGOT_VER,
			'license' 	=> $license_key,
			'item_name' => INGOT_SL_ITEM_NAME,
			'author' 	=> 'Ingot LLC'
		)
	);

}


/**
 * Register setting for license code
 *
 * @uses "admin_init"
 *
 * @since 0.2.0
 */
function ingot_sl_register_option() {
	register_setting('ingot_sl_license', 'ingot_sl_license_key', 'ingot_sl_sanitize_license' );
}

/**
 * Sanatize license code on save
 *
 * @param $new
 *
 * @return mixed
 */
function ingot_sl_sanitize_license( $new ) {
	$old = get_option( 'ingot_sl_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'ingot_sl_license_status' );
	}

	return $new;

}

/**
 * Activate license
 *
 * @since 0.2.0
 *
 * @param bool|string Optional. License to activate. If false, the default, license is pulled from DB.
 *
 * @return bool|void
 */
function ingot_sl_activate_license( $license = false ) {
	$action   = 'activate_license';
	$response = ingot_sl_api( $license, $action );
	if ( is_wp_error( $response ) ) {
		return false;

	}
	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if ( is_object( $license_data) ) {
		update_option( 'ingot_sl_license_status', $license_data->license );

		return $license_data->license;
	}

	return false;

}


/**
 * Deactivate license
 *
 * @since 0.2.0
 *
 * @param bool|string Optional. License to activate. If false, the default, license is pulled from DB.
 *
 * @return bool|void
 */
function ingot_sl_deactivate_license( $license = false ) {
	$action = 'deactivate_license';
	$response = ingot_sl_api( $license, $action );
	if ( is_wp_error( $response ) ) {
		return false;

	}

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if ( $license_data->license == 'deactivated' ) {
		delete_option( 'ingot_sl_license_status' );
	}



}

/**
 * Get license code from DB
 *
 * @since 0.2.0
 *
 * @return string
 */
function ingot_sl_get_license() {
	$license = trim( get_option( 'ingot_sl_license_key' ) );

	return $license;

}

/**
 * Check license status
 *
 * @param bool|false $license
 * @param bool $remote_check Optional. If true, the default, we actually check remote. If false, just check in DB
 *
 * @return bool
 */
function ingot_sl_check_license( $remote_check = true, $license = false ) {
	if( ! $remote_check ) {

		$status = get_option( 'ingot_sl_license_status' );
		if( 'valid' == $status ) {
			return true;

		}else{
			return false;

		}

	}


	$action = 'check_license';
	$response = ingot_sl_api( $license, $action );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if( $license_data->license == 'valid' ) {
		echo 'valid';
		exit;

	} else {
		echo 'invalid';
		exit;

	}
}


/**
 * Make a request to SL API on ingothq.com
 *
 * @param bool|string Optional. License to activate. If false, the default, license is pulled from DB.
 * @param string $action API action
 *
 * @return array|\WP_Error
 */
function ingot_sl_api( $license = false, $action ) {

	if ( ! $license ) {
		$license = trim( get_option( 'ingot_sl_license_key', false ) );
	}

	if ( ! $license ) {
		return false;

	}

	global $wp_version;
	$php_version = PHP_VERSION;

	$api_params = array(
		'edd_action' => $action,
		'license'    => $license,
		'item_name'  => urlencode( INGOT_SL_ITEM_NAME ),
		'url'        => home_url(),
		'details'   => array(
			'wp_version'    => $wp_version,
			'php_version'   => $php_version
		)
	);

	/**
	 * Filter licensing API request parameters before sending
	 *
	 * @since 1.1.0
	 *
	 * @param array $api_params Data to send in body of request.
	 */
	$api_params = apply_filters( 'ingot_licensing_api_request_params', $api_params );

	/**
	 * Filter URL for remote API
	 *
	 * @since 1.1.0
	 *
	 * @param string $api_url The remote URL to request to
	 */
	$api_url = apply_filters( 'ingot_licensing_api_request_url', INGOT_SL_STORE_URL );

	$response = wp_remote_post( $api_url, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params
		)
	);

	/**
	 * Fires after the remote request to licensing API
	 *
	 * @since 1.1.0
	 *
	 * @param array $response Return from wp_remote_post()
	 * @param array $api_params Data sent in body of request.
	 */
	do_action( 'ingot_licensing_api_request_complete', $response, $api_params );

	return $response;

}
