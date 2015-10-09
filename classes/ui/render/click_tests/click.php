<?php
/**
 * Base class for rendering click tests.
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui\render\click_tests;


use ingot\testing\api\rest\test;
use ingot\testing\crud\group;
use ingot\testing\crud\sequence;
use ingot\testing\utility\helpers;

abstract class click {

	/**
	 * Group config
	 *
	 * @since 0.0.5
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $group;

	/**
	 * Sequence config
	 *
	 * @since 0.0.5
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $sequence;

	/**
	 * Test config
	 *
	 * @since 0.0.5
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $test;


	/**
	 * Rendered HTML
	 *
	 * @since 0.0.5
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $html;

	/**
	 * Constructor for class
	 *
	 * @since 0.0.5
	 *
	 * @param int|object $group ID of group to render, or gorup object
	 */
	public function __construct( $group ){
		if ( $group ) {
			$this->set_group( $group );
			if ( ! empty( $this->group ) ) {
				$this->set_sequence();
				if ( ! empty( $this->sequence ) ) {
					$this->set_test();
					if ( ! empty( $this->test ) ) {
						$this->make_html();
					}

				}

			}

		}

	}

	/**
	 * Get prepared HTML
	 *
	 * Also increases count for test in this sequence.
	 *
	 * @since 0.0.5
	 *
	 * @return string
	 */
	public function get_html(){
		if ( is_array( $this->test ) ) {
			\ingot\testing\tests\click\click::increase_total( $this->test['ID'], $this->sequence['ID'] );

			return $this->html;
		}

	}

	/**
	 * Get the type of test this group uses
	 *
	 * @since 0.0.5
	 *
	 * @return string|void
	 */
	public function get_group_type() {
		if( is_array( $this->group ) ) {
			return $this->group[ 'type' ];
		}

	}

	/**
	 * Make HTML for to output and set in the html property of this class
	 *
	 * Should ovveride in final class to avoid outputting nothing, which is bad.
	 *
	 * @since 0.0.5
	 *
	 * @access protected
	 */
	protected function make_html() {
		$this->html = '';

	}

	/**
	 * Set the sequence property of this class
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 */
	private function set_sequence() {
		$_sequence = $this->get_current_sequence();
		if( $_sequence ) {
			$this->sequence = $_sequence;
		}
	}

	/**
	 * Get current sequence for this group and make sure its current
	 *
	 * @since 0.0.5
	 *
	 * @access private
	 */
	private function get_current_sequence(){
		$_sequence = helpers::v( 'current_sequence', $this->group, null );
		if( $_sequence ) {
			$_sequence = sequence::read( $_sequence );
			if( $_sequence && 0 == $_sequence[ 'completed' ] ) {
				return $_sequence;
			}

		}

	}

	protected function set_group( $group ) {
		if( is_array( $group ) ) {
			$this->group = $group;
		}else{
			$this->group = group::read( $group );
		}
	}

	/**
	 * Set the test property of this class
	 *
	 * @since 0.0.5
	 *
	 * @access private
	 */
	private function set_test() {
		$chance = $this->calculate_chance();
		$use_a = \ingot\testing\tests\click\click::choose_a( $chance );
		if ( $use_a ){
			$test_id = $this->sequence[ 'a_id' ];
		}else{
			$test_id = $this->sequence[ 'b_id' ];
		}

		$this->test = \ingot\testing\crud\test::read( $test_id );
	}


	/**
	 * Get chance of using A
	 *
	 * @since 0.0.5
	 *
	 * @access protected
	 */
	protected function calculate_chance(){
		return 50;
	}

	protected function get_group() {
		return $this->group;
	}

	protected function get_test(){
		return $this->test;
	}

	protected function get_sequence() {
		return $this->sequence;
	}

}
