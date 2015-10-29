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
		$test_id = $this->get_test()[ 'ID' ];
		$text = $color_test_text = helpers::v( 'color_test_text', $this->get_group()[ 'meta' ], '' );
		$link = $this->get_group()[ 'link' ];
		$group_id = $this->get_sequence()[ 'ID' ];
		$sequence_id = $this->get_sequence()[ 'ID' ];
		$click_nonce = util::click_nonce( $test_id, $sequence_id, $group_id );
		$style = $this->make_style( $this->get_test() );

		$this->html = sprintf(
			'<button class="ingot-button" %s><a href="%s" class="ingot-test ingot-click-test ingot-click-test-button button" data-ingot-test-id="%d" data-ingot-sequence-id="%d" data-ingot-test-nonce="%s" %s>%s</a></button>',
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

}
