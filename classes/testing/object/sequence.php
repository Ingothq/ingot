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

namespace ingot\testing\object;


class sequence {

	private $a_win;

	private $extra_fields = array(
		'a_win_percentage',
		'b_win_percentage',
		'a_total_percentage',
		'b_total_percentage',
		'total',
		'win_total'
	);

	public function __construct( $sequence ) {
		$sequence = $this->verify_sequence( $sequence );
		if( $sequence ){
			$this->set_properties( $sequence );
		}

	}

	public function __get( $field ) {
		if( in_array( $field, $this->allowed_fields() ) ) {
			if( isset( $this->$field ) ) {
				return $this->$field;
			}else{
				$this->$field = call_user_func( array( $this, $field ) );
				return $this->$field;
			}
		}
	}
	protected function verify_sequence( $sequence ) {
		if( is_numeric( $sequence ) ) {
			$sequence = \ingot\testing\crud\sequence::read( $sequence );
		}

		if( ! is_wp_error( $sequence ) && ! empty( $sequence ) ) {
			return $sequence;
		}

	}

	protected function allowed_fields() {
		return array_merge( \ingot\testing\crud\sequence::get_all_fields(),$this->extra_fields );
	}

	private function set_properties( $sequence ) {

		foreach( \ingot\testing\crud\sequence::get_all_fields() as $field ) {
			$this->$field = $sequence[ $field ];
		}

	}

	/**
	 * Calculate test A's win percentage
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return float
	 */
	protected function a_win_percentage() {

	}

	/**
	 * Calculate test B's win percentage
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return float
	 */
	protected function b_win_percentage() {

	}

	/**
	 * Calculate test A's percentage of times chosen.
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return float
	 */
	protected function a_total_percentage() {

	}

	/**
	 * Calculate test B's percentage of times chosen.
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return float
	 */
	protected function b_total_percentage() {

	}

	/**
	 * Calcualte total number of times tests of this sequence have ran.
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return int
	 */
	protected function total() {

	}

	/**
	 * Calcualte total number of times tests of this sequence have won.
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return int
	 */
	protected function total() {

	}






}
