<?php
/**
 * Render a text test
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

class text extends \ingot\ui\render\click_tests\click {

	protected $extra_span;

	/**
	 * Make HTML for to output and set in the html property of this class
	 *
	 * @since 0.0.6
	 *
	 * @access protected
	 */
	protected function make_html() {
		$test_id = $this->get_test()[ 'ID' ];
		$text = $this->get_test()[ 'text' ];
		$target = $this->get_group()[ 'selector' ];
		$group_id = $this->get_sequence()[ 'ID' ];
		$sequence_id = $this->get_sequence()[ 'ID' ];
		$click_nonce = util::click_nonce( $test_id, $sequence_id, $group_id );
		$this->extra_span = sprintf( '<span id="%s" class="ingot-click-test-data ingot-click-test-text" data-ingot-test-id="%d" data-ingot-sequence-id="%d" data-ingot-test-nonce="%s" data-ingot-target="%s"></span>',
			esc_attr( $this->attr_id() ),
			esc_attr( $test_id ),
			esc_attr( $sequence_id ),
			esc_attr( $click_nonce ),
			esc_attr( $target )
		);

		$this->hook_extra_span();
		$this->html = $text;

	}

	protected function hook_extra_span() {
		add_action( 'wp_footer', function(){
			echo sprintf( '<!--%s-->', __( 'Extra data for tracking Ingot click tests ' ) );
			echo $this->extra_span;
		});
	}


}
