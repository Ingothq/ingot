<?php
/**
 * Render a button color test
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui\render\click_tests;

use ingot\testing\tests\click\click;
use ingot\testing\utility\helpers;
use ingot\ui\util;

class button_color extends \ingot\ui\render\click_tests\button {




	/**
	 * Make HTML for to output and set in the html propery of this class
	 *
	 * @since 0.0.6
	 *
	 * @access protected
	 */
	protected function make_html() {
		$test_id = (int) $this->get_variant()[ 'ID' ];
		$text = $this->get_group()[ 'meta' ][ 'color_test_text' ];
		$link = $this->link();
		$style = $this->make_style( $this->get_variant() );
		$group_id = $this->get_group()[ 'ID' ];

		$this->html = sprintf(
			'<button id="%s" class="ingot-button ingot-group-%d" %s><a href="%s" class="ingot-test ingot-click-test ingot-click-test-button-color button" data-ingot-test-id="%d"  %s>%s</a></button>',
			esc_attr( $this->attr_id() ),
			esc_attr( $group_id ),
			$style,
			esc_url( $link ),
			esc_attr( $test_id ),
			$style,
			wp_kses_post( $text )
		);

		remove_filter( 'ingot_default_button_color', array( $this, 'get_group_default_color' ) );

	}

}
