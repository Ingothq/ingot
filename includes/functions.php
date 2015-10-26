<?php

/**
 * Get complete click type HTML
 *
 * @since 0.0.6
 *
 * @param int $id Group ID
 *
 * @return string
 */
function ingot_click_test( $id ) {
	$group = \ingot\testing\crud\group::read( $id );
	$type = $group[ 'click_type' ];
	switch( $type ) {
		case in_array( $type, array( 'link', 'button', 'text' ) ) :
			$html = ingot_click_html_link( $type, $group );
			break;
		case is_callable( $type ) :
			$html = call_user_func( $type, $group );
			break;
		default :
			$html = '';

	}

	return $html;

}

add_shortcode( 'ingot', 'ingot_shortcode' );
/**
 * Callback for the ingot shortcode
 *
 * @since 0.0.4
 *
 * @param $atts
 *
 * @return string
 */
function ingot_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'id' => 0,
	), $atts, 'ingot' );
	if ( 0 != $atts[ 'id' ] ) {
		$html = ingot_click_test( $atts[ 'id' ] );

	}

	return $html;
}

/**
 * Get HTML for the link of a click test
 *
 * @since 0.0.6
 *
 * @param string $type Test type
 * @param int $group Group ID
 *
 * @return string
 */
function ingot_click_html_link( $type, $group ) {
	switch ( $type ) {
		case 'link' == $type :
			$class = new ingot\ui\render\click_tests\link( $group );
			break;
		case 'button' == $type :
			$class = new \ingot\ui\render\click_tests\button( $group );
			break;
		case 'text' == $type :
			$class = new \ingot\ui\render\click_tests\text( $group );
			break;
		default :
			return '';
		break;
	}

	$html = $class->get_html();

	return $html;

}

/**
 * Get User IP
 *
 * Returns the IP address of the current visitor
 *
 * @since 0.0.7
 * @return string $ip User's IP address
 */
function ingot_get_ip() {

	$ip = '127.0.0.1';

	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		//check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		//to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return apply_filters( 'ingot_get_ip', $ip );

}

/**
 * Get user agent safely
 *
 * @since 0.0.7
 *
 * @return string
 */
function ingot_get_user_agent() {
	if( isset( $_SERVER ) && isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) ){
		return wp_kses( $_SERVER[ 'HTTP_USER_AGENT' ], array() );

	}else{
		return '';

	}

}

/**
 * Get browser details
 *
 * @since 0.0.7
 *
 * @param null|string $user_agent User agent details. Optional. If null, the default ingot_get_user_agent() is used.
 *
 * @return string|bool Broswer name or false if not able to detect
 */
function ingot_get_browser( $user_agent = null ) {
	if( is_null( $user_agent ) && isset( $_SERVER['HTTP_USER_AGENT'] ) ){
		$user_agent = ingot_get_user_agent();
	}else{
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
	}

	if ( is_string( $user_agent ) ) {
		if ( strpos( $user_agent, 'Lynx' ) !== false ) {
			return 'lynx';
		} elseif ( stripos( $user_agent, 'chrome' ) !== false ) {
			return 'chrome';
		} elseif ( stripos( $user_agent, 'safari' ) !== false ) {
			return 'safari';
		} elseif ( ( strpos( $user_agent, 'MSIE' ) !== false || strpos( $user_agent, 'Trident' ) !== false ) && strpos( $user_agent, 'Win' ) !== false ) {
			return 'ie';
		} elseif ( strpos( $user_agent, 'MSIE' ) !== false && strpos( $user_agent, 'Mac' ) !== false ) {
			return 'ie';
		} elseif ( strpos( $user_agent, 'Gecko' ) !== false ) {
			return 'gecko';
		} elseif ( strpos( $user_agent, 'Opera' ) !== false ) {
			return 'opera';
		} elseif ( strpos( $user_agent, 'Nav' ) !== false && strpos( $user_agent, 'Mozilla/4.' ) !== false ) {
			return 'ns4';
		}else{
			return '';
		}

	}

	return '';

}


/**
 * Get reffering source, if tracking from that source
 *
 * @since 0.0.7
 *
 * @return bool
 */
function ingot_get_refferer() {
	$utm = ingot_get_utm();
	if ( ! empty( $utm ) ) {
		$_utm = array_keys( array_flip( $utm ) );
		foreach ( ingot_get_referrers_to_track() as $allowed ) {
			if( in_array( $allowed, $_utm ) ) {
				return $allowed;
			}

		}
	}

	$refferer = false;
	if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
		$refferer = wp_unslash( $_REQUEST['_wp_http_referer'] );
	}
	elseif ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
		$refferer = wp_unslash( $_SERVER['HTTP_REFERER'] );
	}else{
		return false;

	}


	$_parse = parse_url( $refferer );
	if( is_array( $_parse )  && isset( $_parse[ 'host' ] ) ) {
		foreach( ingot_get_referrers_to_track() as $ref ) {
			if( false !== strpos( $_parse[ 'host' ], $ref ) ) {
				return $ref;

			}

		}

	}

	return false;

}

/**
 * Get UTM tags into an array safely.
 *
 * @since 0.0.7
 *
 * @return array
 */
function ingot_get_utm() {
	$utm = array();
	if( ! empty( $_GET ) ) {
		foreach( $_GET as $var => $value ) {
			if( false !== strpos( $var, 'UTM' ) || false !== strpos( $var, 'utm' ) ) {
				$key = sanitize_key( str_replace( 'utm_', '', $var ) );
				$utm[ $key ] = strip_tags( $value );
			}

		}

	}

	return $utm;

}

/**
 * Get networks to track referrals from/
 *
 * @since 0.0.7
 *
 * @return array
 */
function ingot_get_referrers_to_track() {
	$referrers = array(
		'twitter',
		'facebook',
		'google',
	);

	/**
	 * Add or subtract referrers to track from
	 *
	 * @since 0.0.7
	 *
	 * @param array $referrers
	 */
	$referrers = apply_filters( 'ingot_referrers_to_track', $referrers );

	return $referrers;


}

/**
 * Check that admin ajax is happening
 *
 * @since 0.0.8
 *
 * @return bool
 */
function ingot_is_admin_ajax() {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return true;

	}

}

function ingot_validate_boolean( $val ) {
	if( 'on' == $val ) {
		return true;
	}

	if( filter_var( $val, FILTER_VALIDATE_BOOLEAN ) ) {
		return true;
	}
}

/**
 * Get the full URL of the current page
 *
 * @since 0.0.9
 *
 * @return string Full URL of the current page

 */
function ingot_current_url () {
	$url = 'http';

	if ( isset( $_SERVER[ 'HTTPS' ] ) && 'off' != $_SERVER[ 'HTTPS' ] && 0 != $_SERVER[ 'HTTPS' ] )
		$url = 'https';

	$url .= '://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];

	return $url;

}

/**
 * Sanitize Amount
 *
 * Returns a sanitized amount by stripping out thousands separators.
 *
 * Copied form ingot_sanitize_amount()
 *
 * @since 0.0.9
 *
 * @param string $amount Price amount to format
 * @return string $amount Newly sanitized amount
 */
function ingot_sanitize_amount( $amount ) {
	$is_negative   = false;

	/**
	 * Change thousands separator to use for price display
	 *
	 * @since 0.0.9
	 *
	 * @param string $thousands_separator
	 */
	$thousands_sep = apply_filters( 'thousands_separator', ',' );

	/**
	 * Chane decimal separator to use for price display
	 *
	 * @since 0.0.9
	 *
	 * @param string $decimal_separator
	 */
	$decimal_sep   = apply_filters( 'decimal_separator', '.' );

	// Sanitize the amount
	if ( $decimal_sep == ',' && false !== ( $found = strpos( $amount, $decimal_sep ) ) ) {
		if ( ( $thousands_sep == '.' || $thousands_sep == ' ' ) && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
			$amount = str_replace( $thousands_sep, '', $amount );
		} elseif( empty( $thousands_sep ) && false !== ( $found = strpos( $amount, '.' ) ) ) {
			$amount = str_replace( '.', '', $amount );
		}

		$amount = str_replace( $decimal_sep, '.', $amount );
	} elseif( $thousands_sep == ',' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
		$amount = str_replace( $thousands_sep, '', $amount );
	}

	if( $amount < 0 ) {
		$is_negative = true;
	}

	$amount   = preg_replace( '/[^0-9\.]/', '', $amount );

	/**
	 * Filter number of decimals to use for prices
	 *
	 * @since 0.0.9
	 *
	 * @param int $number Number of decimals
	 * @param int|string $amount Price
	 */
	$decimals = apply_filters( 'ingot_sanitize_amount_decimals', 2, $amount );
	$amount   = number_format( (double) $amount, $decimals, '.', '' );

	if( $is_negative ) {
		$amount *= -1;
	}

	/**
	 * Filter the sanitized price before returning
	 *
	 * @since 0.0.9
	 *
	 * @param string $amount Price
	 */
	return apply_filters( 'ingot_sanitize_amount', $amount );
}

/**
 * Get the allowed plugins to make price tests with
 *
 * @since 0.0.9
 *
 * @return array
 */
function ingot_accepted_plugins_for_price_tests() {
	$plugins = array(
		'edd',
		'woo',
	);

	/**
	 * Add or remove allowed plugins for price tests
	 *
	 * @since 0.0.9
	 *
	 * @param array $plugins Array of plugins
	 */
	return apply_filters( 'ingot_accepted_plugins_for_price_tests', $plugins );
}

/**
 * Check if a plugin is acceptable for use in a price test
 *
 * @since 0.0.9
 *
 * @param $plugin
 *
 * @return bool
 */
function ingot_acceptable_plugin_for_price_test( $plugin ){
	return in_array( $plugin, ingot_accepted_plugins_for_price_tests() );
}

/**
 * Check if this is a front-end request
 *
 * @since 0.0.9
 *
 * @return bool
 */
function ingot_is_front_end() {
	if( is_admin() || ingot_is_admin_ajax() || ingot_is_rest_api() || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) ) {
		return false;
	}

	return true;
}

/**
 * Check if this is a REST API request
 *
 * @since 0.0.9
 *
 * @return bool
 */
function ingot_is_rest_api() {
	if( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return true;
	}

}
