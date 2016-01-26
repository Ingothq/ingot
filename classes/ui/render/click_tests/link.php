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
		$test_id = $this->get_variant()[ 'ID' ];
		$text = $this->get_variant_content();
		$link = $this->link();
		$group_id = $this->get_group()[ 'ID' ];

		$this->html = sprintf(
			'<a id="%s" href="%s" class="ingot-test ingot-click-test ingot-click-test-link ingot-group-%d" data-ingot-test-id="%d" >%s</a>',
			esc_attr( $this->attr_id() ),
			esc_url( $link ),
			esc_attr( $group_id ),
			esc_attr( $test_id ),
			wp_kses_post( $text )
		);

	}

}
