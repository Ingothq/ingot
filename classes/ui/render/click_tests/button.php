<?php
/**
 * Render a button test
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

class button extends \ingot\ui\render\click_tests\click {




	/**
	 * Make HTML for to output and set in the html propery of this class
	 *
	 * @since 0.0.6
	 *
	 * @access protected
	 */
	protected function make_html() {
		$test_id = $this->get_test()[ 'ID' ];
		$text = $this->get_test()[ 'text' ];
		$link = $this->get_group()[ 'link' ];
		$group_id = $this->get_sequence()[ 'ID' ];
		$sequence_id = $this->get_sequence()[ 'ID' ];
		$click_nonce = util::click_nonce( $test_id, $sequence_id, $group_id );
		$style = $this->make_style( $this->get_group() );

		$this->html = sprintf(
			'<button id="%s" class="ingot-button" %s><a href="%s" class="ingot-test ingot-click-test ingot-click-test-button button" data-ingot-test-id="%d" data-ingot-sequence-id="%d" data-ingot-test-nonce="%s" %s>%s</a></button>',
			esc_attr( $this->attr_id() ),
			$style,
			esc_url( $link ),
			esc_attr( $test_id ),
			esc_attr( $sequence_id ),
			esc_attr( $click_nonce ),
			$style,
			esc_html( $text )
		);

		remove_filter( 'ingot_default_button_color', array( $this, 'get_group_default_color' ) );

	}

	/**
	 * Create style tags for buttons
	 *
	 * @since 0.1.1
	 *
	 * @acess protected
	 *
	 * @param $config
	 *
	 * @return string
	 */
	protected function make_style( $config ) {
		$color = helpers::get_color_from_meta( $config );
		$background_color = helpers::get_background_color_from_meta( $config );
		return sprintf( 'style="background-color:%s;color:%s"', $background_color, $color );

	}
}
