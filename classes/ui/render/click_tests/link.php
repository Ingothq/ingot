<?php
/**
 * Render a link test
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui\render\click_tests;


use ingot\testing\tests\click\click;
use ingot\ui\util;

class link extends \ingot\ui\render\click_tests\click {

	/**
	 * Make HTML for to output and set in the html property of this class
	 *
	 * @since 0.0.5
	 *
	 * @access protected
	 */
	protected function make_html() {
		$test_id = $this->get_test()[ 'ID' ];
		$text = $this->get_test()[ 'text' ];
		$link = $this->get_group()[ 'link' ];
		$group_id = $this->get_group()[ 'ID' ];
		$sequence_id = $this->get_sequence()[ 'ID' ];
		$click_nonce = util::click_nonce( $test_id, $sequence_id, $group_id );
		$this->html = sprintf(
			'<a id="%s" href="%s" class="ingot-test ingot-click-test ingot-click-test-link" data-ingot-test-id="%d" data-ingot-sequence-id="%d" data-ingot-test-nonce="%s">%s</a>',
			esc_attr( $this->attr_id() ),
			esc_url( $link ),
			esc_attr( $test_id ),
			esc_attr( $sequence_id ),
			esc_attr( $click_nonce ),
			esc_html( $text )
		);

	}

}
