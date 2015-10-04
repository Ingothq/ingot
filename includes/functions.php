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


