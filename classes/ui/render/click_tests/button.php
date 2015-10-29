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


	protected $group_color;

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

		$test =  $this->get_test();
		$color = helpers::get_color_from_meta( $test );
		add_filter( 'ingot_default_button_color', array( $this, 'get_group_default_color' ) );
		$this->html = sprintf(
			'<button class="ingot-button" style="background-color: %s"><a href="%s" class="ingot-test ingot-click-test ingot-click-test-button button" data-ingot-test-id="%d" data-ingot-sequence-id="%d" data-ingot-test-nonce="%s" style="background-color: %s">%s</a></button>',
			$color,
			esc_url( $link ),
			esc_attr( $test_id ),
			esc_attr( $sequence_id ),
			esc_attr( $click_nonce ),
			$color,
			esc_html( $text )
		);

		remove_filter( 'ingot_default_button_color', array( $this, 'get_group_default_color' ) );

	}


	/**
	 * Get the group default color
	 *
	 * @uses "ingot_default_button_color"
	 *
	 * @since 0.1.1
	 *
	 * @return string
	 */
	public function get_group_default_color() {
		return  helpers::get_color_from_meta( $this->get_group() );
	}

}
