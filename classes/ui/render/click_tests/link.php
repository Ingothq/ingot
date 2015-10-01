<?php
/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui\render\click_tests;


use ingot\testing\crud\group;
use ingot\testing\crud\sequence;
use ingot\testing\crud\test;
use ingot\testing\tests\click\click;
use ingot\ui\util;

class link {

	private $group;

	private $sequence;

	private $html;

	private $test;
	public function __construct( $group_id ){
		if ( $group_id ) {
			$this->group = group::read( $group_id );
			if ( ! empty( $this->group ) ) {
				$this->get_current_sequence();
				if ( ! empty( $this->sequence ) ) {
					$this->get_test();
					if ( ! empty( $this->test ) ) {
						$this->make_html();
					}

				}


			}
		}

	}

	public function get_html(){
		click::increase_total( $this->test[ 'ID' ], $this->sequence[ 'ID' ] );
		return $this->html;
	}

	private function get_current_sequence(){
		$sequences = $this->group[ 'sequences' ];
		$args = array(
			'ids' => $sequences,
			'current' => true
		);
		$this->sequence = sequence::get_items( $args );
	}

	private function get_test() {
		$chance = $this->calculate_chance();
		$use_a = click::choose_a( $chance );
		if ( $use_a ){
			$test_id = $this->sequence[ 'a_id' ];
		}else{
			$test_id = $this->sequence[ 'b_id' ];
		}

		$this->test = test::read( $test_id );
	}

	private function make_html() {
		$test_id = $this->test[ 'ID' ];
		$text = $this->test[ 'text' ];
		$link = $this->group[ 'link' ];
		$group_id = $this->group[ 'ID' ];
		$sequence_id = $this->sequence[ 'ID' ];
		$click_nonce = util::click_nonce( $test_id, $sequence_id, $group_id );
		$this->html = sprintf(
			'<a href="%s" class="ingot-test ingot-click-test ingot-click-test-link" data-ingot-test-id="%d" data-ingot-sequence-id="%d" data-ingot-test-nonce="%s">%s</a>',
			esc_url( $link ),
			esc_attr( $test_id ),
			esc_attr( $sequence_id ),
			esc_attr( $click_nonce ),
			esc_html( $text )

		);
	}

	/**
	 * Get chance of using A
	 *
	 * @todo this
	 *
	 * @return int
	 */
	private function calculate_chance(){
		return 50;
	}

}
