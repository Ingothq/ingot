<?php
/**
 * Functions for this plugin
 *
 * @package   Ingot
 * @author    Josh Pollock
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Polloc
 */


add_shortcode( 'ingot_click_test', 'ingot_shortcode' );
function ingot_shortcode( $atts ) {
	return;
	$atts = shortcode_atts( array(
		'sequence_id' => 0,
		'link' => null
	), $atts, 'ingot_click_test' );

	$sequence = \ingot\testing\crud\sequence::get( $atts[ 'sequence_id' ] );
	if ( empty( $sequence ) ) {
		return;

	}

	$click_test = new \ingot\testing\test\click\click( $sequence, $atts[ 'link' ] );

	$html = $click_test->get_html();

	if ( is_string( $html ) ) {
		return $html;

	}

}
