<?php
/**
 * Output for destination tests
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\ui\render\click_tests;


use ingot\testing\tests\click\click;

class destination extends \ingot\ui\render\click_tests\click {

	/**
	 * Make HTML for to output and set in the html property of this class
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 */
	protected function make_html() {
		$test_id = $this->get_variant()[ 'ID' ];
		$text = $this->get_variant_content();
		$group_id = $this->get_group()[ 'ID' ];

		$this->html = sprintf(
			'<span id="%s" class="ingot-test ingot-click-test ingot-click-test-link ingot-group-%d" data-ingot-test-id="%d" >%s</span>',
			esc_attr( $this->attr_id() ),
			esc_attr( $group_id ),
			esc_attr( $test_id ),
			wp_kses_post( $text )
		);

	}
}
