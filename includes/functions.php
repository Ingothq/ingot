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
		return wp_kses( $_SERVER[ 'HTTP_USER_AGENT' ] );

	}else{
		return '';

	}

}

/**
 * Get browser details
 *
 * @since 0.0.7
 *
 * @param bool|false $array Optional. If true return all details. If false, the default, only broswer name is returned.
 * @param null|string $user_agent User agent details. Optional. If null, the default ingot_get_user_agent() is used.
 *
 * @return mixed
 */
function ingot_get_browser( $array = false, $user_agent = null ) {
	if( is_null( $user_agent ) ) {
		$user_agent = ingot_get_user_agent();
	}

	$browser = get_browser( $user_agent );
	if( is_array( $browser ) ) {
		if( $array ) {
			return $browser;

		}else{
			return $browser[ 'browser' ];

		}
	}
}


