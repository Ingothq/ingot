<?php


add_shortcode( 'ingot', 'ingot_shortcode' );
function ingot_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'id' => 0,
	), $atts, 'ingot' );
	if ( 0 != $atts[ 'id' ] ) {
		$test = new \ingot\ui\render\click_tests\link( $atts[ 'id' ] );
		return $test->get_html();
	}
}
